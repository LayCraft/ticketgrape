/*
This JS file is for functions in the main ticket pane. Use vanilla.js to keep things fast

Requires: jquery, 
Required by: index.php

JSON STRUCTURE: { 0: 'requestname', 1: searchvalue, 2: 'searchcolumnname', 3: ['array', 'of', 'returned', 'columns'] }
	(3 is only for query requests)
*/
window.refreshed = true;



// collapse the ticketbody and children of a div when it is clicked
$(document).ready( function() {


	// Minimize open ticket
	$('div#ticketlist').on('click', '.minimize', function() {
		// Toggle ticket display
		$(this).parents('.ticket').children('.ticketbody').toggle();
		
		// Set button text
		$(this).html($(this).html() == ' [hide]'? ' [show]': ' [hide]');
	});
	
	// Minimize completed ticket
	$('div#completedTickets').on('click', '.minimize', function() {
		// Toggle ticket display
		$(this).parents('.ticket').children('.ticketbody').toggle();
		
		// Set button text
		$(this).html($(this).html() == ' [hide]'? ' [show]': ' [hide]');
	});
	
	// Minimize content pane
	$('div#body').on('click', '.minimizeContent', function() {
		// Toggle ticket display
		$(this).parents('.content').children('.collapsible').toggle();
		
		// Set button text
		$(this).html($(this).html() == ' [hide]'? ' [show]': ' [hide]');
	});

	// -- EVENT LISTENERS --//

	$('#tduedate').datepicker({
	  dateFormat: "yy-mm-dd"
	});

	$('#tduetime').timepicker({
	   defaultTime: '15:00',
	   hours: {starts: 8, ends: 20},
	   minutes: {interval: 30},
	   rows: 2
	});

	// remove a task
	$('div#tasks').on('click', '.removetask', function() {
		$(this).parent().remove();
		taskCount--;
		//console.log ("task removed");
	});

	// populate task price
	$('div#tasks').on('change', 'select', function() {
		index = $(this).val()-1;
		price = tasklist[index][2];
		//console.log(price);
		$(this).siblings("input").val(price);
		//console.log("task selected");
	});
	
	// Refresh tickets
	refresh();

	// Get taskmenu
	getTasks();
});

function postData(json){
	$.post("php/controller.php", json, function(data) {
		if (json[0] == 'tick') {
			submitTasks(data);
		}
		closeOverlay();
		refresh();
	});
}

/* populates fields in ticket from database */
function submitTicket(){
	if (document.getElementsByClassName('task').length==0) {
		alert("NO TASKS IDIOT");
		return;
	}
	if (document.getElementById('tcustno').value == '') {
		alert("NO CUSTOMER STUPID");
		return;
	}
// place to assemble the KV pairs from the form
	var formData = {};
	// holds a ticket number if available
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
	var formArray = {0:'tick',1: ticketNum, 2: formData};
	ticketNum = postData(formArray);
}

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

	var formArray = {0:'task',1: ticketNum, 2: formData};
	//console.log(formArray);
	postData(formArray);
}


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
	var formArray = {0:'cust',1: custNum, 2: formData};
	postData(formArray);
	refresh();
}

function submitPayment(){
	// place to assemble the KV pairs from the form
	var formData = {};
	// holds a ticket number if available
	var ticketNum = document.getElementById('tticketno').value;
	// the name of the html id tags to grab and turn into kv pairs
	var fieldIDs = [ 
		'pLOL',
		'pWTF',
		'pIDK',
		];
	// for each field, save a key value pair into formData	
	for (i=0; i < fieldIDs.length; ++i){
		// collect the value of the field
		var myKey = fieldIDs[i].substring(1);
		var myValue = document.getElementById(fieldIDs[i]).value;
		// add key value pair to form data
		formData[myKey] = myValue;
	}
	var formArray = {0:'pay',1: ticketNum, 2: formData};
	postData(formArray);
	refresh();
}

