<?php
/*
this file recieves json arrays and determines what to send to database.php

the C in MVC.

*/

require "../../MrClean.php";
require "database.php";


// put all database column names with numeric SQL datatypes here. (used to determine necessity of quotes)
$numericColumns = array('amount','ticketno','price', 'jobno', 'taskno', 'complete', 'custno', 'empno', 'active');

/*
return a valid SQL INSERT statement string given an array of key-value pairs
where keys are column names, values are data to insert and tablename is a
valid table name or complete join string 
*/
function makeInsertStatement($array, $tablename) {
	global $numericColumns;
	foreach ($array as $k => $v) {
		// skip null value columns
		if (strlen($v) == 0) continue;
		
		// glue date and time together
		if ($k == 'duedate') {
			$columns[] = 'duedate';
			$values[] = "'".$v." ".$array['duetime'].":00'";
		} else if ($k == 'duetime') continue;
		
		// remove non-numeric characters from phone entry
		else if ($k == 'phone') {
			$columns[] = $k;
			$values[] = preg_replace('/\D/','',$v);

		// build (columns) and (values) lists
		} else {
			$columns[] = $k;

			// append to list, with quotations if needed
			if (in_array($k, $numericColumns)) {
				$values[] = clean($v);
			} else {
				$values[] = "'".clean($v)."'";
			}
		}
	}

	// stringify
	$columns = join(",",$columns);
	$values = join(",",$values);
	return "INSERT INTO $tablename ($columns) VALUES ($values)";
}

function makeUpdateStatement($array, $tablename, $whereColumn, $whereValue) {
	global $numericColumns;
	foreach ($array as $k => $v) {

		if (strlen(clean($v))==0) {
			$set[] = $k."=null";
		}
		//glue date and time together
		else if ($k == 'duedate') {
			$set[] = 'duedate='."'".$v." ".$array['duetime'].":00'";
		} else if ($k == 'duetime') continue;

		// remove non-numeric characters from phone entry
		else if ($k == 'phone') {
			$set[] = $k."='".preg_replace('/\D/','',$v)."'";

		// build "columns = values" list
		} else {
			if (in_array($k, $numericColumns)) {
				$set[] = $k."=".clean($v);	
			} else {				
				$set[] = $k."='".clean($v)."'";	
			}
		}
	}

	// stringify
	if (!in_array($whereColumn, $numericColumns)) $whereValue = "'".$whereValue."'";
	$set = join(",",$set);
	return "UPDATE $tablename SET $set WHERE $whereColumn = $whereValue";
}

// updates a ticket to paid or unpaid based on its balance.
function updatePaid($ticketno) {
	$payments = sendQuery("SELECT sum(amount) FROM payment WHERE ticketno = $ticketno");
	$total = sendQuery("SELECT sum(price) FROM task WHERE ticketno = $ticketno");
	$balance = (float)$total[0]-(float)$payments[0];
	if ($balance > 0) {
		$statement = "UPDATE ticket SET status='unpaid' WHERE ticketno = $ticketno";
	} else {
		$statement = "UPDATE ticket SET status='paid' WHERE ticketno = $ticketno";
	}
	return insertQuery($statement);
}

////////////////////////////////
// UPDATE AND INSERT REQUESTS //
////////////////////////////////


// Insert or update entries in customer and ticket tables
if (in_array($_POST[0], array('cust','tick','pay'))) {

	// check for a ticketno or custno to update, if null it's an insert request
	$id = clean($_POST[1]);
	$type = ($id=='') ? 'INSERT' : 'UPDATE';

	// set query table and column for WHERE clause
	switch ($_POST[0]) {
		case 'cust': $table = 'customer'; $key = 'custno'; break;
		case 'tick': $table = 'ticket'; $key = 'ticketno'; break;
		case 'pay': $table = 'payment'; $key = 'transno'; break;
	}

	if ($type == 'INSERT') {
		$statement = makeInsertStatement($_POST[2], $table);
	} else {
		$statement = makeUpdateStatement($_POST[2], $table, $key, $id);
	}

	// returns the last inserted PK, or 0 if update
	echo insertQuery($statement);

	if ($_POST[0] == 'pay') {
		echo updatePaid($_POST[2]['ticketno']);
	}
}

//////////////////
// TASK SUBMITS //
//////////////////

//Insert, Update or delete taskmenu table
else if($_POST[0] == 'taskmenu')
{
	if($_POST[1] == "INSERT")
	{
		$taskName = clean($_POST[2]);
		$price = clean($_POST[3]);
		$values = "'" . $taskName . "'," . $price;
		$statement = "INSERT INTO taskmenu (taskname,price) VALUES ($values)";	
		echo insertQuery($statement);

	}	
	else if($_POST[1] == "DELETE")
	{
		$id = $_POST[2];
		$activeTasks = sendQuery("SELECT taskno FROM ticket JOIN task using(ticketno) WHERE status IN ('paid', 'unpaid') GROUP BY taskno");
		if (in_array($id, $activeTasks)) {
			echo 0;
			return;
		} else {
			//
			insertQuery("UPDATE taskmenu SET active = 0 WHERE taskno = $id");
			insertQuery("DELETE FROM taskmenu WHERE taskno = $id");
			echo 1;
		}
	}
}

