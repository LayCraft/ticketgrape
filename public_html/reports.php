<?php
	require "../MrClean.php";
	require "php/database.php";
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/basic.css">
	<link rel="stylesheet" href="css/activeTicket.css">
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<script src="js/jquery-1.11.2.min.js"></script>
	<script src="js/taskCreation.js"></script>
	<style>
		#centre{
			width: 600px;
			margin: 0 auto;
				
			background: white;
			float: none;
			background: url(../images/bottomGradient.png) repeat-x bottom white;
			padding: 10px 0;
		}
		#centre input{
			display: block;
			margin: 0 0 15px 0;
		}
		#centre p{
			margin: 0 10px 10px 10px;
		}
		.content { text-align: center;}

		.taskInput{
			border-top: 2px black solid;
			border-bottom: 2px black solid;
			margin: 10px;
			padding: 10px 0;
		}
		.taskInput h3{ 
			margin: 10px 0;
		}
		.ticketbody{text-align: left;}
		.currentTasks{
			padding: 10px;
		}

		.currentTasks table tr td{
			padding: 5px;
			border: 2px black solid;
		}
		#ticketbody{
			text-align: left;
			display:inline-block;
			padding: 0 10px;
			float: none;
			background: url(../images/bottomGradient.png) repeat-x bottom white;
			padding: 10px 0;
		}
			
		#taskList tr td{
			padding: 5px;
			border: 2px black solid;
			}
		#leftwrap{display: inline-block; width:45%; min-width: 380px; padding:5px 10px;}
		#rightwrap{display: inline-block; width: 45%; min-width:380px; padding:5px 10px;}
			
</style>
</head>
<body>
<div id="wrapper">
	
	<div id="banner">
		<img src="images/logo.png">
		<a href="index.php" id="settings"> Main </a>
	</div>
	<div class="bannerBoots">
	</div>
				
	<div class="content">
		<div id="leftwrap">
		<div class="tickethat"></div>
			
		<div class="ticketbody">
			<div id="tickethead">
				<h3>Manage your business' tasks</h3>
				<p>This is the page where you can set regular tasks that appear on 
				you do for customers. If you need to price a task a bit cheaper, don't 
				worry about making multiple entries for the same task. When you make a new
				ticket, you can set a custom price by entering a price there!</p>
			</div>
			<div class="taskInput">
				<h3>New Task</h3>
				Task Name:
				<br><input type="text" id="tName">
				Default Price: <br>
				<input type="text" id="tPrice">
				<input type="button" value="save" onclick="saveTask()">
			</div>
			<div class="currentTasks">
				Don't see what you just added? Try a <a onClick="refresh()">[refresh]</a>.</br></br>
				<strong>Tasks:</strong>
				<br>
				<table>
					<div id="taskList">
						<!--tasks are inserted here -->
					</div>
				</table>
			</div>
		</div>
		<div class="ticketboots"></div>
		</div>

</body>
</html>