/* this is the function that is triggered when a checkbox is fired*/
function checkEvent(ticketno, jobno) {
	var complete = (document.getElementById(ticketno+"-"+jobno).checked==true) ? 1 : 0;
	var formArray = {0:'taskcheck',1: ticketno, 2: jobno, 3: complete};
	postData(formArray);
	refresh();
}


function getTickets() {
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
			var custno = parseInt(data[i][1]);
			var fname = data[i][2];
			var lname = data[i][3];
			var notes = data[i][5];
			var status = data[i][10];
		
			// Iterate through tasks and append to one string
			var tasks = "";
			do {
				var checked = data[i][11] == 1 ? ' checked="checked"' : '';
				var jobno = data[i][6];
				tasks += ('<tr>'
					+'<td><input type="checkbox" onchange="checkEvent('+ticketno+','+jobno+')" class="checkbox" id="'+ticketno+'-'+jobno+'"'+checked+'/></td>'
					+'<td>'+data[i][8]+'</td>'
					+'</tr>');
				i++;
			} while (i < data.length && ticketno == data[i][0]);
		
		// Get the appropriate list
		var list;
		if (status == "open") {
			list = $('#openTickets');
		} else if (status == "complete") {
			list = $('#completedTickets');
		}
		
		// Append the ticket to the list
		list.append('<div class="wrap">'
					+ '<div class="tickethat">'
					+ '</div>'
					+ '<div class="ticket" id="'+ticketno+'">'
						+ '<div class="tickethead">'
							+ '<table width="100%">'
								+ '<tbody>'
									+ '<h3 style="text-align:center;"><a onclick="editCust('+custno+')">'+fname+' '+lname+'</a></h3>'
									+ '<tr>'
										+ '<td style="text-align:center;"><a onclick="editTicket('+ticketno+')">Edit Ticket</a></td>'			
									+ '</tr>'
								+ '</tbody>'
							+ '</table>'
						+ '</div>'
						+ '<div class="ticketbody">'
							+ '<table class="checkboxarea">'
								+ '<tbody>'
									+ '<tr>'
										+ '<td width="60%">'
											+ '<table>'
												+ '<tbody>'
													+ tasks
												+ '</tbody>'
											+ '</table>'
										+ '</td>'
										+ '<td width="40%">'
											+ notes
										+ '</td>'
									+ '</tr>'
								+ '</tbody>'
							+ '</table>'
							+ '<table>'
								+ '<td style="text-align:right;"> <a onclick="applyPayment(10)">Pay!Pickup</a> Total Owed</td>'
							+ '</table>'
						+ '</div>'
					+ '</div>'
					+ '<div class="ticketboots">'
					+ '</div>'
				+ '</div>'
		//////////////////////
		// Old ticket style //
		//////////////////////
		/*'<div class="ticket" id="'+ticketno+'">'
		+'<div class="tickethead">'
			+'<table width="100%">'
				+'<tr>'
					+'<td><a class="minimize">[ - ]</a></td>'
					+'<td><h3><a onclick="editCust('+custno+')">'+fname+' '+lname+'</a></h3></td>'
					+'<td><a onclick="editTicket('+ticketno+')">Edit Ticket</a></td>'
					+'<td><a onclick="applyPayment(10)">Pay!Pickup</a></td>'
					+'<td style="text-align:right;">Total Owed</td>'
				+'</tr>'
			+'</table>'
		+'</div>'
		+'<div class="ticketbody">'
			+'<table class="checkboxarea">'
				+'<tr>'
					+'<td width="60%">'
						+'<table>'
						// Tasks
						+ tasks
						+'</table>'
					+'</td>	'			
					+'<td width="40%">'
						+'<p>'+notes+'</p>'
					+'</td>'		
				+'</tr>'
			+'</table>'
		+'</div>'
	+'</div>'*/);
			} // End of loop
		} // End of success function
	}); // End of ajax
	
	window.refreshed=true;
}

function refresh(){
	window.refreshed = false;
	while (window.refreshed == false){
		getTickets();
	}
}