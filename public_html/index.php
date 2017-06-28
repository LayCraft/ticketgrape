<?php
	require "../MrClean.php";
	require "php/database.php";
?>
<!DOCTYPE html>
<html>
	<head>

		<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
		<!-- style for basic page layout elements -->
		<link rel="stylesheet" href="css/basic.css">
		<!-- style for active tickets and stubs in the main list-->
		<link rel="stylesheet" href="css/activeTicket.css"/>
		<!-- style for ticket add/edit window -->
		<link rel="stylesheet" href="css/overlay.css"/>
		
		<link rel="stylesheet" href="css/jquery-ui.min.css">
		<link rel="stylesheet" href="css/jquery-ui.structure.min.css">
		<link rel="stylesheet" href="css/jquery-ui.theme.min.css">
		<link rel="stylesheet" href="css/jquery.ui.timepicker.css">
	
		<script src="js/jquery-1.11.2.min.js"></script>
		<script src="js/jquery-ui.min.js"></script>
		<script src="js/jquery.ui.timepicker.js"></script>
		<script src="js/overlay.js"></script>
		<script src="js/main.js"></script>
	</head>

	<body>
		<!--Loading-->
		<div id="loading">
			<img src="images/loading_small.gif"/>
			<h3 class="loadingText"> Loading...</h3>
		</div>
		<!-- Start of overlay -->
		<div id="overlay" onclick="closeOverlay()"></div>
		<div class="overlaywrap">	
			
			<!--Ticket window-->
			<div id="createticket" class="overlay-content">	
				<div class="overlayhat"></div>
					<a class="cancel-overlay fa fa-close" onclick="closeOverlay()"></a>
					<h2 class="tickethead">Ticket<input type="text" id="tticketno" readonly tabindex="=1" /></h2>
					<form class="overlay-form" id="ticketform"/>	
						<textarea class="overlay-message" id="tmessage" rows="1" readonly tabindex="-1"></textarea>
						<div class="form-group" id="ticketleft">
							<label class="form-label">Phone Number</label>
							<!-- autocomp requires jquery-ui.min.js -->
							<input  type="text" id="tphone">
							<button onclick="lookUp()" type="button" >Lookup</button>
						
							<label class="form-label">Name</label>
							<input type="text" id="tname" readonly />
						
							<label class="form-label">Customer#</label>
							<input type="text" id="tcustno" readonly />
						
							<label class="form-label">Date Due</label>
							<input type="text" id="tduedate" value="<?php echo date('Y-m-d', time()+(3*24*60*60)); ?>"/>
							<input type="time" id="tduetime" style="height:initial;" value="15:00"/>

							<label class="form-label">Notes</label>
							<textarea rows="6" cols="31" id="tnotes"></textarea>
						</div>
						<div class="form-group" id="ticketright">
							<label class="form-label">Tasks<a onclick="addTask()"> [add task]</a></label>
							<div id="task-list">
								<div id="tasks">
								</div>
								<!--<button class="task-button" onclick="addTask()" type="button">New Task</button>-->
							</div>
							<!--  resetTask is in overlay.js -->			
							<!--
							<button class="form-button" type="reset" onclick="resetTasks()">Reset</button>
							-->
							<button class="form-button" type="button" onclick="submitTicket()">Save</button>	
						</div>
						<div class="separator"></div>
													
					</form>
				<div class="overlayboots"></div>
			</div>	
			<!--End of Ticket window-->

			<!--Customer window-->
			<div id="customerinfo" class="overlay-content">
				<div class="overlayhat"></div>
					<a class="cancel-overlay fa fa-close" onclick="closeOverlay()"></a>
					<h2 class="tickethead">Customer Info<input id="ccustno" type="text" readonly="readonly" tabindex="-1" /></h2>
					<form class="overlay-form" id="customerform"/>
						<textarea class="overlay-message" id="cmessage" rows="1" readonly tabindex="-1"></textarea>
						<div class="form-group">
							<label class="form-label">First Name</label>
							<input id="cfirstname" type="text"/>
						</div>
						<div class="form-group">
							<label class="form-label">Last Name</label>
							<input id="clastname" type="text"/>
						</div>
						<div class="form-group">
							<label class="form-label">Phone Number</label>
							<input id="cphone" type="tel"/>
						</div>
						<div class="form-group">
							<label class="form-label">Ext</label>
							<input id="cext" type="text"/>
						</div>
						<div class="form-group">
							<label class="form-label">Address</label>
							<input id="caddress" type="text"/>
						</div>
						<div class="form-group">
							<label class="form-label">Postal Code</label>
							<input id="czip" type="text"/>
						</div>
						<div class="form-group">
							<label class="form-label">Email</label>
							<input id="cemail" type="email"/>
						</div>
						<div class="form-group">
						</div>
						<div class="form-group">
							<button type="button" class="form-button" onclick='submitCustomer();'>Save</button>
						</div>
					</form>
				<div class="overlayboots"></div>	
			</div>
			<!--End of Customer window-->

			<!--Payment Window-->
			<div id="paymentwindow" class="overlay-content">
				<div class="overlayhat"></div>
					<a class="cancel-overlay fa fa-close" onclick="closeOverlay()"></a>
					<h2 class="tickethead">Apply Payment<input id="pticketno" type="text" readonly="readonly" tabindex="-1" /></h2>
					<form class="overlay-form" id="paymentform"/>
						<textarea class="overlay-message" id="pmessage" rows="1" readonly tabindex="-1"></textarea>
						<div class="form-group">
							<label class="form-label method-label">
							<input type="radio" name="pmethod" id="pcash-radio" value="cash" onclick="clickRadio(this)" checked/>Cash</label>
							<label class="form-label method-label">
							<input type="radio" name="pmethod" id="pcredit-radio" value="credit" onclick="clickRadio(this)"/>Credit</label>
							<label class="form-label">Credit Card #</label>
							<input id="pcreditcard" type="text" placeholder="4111111111111111" disabled/>
							<label class="form-label">Expiry</label>
							<input id="pexpdate" type="text" placeholder="1215" disabled/>
							<label class="form-label">Pay Amount $</label>
							<input id="pamount" type="text" onchange="resetPayment(this)"/>
							<button type="button" class="form-button" onclick="submitPayment()">Pay</button>
							<!--<button type="button" id="pclosebutton" class="form-button" onclick="closeOverlay(),refresh()">Close</button>-->
						</div>
						<textarea class="overlay-message" id="phistory" rows="9" readonly tabindex="-1"></textarea>
					</form>

				<div class="overlayboots"></div>	
			</div>
			<!--End of Payment window-->

		</div>
		<!-- End of overlay -->

		<!--This is the div that adjusts the margins on the left and right.-->
		<div id="wrapper">
			<div id="banner">
				<img src="images/logo.png">
				<a href="admin.php" id="settings" class="fa fa-wrench"></a>
				
				<a href="#" id="cross" onclick="newTicket()" class="fa fa-plus-square-o">
					Ticket
				</a>

			</div>
			<div class="bannerBoots">
			</div>
			
			<div id="body"><!--tickets are displayed here-->
			
				<div class="content">
					<div class="ticketlisthead">

						<h3>Active Tickets<a class="minimizeContent"> <i class="fa fa-caret-up"></i></a></h3>
					</div>
					<div class="collapsible">
						<div id="openTickets"></div>
					</div>
				</div>
				
				<div class="content">
					<div class="ticketlisthead">

						<h3>Completed Tickets<a class="minimizeContent"> <i class="fa fa-caret-up"></i></a></h3>
					</div>
					<div class="collapsible">
						<div id="completedTickets"></div>
					</div>
				</div>
				
			</div><!--End of tickets-->
			<div class="footerHat"></div>
			<div id="footer" >
				<p>Project Snordgruv - 2015</p>
			</div>
		</div>
	</body>
</html>
