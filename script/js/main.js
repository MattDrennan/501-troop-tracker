// Add methods to validator
jQuery.validator.addMethod("noSpace", function(value, element)
{ 
	return value.trim().replace(/[\t\n]+/g,' ').length > 1; 
}, "Please enter a comment.");

// bbcoder: Converts textbox content to BB Code
function bbcoder(code)
{
	try
	{
		var old = "";
		var textarea = document.getElementsByName("comments")[0];
		var value = textarea.value;
		var startPos = textarea.selectionStart;
		var endPos = textarea.selectionEnd;
		var selectedText = value.substring(startPos, endPos);

		switch (code)
		{
			case 'B':
				bbbold(textarea, value, startPos, endPos, selectedText);
			break;
			
			case 'U':
				bbunder(textarea, value, startPos, endPos, selectedText);
			break;
			
			case 'I':
				bbitalic(textarea, value, startPos, endPos, selectedText);
			break;
			
			case 'Q':
				bbquote(textarea, value, startPos, endPos, selectedText);
			break;
			
			case 'COLOR':
				bbcolor(textarea, value, startPos, endPos, selectedText);
			break;
			
			case 'SIZE':
				bbsize(textarea, value, startPos, endPos, selectedText);
			break;
			
			case 'URL':
				bburl(textarea, value, startPos, endPos, selectedText);
			break;
			
			default:
				//alert('Invalid argument');
			break;
		}
	}
	catch (e)
	{
		//alert(e.toString());
	}
}

// BB - Bold
function bbbold(textarea, value, startPos, endPos, selectedText)
{
	textarea.value = value.replaceBetween(startPos, endPos, "[b]" + selectedText + "[/b]");
}

// BB - Italic
function bbitalic(textarea, value, startPos, endPos, selectedText)
{
	textarea.value = value.replaceBetween(startPos, endPos, "[i]" + selectedText + "[/i]");
}

// BB - Underline
function bbunder(textarea, value, startPos, endPos, selectedText)
{
	textarea.value = value.replaceBetween(startPos, endPos, "[u]" + selectedText + "[/u]");
}

// BB - Quote
function bbquote(textarea, value, startPos, endPos, selectedText)
{
	textarea.value = value.replaceBetween(startPos, endPos, "[quote]" + selectedText + "[/quote]");
}

// BB - Color
function bbcolor(textarea, value, startPos, endPos, selectedText)
{
	var color = window.prompt("What color?");
	textarea.value = value.replaceBetween(startPos, endPos, "[color=" + color + "]" + selectedText + "[/color]");
}

// BB - Size
function bbsize(textarea, value, startPos, endPos, selectedText)
{
	var size = window.prompt("What font size?");
	textarea.value = value.replaceBetween(startPos, endPos, "[size=" + size + "]" + selectedText + "[/size]");
}

// BB - URL
function bburl(textarea, value, startPos, endPos, selectedText)
{
	textarea.value = value.replaceBetween(startPos, endPos, "[url]" + selectedText + "[/url]");
}

// Replace between string
String.prototype.replaceBetween = function(start, end, what)
{
	return this.substring(0, start) + what + this.substring(end);
};


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
	if(val == "" || val === undefined || val === null || val == "Select a costume...")
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

// selectAdd: Selects we want to search
function selectAdd()
{
	// Search select boxes
	$("#costumeIDEdit").select2();
	$("#costumeChoice").select2();
	$("#costumeID").select2();
	$("#userIDAward").select2();
	$("#awardIDAssign").select2();
	$("#awardIDEdit").select2();
	$("#awardID").select2();
	$("select[name^=eventId]").select2();
	$("select[name^=userID]").select2();
	$("select[name^=modifysignupFormCostume]").select2();
	$("select[name^=modiftybackupcostumeForm]").select2();
	$("#costume").select2();
	$("#backupcostume").select2();
	$("#costumebackup").select2();
	$("select[name^=trooperSelect]").select2();
	$("select[name^=costumeChoice]").select2();
	$("select[name^=attendedcostume]").select2();
	$("select[name^=trooperSelect]").select2();
	$("select[name^=modifysignupFormCostume2]").select2();
	$("select[name^=modiftybackupcostumeForm2]").select2();
	$("select[name^=costumeValSelect]").select2();
	$("select[name^=costumeVal]").select2();
	$("select[name^=attendcostumeVal]").select2();
}