// Task submission (ticket update clears and re-inserts tasks to maintain jobno sequencing)
else if ($_POST[0] == 'taskinsert') 
{
	$ticketno = clean($_POST[1]);

	insertQuery("DELETE FROM task WHERE ticketno = $ticketno");

	// prepare INSERT statement
	$i = 0;
	$tasks = $_POST[2];
	while ($i<count($tasks)) {
		$taskno = clean($tasks[$i][0]);
		$price = clean($tasks[$i][1]);
		$complete = ($tasks[$i][2] == 'true') ? 1 : 0;
		$i++;
		$statement = "INSERT INTO task VALUES ($ticketno,$i,$taskno,$price,$complete)";
		$success = insertQuery($statement);
	}
	echo $success;

	echo updatePaid($ticketno);
} 

else if ($_POST[0] == 'taskcheck') 
{
	$ticketno = clean($_POST[1]);
	$jobno = clean($_POST[2]);
	$complete = clean($_POST[3]);

	// prepare UPDATE statement
	$statement = "UPDATE task SET complete='$complete' WHERE ticketno = $ticketno AND jobno = $jobno";
	insertQuery($statement);

}
// PICKUP
else if ($_POST[0] == 'pickup') {
	// Changes the status to complete, or paid if cancelled
	
	$ticketno = clean($_POST[1]);
	$status = clean($_POST[2]);

	// prepare UPDATE statement
	if ($status == 'complete') {
		$statement = "UPDATE ticket SET status='complete', closedate=SYSDATE() WHERE ticketno = $ticketno";	
	} else {
		$statement = "UPDATE ticket SET status='$status', closedate=NULL WHERE ticketno = $ticketno";
	}
	echo insertQuery($statement);

}

////////////////////
// QUERY REQUESTS //
////////////////////

else if (in_array($_POST[0], array('custsearch','ticksearch','tasksearch','paysearch'))) 
{
	// build WHERE clause
	$where = '';
	if (!is_array($_POST[1])) {

		$id = $_POST[1]; // the value to search for in WHERE clause
		$key = clean($_POST[2]); // column name for WHERE clause
		if (strpos('date', $key)) {
			$id = "DATE('$id')";
			$key = "DATE($key)";
		}
		$where = (in_array($key, $numericColumns)) ? " WHERE $key = $id" : "WHERE $key LIKE '%$id%'";
	} else if (count($_POST[1])>0) {
		for ($i=0; $i<count($_POST[1]); $i++) {
			if (strlen($_POST[1][$i])>0) {
				$id[] = clean($_POST[1][$i]); // the value to search for in WHERE clause
				$key[] = clean($_POST[2][$i]); // column name for WHERE clause
			}
		}
		$where = "WHERE";
		for ($i=0; $i<count($key); $i++) {
			$v = $id[$i]; // the value to search for in WHERE clause
			$k = $key[$i]; // column name for WHERE clause
			if (strpos('date', $k)>0) {
				$v = "DATE('$v')";
				$k = "DATE($k)";
			}
			$where = (in_array($key[$i], $numericColumns)) ? "$where $k = $v" : "$where $k LIKE '%$v%'";
			if ($i < count($key)-1) $where .= " AND ";
		}
		if ($where == "WHERE") $where = '';
	}

	// determine which table(s) to query from
	switch ($_POST[0]) {
		case 'custsearch':
			$table = 'customer';
			break;
		case 'ticksearch':
			$table = 'customer join ticket using (custno) left join task using (ticketno) join taskmenu using (taskno)';
			break;
		case 'tasksearch':
			$table = 'taskmenu';
			break;
		case 'paysearch':
			$table = 'payment join ticket using(ticketno) join customer using (custno)';
			break;
	}

	// construct return columns list
	foreach ($_POST[3] as $colname) {
		$columns[] = clean($colname);
	}
	$columns = join(",",$columns);

	$statement = "SELECT $columns FROM $table $where";

	// append order by clause for tickets
	if ($_POST[0]=='ticksearch') {
		$statement = "$statement ORDER BY ticketno, jobno";
	}

	$results = sendQuery($statement);

	// returns a json encoded array of rows
	echo json_encode($results);
	
} else if ($_POST[0] == 'autocomp') {
	
	$phone = clean($_POST[1]);
	$statement = "SELECT phone FROM customer WHERE phone LIKE '$phone%'";
	$results = sendQuery($statement);
	
	echo json_encode($results);
}


// this was just a tester to show that communication is happening without 
// having the php loaded in the browser
// useful for debugging controller requests
 /*
 $postdump = var_export($_POST, true);
$myFile = "../testFile.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, ($postdump."\n"));
fwrite($fh, ($statement."\n"));
fclose($fh); 
*/


?>
