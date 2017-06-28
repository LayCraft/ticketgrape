<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/basic.css">
	<link rel="stylesheet" href="css/activeTicket.css">
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
	<script src="js/jquery-1.11.2.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery-print.min.js"></script>
	<script src="js/admin.js"></script>
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

		/* cross is the button for adding new tickets. */
		#btn{
			position: fixed;
			right: 0;
			top: 85px;
				font-size: 150%;
			color: #349CD7;
			padding: 20px;
			background-color: white;
			border: 2px #349CD7 solid;
			text-align: center;
			z-index: 1;
		    box-sizing: border-box;
			-webkit-border-radius: 30px;
			-webkit-border-bottom-right-radius: 0;
			-webkit-border-top-right-radius: 0;
			-moz-border-radius: 30px;
			-moz-border-radius-bottomright: 0;
			-moz-border-radius-topright: 0;
			border-radius: 30px;
			border-bottom-right-radius: 0;
			border-top-right-radius: 0;
		}
		#btn:hover{
			
			background-color: white;
			border: 2px outset #F9AF1D;
			text-align: center;
		    box-sizing: border-box;
			color: #F9AF1D;
		}

		/* cross is the button for adding new tickets. */
		#back{
			z-index: 1;
			position: fixed;
			right: 0;
			top: 20px;
			font-size: 200%;
			color: #F9AF1D;
			padding: 0px 10px;
			background-color: white;
			border: 2px #F9AF1D solid;
			text-align: center;
			
		    box-sizing: border-box;
			-webkit-border-radius: 30px;
			-webkit-border-bottom-right-radius: 0;
			-webkit-border-top-right-radius: 0;
			-moz-border-radius: 30px;
			-moz-border-radius-bottomright: 0;
			-moz-border-radius-topright: 0;
			border-radius: 30px;
			border-bottom-right-radius: 0;
			border-top-right-radius: 0;
		}
		#back:hover{
			
			background-color: white;
			border: 2px outset #349CD7;
			text-align: center;
		    box-sizing: border-box;
			color: #349CD7;
		}




h3 a:hover{
	color: #F9AF1D;
}

		input[type="checkbox"] {
			margin:auto;
		}

		div.form {
			padding:20px;
		}

		div.print table {
			margin:10px auto;
			width: auto;
		}

		td, th {
			border: 1px solid;
			font-size: 10pt;
		}
		td {
			font-family: monospace;
		}

</style>
</head>
<body>
<div id="wrapper">
	
