/*
This JS file is for functions in the main ticket pane. Use vanilla.js to keep things fast

Requires: jquery, 
Required by: index.php

*/


///////////////////////////////
//                           //
//    PAGE INITIALIZATION    //
//                           //
///////////////////////////////

// Global variable, true whenever the loading icon is on the screen
var pageLoading = false;

$(document).ready( function() {	

	// PLACE INITIALIZATION FUNCTIONS AND EVENT LISTENERES HERE

	// Minimize content pane
	$('div#body').on('click', '.minimizeContent', function() {
		// Toggle ticket display
		$(this).parents('.content').children('.collapsible').toggle();
		
		// Set button text
		$(this).html($(this).html() == ' <i class="fa fa-caret-up"></i>'? ' <i class="fa fa-caret-down"></i>': ' <i class="fa fa-caret-up"></i>');
	});

	// ticket window date and time picker event listeners
	$('#tduedate').datepicker({
	  dateFormat: "yy-mm-dd"
	});

	$('#tduetime').timepicker({
	   defaultTime: '15:00',
	   hours: {starts: 8, ends: 20},
	   minutes: {interval: 30},
	   rows: 2
	});
	
	// Refresh tickets
	refresh();

	// Get taskmenu
	getTasks();
});


/////////////////////////////////
//                             //
//    FORM SUBMIT FUNCTIONS    //
//                             //
/////////////////////////////////

// send a POST request to controller.php
function postData(json){
	startLoading();

	$.post("php/controller.php", json, function(data) {
		//console.log(data);
		if (json[0] == 'tick') {
			// trigger task submission after ticket submission is done
			ticketNum = data == 'success' ? '' : data;
			submitTasks(ticketNum);
		} else if (json[0] == 'cust' && document.getElementById("tphone").value) {
			// re-route user back to ticket window if they triggered new customer from lookup button
			document.getElementById("tphone").value = document.getElementById("cphone").value;
			lookUp();
			$("#customerinfo").hide();
			$("#createticket").show();
		} else if (json[0] == 'pay') {
			// don't close the window on payment submission, get new balance and update payment history
			refresh();
			getBalance(json[2]['ticketno']);
		} else {
			// every other use case
			refresh();
			closeOverlay();
		}
	});
}

// add or update main ticket details
function submitTicket(){
	if (document.getElementsByClassName('task').length==0) {
		showMsg("No tasks added", "red","tmessage")
		return;
	}
	if (document.getElementById('tcustno').value == '') {
		showMsg("No customer information", "red", "tmessage")
		return;
	}
	// place to assemble the KV pairs from the form
	var formData = {};
	// holds a ticket number if available, or null (not a user input field)
	var ticketNum = document.getElementById('tticketno').value;
	// the name of the html id tags to grab and turn into kv pairs
	var fieldIDs = [ 
		'tcustno',
		'tduedate',
		'tduetime',
		'tnotes'
		];
	// for each field, save a key value pair into formData	
	for (i=0; i < fieldIDs.length; ++i){
		// collect the value of the field
		var myKey = fieldIDs[i].substring(1);
		var myValue = document.getElementById(fieldIDs[i]).value;
		// add key value pair to form data
		formData[myKey] = myValue;
	}

	if (9==0) {
		showMsg("Please enter a valid pickup date", "red", "tmessage");
	} else {
		var formArray = {0:'tick',1: ticketNum, 2: formData};
		ticketNum = postData(formArray);	
	}
}

// submit tasks to database -- only to be triggered on success of submitTicket()
function submitTasks(ID){
	var formData = {};

	// holds the newly inserted ticketno, or existing one for updates
	var ticketNum = (ID == 0) ? document.getElementById('tticketno').value : ID;

	var tasks = document.getElementsByClassName('ttaskno');
	var prices = document.getElementsByClassName('tprice');
	var checks = document.getElementsByClassName('tcomplete');

	// for each task, save a tuple of taskno and price
	for (i=0; i < tasks.length; ++i) {
		// add the tuple to form data
		formData[i] = [tasks[i].value, prices[i].value, checks[i].checked];
	}
	
	var formArray = {0:'taskinsert',1: ticketNum, 2: formData};
	postData(formArray);
}

// add or update customer profile
function submitCustomer(){
	// place to assemble the KV pairs from the form
	var formData = {};
	// holds a ticket number if available
	var custNum = document.getElementById('ccustno').value;
	// the name of the html id tags to grab and turn into kv pairs
	var fieldIDs = [
		'cphone',
		'cfirstname',
		'clastname',
		'cext',
		'caddress',
		'cemail',
		'czip'
		];
	// for each field, save a key value pair into formData	
	for (i=0; i < fieldIDs.length; ++i) {
		// collect the value of the field
		var myKey = fieldIDs[i].substring(1);
		var myValue = document.getElementById(fieldIDs[i]).value;
		// add key value pair to form data
		formData[myKey] = myValue;
		// clear the field from the form
	}

	if (formData['phone'].replace(/\D/g,'').length<10) {
		showMsg("Required field: Invalid phone number", "red", "cmessage");
	} else if (formData['lastname'].replace(/\W/g,'').length==0) {
		showMsg("Required field: Last name", "red", "cmessage");
	} else {
		var formArray = {0:'cust',1: custNum, 2: formData};
		postData(formArray);
	}
}

