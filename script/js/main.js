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
	$("#userIDTitle").select2();
	$("#titleIDAssign").select2();
	$("#titleIDEdit").select2();
	$("#titleID").select2();
	$("select[name^=eventId]").select2();
	$("select[name^=userID]").select2();
	$("select[name^=modifysignupFormCostume]").select2();
	$("select[name^=modiftybackupcostumeForm]").select2();
	$("#costume").select2();
	$("#backupcostume").select2();
	$("#costumebackup").select2();
	$("select[name^=trooperSelect]").select2();
	$("select[name^=costumeChoice]").select2();
	$("select[name^=trooperSelect]").select2();
	$("select[name^=modifysignupFormCostume2]").select2();
	$("select[name^=modiftybackupcostumeForm2]").select2();
	$("select[name^=costumeValSelect]").select2();
	$("select[name^=costumeVal]").select2();
}

$(document).ready(function()
{
	// Add select2 to DOM
	selectAdd();

    // Add rules to clubs - clubs
    $('.clubs').each(function()
    {
        $(this).rules('add',
        {
            required: true,
            range: [0, 4]
        })
    });

    // Add rules to clubs - limits
    $('.limitClass').each(function()
    {
        $(this).rules('add',
        {
            required: false,
            digits: true
        })
    });

	// Before / After Ajax
	$(document).ajaxStart(function ()
	{
		$.LoadingOverlay("show");
	})
	$(document).ajaxStop(function ()
	{
		$.LoadingOverlay("hide");
	});

	// Image Upload - Change Upload Type
	$("body").on("click", "#trooperInformationButton", function(e)
	{
		if($(this).text() == "Show Trooper Information")
		{
			$("[name=trooperInformation]").show();
			$(this).text("Hide Trooper Information");
		}
		else
		{
			$("[name=trooperInformation]").hide();
			$(this).text("Show Trooper Information");
		}
	})
	
	// Master Roster - Add Trooper
	$("body").on("click", "#addTrooperMaster", function(e)
	{
		e.preventDefault();
		
		var r = confirm("Are you sure you want to add this trooper to the roster?");
		
		// If confirmed
		if(r == true)
		{
			// Send data
			var form = $("#addMasterRosterForm");
			
			// Get trooper values
			var trooperid = $("#userID option:selected").val();
			var troopername = $("#userID option:selected").attr("troopername");
			var tkid = $("#userID option:selected").attr("tkid");

			$.ajax({
				type: "POST",
				url: "process.php?do=addmasterroster",
				data: form.serialize(),
				success: function(data)
				{
					// Get JSON
					var json = JSON.parse(data);
					
					// Remove option
					$("#userID option:selected").remove();
					
					// Add to table
					$("#masterRosterTable").append('<tr name="row_' + trooperid + '"><td><a href="index.php?profile=' + trooperid + '" target="_blank">' + troopername + '</a></td><td>' + tkid + '</td><td><select name="changepermission" trooperid="' + trooperid + '"><option value="0">Not A Member</option><option value="1" SELECTED>Regular Member</option><option value="2">Reserve Member</option><option value="3">Retired Member</option><option value="4">Handler</option></select></td></tr>');
					
					// Alert user
					alert(json.data);
				}
			});
		}
	})
	
	// Roster - Update member status
	$("body").on("change", "[name=changepermission]", function(e)
	{
		// Get vars
		var trooperid = $(this).attr("trooperid");
		var permission = $(this).val();
		var club = $("#club").val();
		
		$.ajax({
			type: "POST",
			url: "process.php?do=changepermission",
			data: "trooperid=" + trooperid + "&permission=" + permission + "&club=" + club,
			success: function(data)
			{
				alert("Updated!");
			}
		});
	})

	// Create / Edit Event - Label changed
	$("body").on("change", "#label", function(e)
	{
		// If armor party selected
		if($(this).val() == 10)
		{
			// Set settings
			$("#secure").val(1);
			$("#blasters").val(1);
			$("#lightsabers").val(1);
			$("#parking").val(1);
			$("#mobility").val(1);
			$("#requestedCharacter").val("");
			$("#requestedNumber").val(0);
			$("#numberOfAttend").val(0);
			$("#website").val("");

			// Hide
			$("#options").hide();
		}
		else
		{
			// Show
			$("#options").show();		
		}
	})

	// Image Upload - Change Upload Type
	$("body").on("click", "a[name=jsonshow]", function(e)
	{
		// Is the box visible
		if($("[name=json" + $(this).attr("json") + "]").is(":visible"))
		{
			// Visible - hide
			$("[name=json" + $(this).attr("json") + "]").hide();
		}
		else
		{
			// Not visible - show
			$("[name=json" + $(this).attr("json") + "]").show();
		}
	})

	// Image Upload - Change Upload Type
	$("body").on("click", "#changeUpload", function(e)
	{
		if($("input[name=admin]").val() == 0)
		{
			$("#changeUpload").text("Change To: Regular Upload");
			$("input[name=admin]").val(1);
		}
		else
		{
			$("#changeUpload").text("Change To: Troop Instructional Image Upload");
			$("input[name=admin]").val(0);
		}
	})

	// Home Page - Search
	$("body").on("input", "#controlf", function(e)
	{
		$("#listview div").each(function(index)
		{
			if($(this).text().toLowerCase().includes($("#controlf").val().toLowerCase()))
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
	})

	// Change Limits - Shows / Hide Change Limits
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

	// Change Limits - Reset Default
	$("body").on("click", "#resetDefaultCount", function(e)
	{
		e.preventDefault();

		// Reset
		$("#era").val(4);
		$("#limit501st").val(500);
		$("#limitedEvent").val(0);

		// On index.php, clear all fields
		clearLimit();
	})

	// Event Page - Change Status
	$("body").on("click", "a[name=changestatus]", function(e)
	{
		e.preventDefault();
		
		// Load data from form
		var trooperid = $(this).attr("trooperid");
		var eventid = $("#troopidC").val();
		var signid = $(this).attr("signid");
		var buttonid = $(this).attr("buttonid");
		
		// Get AJAX data
		$.ajax({
			type: "POST",
			url: "process.php?do=changestatus",
			data: "trooperid=" + trooperid + "&eventid=" + eventid + "&signid=" + signid + "&buttonid=" + buttonid,
			success: function(data)
			{
				// Get JSON
				var json = JSON.parse(data);

				// Change buttons based on data
				$("div[name=changestatusarea][trooperid=" + trooperid + "][signid=" + signid + "]").html(json.message);

				// Change counts for admin
				$("div[name=troopersRemainingDisplay]").html(json.message2);
			}
		});
	})

	// Account Setup - Change Squad
	$("body").on("change", "#squad", function(e)
	{
		// If Rebel Legion
		if($(this).val() == 6)
		{
			$("#rebelid").show();
		}
		else
		{
			// If 501st
			$("#rebelid").hide();
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
			// Only hide if calendar view is not visible
			if($("#calendarview").is(":hidden"))
			{
				$("a[id=changeview]").hide();
			}
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
		$("#trooperSelect option, #userID option, #userIDAward option, #userIDTitle option").each(function(index)
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
	
	// Photo Management - Delete Photo
	$("a[name='deletephoto']").click(function(e)
	{
		e.preventDefault();
		
		var troopid = $(this).attr("troopid");
		
		var r = confirm("Are you sure you want to delete this photo?");
		
		// If confirmed
		if(r == true)
		{
			// Send data
			$.ajax({
				type: "POST",
				url: "process.php?do=deletephoto",
				data: { photoid: $(this).attr("photoid"), troopid: $(this).attr("troopid") },
				success: function(data)
				{
					// Get JSON
					var json = JSON.parse(data);
					
					// Alert user
					alert(json.data);
					
					// Redirect trooper
					window.location = "index.php?event=" + troopid;
				}
			});
		}
	})
	
	// Photo Management - Make Admin / Regular
	$("a[name='adminphoto']").click(function(e)
	{
		e.preventDefault();
		
		var r = confirm("Are you sure you want to change the status of this photo?");
		
		var elementS = $(this);
		
		// If confirmed
		if(r == true)
		{
			// Send data
			$.ajax({
				type: "POST",
				url: "process.php?do=adminphoto",
				data: { photoid: $(this).attr("photoid") },
				success: function(data)
				{
					// Get JSON
					var json = JSON.parse(data);
					
					// If success
					if(json.data == 1)
					{
						if(elementS.text() == "Make Troop Instruction Photo")
						{
							elementS.text("Make Regular Photo");
						}
						else if(elementS.text() == "Make Regular Photo")
						{
							elementS.text("Make Troop Instruction Photo");
						}
					}
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
					$("#phone").val(json.phone);
					$("#squad").val(json.squad);
					$("#permissions").val(json.permissions);

					// Special permissions

					// Reset
					$("[name=spTrooper]").prop("checked", false);
					$("[name=spCostume]").prop("checked", false);
					$("[name=spAward]").prop("checked", false);
					$("[name=spTrooper]").val(0);
					$("[name=spCostume]").val(0);
					$("[name=spAward]").val(0);

					// Set checkboxes
					if(json.spTrooper == 1) { $("[name=spTrooper]").prop("checked", true); $("[name=spTrooper]").val(1); }
					if(json.spCostume == 1) { $("[name=spCostume]").prop("checked", true); $("[name=spCostume]").val(1); }
					if(json.spAward == 1) { $("[name=spAward]").prop("checked", true); $("[name=spAward]").val(1); }

					// Loop through clubs
					for(var i = 0; i <= (clubArray.length - 1); i++)
					{
						$("#" + clubArray[i]).val(json[clubArray[i]]);
					}

					$("#tkid").val(json.tkid);
					$("#forumid").val(json.forumid);

					// Loop through clubs
					for(var i = 0; i <= (clubDB3Array.length - 1); i++)
					{
						$("#" + clubDB3Array[i]).val(json[clubDB3Array[i]]);
					}

					$("#supporter").val(json.supporter);

					// If a moderator, show special permissions
					if(json.permissions == 2)
					{
						$("[name=specialPermissions]").show();
					}
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

				// If roster is visible
				if($("#rosterInfo").is(":visible"))
				{
					// Hide Roster
					$("#submitRoster").val("Roster");
					$("#rosterInfo").html("");
					$("#rosterInfo").hide();
				}
				
				// If charity is visible
				if($("#charityAmount").is(":visible"))
				{
					// Hide Charity
					$("#charityAmount").hide();
					$("#submitCharity").val("Set Charity Amount");
				}

				// If edit event is hidden
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

					// Loop through clubs
					for(var i = 0; i <= (clubArray.length - 1); i++)
					{
						$("#" + clubArray[i]).val(json[clubArray[i]]);
					}

					$("#referred").val(json.referred);

					// Hide options if armor party
					if(json.label == 10)
					{
						// Hide
						$("#options").hide();
					}
					
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
		if($("#editEventInfo").is(":visible"))
		{
			$("#editEventInfo").hide();
			$("#submitEdit").val("Edit");
		}

		// Hide roster info
		if($("#rosterInfo").is(":visible"))
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
				if($("#editEventInfo").is(":visible"))
				{
					// Hide Edit Form
					$("#editEventInfo").hide();
					$("#submitEdit").val("Edit");
				}
				
				// If charity is visible
				if($("#charityAmount").is(":visible"))
				{
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
		$("#comment").val($("#comment").val() + "[quotec trooperid=" + $(this).attr("trooperid") + " name=" + $(this).attr("troopername") + " tkid=" + $(this).attr("tkid") + " commentid=" + id + "]" + $("table[name=comment_" + id + "] td[name=insideComment]").text() + "[/quotec]\n\n");
	});
	
	// When trooper quotes a comment
	$("body").on("click", "[id^=editComment]", function(e)
	{
		e.preventDefault();

		// Get ID of comment
		var id = $(this).attr("name");
		
		// Add comment to comment text area with HTML for display purposes
		$("table[name=comment_" + id + "] td[name=insideComment]").html('<textarea commentid="' + id + '">' + $("table[name=comment_" + id + "] td[name=insideComment]").text().replace('<br/>', '\n').replace('<br />', '\n') + '</textarea><br /><input type="submit" name="editCommentSubmit" commentid="' + id + '" value="Save" />');
	});
	
	// When trooper quotes a comment
	$("body").on("click", "[name=editCommentSubmit]", function(e)
	{
		e.preventDefault();

		// Get ID of comment
		var id = $(this).attr("commentid");
		
		// Get comment
		var comment = $("table[name=comment_" + id + "] td[name=insideComment] textarea").val().replace(/\n/g, '\n<br />')
		
		// Add text area to comment
		$("table[name=comment_" + id + "] td[name=insideComment]").html(comment);
		
		// Save comment
		$.ajax({
			type: "POST",
			url: "process.php?do=editcomment",
			data: "commentid=" + id + "&comment=" + comment,
			success: function(data)
			{
				// Alert to success
				alert("Comment updated!");
			}
		});
	});

	$("#changephoneLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").show();
		$("#changename").hide();
		$("#unsubscribe").hide();
		$("#changetheme").hide();
	});
	
	$("#changethemeLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").hide();
		$("#unsubscribe").hide();
		$("#changetheme").show();
	});

	$("#changenameLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").show();
		$("#unsubscribe").hide();
		$("#changetheme").hide();
	});


	$("#emailSettingLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").hide();
		$("#unsubscribe").show();
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

					// Loop through clubs
					for(var i = 0; i <= (clubDB3Array.length - 1); i++)
					{
						$("#" + clubDB3Array[i] + "Table").html("");
					}

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

					// Loop through clubs
					for(var i = 0; i <= (clubDB3Array.length - 1); i++)
					{
						$("#" + clubDB3Array[i] + "Table").html("");
					}
					
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

	// Edit Event - View Event
	$("#viewEvent").button().click(function(e)
	{
		e.preventDefault();
		
		// Open in new tab
		window.open('index.php?event=' + $("#eventId option:selected").val(), '_blank');
	})
	
	// Manage Trooper - View Profile
	$("#submitViewProfile").button().click(function(e)
	{
		e.preventDefault();
		
		// Open in new tab
		window.open('index.php?profile=' + $("#userID option:selected").val(), '_blank');
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

				// Show Inputs for edit
				$("[name=costume2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=backup2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=status2" + trooperid + "][signid=" + signid + "]").show();
				$("[name=dateAttending" + trooperid + "][signid=" + signid + "]" + "Edit").show();
				$("[name=dateAttended" + trooperid + "][signid=" + signid + "]" + "Edit").show();

				// Hide static values
				$("[name=costume1" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=backup1" + trooperid + "][signid=" + signid + "]").hide();
				$("[name=status1" + trooperid + "][signid=" + signid + "]").hide();
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
							$("[name=dateAttending" + trooperid + "][signid=" + signid + "]" + "Edit").hide();
							$("[name=dateAttended" + trooperid + "][signid=" + signid + "]" + "Edit").hide();
							
							// Set values
							$("[name=costume1" + trooperid + "][signid=" + signid + "]").html($("[name=costume2" + trooperid + "][signid=" + signid + "]").find("select :selected").text());
							$("[name=backup1" + trooperid + "][signid=" + signid + "]").html($("[name=backup2" + trooperid + "][signid=" + signid + "]").find("select :selected").text());
							$("[name=status1" + trooperid + "][signid=" + signid + "]").html($("[name=status2" + trooperid + "][signid=" + signid + "]").find("select :selected").text());

							// Show static values
							$("[name=costume1" + trooperid + "][signid=" + signid + "]").show();
							$("[name=backup1" + trooperid + "][signid=" + signid + "]").show();
							$("[name=status1" + trooperid + "][signid=" + signid + "]").show();
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
				var json = JSON.parse(data);

				// Go to else?
				var shouldElse = true;
				
				// If JSON did not fail
				if(json.success != "failed")
				{
					// Adjust options based on costume - 501
					if(($("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "] option:selected").attr("club") == 0 && (json.limit501st - json.limit501stTotal) > 0) && json.staus != 4)
					{
							// Empty select
							signupForm3.empty();

							// Refill
							signupForm3.append("<option value='0'>I'll be there</option> <option value='2'>Tentative</option> <option value='4'>Cancel</option>");

							// Set selected value
							signupForm3.val(json.status);

							// Set
							shouldElse = false;
					}

					// Loop through clubs
					for(var i = 0; i <= (clubArray.length - 1); i++)
					{
						// Adjust options based on costumes - other clubs
						if(($("select[name=modifysignupFormCostume][trooperid=" + trooperid + "][signid=" + signid + "] option:selected").attr("club") == 1 && (json[clubArray[i]] - json[clubArray[i] + "Total"]) > 0) && json.staus != 4)
						{
							// Empty select
							signupForm3.empty();

							// Refill
							signupForm3.append("<option value='0'>I'll be there</option> <option value='2'>Tentative</option> <option value='4'>Cancel</option>");

							// Set selected value
							signupForm3.val(json.status);

							// Set
							shouldElse = false;
						}
					}

					if(shouldElse)
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

		// Show options to prevent not showing when changing
		$("#options").show();
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

					// Loop through clubs
					for(var i = 0; i <= (clubDB3Array.length - 1); i++)
					{
						// Check
						if(json[clubDB3Array[i]] == 0 || json[clubDB3Array[i]] == "")
						{
							// If not member
							$("#" + clubDB3Array[i] + "Table").html('N/A');
						}
						else
						{
							// If Rebel Legion ** CUSTOM **
							if(clubDB3Array[i].includes("rebel"))
							{
								// If member
								$("#" + clubDB3Array[i] + "Table").html('<a href="https://www.forum.rebellegion.com/forum/profile.php?mode=viewprofile&u=' + json[clubDB3Array[i]] + '" target="_blank">' + json[clubDB3Array[i]] + '</a>');
							}
							else
							{
								// If member
								$("#" + clubDB3Array[i] + "Table").html('#' + json[clubDB3Array[i]]);
							}
						}
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

			// Loop through clubs
			for(var i = 0; i <= (clubDB3Array.length - 1); i++)
			{
				$("#" + clubDB3Array[i] + "Table").html("");
			}
			
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
					$("#costumeID").select2();
					
					// Change values for edit form
					$("#costumeIDEdit :selected").text($("#costumeNameEdit").val());
					$("#costumeIDEdit :selected").attr("costumeName", $("#costumeNameEdit").val());
					$("#costumeIDEdit :selected").attr("costumeEra", $("#costumeEraEdit").val());
					$("#costumeIDEdit :selected").attr("costumeClub", $("#costumeClubEdit").val());
					$("#costumeIDEdit").select2();

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

	/************ TITLES ********************/
	
	// Titles - select change
	$("body").on("change", "#userIDTitle", function(e)
	{
		// Get trooper ID
		var trooperid = $("#userIDTitle option:selected").val();
		var titleid = $("#titleIDAssign option:selected").val();
		
		$.ajax({
			type: "POST",
			url: "process.php?do=assigntitles",
			data: "gettitle=1&titleid=" + titleid + "&trooperid=" + trooperid,
			success: function(data)
			{
				// Get JSON
				var json = JSON.parse(data);
				
				// Check if has award
				if(json.hasTitle == 0)
				{
					$("#title").show();
					$("#titleRemove").hide();
				}
				else
				{
					$("#title").hide();
					$("#titleRemove").show();
				}
			}
		});
	});

	// Titles - Edit select change
	$("body").on("change", "#titleIDEdit", function(e)
	{
		// If click please select, hide list
		if($("#titleIDEdit :selected").val() == 0)
		{
			$("#editTitleList").hide();
		}
		else
		{
			$("#editTitleList").show();
		}

		$("#editTitle").val($("#titleIDEdit :selected").attr("title"));
		$("#editTitleImage").val($("#titleIDEdit :selected").attr("titleImage"));
	});

	// Titles - Finsih Edit
	$("body").on("click", "#submitEditTitle", function(e)
	{
		e.preventDefault();

		var form = $("#titleEdit");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to edit this title?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitEditTitle=1",
				success: function(data)
				{
					// Change values for delete form
					$("#titleID").children("option[value='" + $("#titleIDEdit :selected").val() + "']").text($("#editTitle").val());
					$("#titleID").select2();

					$("#titleIDAssign").children("option[value='" + $("#titleIDEdit :selected").val() + "']").text($("#editTitle").val());
					$("#titleIDAssign").select2();
					
					// Change values in edit form select
					$("#titleIDEdit :selected").text($("#editTitle").val());
					$("#titleIDEdit :selected").attr("title", $("#editTitle").val());
					$("#titleIDEdit :selected").attr("titleImage", $("#editTitleImage").val());
					$("#titleIDEdit").select2();

					$("#editTitleList").hide();

					$("#titleIDEdit").val(0);

					// Alert to success
			  		alert("The title was edited successfully!");
				}
			});
		}
	})

	// Titles - Add title
	$("body").on("click", "#submitTitleAdd", function(e)
	{
		e.preventDefault();

		var form = $("#addTitle");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to add this title?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitAddTitle=1",
				success: function(data)
				{
					var json = JSON.parse(data);

					// Clear form
					$("#titleName").val("");
					$("#titleImage").val("");

					// Alert to success
			  		alert(json[0].message);
					
					// Populate result
					$("#titlearea").html(json[0].result);
					$("#assignarea").html(json[0].result2);
					selectAdd();
				}
			});
		}
	})
	

	// Titles - Give title
	$("body").on("click", "#title", function(e)
	{
		e.preventDefault();

		var form = $("#titleUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to give this title to this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitTitle=1",
				success: function(data)
				{
					var json = JSON.parse(data);
					
					// Show / Hide
					$("#title").hide();
					$("#titleRemove").show();

					// Alert to success
			  		alert(json[0].message);
				}
			});
		}
	})
	
	// Titles - Remove title
	$("body").on("click", "#titleRemove", function(e)
	{
		e.preventDefault();

		var form = $("#titleUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to remove this title from this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&removeTitle=1",
				success: function(data)
				{
					var json = JSON.parse(data);
					
					// Show / Hide
					$("#title").show();
					$("#titleRemove").hide();

					// Alert to success
			  		alert(json[0].message);
				}
			});
		}
	})

	// Titles - Delete Title
	$("body").on("click", "#submitDeleteTitle", function(e)
	{
		e.preventDefault();

		var form = $("#titleUserDelete");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this title?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDeleteTitle=1",
				success: function(data)
				{
					// Delete from title list
					$("#titleIDEdit").children("option[value='" + $("#titleID :selected").val() + "']").remove();

					// Delete from title assign list
					$("#titleIDAssign").children("option[value='" + $("#titleID :selected").val() + "']").remove();
					
					// Clear
					$("#titleID").find("option:selected").remove();

					// Clear edit area
					$("#editTitleList").hide();

					// Alert to success
			  		alert("The title was deleted successfully!");

			  		// Show message if empty
			  		if($("#titleID option").length <= 0)
			  		{
			  			$("#titleUserDelete").html("No title to display.");
			  		}
					
			  		// Show message if empty - edit
			  		if($("#titleIDEdit option").length <= 1)
			  		{
			  			$("#titleEdit").html("No title to display.");
			  		}
					
			  		// Show message if empty - assign
			  		if($("#titleIDAssign option").length <= 0)
			  		{
			  			$("#assignarea").html("No title to display.");
			  		}
				}
			});

	  		// Show message if empty
	  		if($("#titleID option").length <= 0)
	  		{
	  			$("#titleUserDelete").html("No title to display.");
	  		}
		}
	})
	
	/************ AWARD ********************/
	
	// Awards - select change
	$("body").on("change", "#userIDAward", function(e)
	{
		// Get trooper ID
		var trooperid = $("#userIDAward option:selected").val();
		var awardid = $("#awardIDAssign option:selected").val();
		
		$.ajax({
			type: "POST",
			url: "process.php?do=assignawards",
			data: "getaward=1&awardid=" + awardid + "&trooperid=" + trooperid,
			success: function(data)
			{
				// Get JSON
				var json = JSON.parse(data);
				
				// Check if has award
				if(json.hasAward == 0)
				{
					$("#award").show();
					$("#awardRemove").hide();
				}
				else
				{
					$("#award").hide();
					$("#awardRemove").show();
				}
			}
		});
	});

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
					$("#awardID").select2();

					$("#awardIDAssign").children("option[value='" + $("#awardIDEdit :selected").val() + "']").text($("#editAwardTitle").val());
					$("#awardIDAssign").select2();
					
					// Change values in edit form select
					$("#awardIDEdit :selected").text($("#editAwardTitle").val());
					$("#awardIDEdit :selected").attr("awardTitle", $("#editAwardTitle").val());
					$("#awardIDEdit :selected").attr("awardImage", $("#editAwardImage").val());
					$("#awardIDEdit").select2();

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
					
					// Populate result
					$("#awardarea").html(json[0].result);
					$("#assignarea").html(json[0].result2);
					selectAdd();
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
					
					// Hide / Show
					$("#award").hide();
					$("#awardRemove").show();

					// Alert to success
			  		alert(json[0].message);
				}
			});
		}
	})
	
	// Awards - Remove award
	$("body").on("click", "#awardRemove", function(e)
	{
		e.preventDefault();

		var form = $("#awardUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to remove this award from this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&removeAward=1",
				success: function(data)
				{
					var json = JSON.parse(data);
					
					// Show / Hide
					$("#award").show();
					$("#awardRemove").hide();

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

					// Delete from title assign list
					$("#awardIDAssign").children("option[value='" + $("#awardID :selected").val() + "']").remove();
					
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

		// Convert to array (by new line)
		var textArray = text.split(/\r?\n/);

		// Set up error message
		var error = "";

		// Loop through each line
		textArray.forEach(function(line, i)
		{
			// Event title
			if(line.includes("Event Name:"))
			{
				$("#eventName").val(line.split("Event Name:")[1].trim());
			}

			// Event venue
			if(line.includes("Venue:"))
			{
				$("#eventVenue").val(line.split("Venue:")[1].trim());
			}

			// Event Location
			if(line.includes("Venue address:"))
			{
				// Set up
				var address = "";
				
				// Get first line
				address = line.split("Venue address:")[1].trim();

				// Loop for multi-line
				for(j = i + 1; j <= textArray.length; j++)
				{
					if(!textArray[j].includes("Event Start:"))
					{
						// Add to comments
						address += " " + textArray[j];
					}
					else
					{
						// End
						break;
					}
				}

				// Set
				$("#location").val(address);
			}

			// Date Start
			if(line.includes("Event Start:"))
			{
				$("#datepicker").val(moment(new Date(line.split("Event Start:")[1].trim())).format('MM/DD/YYYY HH:mm'));

				// Check if date is invalid
				if($("#datepicker").val() == "Invalid date")
				{
					// Make date fields blank
					$("#datepicker").val("");

					// Add to message
					error += "-Date Start is invalid\n";
				}
			}

			// Date Start
			if(line.includes("Event End:"))
			{
				$("#datepicker2").val(moment(new Date(line.split("Event End:")[1].trim())).format('MM/DD/YYYY HH:mm'));

				// Check if date is invalid
				if($("#datepicker2").val() == "Invalid date")
				{
					// Make date fields blank
					$("#datepicker2").val("");

					// Add to message
					error += "-Date End is invalid\n";
				}
			}

			// Website
			if(line.includes("Event Website:"))
			{
				$("#website").val(line.split("Event Website:")[1].trim());
			}

			// Expected number of attendees
			if(line.includes("Expected number of attendees:"))
			{
				$("#numberOfAttend").val(line.split("Expected number of attendees:")[1].trim());

				// Check if invalid
				if(isNaN(parseInt($("#numberOfAttend").val())))
				{
					// Make date fields blank
					$("#numberOfAttend").val("");

					// Add to message
					error += "-Number of attendees is not a number\n";
				}
			}

			// Requested number of characters:
			if(line.includes("Requested number of characters:"))
			{
				$("#requestedNumber").val(line.split("Requested number of characters:")[1].trim());

				// Check if invalid
				if(isNaN(parseInt($("#requestedNumber").val())))
				{
					// Make date fields blank
					$("#requestedNumber").val("");

					// Add to message
					error += "-Requested number of characters is not a number\n";
				}
			}

			// Requested number of characters:
			if(line.includes("Requested character types:"))
			{
				$("#requestedCharacter").val(line.split("Requested character types:")[1].trim());
			}

			// Secure Changing Area?
			if(line.includes("Secure changing/staging area"))
			{
				if(line.includes("No"))
				{
					$("#secure").val(0);
				}
				else
				{
					$("#secure").val(1);
				}
			}

			// Can Troopers Carry Blasters?
			if(line.includes("Can troopers carry blasters"))
			{
				if(line.includes("No"))
				{
					$("#blasters").val(0);
				}
				else
				{
					$("#blasters").val(1);
				}
			}

			// Parking
			if(line.includes("Is parking available"))
			{
				if(line.includes("No"))
				{
					$("#parking").val(0);
				}
				else
				{
					$("#parking").val(1);
				}
			}

			// Parking
			if(line.includes("Can troopers carry/bring props like lightsabers and staffs"))
			{
				if(line.includes("No"))
				{
					$("#lightsabers").val(0);
				}
				else
				{
					$("#lightsabers").val(1);
				}
			}

			// Mobility
			if(line.includes("Is venue accessible to those with limited mobility"))
			{
				if(line.includes("No"))
				{
					$("#mobility").val(0);
				}
				else
				{
					$("#mobility").val(1);
				}
			}

			// Amenities
			if(line.includes("Amenities available at venue:"))
			{
				$("#amenities").val(line.split("Amenities available at venue:")[1].trim());
			}

			// Comments
			if(line.includes("Comments:"))
			{
				// Set up
				var comment = "";

				// Get first line
				comment = line.split("Comments:")[1].trim();

				// Loop for multi-line
				for(j = i + 1; j <= textArray.length; j++)
				{
					if(!textArray[j].includes("Referred by:"))
					{
						// Add to comments
						comment += textArray[j];
					}
					else
					{
						// End
						break;
					}
				}

				// Set
				$("#comments").val(comment);
			}

			// Referred By
			if(line.includes("Referred by:"))
			{
				$("#referred").val(line.split("Referred by:")[1].trim());
			}
		});

		// If error
		if(error != "")
		{
			// Add to error
			error += "\nPlease be sure to verify all information is accurate.";

			// Show message
			alert(error);
		}
	})
});