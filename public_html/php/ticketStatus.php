<?php //NO LONGER IN USE!!!
	// Receives a ticketno and updates the tickets status in the database

	require "../../MrClean.php";
	require "database.php";
	
	$ticket = clean($_POST[0]);
	$oldStatus = clean($_POST[1]);
	$ticket = str_pad($ticket, 6, "0", STR_PAD_LEFT);
	
	// Get ticket and task statuses
	$tasks = sendQuery("SELECT t.status, s.complete"
	." FROM ticket t"
	." INNER JOIN task s"
	." USING (ticketno)"
	." WHERE ticketno='".$ticket."'");
	
	// Check if ticket is complete
	$status = "complete";
	//$oldStatus = $tasks[0][0];
	$foo = " ";
	foreach ($tasks as $t) {
		$foo = $foo.$t[1] . ", ";
		if ($t[1] == 0) {
			$status = "open";
		}
	}
	$foo = $foo ."\n<br/>";
	
	// Check if page needs to be updated and return string
	if ($status == $oldStatus) {
		$update = "no";
	} else {
		insertQuery("UPDATE ticket"
		." SET status='".$status."'"
		." WHERE ticketno='".$ticket."'");
		$update = "yes";
	}
	echo $foo;//$update; //"New: ".$status.", Old: ".$oldStatus;
?>