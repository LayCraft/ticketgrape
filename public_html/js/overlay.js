/*
This file requires: index.php, tasksearch.php, 
Files that require this file: createticket.php

*/

///////////////////////////////
//                           //
//    PAGE INITIALIZATION    //
//                           //
///////////////////////////////

$(document).ready( function() {

	// PLACE INITIALIZATION FUNCTIONS AND EVENT LISTENERES HERE

	// remove a task from task pane
	$('div#tasks').on('click', '.removetask', function() {
		$(this).parent().parent().remove();
		taskCount--;
	});

	// populate task price on selection of a dropdown item
	$('div#tasks').on('change', 'select', function() {
		for (i=0; i<tasklist.length; i++) {
			if ($(this).val()==tasklist[i][0]) {
				price = tasklist[i][2];
			}
		}
		$(this).parent().next().children('p').children('input').val(price);
		//console.log("task selected");
	});

});

// close any open overlays and reset all form and buttons to default state
function closeOverlay() {
	$("#overlay").animate({"opacity": 0}, 100, "linear", function() {
		$("#overlay").hide();
	});
	// hide the createTicket div
	$("#createticket").animate({"opacity": 0}, 100, "linear", function() {
		$("#createticket").hide();
	});
	// hide the customerinfo div
	$("#customerinfo").animate({"opacity": 0}, 100, "linear", function() {
		$("#customerinfo").hide();
	});
	// hide the paymentwindow div
	$("#paymentwindow").animate({"opacity": 0}, 100, "linear", function() {
		$("#paymentwindow").hide();
	});

	// clear all form elements
	document.getElementById('pcreditcard').disabled = true;
	document.getElementById('pexpdate').disabled = true;
	document.getElementById('tphone').disabled = false;
	var forms = document.getElementsByTagName("form");
	for (i = 0; i < forms.length; i++) {
		forms[i].reset();
	}
	// clear tasks pane
	resetTasks();
}

// trigger the transparent overlay shade
// not to be triggered directly by the user
function openOverlay() {
	$("#overlay").show();
	$("#overlay").animate({"opacity": 0.6}, 100, "linear");
}


//////////////////////////////////////////
//                                      //
//    TICKET OVERLAY: MAIN & DETAILS    //
//                                      //
//////////////////////////////////////////

// GLOBAL VARIABLES

var taskCount = 0;
var autovals;


// FUNCTIONS

// trigger new ticket overlay
function newTicket() {
	openOverlay();
	$("#createticket").show();
	$("#createticket").animate({"opacity": 1}, 100, "linear");
}

// trigger edit ticket overlay
function editTicket(id) {
	// Prepare the ticket form
	document.getElementById('tphone').setAttribute("disabled", "disabled");
	fillTicket(id);
	// Open it
	newTicket();
}

// populate an existing ticket's details into form
function fillTicket(id) {
	startLoading();
	$.ajax({
		type: "POST",
		url: "php/controller.php",
		data: {0: 'ticksearch', 1: id, 2: 'ticketno',
		3: ["phone", "custno", "lastname", "firstname", "duedate","ticketno","notes","jobno","taskno","task.price", "complete"]},
		dataType: "json",
		success: function(output) {
			//console.log(output);
			document.getElementById("tphone").value = (output[0][0]);
			document.getElementById("tcustno").value = (output[0][1]);
			document.getElementById("tname").value = (output[0][2]+", "+output[0][3]);
			document.getElementById("tduedate").value = (output[0][4].substring(0,10));
			document.getElementById("tduetime").value = (output[0][4].substring(11,16));
			document.getElementById("tticketno").value = (output[0][5]);
			document.getElementById("tnotes").value = (output[0][6]);

			// populate tasks
			for (i = 0; i < output.length; i++) {
				addTask();
				document.getElementsByClassName("ttaskno")[i].value = output[i][8];
				document.getElementsByClassName("tprice")[i].value = (output[i][9]);
				document.getElementsByClassName("tcomplete")[i].checked = parseInt(output[i][10]);
			}
			$('.checks').show();
			stopLoading();
		}
	});
}

// phone number autocomplete function
$(function() {
	$("#tphone").autocomplete( {
		appendTo: "form",
		minLength: 4,
		source: function ( request, response ) {
			startLoading();
			$.ajax({
				type: "POST",
				url: "php/controller.php",
				data: {0: 'autocomp', 1: request.term},
				dataType: "json",
				success: function(output) {			
					response(output);
					stopLoading();
				}
			});
		},
		select: function( event, ui ) {
			event.preventDefault();
			$("#tphone").val(ui.item.value);
			lookUp();
		}
	});
});

// search database for a customer by phone number
function lookUp() {
	var digits = $("#tphone").val();
	if (digits.length==7 || digits.length==10) {
		$.ajax({
			type: "POST",
			url: "php/controller.php",
			data: {0: 'custsearch', 1: digits, 2: 'phone', 3: ["lastname", "firstname","custno"]},
			dataType: "json",
			success: function(output) {
				if (output.length>0) {
					// populate customer details into ticket window
				   document.getElementById("tname").value = (output[0][0]+", "+output[0][1]);
				   document.getElementById("tcustno").value = (output[0][2]);
				} else {
					// prompt user to add a new customer
					document.getElementById("tname").value = null;
				    document.getElementById("tcustno").value = null;
				    if (window.confirm("Customer not found. Add new?") == true){
						document.getElementById("customerform").reset();
				    	document.getElementById("cphone").value = document.getElementById("tphone").value;
						$("#createticket").hide();
				    	newCust();
				    } else {console.log("nope");}
				}
			}
		});
	}
}