// submit a payment to database
function submitPayment() {
	// scrape the form (ew...)
	var ticketNum = document.getElementById('pticketno').value;
	var amount = parseFloat(document.getElementById('pamount').value);
	var card = document.getElementById('pcreditcard').value;
	var exp = document.getElementById('pexpdate').value;

	if (amount > 0) {
		// assemble transaction data
		var payData = {};
		payData['AMOUNT'] = amount;

		// process credit card payment
		if (document.getElementById('pcredit-radio').checked) {
			payData['CREDITCARD'] = card;
			payData['EXPDATE'] = exp;

			// send transaction request to PayPal
			$.post("php/payment.php", payData, function(data) {
				console.log(data);
				var json = JSON.parse(data);
				// display Approvad/Decline message
				if (json['RESPMSG'] == 'Approved') {
					showMsg("Approved AUTH: "+json["AUTHCODE"], "green", "pmessage");

					// save transaction to database
					var formArray = {0:'pay',1: '', 2: {"amount":amount, "ticketno":ticketNum, "auth":json["AUTHCODE"]} };
					postData(formArray);
				} else {
					showMsg("Result "+ json["RESULT"]+ ": " + json["RESPMSG"], "red", "pmessage");
				}	
			});
		// process cash payment
		} else if (document.getElementById('pcash-radio').checked) {
			// determine payment amount to be stored in database
			paid = amount > currentBalance ? currentBalance : amount;

			// display change due
			showMsg("Change due: $" + (amount-paid).toFixed(2), "green", "pmessage");

			// save transaction to database
			var formArray = {0:'pay',1: '', 2: {"amount":paid, "ticketno":ticketNum, "auth":"cash"} };
			postData(formArray);
		}

	} else {
		showMsg("Please enter a valid payment amount", "red", "pmessage");
	}
}

// displays a feedback message at the top of overlay windows.
// textareaid can be one of pmessage, tmessage, cmessage
function showMsg(message, color, textareaid) {
	document.getElementById(textareaid).style.color = color;
	document.getElementById(textareaid).value = message;
}


/////////////////////////////////
//                             //
//    TICKET PANE FUNCTIONS    //
//                             //
/////////////////////////////////

function startLoading() {
	pageLoading = true;
	
	// Display loading message
	document.getElementById("loading").style.display = "block";
	
	// Disable buttons
	$('button.form-button').attr("disabled", true);
}
function stopLoading() {
	pageLoading = false;
	
	// Hide loading message
	document.getElementById("loading").style.display = "none";
	
	// Disable buttons
	$('button.form-button').attr("disabled", false);
}

// Occurs when a ticket checkbox is clicked
function checkEvent(ticketno, jobno) {
	
	// Disable checkbox
	var checkbox = document.getElementById(ticketno+"-"+jobno);
	checkbox.disabled = true;
	
	// Create an array with the ticketno, jobno, and checked status
	var complete = (checkbox.checked==true) ? 1 : 0;
	var formArray = {0:'taskcheck',1: ticketno, 2: jobno, 3: complete};
	
	// Send the array to the controller
	$.post("php/controller.php", formArray, function(data) {
		
 	    // Check if the status has changed
		var oldStatus = getStatus(ticketno);
		var status = checkStatus(ticketno);
		
		if (status == oldStatus) {
			
			// Re-enable checkbox
			checkbox.disabled = false;
	    } else {
			
			// Refresh the ticket list if the ticket needs to move to or from completed tickets
			startLoading();
            refresh();
		}
	});
}

// Check the currently displayed status of the ticket
function getStatus(ticketno) {
	if ($('div#completedTickets').find($('div.ticketWrapper#'+ticketno)).length == 1) {
		var status = "complete";
	} else {
		var status = "open";
	}
    return status;
}

function checkStatus(ticketno) {
	// Get the check boxes for this ticket
	var ticket = $('div.ticketWrapper#'+ticketno);
	var boxes = ticket.find('.checkbox');

    // Check if the status has changed	
    var status = "complete"
	for (i = 0; i < boxes.length; i++) {
		if (boxes[i].checked == false) {
			status = "open"
			break;
		}
	}
    return status;
}

