<?php
	require "../../MrClean.php";
	require "database.php";

	$tickets = sendQuery("SELECT t.ticketno, t.custno, c.firstname, c.lastname, t.duedate, t.notes, s.jobno, s.taskno, m.taskname, s.price, t.status, s.complete "
	. "FROM ticket t "
	. "INNER JOIN customer c "
	. "USING (custno) "
	. "LEFT OUTER JOIN task s "
	. "USING (ticketno) "
	. "INNER JOIN taskmenu m "
	. "USING (taskno) "
	. "WHERE status<>'complete' "
	. "ORDER BY duedate ASC, ticketno ASC, jobno ASC");
	echo json_encode($tickets);
?>

	