//////////////////////////////////////
//                                  //
//    TICKET OVERLAY: TASKS PANE    //
//                                  //
//////////////////////////////////////

// GLOBAL VARIABLES

var tasklist;


// FUNCTIONS

// retrieve generic task menu from database and stores in global variable tasklist
function getTasks() {
	request = {0: 'tasksearch', 1: '1', 2: 'active', 3: ["*"]};
	$.post("php/controller.php", request, function(data) {
		tasklist = JSON.parse(data);
	});
}

// addtask is triggered by a link or method in createticket.php
// this is a way to remove all tasks in the add/edit ticket window.
function resetTasks() {
	$(".task").remove();
	taskCount=0;
}

function removeTask() {
	if (taskCount>0) {
		$(".task")[taskCount-1].remove();
		taskCount--;
	} else {
		console.log("No more tasks\n");
	}
}

// addtask is triggered by a link or method in ticket window
// it builds HTML to add tasks in the ticket window
function addTask() {
	if (tasklist==null) {
		console.log("Unable to retrieve tasks\n");
		return;
	}

	if (taskCount < 10) {
		// build option list for dropdown
		options = "";
		for (var i = 0; i < tasklist.length; i++) {
			options = options.concat('<option class="taskchoice" value="', tasklist[i][0], '">', tasklist[i][1], '</option>');
		}

		// build dropdown select HTML element
		$("#tasks").append(
			'<div class="task">'
			+ '<p><a style="float: right;" class="removetask">x</a></p>'
			+ '<p>Task: <select class="ttaskno"><option value="0" selected disabled>Choose a task</option>'
			+ options
			+ '</select></p>'
			+ '<div>'
			+ '<p>Price <input type="text" class="tprice" /></p>'
			+ '<p class="checks">Completed <input type="checkbox" class="tcomplete"></p>'
			+ '</div>'
			+ '</div>'
		);
		taskCount++;
	}
}


////////////////////////////
//                        //
//    CUSTOMER OVERLAY    //
//                        //
////////////////////////////

function newCust() {
	openOverlay();
	$("#customerinfo").show();
	$("#customerinfo").animate({"opacity": 1}, 200, "linear");
	
}

function editCust(id) {
	fillInfo(id);
	newCust();
}

// populates customer details into customer window
function fillInfo(id) {
	startLoading();
	$.ajax({
		type: "POST",
		url: "php/controller.php",
		data: {0: 'custsearch', 1: id, 2: 'custno', 3: ["custno", "firstname", "lastname", "phone", "ext", "address", "email", "zip"]},
		dataType: "json",
		success: function(output) {
			document.getElementById("ccustno").value = (output[0][0]);
			document.getElementById("cfirstname").value = (output[0][1]);
			document.getElementById("clastname").value = (output[0][2]);
			document.getElementById("cphone").value = (output[0][3]);
			document.getElementById("cext").value = (output[0][4]);
			document.getElementById("caddress").value = (output[0][5]);
			document.getElementById("cemail").value = (output[0][6]);
			document.getElementById("czip").value = (output[0][7]);
			stopLoading();
		}
	});
}


///////////////////////////
//                       //
//    PAYMENT OVERLAY    //
//                       //
///////////////////////////

// GLOBAL VARIABLES

var currentBalance;


// FUNCTIONS

function applyPayment(id) {
	document.getElementById("pticketno").value = ('000000'+id).slice(-6);
	getBalance(id);
	openOverlay();
	startLoading();
	$("#paymentwindow").show();
	$("#paymentwindow").animate({"opacity": 1}, 100, "linear");
	//document.getElementById("pclosebutton").style.display="none";

}

// stores the current balance of a ticket in global variable currentBalance
// retrieves payment history for a ticket and displays in payment window
function getBalance(ticketno) {
	startLoading();
	var total;
	$.post("php/controller.php", {0: 'ticksearch', 1: ticketno, 2: 'ticketno', 3: ["sum(task.price)"]}, function(data) {
		data = JSON.parse(data);
		total = parseFloat(data[0]);

		$.post("php/controller.php", {0: 'paysearch', 1: ticketno, 2: 'ticketno', 3: ["amount", "paydate", "auth"]}, function(output) {
			var totalPayment = 0;
			var paymentHistory = 'Payment History: \r\n';
			output = JSON.parse(output);

			if (output.length > 0) {
				// build payment history list
				for (var i = 0; i < output.length; i++) {
					totalPayment += parseFloat(output[i][0]);
					paymentHistory += output[i][1] + ": $" + output[i][0];
					paymentHistory += output[i][2] == 'cash' ? " cash\r\n" : " credit\r\n";
				}
				// display payment history
				document.getElementById("phistory").value = paymentHistory;
			}

			// set global variable currentBalance
			currentBalance = (total-totalPayment).toFixed(2);
			document.getElementById("pamount").value = currentBalance;

			stopLoading();
		});
	});
}

// enable or disable credit card and expiry date fields as appropriate for payment method
function clickRadio(radio) {
	if (radio.value=='cash') {
		document.getElementById("pcreditcard").disabled = true;
		document.getElementById("pexpdate").disabled = true;
	} else if (radio.value=='credit') {
		document.getElementById("pcreditcard").disabled = false;
		document.getElementById("pexpdate").disabled = false;
		resetPayment(document.getElementById("pamount"));
	}
}

// disallow credit card payments greater than the balance due
function resetPayment(amountbox) {
	if ((parseInt(amountbox.value) > currentBalance) && (document.getElementById('pcredit-radio').checked)) {
		amountbox.value = currentBalance;
	}
}