// Pickup button event
function pickup(ticketno) {
	// Get elements
	var ticket = $('div.ticketWrapper#'+ticketno);
	var status = ticket.find('.ticketStatus');
	var button = ticket.find('.ticketPickupButton');
	
	// Get the status
	if (status.html() == "PAID") {
		// PICKUP
		var newStatus = 'complete';
	} else {
		// CANCEL PICKUP
		var newStatus = 'paid';
	}
	var array = {0: 'pickup', 1: ticketno, 2: newStatus};
	$.post("php/controller.php", array, function(data) {
		if (newStatus == 'paid') {
			// CANCEL PICKUP
			status.html("PAID");
			button.html("Pickup");
		} else {
			// PICKUP
			status.html("PICKED UP");
			button.html("Cancel Pickup");
		}
	});
}

function refresh() {
	// Refresh the ticket pane
	$.ajax({
		url: "php/tickets.php",		
		dataType: "json",
		success: function(data) { 
		/* 
		JSON 2D array is returned
		
		Query results:
		t - ticket
		c - custno
		s - task
		m - taskmenu
		
		0 - t.ticketno
		1 - t.custno
		2 - c.firstname
		3 - c.lastname
		4 - t.duedate
		5 - t.notes
		6 - s.jobno
		7 - s.taskno
		8 - m.taskname
		9 - s.price
		10 - t.status
		11 - s.complete
		*/
		
		// Clear any existing tickets
		$('#openTickets').html("");
		$('#completedTickets').html("");
		
		// Iterate through each ticket and append to ticketlist
		var i = 0;
		while (i < data.length) {
			var ticketno = parseInt(data[i][0], 10);
			var custno = parseInt(data[i][1], 10);
			var fname = data[i][2];
			var lname = data[i][3];
			var due = data[i][4];
			var notes = data[i][5];
			var status = data[i][10];
			var ticketprice = 0;
			due = due.substr(0, 16);
			
			// Will become false if any tasks are incomplete
			var tasksComplete = true;
			
			// Iterate through tasks and append to one string
			var tasks = "";
			do {
				// If the task is marked as complete
				if (data[i][11] == 1) {
					var checked = ' checked="checked"';
				} else {
					var checked = '';
					tasksComplete = false;
				}
				var checked = data[i][11] == 1 ? ' checked="checked"' : '';
				var jobno = data[i][6];
				var taskprice = parseFloat(data[i][9]);
				ticketprice += taskprice;
				tasks += ('<p>'
					+'<input type="checkbox" onchange="checkEvent('+ticketno+','+jobno+')" class="checkbox" id="'+ticketno+'-'+jobno+'"'+checked+'/>'
					+ data[i][8] 
					+ '<span style="float: right;">$'+taskprice.toFixed(2)+'</span>'
					+'</p>');
				i++;
			} while (i < data.length && ticketno == parseInt(data[i][0], 10));
		
			// Get the appropriate list
			// Hide notes if all tasks are complete
			var ticketBody = ('<h3>Tasks:</h3>' + tasks);
			if (tasksComplete) {
				// TASKS COMPLETE
				var list = $('#completedTickets');
			} else {
				// TASKS INCOMPLETE
				var list = $('#openTickets');
				// Show notes only if they exist
				if (notes != '') ticketBody += ('<h3>Notes:</h3><p>' + notes + '</p>');
			}
			
			// Determine bottom of ticket based on payment status
			if (status == "unpaid") {
				// UNPAID
				ticketBody += (''
					+ '<h2 style="text-align:right; padding: 10px 20px;" class="ticketStatus">'
					+ 'Total: $'+ticketprice.toFixed(2)+'</h2>'
					+ '<p style="text-align:center;"><a onclick="applyPayment('+ticketno+')">Pay</a></p>');
			} else if (status == "paid") {
				// PAID
				ticketBody += ('<h2 style="text-align:right; padding: 10px 20px;" class="ticketStatus">PAID</h2>');
				if (tasksComplete) {
					// PAID AND FINISHED
					ticketBody += '<p style="text-align:center;"><a onclick="pickup('+ticketno+')" class="ticketPickupButton">Pickup</a></p>';
				}
			}
		
			// Append the ticket to the list
			list.append('<div class="ticketWrapper" id="'+ticketno+'">'
					// Hat
					+ '<div class="tickethat"></div>'
					// Head
					+ '<div class="tickethead">'
						+ '<h2><a onclick="editCust('+custno+')">'+fname+' '+lname+'</a></h2>'
						+ '<p style="font-size:small; display:block;">&emsp;'+due+'</p>'
						+ '<h3><a onclick="editTicket('+ticketno+')">Edit Ticket</a></h3>'			
					+ '</div>'
					// Body
					+ '<div class="ticketbody">'+ticketBody+'</div>'
					// Boots
					+ '<div class="ticketboots"></div>'
				+ '</div>');
			} // End of loop
			$('#openTickets').append('<div class="ticketWrapper"></div><div class="ticketWrapper"></div><div class="ticketWrapper"></div><div class="ticketWrapper"></div>');
			$('#completedTickets').append('<div class="ticketWrapper"></div><div class="ticketWrapper"></div><div class="ticketWrapper"></div><div class="ticketWrapper"></div>');
			stopLoading();
		} // End of success function
	}); // End of ajax
}