<!--		<a onclick="showSec('#Reports')" id="btn" class="fa fa-wrench"> Admin</a>
-->
		<a href="index.php" id="back" class="fa fa-close"></a>

	<div id="banner">
		<img src="images/logo.png">
	</div>
	<div class="bannerBoots">
	</div>
				
	<div class="tickethat"></div>

	<!--TASK DIV START -->
			
		<div class="ticketbody toggle" id="Tasks">
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
			<div id="tickethead">
				<h3>Manage Tasks</h3>
				<p>This is the page where you can set regular tasks that appear on 
				you do for customers. If you need to price a task a bit cheaper, don't 
				worry about making multiple entries for the same task. When you make a new
				ticket, you can set a custom price by entering a price there!</p>
			</div>
			<div class="taskInput">
				<h3>New Task</h3>
				Task Name:
				<br>
				<input type="text" id="tName">
				<br>
				Default Price:
				<br>
				<input type="text" id="tPrice">
				<br>
				<input type="button" value="save" onclick="saveTask()">
			</div>
			<div class="currentTasks">
				Don't see what you just added? Try a <a onClick="refresh()">[refresh]</a>.<br><br>
				<strong>Tasks:</strong>
				<br>
				<table>
					<div id="taskList">
					<!--tasks are inserted here -->
					</div>
				</table>
			</div>
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
		</div>

	<!-- TASK DIV END -->
	
	<!-- REPORTS DIV START -->
	<div class="ticketbody toggle" id="Reports">
	<div class="form-group">
		<h3>Reports</h3>
		<p>
		<a onclick="showSec('#Report1')">Ticket Search</a><br>
		Display ticket history by date/customer
		</p>
		<p>
		<a onclick="showSec('#Report2')">Payment Search</a><br>
		Display payment history by date/customer
		</p>
		<p>
		<a onclick="showSec('#Report3')">Summary Report</a><br>
		Generate a summary report
		</p>
	</div>
	<div class="form-group">
		<h3>Tasks</h3>
		<p>
		<a onclick="showSec('#Tasks')">Manage Tasks</a><br>
		Add/Remove tasks from task menu
		</p>
	</div>
	</div>
	<!-- REPORTS DIV END -->

	<!--TICKET SEARCH -->
	<div class="ticketbody toggle" id="Report1">
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
		<h3>Reports | Ticket Search</h3>
		<p>Enter in selection filters to display ticket history. The date field accepts partial dates like <em>2010</em> or <em>2011-02</em> for a range of dates.</p>
		<div class="form">
			<h5>Customer filter: </h5>
			<input id="form1-input">
			<br>
			<label>By:</label>
			<input type="radio" name="form1-radio" value="custno" checked> Customer #
			<input type="radio" name="form1-radio" value="phone"> Phone
			<input type="radio" name="form1-radio" value="lastname"> Last name
			<br><br>
			<h5>Date Filter: </h5>
			<input id="form1-date" class="datepicker">
			<br>
			<input type="radio" name="form1-radio2" value="createdate" checked> Order date
			<input type="radio" name="form1-radio2" value="duedate"> Due date
			<br>
			<button onclick="searchTickets()" class="fa fa-search">Run</button>
			<div class="print">
				<table id="form1-table">
				<!-- Report data goes here -->
				</table>
			</div>
			<button class="print fa fa-print" onclick="printForm(this)">Print</button>
		</div>
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
	</div>
	
	<!-- TICKET SEARCH END -->


	<!-- PAYMENT SEARCH -->
	<div class="ticketbody toggle" id="Report2">
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
		<h3>Reports | Payment Search</h3>
		<p>Enter in selection filters to display payment history. The date field accepts partial dates like <em>2010</em> or <em>2011-02</em> for a range of dates.</p>
		<div class="form">
			<h5>Customer filter: </h5>
			<input id="form2-input">
			<br>
			<label>By:</label>
			<input type="radio" name="form2-radio" value="custno" checked> Customer #
			<input type="radio" name="form2-radio" value="phone"> Phone
			<input type="radio" name="form2-radio" value="lastname"> Last name
			<br><br>
			<h5>Date Filter: </h5>
			<input id="form2-date" class="datepicker">
			<br>
			<button onclick="searchPayments()" class="fa fa-search">Run</button>
			<div class="print">
				<table id="form2-table">
				<!-- Report data goes here -->
				</table>
			</div>
			<button class="print fa fa-print" onclick="printForm(this)">Print</button>
		</div>
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
	</div>
	<!-- PAYMENT SEARCH END -->

	<!-- SUMMARY SEARCH -->
	<div class="ticketbody toggle" id="Report3">
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
		<h3>Reports | Summary Report</h3>
		<p>Enter a date to display a summary report. The date field accepts partial dates like <em>2010</em> or <em>2011-02</em> for a range of dates.</p>
		<div class="form">
			<h5>Date Filter: </h5>
			<input id="form3-date" class="datepicker">
			<br>
			<button onclick="getReport()" class="fa fa-search">Run</button>
			<div class="print">
				<table id="form3-table">
				<!-- Report data goes here -->
				</table>
			</div>
			<button class="print fa fa-print" onclick="printForm(this)">Print</button>
		</div>
                <h3><a onclick="showSec('#Reports')" class="fa fa-reply"></a></h3>
	</div>
	<!-- PAYMENT SEARCH END -->


	<div class="ticketboots"></div>
	
</body>
</html>