$(document).ready(function()
{
	// Add select2 to DOM
	selectAdd();

	// Before / After Ajax
	$(document).ajaxStart(function ()
	{
		$.LoadingOverlay("show");
	})
	$(document).ajaxStop(function ()
	{
		$.LoadingOverlay("hide");
	});
	// Shows / Hide Change Limits
	$("body").on("click", "#limitChange", function(e)
	{
		e.preventDefault();
		
		if($("#limitChangeArea").is(":hidden"))
		{
			$("#limitChangeArea").show();
			$("#limitChange").text("Hide Limits");
		}
		else
		{
			$("#limitChangeArea").hide();
			$("#limitChange").text("Change Limits");
		}
	})

	// Event Notifications - Subscribe / Unsubscribe
	$("body").on("click", "#subscribeupdates", function(e)
	{
		e.preventDefault();

		$.ajax({
			type: "POST",
			url: "process.php?do=eventsubscribe",
			data: "eventsubscribe=1&event=" + $("#subscribeupdates").attr("event"),
			success: function(data)
			{
				// Get JSON
				var json = JSON.parse(data);

				if($("#subscribeupdates").text() == "Subscribe Updates")
				{
					alert(json.message);
					$("#subscribeupdates").text("Unsubscribe Updates");
				}
				else
				{
					alert(json.message);
					$("#subscribeupdates").text("Subscribe Updates");
				}
			}
		});
	})
	
	// Add shift variable
	var shifts = 2;
	var pair = 1;
	
	// Add shift
	$("body").on("click", "#addshift", function(e)
	{
		e.preventDefault();
		
		// Add input
		$("#datetimeadd").append('<div id="pair' + pair + '" name="pair' + pair + '"><hr /><input type="hidden" name="shiftpost' + pair + '" value="' + pair + '" /><p>Date/Time Start:</p> <input type="text" name="adddateStart' + pair + '" id="datepicker' + (shifts + 1) + '" shifts1="' + (shifts + 1) + '" shifts2="' + (shifts + 2) + '" /> <p>Date/Time End:</p> <input type="text" name="adddateEnd' + pair + '" id="datepicker2' + (shifts + 2) + '" shifts1="' + (shifts + 1) + '" shifts2="' + (shifts + 2) + '" /> <input type="submit" name="removeshift" pair="' + pair + '" value="Remove Shift" /></div>');
		
		// Make date/time object
		$("#datepicker" + (shifts + 1)).datetimepicker();
		$("#datepicker2" + (shifts + 2)).datetimepicker();
		
		// Only allow one day
		$("#datepicker" + (shifts + 1)).on("change", function()
		{
			// Only allow one day option
			$("#datepicker2" + $(this).attr("shifts2")).datetimepicker("option", "minDate", $("#datepicker" + $(this).attr("shifts1")).val());
			$("#datepicker2" + $(this).attr("shifts2")).datetimepicker("option", "maxDate", $("#datepicker" + $(this).attr("shifts1")).val());
		});
		
		// Increment
		shifts += 2;
		pair++;
	})
	
	// Remove Shift
	$("body").on("click", "input[name=removeshift]", function(e)
	{
		e.preventDefault();
		
		// Remove shift
		$("#pair" + $(this).attr("pair")).remove();
	})
	
	// Shows / Hide Trooper Stats
	$("body").on("click", "#showstats", function(e)
	{
		e.preventDefault();
		
		if($("#mystats").is(":hidden"))
		{
			$("#mystats").show();
			$("#showstats").text("Hide My Stats");
		}
		else
		{
			$("#mystats").hide();
			$("#showstats").text("Show My Stats");
		}
	})
	
	// Hide calendar view if screen is too small
	$(window).resize(function()
	{
		if($(window).width() < 800)
		{
			$("a[id=changeview]").hide();
		}
		else
		{
			$("a[id=changeview]").show();
		}
	});
	
	// List/Calendar View
	$("#changeview").click(function(e)
	{
		e.preventDefault();
		
		if($("#calendarview").is(":hidden"))
		{
			$("#calendarview").show();
			$("#listview").hide();
			$("#changeview").text("List View");
		}
		else
		{
			$("#listview").show();
			$("#calendarview").hide();
			$("#changeview").text("Calendar View");
		}
	})
	
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
		$("#trooperSelect option, #userID option, #userIDAward option").each(function(index)
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
	
	// Trooper Check - Reserve Member Button
	$("#submitTroopCheckReserve").click(function(e)
	{
		e.preventDefault();
		
		var r = confirm("Are you sure you want to makes these troopers RESERVE status?");
		
		// If confirmed
		if(r == true)
		{
			// Send data
			var form = $("#trooperCheckForm");

			$.ajax({
				type: "POST",
				url: "process.php?do=troopercheckreserve",
				data: form.serialize(),
				success: function(data)
				{
					// Get JSON
					var json = JSON.parse(data);
					
					// Loop through checkboxes
					$(":checkbox:checked").each(function() {
						// Change permission
						$("tr[name=row_" + $(this).val() + "] td[name=permission]").text("Reserve Member");
						
						// Uncheck
						$(this).prop("checked", false);
					});
					
					// Alert user
					alert(json.data);
				}
			});
		}
	})
	
	// Trooper Check - Retired Member Button
	$("#submitTroopCheckRetired").click(function(e)
	{
		e.preventDefault();
		
		var r = confirm("Are you sure you want to makes these troopers RETIRED status?");
		
		// If confirmed
		if(r == true)
		{
			// Send data
			var form = $("#trooperCheckForm");

			$.ajax({
				type: "POST",
				url: "process.php?do=troopercheckretired",
				data: form.serialize(),
				success: function(data)
				{
					// Get JSON
					var json = JSON.parse(data);
					
					// Loop through checkboxes
					$(":checkbox:checked").each(function() {
						// Remove row
						$("tr[name=row_" + $(this).val() + "]").remove();
						
						// Uncheck
						$(this).prop("checked", false);
					});
					
					// Alert user
					alert(json.data);
				}
			});
		}
	})
	
	// Get Location Button
	$("a[name='deletephoto']").click(function(e)
	{
		e.preventDefault();
		
		var elementS = $(this);
		
		var r = confirm("Are you sure you want to delete this photo?");
		
		// If confirmed
		if(r == true)
		{
			// Send data
			$.ajax({
				type: "POST",
				url: "process.php?do=deletephoto",
				data: { photoid: $(this).attr("photoid") },
				success: function(data)
				{
					// Get JSON
					var json = JSON.parse(data);
					
					// Alert user
					alert(json.data);
					
					// Remove elements
					$("#photo" + elementS.attr("photoid")).remove();
					elementS.remove();
				}
			});
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
	
	// Change Site Settings - Edit Support Goal
	$("#submitSupportGoal").button().click(function(e)
	{
		e.preventDefault();
		
		// Check it edit support goal area is clear
		if($("#settingsEditArea").html() == "")
		{
			// Load data from form
			var form = $("#changeSettingsForm");
			var url = form.attr("action");
			
			// Get AJAX data
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&getSupportGoal=1",
				success: function(data)
				{
					// Get JSON data
					var json = JSON.parse(data);
					
					// Show setting
					$("#settingsEditArea").html('<p>$<input type="number" name="supportgoal" id="supportgoal" value="' + json.data + '" /> <input type="submit" name="saveSupportGoal" id="saveSupportGoal" value="Save" /></p>');
				}
			});
		}
		else
		{
			// Clear
			$("#settingsEditArea").html("");
		}
	})
	
	// Change Site Settings - Save Support Goal
	$("body").on("click", "#saveSupportGoal", function(e)
	{
		e.preventDefault();
		
		// Load data from form
		var form = $("#changeSettingsForm");
		var url = form.attr("action");
		
		// Get AJAX data
		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&saveSupportGoal=1",
			success: function(data)
			{
				// Clear
				$("#settingsEditArea").html("");	
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
				selectAdd();
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
					$("#rebelforum").val(json.rebelforum);
					$("#mandoid").val(json.mandoid);
					$("#sgid").val(json.sgid);
					$("#supporter").val(json.supporter);
				}
				else
				{
					$("#submitEditUser").val("Edit");
					$("#editUserInfo").hide();
				}
			}
		});
	})

	// Edit Event Drop Down - Show Edit Form
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
					// Hide Roster
					$("#submitRoster").val("Roster");
					$("#rosterInfo").html("");
					$("#rosterInfo").hide();
					
					// Hide Charity
					$("#charityAmount").hide();
					$("#submitCharity").val("Set Charity Amount");
				}

				if($("#editEventInfo").is(":hidden"))
				{
					$("#submitEdit").val("Close");
					$("#editEventInfo").show();
					
					var date1 = moment(json.dateStart).format("MM/DD/YYYY HH:mm");
					var date2 = moment(json.dateEnd).format("MM/DD/YYYY HH:mm");

					$("#eventLink").val(json.eventLink);
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
					$("#limitOther").val(json.limitOther);
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
	
	/************************* CHARITY *******************************/
	
	// Edit charity amount button
	$("#submitCharity").button().click(function(e)
	{
		e.preventDefault();
		
		// Hide event info
		if(!$("#editEventInfo").is(":hidden"))
		{
			$("#editEventInfo").hide();
			$("#submitEdit").val("Edit");
		}

		// Hide roster info
		if(!$("#rosterInfo").is(":hidden"))
		{
			$("#submitRoster").val("Roster");
			$("#rosterInfo").html("");
			$("#rosterInfo").hide();
		}
		
		// Show charity button, when pressed
		if($("#charityAmount").is(":hidden"))
		{
			// Show charity
			$("#charityAmount").show();
			
			// Change button to close
			$("#submitCharity").val("Close");
			
			// Get form info
			var form = $("#editEvents");
			var url = form.attr("action");

			// Request event data
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitEdit=1",
				success: function(data)
				{
					var json = JSON.parse(data);
					
					// Set charity field
					$("#charityAmountField").val(json.moneyRaised);
				}
			});
		}
		else
		{
			// Hide charity form
			$("#charityAmount").hide();
			$("#submitCharity").val("Set Charity Amount");
		}
	})
	
	// Set charity amount button
	$("#charityAmountSave").button().click(function(e)
	{
		e.preventDefault();
		
		// Get form info
		var form = $("#editEvents");
		var url = form.attr("action");

		if(parseInt($("#charityAmountField").val()) || parseInt($("#charityAmountField").val()) === 0)
		{
			// Save
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitCharity=1&charity=" + $("#charityAmountField").val(),
				success: function(data)
				{
					// Hide charity form
					$("#charityAmount").hide();
					$("#submitCharity").val("Set Charity Amount");
			
					// Send success message
					alert("Success!");
				}
			});
		}
		else
		{
			alert("Enter a valid number.");
		}
	})
	
	/************************* END CHARITY *******************************/

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
					// Hide Edit Form
					$("#editEventInfo").hide();
					$("#submitEdit").val("Edit");
					
					// Hide Charity
					$("#charityAmount").hide();
					$("#submitCharity").val("Set Charity Amount");
				}

				if($("#rosterInfo").is(":hidden"))
				{
					$("#submitRoster").val("Close");
					$("#rosterInfo").html(data);
					selectAdd();
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

	// E-mail settings - unsubscribe / subscribe all button
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
				if($("#unsubscribeButton").val() == "Unsubscribe All")
				{
					alert(data);
					$("#unsubscribeButton").val("Subscribe");
					$("#emailSettingsOptions").hide();
				}
				else
				{
					alert(data);
					$("#unsubscribeButton").val("Unsubscribe All");
					$("#emailSettingsOptions").show();
				}
			}
		});
	})
	
	// E-mail settings - click checkbox
	$("body").on("click", "#emailsettingsForm input", function(e)
	{
		e.preventDefault();

		var form = $("#emailsettingsForm");
		var url = form.attr("action");
		
		var checkbox = $(this);
		var name = $(this).attr("name");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&setemailsettings=1&setting=" + name,
			success: function(data)
			{
				// Is checked?
				if(checkbox.is(":checked"))
				{
					checkbox.prop("checked", false);
				}
				else
				{
					checkbox.prop("checked", true);
				}
			}
		});
	});
	
	// When admin clicks delete comment icon
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
	
	// When trooper quotes a comment
	$("body").on("click", "[id^=quoteComment]", function(e)
	{
		e.preventDefault();

		// Get ID of comment
		var id = $(this).attr("name");
		
		// Add comment to comment text area
		$("#comment").val($("#comment").val() + "[quote]" + $("table[name=comment_" + id + "] td[name=insideComment]").text() + "[/quote]\n\n");
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

	$("#emailSettingLink").click(function(e)
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
					$("#forumRebelTable").html("");
					$("#mandoTable").html("");
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
	
	// Manage Trooper - Reset Password
	$("#submitResetPasswordUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to reset this user's password?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitResetUser=1",
				success: function(data)
				{
					// Alert to success
			  		alert("The user's password was reset! New password: ineedanewpassword123");
				}
			});
		}
	})
	
	// Manage Trooper - View Profile
	$("#submitViewProfile").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editUser");
		var url = form.attr("action");
		
		window.location = 'index.php?profile=' + $("#userID option:selected").val();
	})

	// Manage Trooper - Delete Trooper
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
		
		// Set up vasriables
		var trooperid = $("input[name=trooperSelectEdit]:checked").val();
		var signid = $("input[name=trooperSelectEdit]:checked").attr("signid");

		var form = $("#troopRosterForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to remove this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&removetrooper=1&signid=" + signid,
				success: function(data)
				{
					var json = JSON.parse(data);
					
					// Get variables
					var eventId = $("input[name=eventId][signid=" + signid + "]").val();
					var troopername = $("input[name=troopername][signid=" + signid + "]").val();
					var tkid = $("input[name=tkid][signid=" + signid + "]").val();
					
					// Add to trooper list
					$("#trooperSelect").append('<option value="' + trooperid + '" troopername="' + troopername + '" tkid="' + tkid + '">' + troopername + ' - ' + tkid + '</option>');

					// Remove
					$("tr[name=roster_" + trooperid + "][signid=" + signid + "]").remove();

					// Display message
					alert(json.data);
				}
			});
		}
	});

	$("body").on("click", "#edittrooper", function(e)
	{
		e.preventDefault();
		
		// Set up vasriables
		var trooperid = $("input[name=trooperSelectEdit]:checked").val();
		var signid = $("input[name=trooperSelectEdit]:checked").attr("signid");

		if($("input[name=trooperSelectEdit]").is(":checked"))
		{
			if($("#edittrooper").val() != "Save")
			{
				// Change submit button
				$("#edittrooper").val("Save");
				
				// Replace None
				if($("[name=reason2" + trooperid + "][signid=" + signid + "]").find("input").val() == "None")
				{
					$("[name=reason2" + trooperid + "][signid=" + signid + "]").find("input").val("");
				}

				// Show Inputs for edit
				$("[name=costume2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=backup2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=status2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=reason2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=attendcostume2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=dateAttending" + trooperid + "][signid=" + signid + "]" + "Edit").show();
				$("[name=dateAttended" + trooperid + "][signid=" + signid + "]" + "Edit").show();

				// Hide static values
				$("[name=costume1" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=backup1" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=status1" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=reason1" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=attendcostume1" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=dateAttending" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=dateAttended" + trooperid + "][signid=" + signid + "]").hide();
				
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
						data: form.serialize() + "&submitEditRoster=1&signid=" + signid,
						success: function(data)
						{
							// Change submit button
							$("#edittrooper").val("Edit Trooper");

							// Hide Inputs for edit
							$("[name=costume2" + trooperid + "][signid=" + signid + "]").hide();
							$("[name=backup2" + trooperid + "][signid=" + signid + "]").hide();
							$("[name=status2" + trooperid + "][signid=" + signid + "]").hide();
							$("[name=reason2" + trooperid + "][signid=" + signid + "]").hide();
							$("[name=attendcostume2" + trooperid + "][signid=" + signid + "]").hide();
							$("[name=dateAttending" + trooperid + "][signid=" + signid + "]" + "Edit").hide();
							$("[name=dateAttended" + trooperid + "][signid=" + signid + "]" + "Edit").hide();
							
							// Set the comment back to "None" if no value
							if($("[name=reason2" + trooperid + "][signid=" + signid + "]").find("input").val() == "")
							{
								$("[name=reason2" + trooperid + "][signid=" + signid + "]").find("input").val("None");
							}
							
							// Set values
							$("[name=costume1" + trooperid + "][signid=" + signid + "]").html($("[name=costume2" + trooperid + "][signid=" + signid + "]").find("select :selected").text());
							$("[name=backup1" + trooperid + "][signid=" + signid + "]").html($("[name=backup2" + trooperid + "][signid=" + signid + "]").find("select :selected").text());
							$("[name=status1" + trooperid + "][signid=" + signid + "]").html($("[name=status2" + trooperid + "][signid=" + signid + "]").find("select :selected").text());
							$("[name=reason1" + trooperid + "][signid=" + signid + "]").html($("[name=reason2" + trooperid + "][signid=" + signid + "]").find("input").val());
							$("[name=attendcostume1" + trooperid + "][signid=" + signid + "]").html($("[name=attendcostume2" + trooperid + "][signid=" + signid + "]").find("select :selected").text());
							
							// Set attended text
							if($("[name=attendcostume2" + trooperid + "][signid=" + signid + "]").find("select :selected").text() == "None")
							{
								$("[name=attendcostume1" + trooperid + "][signid=" + signid + "]").text("Not Submitted");
							}

							// Show static values
							$("[name=costume1" + trooperid + "][signid=" + signid + "]").show();
							$("[name=backup1" + trooperid + "][signid=" + signid + "]").show();
							$("[name=status1" + trooperid + "][signid=" + signid + "]").show();
							$("[name=reason1" + trooperid + "][signid=" + signid + "]").show();
							$("[name=attendcostume1" + trooperid + "][signid=" + signid + "]").show();
							$("[name=dateAttending" + trooperid + "][signid=" + signid + "]").show();
							$("[name=dateAttended" + trooperid + "][signid=" + signid + "]").show();

							var json = JSON.parse(data);
							
							$("[name=dateAttending" + trooperid + "][signid=" + signid + "]").html(json.data);
							$("[name=dateAttended" + trooperid + "][signid=" + signid + "]").html(json.data2);
							
							// Re-enable all input radios to prevent issues
							$("input[name=trooperSelectEdit]").each(function( index )
							{
								$(this).prop("disabled", false);
							});

							// Alert to success
					  		alert("Roster updated!");
						}
					});
				}
			}
		}
	});
	
	// Modify Sign Up Form Change
	$("body").on("change", "select[name=modifysignupFormCostume], select[name=modiftybackupcostumeForm], select[name=modifysignupStatusForm]", function(e)
	{
		var trooperid = $(this).attr("trooperid");
		var signid = $(this).attr("signid");
		var signupForm1 = $("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "]");
		var signupForm2 = $("select[name=modiftybackupcostumeForm][trooperid=" + trooperid + "][signid=" + signid + "]");
		var signupForm3 = $("select[name=modifysignupStatusForm][trooperid=" + trooperid + "][signid=" + signid + "]");
		
		$.ajax({
			type: "POST",
			url: "process.php?do=modifysignup",
			data: "costume=" + signupForm1.val() + "&costume_backup=" + signupForm2.val() + "&status=" + signupForm3.val() + "&troopid=" + $("#modifysignupTroopIdForm").val() + "&limitedevent=" + $("#limitedEventCancel").val() + "&trooperid=" + trooperid + "&signid=" + signid,
			success: function(data)
			{
				console.log(data);
				var json = JSON.parse(data);
				
				// If JSON did not fail
				if(json.success != "failed")
				{
					// Adjust options based on costume
					if((($("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "] option:selected").attr("club") == 0 && (json.limit501st - json.limit501stTotal) > 0) || ($("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "] option:selected").attr("club") == 1 && (json.limitRebels - json.limitRebelsTotal) > 0) || ($("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "] option:selected").attr("club") == 2 && (json.limitMando - json.limitMandoTotal) > 0) || ($("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "] option:selected").attr("club") == 3 && (json.limitDroid - json.limitDroidTotal) > 0) || ($("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "] option:selected").attr("club") == 4 && (json.limitOther - json.limitOtherTotal) > 0)) && json.staus != 4)
					{
						// Empty select
						signupForm3.empty();

						// Refill
						signupForm3.append("<option value='0'>I'll be there</option> <option value='2'>Tentative</option> <option value='4'>Cancel</option>");

						// Set selected value
						signupForm3.val(json.status);
					}
					else
					{
						// Empty select
						signupForm3.empty();

						// If not stand by
						if(json.status != 1)
						{
							// Refill
							signupForm3.append("<option value='0'>I'll be there</option> <option value='2'>Tentative</option> <option value='4'>Cancel</option>");
						}
						// Troop is full, show stand by
						else
						{
							// Refill
							signupForm3.append("<option value='1'>Stand By</option> <option value='4'>Cancel</option>");
						}

						// Set selected value
						signupForm3.val(json.status);
					}

					// Update troopers remaining on the page
					$("div[name=troopersRemainingDisplay]").html(json.troopersRemaining);

					// Change text on page
					if(signupForm3.val() == 4)
					{
						$("#signeduparea").html("<p><b>You have canceled this troop.</b></p>");
					}
					else
					{
						$("#signeduparea").html("<p><b>You are signed up for this troop!</b></p>");
					}
					
					alert("Status Updated!");
				}
				else
				{
					alert(json.data);
				}
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

	// When command staff event select box is changed
	$("#eventId").on("change", function()
	{
		// Hide Edit Info
		if(!$("#editEventInfo").is(":hidden"))
		{
			$("#editEventInfo").hide();
			$("#submitEdit").val("Edit");
		}

		// Hide Roster Info
		if(!$("#rosterInfo").is(":hidden"))
		{
			$("#submitRoster").val("Roster");
			$("#rosterInfo").hide();	
		}
		
		// Hide Charity
		if(!$("#charityAmount").is(":hidden"))
		{
			$("#charityAmount").hide();
			$("#submitCharity").val("Set Charity Amount");
		}
	});

	$("body").on("change", "#userID", function(e)
	{
		if($(this).val() > 0)
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
		}
	});
	
	// Approve Trooper Requests - On Trooper Change
	$("body").on("change", "#userID2", function(e)
	{
		// Prevent errors by user selecting first option
		if($("#userID2").val() != -1)
		{
			if(!$("#editEventInfo").is(":hidden"))
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
					$("#nameTable").html('<a href="index.php?action=commandstaff&do=managetroopers&uid=' + $("#userID2").val() + '">' + ifEmpty(json.name) + '</a>');
					$("#emailTable").html(ifEmpty(json.email));
					$("#forumTable").html('<a href="https://www.fl501st.com/boards/memberlist.php?mode=viewprofile&un=' + json.forum + '" target="_blank">' + json.forum + '</a>');
					
					// If not a Rebel Legion member
					if(json.rebelforum == "")
					{
						$("#forumRebelTable").html('N/A');
					}
					else
					{
						// If a Rebel Legion member
						$("#forumRebelTable").html('<a href="https://www.forum.rebellegion.com/forum/profile.php?mode=viewprofile&u=' + json.rebelforum + '" target="_blank">' + json.rebelforum + '</a>');
					}
					
					// If not a Mando member
					if(json.mandoid == 0)
					{
						$("#mandoTable").html('N/A');
					}
					else
					{
						// If a Rebel Legion member
						$("#mandoTable").html('CAT #' + json.mandoid);
					}

					// If not a Saber Guild member
					if(json.sgid == 0)
					{
						$("#sgTable").html('N/A');
					}
					else
					{
						// If a Rebel Legion member
						$("#sgTable").html('SG #' + json.sgid);
					}
					
					$("#phoneTable").html(ifEmpty(json.phone));
					$("#squadTable").html(ifEmpty(json.squad));
					
					// Check if link is valid
					if(json.link != "")
					{
						$("#tkTable").html(ifEmpty('<a href="' + json.link + '" target="_blank">' + json.tkid + '</a>'));
					}
					else
					{
						$("#tkTable").html(ifEmpty(json.tkid));	
					}
				}
			});
		}
		else
		{
			// Reset table
			$("#nameTable").html("");
			$("#emailTable").html("");
			$("#forumTable").html("");
			$("#forumRebelTable").html("");
			$("#mandoTable").html("");
			$("#sgTable").html("");
			$("#phoneTable").html("");
			$("#squadTable").html("");
			$("#tkTable").html("");
		}
	});
	
	/************ COSTUME *******************/
	
	// Costume Management - Edit select change
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

		$("#costumeNameEdit").val($(this).find('option:selected').attr("costumeName"));
		$("#costumeEraEdit").val($(this).find('option:selected').attr("costumeEra"));
		$("#costumeClubEdit").val($(this).find('option:selected').attr("costumeClub"));
	});
	
	// Costume Management - Edit Costume Button
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

	// Costume management - Add costume button
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
						selectAdd();
					}

					// Alert to success
			  		alert(json[0].message);
				}
			});
		}
	})
	
	// Costume Management - Delete Costume
	$("body").on("click", "#submitDeleteCostume", function(e)
	{
		e.preventDefault();

		var form = $("#costumeDeleteForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this costume?");

		if (r == true && $("#costumeID").val() > 0)
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

			  		// Clear edit area
			  		$("#editCostumeList").hide();

			  		// Show message if empty
			  		if($("#costumeID option").length <= 0)
			  		{
			  			$("#costumeDeleteForm").html("No costume to display.");
						
			  		}
					
			  		// Show message if empty - edit
			  		if($("#costumeIDEdit option").length <= 1)
			  		{
			  			$("#costumeEditForm").html("No costume to display.");
			  		}
				}
			});

	  		// Show message if empty
	  		if($("#costumeID option").length <= 0)
	  		{
	  			$("#costumeDeleteForm").html("No costume to display.");
	  		}
		}
		else
		{
			alert("Please select a costume.");
		}
	})
	
	/************ AWARD ********************/

	// Awards - Edit select change
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

	// Awards - Finsih Edit
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

	// Awards - Add award
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
						selectAdd();
					}
				}
			});
		}
	})

	// Awards - Give award
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

	// Awards - Delete Award
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

					// Clear edit area
					$("#editAwardList").hide();

					// Alert to success
			  		alert("The award was deleted successfully!");

			  		// Show message if empty
			  		if($("#awardID option").length <= 0)
			  		{
			  			$("#awardUserDelete").html("No award to display.");
			  		}
					
			  		// Show message if empty - edit
			  		if($("#awardIDEdit option").length <= 1)
			  		{
			  			$("#awardEdit").html("No award to display.");
			  		}
					
			  		// Show message if empty - assign
			  		if($("#awardIDAssign option").length <= 0)
			  		{
			  			$("#assignarea").html("No award to display.");
			  		}
				}
			});

	  		// Show message if empty
	  		if($("#awardID option").length <= 0)
	  		{
	  			$("#awardUserDelete").html("No award to display.");
	  		}
		}
	})

	// Edit Event - Finish Event
	$("#submitFinish").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to finish this event?");

		if (r == true)
		{
			// AJAX
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitFinish=1",
				success: function(data)
				{
					// Alert to success
					alert("The event was finished successfully!");
				}
			});
			// END AJAX
		}
	})
	
	// Edit Event - Open Event
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
	
	// Edit Event - Lock Event
	$("#submitLock").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to lock this event?");

		if (r == true)
		{
			// AJAX
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitLock=1",
				success: function(data)
				{
					// Alert to success
					alert("The event was locked successfully!");
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
			if($("#costumeChoice option:selected").val() != "" && $("#costumeChoice option:selected").val() !== undefined && $("#costumeChoice option:selected").val() !== null)
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
				            	selectAdd();
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
				            selectAdd();
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