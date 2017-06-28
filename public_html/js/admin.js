$(document).ready( function() {
	


	$('.datepicker').datepicker({
	  dateFormat: "yy-mm-dd"
	});


	refresh();
	hideAll()

	// Choose which section to show first
	$('#Reports').show()



});

function refresh(){
	setTimeout('getTasks()', 75);
}

function hideAll() {
	$('.toggle').hide();
	$('.print').hide();
}

function showSec(selector) {
	hideAll()
	$(selector).show()
}

function postData(json){
	$.post("php/controller.php", json, function(data) {
		console.log(data);
		refresh();
	});
}

function deleteTask(ID)
{	
	var formArray = {0:'taskmenu',1:'DELETE',2:ID};
	postData(formArray);
}

function saveTask()
{
	if (document.getElementById('tName').value == '') {
		alert("NO TASK NAME...  IDIOT");
		return;
	}
	if (document.getElementById('tPrice').value == '') {
		alert("NO PRICE... HHRRRMMPPHH");
		return;
	}
	var name = document.getElementById('tName').value;
	var price = document.getElementById('tPrice').value;

	var formArray = {0:'taskmenu',1:'INSERT',2:name,3:price};
	postData(formArray);
	document.getElementById('tPrice').value = '';
	document.getElementById('tName').value = '';
}

function getTasks()
{
	request = {0: 'tasksearch', 1: '1', 2: 'active', 3: ["*"]};
	$.post("php/controller.php", request, function(data) {
		console.log(data);
		data = JSON.parse(data);
		$('#taskList').html("");	
			
		var i = 0;
		while(i < data.length){

			var list;
			list = $('#taskList');

			if(i == 0)
			{
				list.append('<tr><td>Task</td><td>Price</td><td>Delete</td></tr>');
			}
			var id = data[i][0];
			var task = data[i][1];
			var price = data[i][2];

			list.append('<tr> <td>' + task + '</td><td>' + price + '</td><td style="text-align: center"><a id="task'+id+'" onclick="deleteTask('+id+')">X</a></td></tr>');
			i++
		}	
	});
}

function searchTickets() {

	// Get form input values
	field = document.getElementById('form1-input').value;
	radios = document.getElementsByName('form1-radio');
	for (i=0; i<radios.length; i++) {
		if (radios[i].checked == true) {
			column = radios[i].value;
		}
	}
	date = document.getElementById('form1-date').value;
	radios = document.getElementsByName('form1-radio2');
	for (i=0; i<radios.length; i++) {
		if (radios[i].checked == true) {
			datecolumn = radios[i].value;
		}
	}

	// Make table header
	header = '<tr><th colspan="10"><center>Ticket Search<br>'+column+': '+field+'<br>'+datecolumn+': '+date.substring(0,10)+'</center></th></tr>'
		+ '<tr><th>Ticket#</th><th>Name</th><th>Order Date</th><th>Due Date</th><th>Tasks</th><th>Price</th><th>Status</th><th>Picked Up</th></tr>';
	
	// Assemble request
	json = {0: 'ticksearch', 1: [field, date], 2: [column, datecolumn],
		3: ['ticketno', 'lastname', 'firstname', 'createdate', 'duedate','task.price', 'status', 'closedate']};

	// Post request
	$.post("php/controller.php", json, function(data) {
		//console.log(data);
		data = JSON.parse(data);

		table = $('#form1-table');

		// Reset the table
		table.html("");
		table.append(header);

		// Loop through Individual tasks
		var i = 0;
		while (i<data.length) {
			ticketno = data[i][0];
			name = data[i][1] + ', ' + data[i][2][0] +'.';
			createdate = data[i][3].substring(0,11);
			duedate = data[i][4].substring(0,11);
			taskcount = 0;
			price = 0;
			status = data[i][6];
			closedate = data[i][7] == null ? '' : data[i][7].substring(0,11);

			// Count tasks until ticket number changes
			do {
				taskcount++;
				price = price + parseFloat(data[i][5]);
				i++;
			} while (i < data.length && ticketno == data[i][0]);
			i--;

			// Assemble and insert row into table
			price = price.toFixed(2);
			table.append('<tr><td>'+ticketno+'</td><td>'+name+'</td><td>'+createdate+'</td><td>'+duedate+'</td>><td>'+taskcount+'</td><td>$'+price+'</td><td>'+status+'</td><td>'+closedate+'</td></tr>');
			i++;
		}

		// Show empty result message
		if (data.length == 0) {
			table.append('<tr><td colspan="10"><center><em>no results</em></center></td></tr>');
		}
		
		// Unhide print button
		$('.print').show();
		
	});
}

function searchPayments() {

	// Get form input values
	field = document.getElementById('form2-input').value;
	radios = document.getElementsByName('form2-radio');
	for (i=0; i<radios.length; i++) {
		if (radios[i].checked == true) {
			column = radios[i].value;
		}
	}
	date = document.getElementById('form2-date').value;

	// Make table header
	header = '<tr><th colspan="10"><center>Payment Search<br>'+column+': '+field+'<br>paydate: '+date.substring(0,10)+'</center></th></tr>'
		+ '<tr><th>Date</th><th>Ticket#</th><th>Customer Name</th><th>Amount</th><th>Payment Method</th>></tr>';
	
	// Assemble request
	json = {0: 'paysearch', 1: [field, date], 2: [column, 'paydate'],
		3: ['paydate', 'ticketno','amount', 'auth', 'lastname','firstname']};

	// Post request
	$.post("php/controller.php", json, function(data) {
		console.log(data);
		data = JSON.parse(data);

		table = $('#form2-table');

		// Reset the table
		table.html("");
		table.append(header);

		// Loop through Individual tasks
		var i = 0;
		while (i<data.length) {
			paydate = data[i][0];
			ticketno = data[i][1];
			amount = parseFloat(data[i][2]).toFixed(2);
			auth = data[i][3]=='cash'? 'cash' : 'credit card';
			name = data[i][4] + ', ' + data[i][5][0] + '.';

			table.append('<tr><td>'+paydate+'</td><td>'+ticketno+'</td><td>'+name+'</td><td>'+amount+'</td>><td>'+auth+'</td></td></tr>');
			i++;
		}

		// Show empty result message
		if (data.length == 0) {
			table.append('<tr><td colspan="10"><center><em>no results</em></center></td></tr>');
		}
		
		// Unhide print button
		$('.print').show();

	});
}

function printForm(button) {
	$(button).siblings('div.print').printElement({
		pageTitle:'Report',
		printBodyOptions: {styleToAdd:'margin:0 auto; width: 90%;'}
	});
}
