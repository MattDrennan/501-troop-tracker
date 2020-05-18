$(document).ready(function()
{
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

					var json = JSON.parse(data);
					$("#eventIdE").val(json.id);
					$("#eventName").val(json.name);
					$("#eventVenue").val(json.venue);
					$("#location").val(json.location);
					$("#datepicker").val(json.dateStart);
					$("#datepicker2").val(json.dateEnd);
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
					$("#referred").val(json.referred);
				}
				else
				{
					$("#submitEdit").val("Edit");
					$("#editEventInfo").hide();
				}
			}
		});
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

					$("#roster_" + $("#trooperSelectEdit").val()).remove();

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

				// Show Inputs for edit
				$("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#status2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).show();

				// Hide static values
				$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).hide();

				// Put current text in it
				$("#costumeVal" + $("input[name=trooperSelectEdit]:checked").val()).val($("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).text());
				$("#reasonVal" + $("input[name=trooperSelectEdit]:checked").val()).val($("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).text());
				$("#attendcostumeVal" + $("input[name=trooperSelectEdit]:checked").val()).val($("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).text());
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

							// Set values
							$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val());
							$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#status2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val());
							$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val());

							// Show static values
							$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).show();

							// Alert to success
					  		alert("Roster updated!");
						}
					});
				}
			}
		}
	});

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

	$("#submitFinish").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to finish this event?");

		if (r == true)
		{
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
		eventTitle[0] = eventTitle[0].slice(1);
		$("#eventName").val(eventTitle);

		// Event Venue
		var eventVenue = textArray[2].split("Venue address");
		$("#eventVenue").val(eventVenue[0].slice(1));

		// Event Location
		var eventLocation = textArray[3].split("Event Start");
		eventLocation[0] = eventLocation[0].replace(/[\r\n]+/g, " ");
		$("#location").val(eventLocation[0].slice(1));

		// Event Time ISSUE WITH : in time need to fix
		var eventTime = textArray[4].slice(1) + ":" + textArray[5].split("Event End")[0];
		$("#datepicker").val(eventTime);

		// Event Time 2
		var eventTime2 = textArray[6].slice(1) + ":" + textArray[7].split("Event Website")[0];
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