TO BE DELETED

<!--
	Requires: index.php, jquery libraries, overlay.js, jquery-ui.js, database.php, MrClean.php, 
	required by: 

 -->


<?php 

//print_r($_POST);

if (isset($_POST['task1'])){ // 2 birds one stone, checks for createticket trigger and existence of a task
	// make form contents useful
	// key is defined by the name element in the html form
	foreach ($_POST as $key=>$value) {
		if ($key == "duedate") {
			$columns[] = "due";
			$values[] = "'".$value." ".$_POST['duetime'].":00'";
		} else if ($key == "duetime") {
			continue;
		} else if (substr($key,0,4) == "task") {
			$tasks[] = clean($value);
		} else if (substr($key,0,5) == "price") {
			$prices[] = clean($value);
		} else {
			$columns[] = $key;
			$values[] = "'".clean($value)."'";
		}
	}
	$columns = parseColumns($columns);
	$values = join(",", $values);
	$query = "INSERT INTO ticket($columns) VALUES ($values)";
	//echo $query;
	$tickno = insertQuery($query);
	if ($tickno) echo "Ticket saved #".$tickno;
	else {echo "Ticket not saved";}

	$i=0;
	while ($i < sizeof($tasks)) {
		$query = "INSERT INTO task(ticketno,jobno,taskno,price) VALUES ($tickno,".($i+1).",$tasks[$i],$prices[$i])";
		$jobno = insertQuery($query);
		$i += 1;
	}
	echo " Tasks: ".$i;
	$_POST = array(); // clear POST array for subsequent submits
}

?>
