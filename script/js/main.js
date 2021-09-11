/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myFunction() {
	var x = document.getElementById("myTopnav");
	if(x.className === "topnav")
	{
		x.className += " responsive";
	}
	else
	{
		x.className = "topnav";
	}
}

// ifEmpty: If Empty Function
function ifEmpty(val)
{
	if(val == "" || val === undefined || val === null)
	{
		return "N/A";
	}
	else
	{
		return val;
	}
}

// ifEmpty2: If Empty Function - with custom attribute
function ifEmpty2(val, textValue)
{
	if(val == "" || val === undefined || val === null)
	{
		return textValue;
	}
	else
	{
		return val;
	}
}

// ifEmpty3: If Empty Function - with custom attributes
function ifEmpty3(val, textValue)
{
	if(val == "" || val === undefined || val === null || val === "Select a costume...")
	{
		return textValue;
	}
	else
	{
		return val;
	}
}

// didAttend: Did the trooper attend?
function didAttend(value)
{
	var returnValue = "";

	if(value == 3)
	{
		returnValue = "Attended";
	}
	else
	{
		returnValue = "Did not attend";
	}

	return returnValue;
}

// getStatus: gets status of trooper - 0 = Going, 1 = Stand by, 2 = Tentative, 3 = Attended, 4 = Canceled, 5 = Pending, 6 = Not Picked
function getStatus(value)
{
	var returnValue = "";

	if(value == 0)
	{
		returnValue = "Going";
	}
	else if(value == 1)
	{
		returnValue = "Stand By";
	}
	else if(value == 2)
	{
		returnValue = "Tentative";
	}
	else if(value == 3)
	{
		returnValue = "Attended";
	}
	else if(value == 4)
	{
		returnValue = "Canceled";
	}
	else if(value == 5)
	{
		returnValue = "Pending";
	}
	else if(value == 6)
	{
		returnValue = "Not Picked";
	}

	return returnValue;
}

// echoSelect: Selects the users set value
function echoSelect(value1, value2)
{
	var returnValue = "";

	if(value1 == value2)
	{
		returnValue = "SELECTED";
	}

	return returnValue;
}

$(document).ready(function()
{
	// Easy Fill Button: Show/Hide
	$("#easyfilltoolbutton").click(function(e)
	{
		e.preventDefault();
		
		if($("#easyfilltoolarea").is(":hidden"))
		{
			$("#easyfilltoolarea").show();
			$("#easyfilltoolbutton").text("Hide");
		}
		else
		{
			$("#easyfilltoolarea").hide();
			$("#easyfilltoolbutton").text("Easy Fill Tool");
		}
	})
	
	// Make contains, case insensitive
	jQuery.expr[':'].contains = function(a, i, m)
	{
		return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
	};
	
	// Trooper Member Search
	$("body").on("input", "input[id='trooperSearch']", function(e)
	{
		// Loop through options
		$("#trooperSelect option").each(function(index)
		{	
			// If contains search query
			if($(this).is(":contains(" + $("input[id='trooperSearch']").val() + ")"))
			{
				// Show and select
				$(this).show();
				$(this).prop('selected', true);
			}
			else
			{
				// Hide and unselect
				$(this).hide();
				$(this).prop('selected', false);
			}
		});
	})
	
	// Troop Tracker Search
	$("input[name='searchType']").click(function()
	{
		if($("input[name='searchType']:checked").val() == "regular")
		{
			$("#searchNameDiv").show();
			$("#tkIDDiv").show();
			$("#trooper_count_radio").hide();
		}
		else
		{
			$("#searchNameDiv").hide();
			$("#tkIDDiv").hide();
			$("#trooper_count_radio").show();
		}
	})
	
	// Get Location Button
	$("#getLocation").button().click(function(e)
	{
		e.preventDefault();
		
		// Ensure value is not blank
		if($("#location").val() != "")
		{
			// Send data
			$.ajax({
				type: "POST",
				url: "process.php?do=getlocation",
				data: { location: $("#location").val() },
				success: function(data)
				{
					var json = JSON.parse(data);
					$("#squadm").val(json.squad);
				}
			});
		}
		else
		{
			// If empty
			alert("Location can not be empty.");
		}
	})
	
	// Change Site Settings - Close Website
	$("#submitCloseSite").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#changeSettingsForm");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize()  + "&submitCloseSite=1",
			success: function(data)
			{
				if($("#submitCloseSite").prop("value") == "Close Website")
				{
					$("#submitCloseSite").prop("value", "Open Website");
				}
				else
				{
					$("#submitCloseSite").prop("value", "Close Website");
				}
				
				alert("Changes submitted!");
			}
		});
	})
	
	// Change Site Settings - Close Sign Ups
	$("#submitCloseSignUps").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#changeSettingsForm");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitCloseSignUps=1",
			success: function(data)
			{
				if($("#submitCloseSignUps").prop("value") == "Close Sign Ups")
				{
					$("#submitCloseSignUps").prop("value", "Open Sign Ups");
				}
				else
				{
					$("#submitCloseSignUps").prop("value", "Close Sign Ups");
				}
				
				alert("Changes submitted!");
			}
		});
	})
	
	// Modify sign up submit button
	$("#submitModifySignUp").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#modifysignup");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitModifySignUp=1",
			success: function(data)
			{
				var json = JSON.parse(data);
				$("#when" + json.id).html(json.data);
				alert("Changes submitted!");
			}
		});
	})

	$("#submitEditUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editUser");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitEditUser=1",
			success: function(data)
			{
				if($("#editUserInfo").is(":hidden"))
				{
					$("#submitEditUser").val("Close");
					$("#editUserInfo").show();

					var json = JSON.parse(data);
					$("#userIDE").val(json.id);
					$("#user").val(json.name);
					$("#email").val(json.email);
					$("#phone").val(json.phone);
					$("#squad").val(json.squad);
					$("#permissions").val(json.permissions);
					$("#tkid").val(json.tkid);
					$("#forumid").val(json.forumid);
				}
				else
				{
					$("#submitEditUser").val("Edit");
					$("#editUserInfo").hide();
				}
			}
		});
	})

	$("#submitEdit").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitEdit=1",
			success: function(data)
			{
				var json = JSON.parse(data);

				if($("#rosterInfo").is(":hidden"))
				{
					//$("#submitRoster").val("Close");
					//$("#rosterInfo").html(data);
					//$("#rosterInfo").show();
				}
				else
				{
					$("#submitRoster").val("Roster");
					$("#rosterInfo").html("");
					$("#rosterInfo").hide();
				}

				if($("#editEventInfo").is(":hidden"))
				{
					$("#submitEdit").val("Close");
					$("#editEventInfo").show();
					
					var date1 = moment(json.dateStart).format("MM/DD/YYYY HH:mm");
					var date2 = moment(json.dateEnd).format("MM/DD/YYYY HH:mm");

					$("#eventIdE").val(json.id);
					$("#eventName").val(json.name);
					$("#eventVenue").val(json.venue);
					$("#location").val(json.location);
					$("#squadm").val(json.squad);
					$("#datepicker").val(date1);
					$("#datepicker2").val(date2);
					$("#website").val(json.website);
					$("#numberOfAttend").val(json.numberOfAttend);
					$("#requestedNumber").val(json.requestedNumber);
					$("#requestedCharacter").val(json.requestedCharacter);
					$("#secure").val(json.secureChanging);
					$("#blasters").val(json.blasters);
					$("#lightsabers").val(json.lightsabers);
					$("#parking").val(json.parking);
					$("#mobility").val(json.mobility);
					$("#amenities").val(json.amenities);
					$("#comments").val(json.comments);
					$("#label").val(json.label);
					$("#limitedEvent").val(json.limitedEvent);
					$("#era").val(json.limitTo);
					$("#limitRebels").val(json.limitRebels);
					$("#limit501st").val(json.limit501st);
					$("#limitMando").val(json.limitMando);
					$("#limitDroid").val(json.limitDroid);
					$("#limitTotal").val(json.limitTotal);
					$("#referred").val(json.referred);
					
					// Prevent an issue with old data, convert blank selects to have a value
					$('select').each(function()
					{
						// If no value or null
						if($(this).val() == "" || $(this).val() == null)
						{
							// Select
							$(this).find('option[value="null"]').prop('selected', true);
						}
					});
				}
				else
				{
					$("#submitEdit").val("Edit");
					$("#editEventInfo").hide();
				}
			}
		});
	})
	
	// Edit charity amount button
	$("#submitCharity").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");
		
		var amount = prompt("How much money did this event raise for charity?", 0);

		if(amount != null && parseInt(amount))
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitCharity=1&charity=" + amount,
				success: function(data)
				{
					alert("Success!");
				}
			});
		}
		else
		{
			alert("Invalid");
		}
	})

	$("#submitRoster").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitRoster=1",
			success: function(data)
			{
				if($("#editEventInfo").is(":hidden"))
				{
					//$("#editEventInfo").show();
					//$("#submitEdit").val("Close");
				}
				else
				{
					$("#editEventInfo").hide();
					$("#submitEdit").val("Edit");
				}

				if($("#rosterInfo").is(":hidden"))
				{
					$("#submitRoster").val("Close");
					$("#rosterInfo").html(data);
					$("#rosterInfo").show();
				}
				else
				{
					$("#submitRoster").val("Roster");
					$("#rosterInfo").html("");
					$("#rosterInfo").hide();
				}
			}
		});
	})

	$("#unsubscribeButton").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#unsubscribeForm");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&unsubscribeButton=1",
			success: function(data)
			{
				if($("#unsubscribeButton").val() == "Unsubscribe")
				{
					alert(data);
					$("#unsubscribeButton").val("Subscribe");
				}
				else
				{
					alert(data);
					$("#unsubscribeButton").val("Unsubscribe");
				}
			}
		});
	})
	$("body").on("click", "[id^=deleteComment]", function(e)
	{
		e.preventDefault();

		var r = confirm("Are you sure you want to delete this comment?");

		var id = $(this).attr("name");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: "index.php?event=" + $(this).attr("name"),
				data: "comment=" + id + "&deleteComment=1",
				success: function(data)
				{
					// Remove comment
					$("#comment_" + id).remove();

					// Alert to success
			  		alert("The comment was removed successfully!");
				}
			});
		}
	});

	$("#changephoneLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").show();
		$("#changename").hide();
		$("#changeemail").hide();
		$("#unsubscribe").hide();
		$("#changepassword").hide();
		$("#changetheme").hide();
	});
	
	$("#changethemeLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").hide();
		$("#changeemail").hide();
		$("#unsubscribe").hide();
		$("#changepassword").hide();
		$("#changetheme").show();
	});

	$("#changenameLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").show();
		$("#changeemail").hide();
		$("#unsubscribe").hide();
		$("#changepassword").hide();
		$("#changetheme").hide();
	});

	$("#changepasswordLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changepassword").show();
		$("#changename").hide();
		$("#changeemail").hide();
		$("#unsubscribe").hide();
		$("#changetheme").hide();
	});

	$("#changeemailLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").hide();
		$("#changeemail").show();
		$("#unsubscribe").hide();
		$("#changepassword").hide();
		$("#changetheme").hide();
	});

	$("#unsubscribeLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").hide();
		$("#changeemail").hide();
		$("#unsubscribe").show();
		$("#changepassword").hide();
		$("#changetheme").hide();
	});

	$("#submitDelete").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this event?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDelete=1",
				success: function(data)
				{
					// Remove from select option
					$("#eventId").find("option:selected").remove();

					// Alert to success
			  		alert("The event was removed successfully!");
				}
			});
		}
	})

	$("#submitApproveUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#approveTroopers");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to approve this user?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitApproveUser=1",
				success: function(data)
				{
					// Remove from select option
					$("#userID2").find("option:selected").remove();
					
					// Alert to success
			  		alert("The user was approved successfully!");
					
					// Move Select Val
					$("#userID2").val(-1);
					
					// Reset
					$("#nameTable").html("");
					$("#emailTable").html("");
					$("#forumTable").html("");
					$("#phoneTable").html("");
					$("#squadTable").html("");
					$("#tkTable").html("");
					
					// Set Button
					$("#trooperRequestButton").text("Approve Trooper Requests - ("+ ($("#userID2 option").length - 1) +")");

			  		// Show message if empty
			  		if($("#userID2 option").length <= 1)
			  		{
			  			$("#approveTroopers").html("There are no troopers to display.");
						
						// Hide table if noone left
						$("#userListTable").hide();
			  		}
				}
			});
		}
	})

	$("#submitDenyUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#approveTroopers");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to deny this user?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDenyUser=1",
				success: function(data)
				{
					// Remove from select option
					$("#userID2").find("option:selected").remove();

					// Alert to success
			  		alert("The user was denied successfully!");
					
					// Move Select Val
					$("#userID2").val(-1);
					
					// Reset
					$("#nameTable").html("");
					$("#emailTable").html("");
					$("#forumTable").html("");
					$("#phoneTable").html("");
					$("#squadTable").html("");
					$("#tkTable").html("");
					
					// Set Button
					$("#trooperRequestButton").text("Approve Trooper Requests - ("+ ($("#userID2 option").length - 1) +")");

			  		// Show message if empty
			  		if($("#userID2 option").length <= 1)
			  		{
			  			$("#approveTroopers").html("There are no troopers to display.");
						
						// Hide table if noone left
						$("#userListTable").hide();
			  		}
				}
			});
		}
	})

	$("#submitDeleteUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this user?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDeleteUser=1",
				success: function(data)
				{
					// Remove from select option
					$("#userID").find("option:selected").remove();

					// Alert to success
			  		alert("The user was removed successfully!");

			  		// Show message if empty
			  		if($("#userID option").length <= 1)
			  		{
			  			$("#approveTroopers").html("There are no troopers to display.");
			  		}
				}
			});
		}
	})

	$("#submitCancel").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to cancel this event?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitCancel=1",
				success: function(data)
				{
					// Alert to success
			  		alert("The event was canceled successfully!");
				}
			});
		}
	})

	$("body").on("click", "#removetrooper", function(e)
	{
		e.preventDefault();

		var form = $("#troopRosterForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to remove this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&removetrooper=1",
				success: function(data)
				{
					var json = JSON.parse(data);
					
					var eventId = $("#roster_" + $("input[name=trooperSelectEdit]:checked").val() + " input[name=eventId]").val();
					var troopername = $("#roster_" + $("input[name=trooperSelectEdit]:checked").val() + " input[name=troopername]").val();
					var tkid = $("#tknumber1" + $("input[name=trooperSelectEdit]:checked").val()).text();
					
					console.log(tkid);
					console.log(troopername);
					console.log(eventId);
					
					// Add to trooper list
					$("#trooperSelect").append('<option value="' + eventId + '" troopername="' + troopername + '" tkid="' + tkid + '">' + troopername + ' - ' + tkid + '</option>');

					// Remove
					$("#roster_" + $("input[name=trooperSelectEdit]:checked").val()).remove();

					// Display message
					alert(json.data);
				}
			});
		}
	});

	$("body").on("click", "#edittrooper", function(e)
	{
		e.preventDefault();

		if($("input[name=trooperSelectEdit]").is(":checked"))
		{
			if($("#edittrooper").val() != "Save")
			{
				// Change submit button
				$("#edittrooper").val("Save");
				
				// Replace None
				if($("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val() == "None")
				{
					$("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val("");
				}

				// Show Inputs for edit
				$("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#status2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").show();
				$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").show();

				// Hide static values
				$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				
				// Loop through inputs
				$("input[name=trooperSelectEdit]").each(function( index )
				{
					// Disable all other input radios to prevent issues
					if(!$(this).is(":checked"))
					{
						$(this).prop("disabled", true);
					}
				});
			}
			else
			{
				// Save
				e.preventDefault();

				var form = $("#troopRosterForm");
				var url = form.attr("action");

				var r = confirm("Are you sure you want to edit this roster?");

				if (r == true)
				{
					$.ajax({
						type: "POST",
						url: url,
						data: form.serialize() + "&submitEditRoster=1",
						success: function(data)
						{
							// Change submit button
							$("#edittrooper").val("Edit Trooper");

							// Hide Inputs for edit
							$("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#status2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").hide();
							$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").hide();
							
							// Set the comment back to "None" if no value
							if($("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val() == "")
							{
								$("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val("None");
							}
							
							// Set values
							$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#status2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val());
							$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							
							// Set attended text
							if($("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text() == "None")
							{
								$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).text("Not Submitted");
							}

							// Show static values
							$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val()).show();

							var json = JSON.parse(data);
							
							$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val()).html(json.data);
							$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val()).html(json.data2);
							
							// Re-enable all input radios to prevent issues
							$("input[name=trooperSelectEdit]").prop("disabled", false);

							// Alert to success
					  		alert("Roster updated!");
						}
					});
				}
			}
		}
	});
	
	// Undo Cancel
	
	// Have to put this here for it to work
	$("body").on("click", "#submitCancelTroop", function(e)
	{
		$("form[name='cancelForm']").validate({
			// Specify validation rules
			rules: {
				// The key name on the left side is the name attribute
				// of an input field. Validation rules are defined
				// on the right side
			},
			// Specify validation error messages
				messages: {
			},
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {

				var r = confirm("Are you sure you want to cancel this troop?");

				var id = $("#myId").val();

				if (r == true)
				{
					$.ajax({
					type: "POST",
					url: form.action,
					data: $(form).serialize() + "&submitCancelTroop=1&troopidC=" + $('#troopidC').val() + "&myId=" + $('#myId').val() + "&limitedevent=" + $("#limitedEventCancel").val(),
					success: function(data)
					{
						var html = `
						<div class="cancelFormArea">
						<p>
						<b>You have canceled this troop.</b>
						</p>

						<form action="process.php?do=undocancel" method="POST" name="undoCancelForm" id="undoCancelForm">
						<input type="submit" name="undoCancelButton" id="undoCancelButton" value="I changed my mind" />
						</form>
						</div>`;

						// Change to undo cancel
						$("#signeduparea").html(html);

						// Change select options
						$("#trooperRosterCostume").html($("#modifysignupFormCostume2 option:selected").text());
						$("#trooperRosterBackup").html($("#modiftybackupcostumeForm2 option:selected").text());
						$("#" + id + "Status").html("<div name='trooperRosterStatus' id='trooperRosterStatus'>Canceled</div>");
					}});
				}
			}
		});
	});
	
	$("body").on("click", "#undoCancelButton", function(e)
	{
		e.preventDefault();

		var form = $("#undoCancelForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to attend this event?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&undocancel=1&limitedevent=" + $("#limitedEventCancel").val(),
				success: function(data)
				{
					var json = JSON.parse(data);

					// Update roster to have form fields again
					$("#trooperRosterCostume").html(json[0].costume);
					$("#trooperRosterBackup").html(json[0].backup);
					$("#trooperRosterStatus").html(json[0].status);
					
					// Hide cancel form area
					$(".cancelFormArea").hide();

					// Display message
					alert(json[0].data);
				}
			});
		}
	});
	
	$("body").on("change", "select[name=modifysignupFormCostume2], select[name=modiftybackupcostumeForm2], select[name=modifysignupStatusForm2]", function(e)
	{
		var signupForm1 = $("select[name=modifysignupFormCostume2][trooperid=" + $(this).attr("trooperid") + "]");
		var signupForm2 = $("select[name=modiftybackupcostumeForm2][trooperid=" + $(this).attr("trooperid") + "]");
		var signupForm3 = $("select[name=modifysignupStatusForm2][trooperid=" + $(this).attr("trooperid") + "]");
			
		// Make sure values were changed
		if(signupForm1.val() != 0)
		{
			// Make sure this is not a limited event
			if($("#modifysignupStatusForm2").val() != -1 && $("#limitedEventCancel").val() == 0)
			{
				$.ajax({
					type: "POST",
					url: "process.php?do=undocancel&do2=undofinish",
					data: "costume=" + signupForm1.val() + "&costume_backup=" + signupForm2.val() + "&status=" + signupForm3.val() + "&troopid=" + $("#modifysignupTroopIdForm").val() + "&limitedevent=" + $("#limitedEventCancel").val() + "&trooperid=" + $(this).attr("trooperid"),
					success: function(data)
					{
						alert("Status Updated!");
						
						var html = `
						<p><b>You are signed up for this troop!</b></p>

						<form action="index.php" method="POST" name="cancelForm" id="cancelForm">
							<p>Reason why you are canceling:</p>
							<input type="text" name="cancelReason" id="cancelReason" />
							<input type="submit" name="submitCancelTroop" id="submitCancelTroop" value="Cancel Troop" />
						</form>`;
						
						$("#signeduparea").html(html);
					}
				});
			}
		}
	});
	
	// End of Undo Cancel
	
	// Modify Sign Up Form Change
	
	$("body").on("change", "select[name=modifysignupFormCostume], select[name=modiftybackupcostumeForm], select[name=modifysignupStatusForm]", function(e)
	{
		var signupForm1 = $("select[name=modifysignupFormCostume][trooperid=" + $(this).attr("trooperid") + "]");
		var signupForm2 = $("select[name=modiftybackupcostumeForm][trooperid=" + $(this).attr("trooperid") + "]");
		var signupForm3 = $("select[name=modifysignupStatusForm][trooperid=" + $(this).attr("trooperid") + "]");
		
		$.ajax({
			type: "POST",
			url: "process.php?do=modifysignup",
			data: "costume=" + signupForm1.val() + "&costume_backup=" + signupForm2.val() + "&status=" + signupForm3.val() + "&troopid=" + $("#modifysignupTroopIdForm").val() + "&limitedevent=" + $("#limitedEventCancel").val() + "&trooperid=" + $(this).attr("trooperid"),
			success: function(data)
			{
				alert("Status Updated!");
			}
		});
	});
	
	// End Modify Sign Up Form Change

	$("body").on("change", "#status", function(e)
	{
		if($("#status").val() == "4")
		{
			$("#reasonBlock").show();
		}
		else
		{
			$("#reasonBlock").hide();
		}

		if($("#status").val() == "3")
		{
			$("#attendBlock").show();
		}
		else
		{
			$("#attendBlock").hide();
		}
	});

	// If date picker (first option) is changed
	$("#datepicker").on("change", function()
	{
		// Only allow one day option
		$("#datepicker2").datetimepicker("option", "minDate", $("#datepicker").val());
		$("#datepicker2").datetimepicker("option", "maxDate", $("#datepicker").val());
	});

	$("#eventId").on("change", function()
	{
		if($("#editEventInfo").is(":hidden"))
		{
			//$("#editEventInfo").show();
			//$("#submitEdit").val("Close");
		}
		else
		{
			$("#editEventInfo").hide();
			$("#submitEdit").val("Edit");
		}

		if($("#rosterInfo").is(":hidden"))
		{
			//$("#submitRoster").val("Close");
			//$("#rosterInfo").show();
		}
		else
		{
			$("#submitRoster").val("Roster");
			$("#rosterInfo").hide();	
		}
	});

	$("body").on("change", "#userID", function(e)
	{
		if($("#editEventInfo").is(":hidden"))
		{
			//$("#editUserInfo").show();
			//$("#submitEditUser").val("Close");
		}
		else
		{
			$("#editUserInfo").hide();
			$("#submitEditUser").val("Edit");
		}

		// Only used for approving area
		$.ajax({
			type: "POST",
			url: "process.php?do=getuser",
			data: "id=" + $("#userID").val() + "&getuser=1",
			success: function(data)
			{
				var json = JSON.parse(data);
				$("#nameTable").html(json.name);
				$("#emailTable").html(json.email);
				$("#phoneTable").html(json.phone);
				$("#squadTable").html(json.squad);
				$("#tkTable").html(json.tkid);
			}
		});
	});
	
	$("body").on("change", "#userID2", function(e)
	{
		if($("#editEventInfo").is(":hidden"))
		{
			//$("#editUserInfo").show();
			//$("#submitEditUser").val("Close");
		}
		else
		{
			$("#editUserInfo").hide();
			$("#submitEditUser").val("Edit");
		}

		// Only used for approving area
		$.ajax({
			type: "POST",
			url: "process.php?do=getuser",
			data: "id=" + $("#userID2").val() + "&getuser=1",
			success: function(data)
			{
				var json = JSON.parse(data);
				$("#nameTable").html(ifEmpty(json.name));
				$("#emailTable").html(ifEmpty(json.email));
				$("#forumTable").html('<a href="https://www.fl501st.com/boards/memberlist.php?mode=viewprofile&un=' + json.forum + '" target="_blank">' + json.forum + '</a>');
				$("#phoneTable").html(ifEmpty(json.phone));
				$("#squadTable").html(ifEmpty(json.squad));
				$("#tkTable").html(ifEmpty(json.tkid));
			}
		});
	});
	
	/************ COSTUME *******************/
	
	// Costume Edit Select Change
	$("body").on("change", "#costumeIDEdit", function(e)
	{
		// If click please select, hide list
		if($("#costumeIDEdit :selected").val() == 0)
		{
			$("#editCostumeList").hide();
		}
		else
		{
			$("#editCostumeList").show();
		}

		$("#costumeNameEdit").val($("#costumeIDEdit :selected").attr("costumeName"));
		$("#costumeEraEdit").val($("#costumeIDEdit :selected").attr("costumeEra"));
		$("#costumeClubEdit").val($("#costumeIDEdit :selected").attr("costumeClub"));
	});
	
	$("body").on("click", "#submitEditCostume", function(e)
	{
		e.preventDefault();

		var form = $("#costumeEditForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to edit this costume?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitEditCostume=1",
				success: function(data)
				{
					// Change values for delete form
					$("#costumeID").children("option[value='" + $("#costumeIDEdit :selected").val() + "']").text($("#costumeNameEdit").val());
					
					// Change values for edit form
					$("#costumeIDEdit :selected").text($("#costumeNameEdit").val());
					$("#costumeIDEdit :selected").attr("costumeName", $("#costumeNameEdit").val());
					$("#costumeIDEdit :selected").attr("costumeEra", $("#costumeEraEdit").val());
					$("#costumeIDEdit :selected").attr("costumeClub", $("#costumeClubEdit").val());

					$("#editCostumeList").hide();

					$("#costumeIDEdit").val(0);

					// Alert to success
			  		alert("The costume was edited successfully!");
				}
			});
		}
	})

	$("body").on("click", "#addCostumeButton", function(e)
	{
		e.preventDefault();

		var form = $("#addCostumeForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to add this costume?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&addCostumeButton=1",
				success: function(data)
				{
					var json = JSON.parse(data);
					
					// Add to lists
					$("#costumeID").append("<option value='" + json[0].id + "'>" + $("#costumeName").val() + "</option>");
					$("#costumeIDEdit").append("<option value='" + json[0].id + "' costumeName='" + $("#costumeName").val() + "' costumeID='" + json[0].id + "' costumeEra='" + $("#costumeEra").val() + "' costumeClub='" + $("#costumeClub").val() + "'>" + $("#costumeName").val() + "</option>");

					// Clear form
					$("#costumeName").val("");
					$("#costumeEra").val("1");
					$("#costumeClub").val("0");
					
					if($("#costumeID option").length <= 1)
					{
						// Populate result
						$("#costumearea").html(json[0].result);
					}

					// Alert to success
			  		alert(json[0].message);
				}
			});
		}
	})
	
	$("body").on("click", "#submitDeleteCostume", function(e)
	{
		e.preventDefault();

		var form = $("#costumeDeleteForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this costume?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDeleteCostume=1",
				success: function(data)
				{
					// Delete from edit list
					$("#costumeIDEdit").children("option[value='" + $("#costumeID :selected").val() + "']").remove();

					// Clear
					$("#costumeID").find("option:selected").remove();
					
					// Alert to success
			  		alert("The costume was deleted successfully!");

			  		// Show message if empty
			  		if($("#costumeID").has("option").length <= 0)
			  		{
			  			$("#costumeDeleteForm").html("No costume to display.");
						
			  		}
					
			  		// Show message if empty - edit
			  		if($("#costumeIDEdit").has("option").length <= 1)
			  		{
			  			$("#costumeEditForm").html("No costume to display.");
			  		}
				}
			});

	  		// Show message if empty
	  		if($("#costumeID").has("option").length <= 0)
	  		{
	  			$("#costumeDeleteForm").html("No costume to display.");
	  		}
		}
	})
	
	/************ AWARD ********************/

	// Award Edit Select Change
	$("body").on("change", "#awardIDEdit", function(e)
	{
		// If click please select, hide list
		if($("#awardIDEdit :selected").val() == 0)
		{
			$("#editAwardList").hide();
		}
		else
		{
			$("#editAwardList").show();
		}

		$("#editAwardTitle").val($("#awardIDEdit :selected").attr("awardTitle"));
		$("#editAwardImage").val($("#awardIDEdit :selected").attr("awardImage"));
	});

	$("body").on("click", "#submitEditAward", function(e)
	{
		e.preventDefault();

		var form = $("#awardEdit");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to edit this award?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitEditAward=1",
				success: function(data)
				{
					// Change values for delete form
					$("#awardID").children("option[value='" + $("#awardIDEdit :selected").val() + "']").text($("#editAwardTitle").val());
					$("#awardIDAssign").children("option[value='" + $("#awardIDEdit :selected").val() + "']").text($("#editAwardTitle").val());
					
					// Change values in edit form select
					$("#awardIDEdit :selected").text($("#editAwardTitle").val());
					$("#awardIDEdit :selected").attr("awardTitle", $("#editAwardTitle").val());
					$("#awardIDEdit :selected").attr("awardImage", $("#editAwardImage").val());

					$("#editAwardList").hide();

					$("#awardIDEdit").val(0);

					// Alert to success
			  		alert("The award was edited successfully!");
				}
			});
		}
	})

	$("body").on("click", "#submitAwardAdd", function(e)
	{
		e.preventDefault();

		var form = $("#addAward");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to add this award?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitAddAward=1",
				success: function(data)
				{
					var json = JSON.parse(data);

					// Clear form
					$("#awardName").val("");
					$("#awardImage").val("");

					// Alert to success
			  		alert(json[0].message);
					
					if($("#awardID option").length <= 1)
					{
						// Populate result
						$("#awardarea").html(json[0].result);
						$("#assignarea").html(json[0].result2);
					}
				}
			});
		}
	})

	$("body").on("click", "#award", function(e)
	{
		e.preventDefault();

		var form = $("#awardUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to award this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitAward=1",
				success: function(data)
				{
					var json = JSON.parse(data);

					// Alert to success
			  		alert(json[0].message);
				}
			});
		}
	})

	$("body").on("click", "#submitDeleteAward", function(e)
	{
		e.preventDefault();

		var form = $("#awardUserDelete");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this award?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDeleteAward=1",
				success: function(data)
				{
					// Delete from award list
					$("#awardIDEdit").children("option[value='" + $("#awardID :selected").val() + "']").remove();
					
					// Clear
					$("#awardID").find("option:selected").remove();

					// Alert to success
			  		alert("The award was deleted successfully!");

			  		// Show message if empty
			  		if($("#awardID").has("option").length <= 0)
			  		{
			  			$("#awardUserDelete").html("No award to display.");
			  		}
					
			  		// Show message if empty - edit
			  		if($("#awardIDEdit").has("option").length <= 1)
			  		{
			  			$("#awardEdit").html("No award to display.");
			  		}
					
			  		// Show message if empty - assign
			  		if($("#awardIDAssign").has("option").length <= 0)
			  		{
			  			$("#assignarea").html("No award to display.");
			  		}
				}
			});

	  		// Show message if empty
	  		if($("#awardID").has("option").length <= 0)
	  		{
	  			$("#awardUserDelete").html("No award to display.");
	  		}
		}
	})

	$("#submitFinish").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to finish this event?");

		if (r == true)
		{
			$("<form>Charity Money Raised:<br /><br /><input type=\"number\" style=\"z-index:10000\" name=\"charity\" /><br /></form>").dialog({
				modal: true,
				dialogClass: "no-close",
				closeOnEscape: false,

				buttons: {
					"OK": function () {
						var charity = $("input[name=\"charity\"]").val();

						// AJAX
						$.ajax({
							type: "POST",
							url: url,
							data: form.serialize() + "&submitFinish=1&charity=" + charity,
							success: function(data)
							{
								// Alert to success
						  		alert("The event was finished successfully!");
							}
						});
						// END AJAX

						$(this).dialog("close");
					}
				}
			});
		}
	})
	
	$("#submitOpen").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to open this event?");

		if (r == true)
		{
			// AJAX
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitOpen=1",
				success: function(data)
				{
					// Alert to success
					alert("The event was opened successfully!");
				}
			});
			// END AJAX
		}
	})

	$("#submitConfirmList").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#confirmListForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to confirm these events?");


		if (r == true)
		{
			if($("#costumeChoice").val() != "" || $("#costumeChoice").val() !== undefined || $("#costumeChoice").val() !== null)
			{
				$.ajax({
					type: "POST",
					url: url,
					data: form.serialize() + "&submitConfirmList=1",
					success: function(data)
					{
			            if($("input:checkbox:checked").length == 0)
			            {
			            	// Select a troop
			            	alert("Please select a troop to confirm.");
			            }
			            else
			            {
				            // If all items gone
				            if($("input:checkbox:checked").length == $("input:checkbox").length)
				            {
				            	// Hide whole area
				            	$("#confirmArea").html("");
				            }
				            else
				            {
				            	// If there is still data
				            	var json = JSON.parse(data);
				            	$("#confirmArea2").html(json.data);
				            }

				            alert("Troops confirmation submitted!");
			        	}
					}
				});
			}
			else
			{
				alert("Please select a costume.");
			}
		}
	})

	$("#submitConfirmListDelete").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#confirmListForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you did NOT attend these events?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitConfirmListDelete=1",
				success: function(data)
				{
		            if($("input:checkbox:checked").length == 0)
		            {
		            	// Select a troop
		            	alert("Please select a troop to confirm.");
		            }
		            else
		            {
			            // If all items gone
			            if($("input:checkbox:checked").length == $("input:checkbox").length)
			            {
			            	// Hide whole area
			            	$("#confirmArea").html("");
			            }
			            else
			            {
			            	// If there is still data
			            	var json = JSON.parse(data);
				            $("#confirmArea2").html(json.data);
			            }

			            alert("Troops confirmation submitted!");
			        }
				}
			});
		}
	})

	$("#easyFillButton").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#easyFillTool");
		var url = form.attr("action");

		// Get Text
		var text = $("#easyFill").val();

		// Convert to array
		var textArray = text.split(":");

		// Event Title
		var eventTitle = textArray.indexOf("Event Name");	
		eventTitle = textArray[eventTitle + 1].split("\n");
		$("#eventName").val(eventTitle[0]);

		// Event Venue
		var eventVenue = textArray[2].split("Venue address");
		$("#eventVenue").val(eventVenue[0].slice(1));

		// Event Location
		var eventLocation = textArray[3].split("Event Start");
		eventLocation[0] = eventLocation[0].replace(/\n/g, " ");
		$("#location").val(eventLocation[0].slice(1));

		// Event Time ISSUE WITH : in time need to fix
		var eventTime = textArray[4].slice(1) + ":" + textArray[5].split("Event End")[0];
		eventTime = eventTime.split("-");
		eventTime = eventTime[0] + eventTime[1];
		eventTime = moment(new Date(eventTime)).format('MM/DD/YYYY HH:mm');
		$("#datepicker").val(eventTime);

		// Event Time 2
		var eventTime2 = textArray[6].slice(1) + ":" + textArray[7].split("Event Website")[0];
		eventTime2 = eventTime2.split("-");
		eventTime2 = eventTime2[0] + eventTime2[1];
		eventTime2 = moment(new Date(eventTime2)).format('MM/DD/YYYY HH:mm');
		$("#datepicker2").val(eventTime2);

		// Website
		var website = textArray[8].slice(1).split("Expected number of attendees")[0];
		$("#website").val(website);

		// Number of Attendees
		var numberOfAttend = textArray[9].slice(1).split("Requested number of characters")[0];
		$("#numberOfAttend").val(parseInt(numberOfAttend));

		// Number of Requested Characters
		var requestedNumber = textArray[10].slice(1).split("Requested character types")[0];
		$("#requestedNumber").val(parseInt(requestedNumber));

		// Requested Characters
		var requestedCharacter = textArray[11].slice(1).split("Secure changing/staging area")[0];
		$("#requestedCharacter").val(requestedCharacter);

		// Secure Changing Area?
		var secure = textArray[12].slice(1).split("Can troopers carry blasters")[0];
		if(secure.includes("No"))
		{
			$("#secure").val(0);
		}
		else
		{
			$("#secure").val(1);
		}

		// Can Troopers Carry Blasters?
		var blasters = textArray[13].slice(1).split("Can troopers carry/bring props like lightsabers and staffs")[0];
		if(blasters.includes("NO") || blasters.includes("no") || blasters.includes("No"))
		{
			$("#blasters").val(0);
		}
		else
		{
			$("#blasters").val(1);
		}

		// Can Troopers Carry Lightsabers?
		var lightsabers = textArray[14].slice(1).split("Is parking available")[0];
		if(lightsabers.includes("NO") || lightsabers.includes("no") || lightsabers.includes("No"))
		{
			$("#lightsabers").val(0);
		}
		else
		{
			$("#lightsabers").val(1);
		}

		// Parking?
		var parking = textArray[15].slice(1).split("Is venue accessible to those with limited mobility")[0];
		if(parking.includes("NO") || parking.includes("no") || parking.includes("No"))
		{
			$("#parking").val(0);
		}
		else
		{
			$("#parking").val(1);
		}

		// Mobility?
		var mobility = textArray[16].slice(1).split("Amenities available at venue")[0];
		if(mobility.includes("NO") || mobility.includes("no") || mobility.includes("No"))
		{
			$("#mobility").val(0);
		}
		else
		{
			$("#mobility").val(1);
		}

		// Amenities?
		var amenities = textArray[17].slice(1).split("Comments")[0];
		$("#amenities").val(amenities);

		// Comments
		var comments = textArray[18].slice(1).split("Referred by")[0];
		$("#comments").val(comments);

		// Referred By
		var referred = textArray[19].slice(1).split("Referred by")[0];
		$("#referred").val(referred);
	})
});