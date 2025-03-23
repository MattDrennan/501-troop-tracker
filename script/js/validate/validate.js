// Wait for the DOM to be ready
$(function()
{
    $("form[name='createEventForm']").validate(
    {
        rules:
        {
            eventName: "required",
            eventVenue: "required",
            location: "required",
            datepicker: "date",
            datepicker2: "date",
            numberOfAttend:
            {
                required: true,
                digits: true
            },
            requestedNumber:
            {
                required: true,
                digits: true
            },
            squadm:
            {
                required: true,
                range: [0, 5]
            },
            secure:
            {
                required: true,
                range: [0, 1]
            },
            blasters:
            {
                required: true,
                range: [0, 1]
            },
            lightsabers:
            {
                required: true,
                range: [0, 1]
            },
            parking:
            {
                required: true,
                range: [0, 1]
            },
            mobility:
            {
                required: true,
                range: [0, 1]
            },
            label:
            {
                required: true,
                range: [0, 11]
            },
            limitedEvent:
            {
                required: true,
                range: [0, 1]
            }
        },
        messages:
        {
            eventName: "Please enter the event name.",
            eventVenue: "Please enter the event venue.",
            location: "Please enter the location.",
            squadm: "Please select a squad.",
            datepicker: "Please enter a proper date/time.",
            datepicker2: "Please enter a proper date/time.",
            numberOfAttend: "Please enter a valid number.",
            requestedNumber: "Please enter a valid number.",
            secure: "Please enter a valid option.",
            blasters: "Please enter a valid option.",
            lightsabers: "Please enter a valid option.",
            parking: "Please enter a valid option.",
            mobility: "Please enter a valid option.",
            label: "Please enter a valid option.",
            limitedEvent: "Please enter a valid option."
        },
        submitHandler: function(form)
        {
            var r = confirm("Are you sure you want to create this event?");

            if (r == true)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&submitEvent=1",
                    success: function(data)
                    {
                        var json = JSON.parse(data);

                        // If success
                        if (json.success == "success")
                        {
                            // Clear Form
                            $("#eventName").val("");
                            $("#eventVenue").val("");
                            $("#location").val("");
                            $("#squadm").val("null");
                            $("#datepicker").val("");
                            $("#datepicker2").val("");
                            $("#website").val("");
                            $("#numberOfAttend").val("");
                            $("#requestedNumber").val("");
                            $("#requestedCharacter").val("");
                            $("#secure").val("null");
                            $("#blasters").val("null");
                            $("#lightsabers").val("null");
                            $("#parking").val("null");
                            $("#mobility").val("null");
                            $("#amenities").val("");
                            $("#comments").val("");
                            $("#label").val("null");
                            $("#limitedEvent").val("0");
                            $("#limit501st").val("500");
                            $("[name=smileyarea]").html("");
                            $("#postToBoards").val(1);

                            // Loop through clubs
                            for(var i = 0; i <= (clubArray.length - 1); i++)
                            {
                                $("#" + clubArray[i]).val(500);
                            }

                            $("#referred").val("");
							$("#poc").val("");
                            $("#options").show();

                            // Remove all shift boxes
                            $("div[name*='pair']").each(function()
                            {
                                $(this).remove();
                            });
							
							// Set event link
							$("#create_event_area").html('<p><a href="index.php?event=' + json.eventid + '" class="button">View Posted Event</a>');
                        }

                        // Alert to success
                        alert(json.data);
                    }
                });
            }
        }
    });

    $("form[name='editUserForm']").validate(
    {
        rules:
        {
            user: "required",
            forumid: "required",
            squad:
            {
                required: true
            },
            permissions:
            {
                required: true,
                range: [0, 3]
            },
            p501:
            {
                required: true,
                range: [0, 4]
            },
            tkid:
            {
                required: true,
                digits: true
            }
        },
        messages:
        {
            user: "Please enter a name.",
            squad: "Please enter a squad.",
            permissions: "Please enter permissions.",
            tkid: "Please enter a TKID.",
            forumid: "Please enter a forum ID."
        },
        submitHandler: function(form)
        {
            var r = confirm("Are you sure you want to edit this trooper?");

            if (r == true)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&submitUserEdit=1",
                    success: function(data)
                    {
                        var json = JSON.parse(data);

                        // Hide interface
                        $("#submitEditUser").val("Edit");
                        $("#editUserInfo").hide();

                        // Fix text when changing the title
                        $("#userID option:selected").text(json.newname);
                        $("#userID").select2();

                        // Alert to success
                        alert(json.data);
                    }
                });
            }
        }
    });

    // Command Staff Roster Add
    $("form[name='editEventForm']").validate(
    {
        rules:
        {
            eventName: "required",
            eventVenue: "required",
            location: "required",
            datepicker: "date",
            datepicker2: "date",
            numberOfAttend:
            {
                required: true,
                digits: true
            },
            requestedNumber:
            {
                required: true,
                digits: true
            },
            squadm:
            {
                required: true,
                range: [0, 5]
            },
            secure:
            {
                required: true,
                range: [0, 1]
            },
            blasters:
            {
                required: true,
                range: [0, 1]
            },
            lightsabers:
            {
                required: true,
                range: [0, 1]
            },
            parking:
            {
                required: true,
                range: [0, 1]
            },
            mobility:
            {
                required: true,
                range: [0, 1]
            },
            label:
            {
                required: true,
                range: [0, 11]
            },
            limitedEvent:
            {
                required: true,
                range: [0, 1]
            }
        },
        messages:
        {
            eventName: "Please enter the event name.",
            eventVenue: "Please enter the event venue.",
            location: "Please enter the location.",
            squadm: "Please select a squad.",
            datepicker: "Please enter a proper date/time.",
            datepicker2: "Please enter a proper date/time.",
            numberOfAttend: "Please enter a valid number.",
            requestedNumber: "Please enter a valid number.",
            secure: "Please enter a valid option.",
            blasters: "Please enter a valid option.",
            lightsabers: "Please enter a valid option.",
            parking: "Please enter a valid option.",
            mobility: "Please enter a valid option.",
            label: "Please enter a valid option.",
            limitedEvent: "Please enter a valid option."
        },
        submitHandler: function(form)
        {
            var r = confirm("Are you sure you want to edit this event?");

            if (r == true)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&submitEventEdit=1",
                    success: function(data)
                    {
                        var json = JSON.parse(data);

                        // Hide interface
                        $("#submitEdit").val("Edit");
                        $("#options").show();
                        $("#editEventInfo").hide();
                        $("#limitChangeArea").hide();
                        $("#limitChange").text("Change Limits");
                        $("[name=smileyarea]").html("");

                        // Populate select with events
                        $("#editEvents select").html(json.returnData);

                        if(json.returnData == "") {
                             $("#editEvents").html("No events to display.");
                        }

                        $("#eventId").select2();

                        // Refresh Select 2
                        selectAdd();

                        // Alert to success
                        alert(json.data);
                    }
                });
            }
        }
    });

    $("form[name='loginForm']").validate(
    {
        rules:
        {
            tkid: "required",
            password: "required"
        },
        messages:
        {
            tkid: "Please enter your Garrison Board username.",
            password: "Please enter your Garrison Board password."
        },
        submitHandler: function(form)
        {
            form.submit();
        }
    });

    $("form[name='changePhoneForm']").validate(
    {
        rules:
        {
            phone:
            {
                required: false,
                phoneUS: true,
            },
        },
        messages:
        {
            phone: "Please enter a valid phone number.",
        },
        submitHandler: function(form)
        {
            var form = $("#changePhoneForm");
            var url = form.attr("action");

            $.ajax(
            {
                type: "POST",
                url: url,
                data: form.serialize() + "&phoneButton=1",
                success: function(data)
                {
                    alert(data);
                }
            });
        }
    });

    $("form[name='changeNameForm']").validate(
    {
        rules:
        {
            name: "required"
        },
        messages:
        {
            name: "Please enter a name."
        },
        submitHandler: function(form)
        {
            var form = $("#changeNameForm");
            var url = form.attr("action");

            $.ajax(
            {
                type: "POST",
                url: url,
                data: form.serialize() + "&nameButton=1",
                success: function(data)
                {
                    alert(data);
                }
            });
        }
    });

    $("form[name='requestAccessForm']").validate(
    {
        rules:
        {
            tkid:
            {
				required: function()
				{
					// Handler account - check if TKID is required
					if($("[name=accountType]:checked").val() == 1 && $("[name=squad]").val() <= squadCount) {
						return true;
					} else {
						return false;
					}
				},
                digits: true,
                maxlength: 11,
				min: function() {
					// Handler account - prevent TKID 0 for regular accounts
					if($("[name=accountType]:checked").val() == 1 && $("[name=squad]").val() <= squadCount) {
						return 1;
					} else {
						return 0;
					}
				}
            },
            forumid: "required",
            forumpassword: "required",
            name: "required",
            phone:
            {
                required: false,
                phoneUS: true,
            },
        },
        messages:
        {
            tkid: "Please enter your TKID and make sure it is no more than eleven (11) characters.",
            forumid: "Please enter your Garrison 501st Forum Username.",
            forumpassword: "Please enter your password.",
            name: "Please enter your name.",
			rebelforum: "Please enter your Rebel Legion forum username.",
			mandoid: "Please enter your Mando Mercs CAT #.",
            phone: "Please enter a valid phone number.",
        },
        submitHandler: function(form)
        {
            $.ajax(
            {
                type: "POST",
                url: form.action,
                data: $(form).serialize() + "&submitRequest=1",
                success: function(data)
                {
                    // Hide Form
                    $("#requestAccessFormArea").hide();

                    // Show data
                    $("#requestAccessFormArea2").html(data);
                }
            });
        }
    });

    $("body").on("click", "[name=submitSignUp]", function(e) {
        $("form[name='signupForm']").validate(
        {
            rules:
            {
                costume:
                {
                    required: true,
                    digits: true
                },
                status:
                {
                    required: true,
                    range: [0, 5]
                }
            },
            messages:
            {
                costume: "Please choose a costume.",
                status: "Please choose your status."
            },
            submitHandler: function(form)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&submitSignUp=1",
                    success: function(data)
                    {
                        $("#signeduparea").show();
                        $("#signuparea").hide();
                        $("#addfriend").show();
                        selectAdd();
                    }
                });
            }
        });

        $("form[name='signupForm2']").validate(
        {
            rules:
            {
                costume:
                {
                    required: true,
                    digits: true
                },
                status:
                {
                    required: true,
                    range: [0, 5]
                }
            },
            messages:
            {
                costume: "Please choose a costume.",
                status: "Please choose your status."
            },
            submitHandler: function(form)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&submitSignUp=1",
                    success: function(data)
                    {
                        // Get JSON Data
                        var json = JSON.parse(data);

                        if (json.success == "failed")
                        {
                            alert(json.data);
                        }
                        else if(json.success == "check_linked_fail")
                        {
                            alert(json.success_message);
                        }
                        else
                        {
                            // Put data in html
                            $("#signuparea1").html(json.data);
                            selectAdd();

                            // Do the rest...
                            $("#addfriend").show();
                            selectAdd();
                            $("#signuparea").hide();
                            $("#rosterTableNoData").hide();

                            // Update troopers remaining on the page
                            $("div[name=troopersRemainingDisplay]").html(json.troopersRemaining);
                            $("div[name=troopersRemainingDisplayAdmin]").html(json.troopersRemainingAdmin);
                        }
                    }
                });
            }
        });

        $("form[name='signupForm3']").validate(
        {
            rules:
            {
                trooperSelect:
                {
                    required: true,
                    digits: true
                },
                costume:
                {
                    required: true,
                    digits: true
                },
                status:
                {
                    required: true,
                    range: [0, 5]
                }
            },
            messages:
            {
                costume: "Please choose a costume.",
                status: "Please choose your status."
            },
            submitHandler: function(form)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&submitSignUp=1&addfriend=1",
                    success: function(data)
                    {
                        // Get JSON Data
                        var json = JSON.parse(data);

                        if (json.success == "success")
                        {
                            // Put data in html
                            $("#signuparea1").html(json.data);
                            $("#signuparea1").show();
                            $("#signuparea").hide();

                            // Set search boxes
                            selectAdd();

    						// Check if placeholder account
    						if($("select[name=trooperSelect]").find("option:selected").val() != placeholder)
    						{
    							// Remove friend from list
    							$("select[name=trooperSelect]").find("option:selected").remove();
    						}

                            // Reset fields
    						$("#trooperSelect").val($("#trooperSelect option:first").val()).trigger("change");
                            $("#costume").val("null").trigger("change");
                            $("select[name=status]").val("null");
                            $("#backupcostume").val(0).trigger("change");

                            // Update troopers remaining on the page
                            $("div[name=troopersRemainingDisplay]").html(json.troopersRemaining);
                            $("div[name=troopersRemainingDisplayAdmin]").html(json.troopersRemainingAdmin);
                        }
    					else
    					{
    						alert(json.success_message);
    					}
    					
    					// Check number of friends
    					if(json.numFriends <= 0)
    					{
    						$("#addfriend").hide();
    					}
                    }
                });
            }
        });
    });

    $("form[name='commentForm']").validate(
    {
        rules:
        {
            comment:
			{
				required: true,
				noSpace: true
			},
            important:
            {
                required: true,
                range: [0, 1]
            }
        },
        messages:
        {
            comment: "Please enter a comment.",
            important: "Please pick an importance."
        },
        submitHandler: function(form)
        {
			// Disable button
			$("input[name=submitComment]").prop("disabled", true);
			
			// Send data
            $.ajax(
            {
                type: "POST",
                url: "process.php?do=postcomment",
                data: $(form).serialize() + "&submitComment=1",
                success: function(data)
                {
                    var json = JSON.parse(data);

                    // Update HTML
                    $("#commentArea").html(json.data);

                    // Return vars to default
                    $("#comment").val("");
                    $("#important").val("0");
                    $("[name=smileyarea]").html("");
					
					// Re-enable button
					$("input[name=submitComment]").prop("disabled", false);
					
					// Alert trooper
					alert("Message posted!");
                }
            });
        }
    });

    $("form[name='registerForm']").validate(
    {
        rules:
        {
            tkid:
            {
                required: true
            },
            forum_id:
            {
                required: true
            },
            password:
            {
                required: true
            },
            tkid2:
            {
                digits: true,
                required: function()
                {
                    return $('#squad').val() == 6;
                }
            }
        },
        messages:
        {
            tkid: "Please enter your TKID.",
            forum_id: "Please enter your Garrison Board username.",
            password: "Please enter your Garrison Board password.",
            tkid2: "Please enter an ID."
        },
        submitHandler: function(form)
        {
            form.submit();
        }
    });

    $("form[name='editcharityForm']").validate(
    {
        rules:
        {
            charityDirectFunds:
            {
                required: false,
                digits: true
            },
            charityIndirectFunds:
            {
                required: false,
                digits: true
            },
            charityAddHours:
            {
                required: false,
                digits: true
            }
        },
        messages:
        {
            charityDirectFunds: "Please enter a valid number.",
            charityIndirectFunds: "Please enter a valid number.",
            charityAddHours: "Please enter a valid number."
        },
        submitHandler: function(form)
        {
            $.ajax({
                type: "POST",
                url: form.action,
                data: $('#editcharityForm').serialize() + "&eventId=" + $("#eventId").val() + "&submitCharity=1",
                success: function(data)
                {
                    // Hide charity form
                    $("#charityAmount").hide();
                    $("#submitCharity").val("Charity");
            
                    // Send success message
                    alert("Charity updated!");
                }
            });
        }
    });

    $("body").on("click", "#troopRosterFormAdd", function(e)
    {
        $("form[name='troopRosterFormAdd']").validate(
        {
            rules:
            {
                trooperSelect: "required",
                costume:
                {
                    required: true,
                    range: [0, 9999]
                },
                status:
                {
                    required: true,
                    range: [0, 4]
                }
            },
            messages:
            {
                costume: "Please select a costume.",
                status: "Please select a status."
            },
            submitHandler: function(form)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&troopRosterFormAdd=1",
                    success: function(data)
                    {
						var json = JSON.parse(data);
						
                        // Set variables
                        var troopername = $("#trooperSelect option[value='" + $("#trooperSelect").val() + "']").attr("troopername");
                        var tkid = $("#trooperSelect option[value='" + $("#trooperSelect").val() + "']").attr("tkid");
						var trooperid = $("#trooperSelect").val();
						var signid = json.signid;
						
                        // Send alert
                        alert("Trooper added to roster!");

                        // Show table if form is hidden
                        if ($("#troopRosterForm").is(":hidden"))
                        {
                            $("#troopRosterForm").show();
                        }

                        // Costume One
                        var string1 = '<select name="costumeValSelect' + trooperid + '" signid="' + signid + '">';

                        for (var i = 0; i <= jArray1.length - 1; i++)
                        {
                            if ($("#costume").val() == jArray2[i])
                            {
                                string1 += '<option value="' + jArray2[i] + '" SELECTED>' + jArray1[i] + '</option>';
                            }
                            else
                            {
                                string1 += '<option value="' + jArray2[i] + '">' + jArray1[i] + '</option>';
                            }
                        }

                        string1 += '</select>';

                        // Costume Two
                        var string2 = '<select name="costumeVal' + trooperid + '" signid="' + signid + '">';

                        // Checks to see if a option selected
                        var string2Set = false;

                        for (var i = 0; i <= jArray1.length - 1; i++)
                        {
                            if ($("#costumebackup").val() == jArray2[i])
                            {
                                string2 += '<option value="' + jArray2[i] + '" SELECTED>' + jArray1[i] + '</option>';
                                string2Set = true;
                            }
                            else
                            {
                                string2 += '<option value="' + jArray2[i] + '">' + jArray1[i] + '</option>';
                            }
                        }

                        if (!string2Set)
                        {
                            string2 += '<option value="0" SELECTED>Please select a costume...</option>';
                        }

                        string2 += '</select>';
						
						var string3 = '';
						
						// Make sure not placeholder
						if(trooperid == placeholder)
						{
							string3 = '<input type="text" name="placeholdertext" signid="' + signid + '" value="" />';
						}

                        // Show form / table
                        $('#rosterTable').append('<tr name="roster_' + trooperid + '" signid="' + signid + '"><td><input type="hidden" name="tkid" signid="' + signid + '" value = "' + tkid + '" /><input type="hidden" name="troopername" signid="' + signid + '" value = "' + troopername + '" /><input type="hidden" name="eventId" signid="' + signid + '" value = "' + $("#troopid").val() + '" /><input type="radio" name="trooperSelectEdit" signid="' + signid + '" value="' + trooperid + '" /></td><td><div name="tknumber1' + trooperid + '" signid="' + signid + '"><a href="index.php?profile=' + trooperid + '" target="_blank">' + tkid + ' - ' + troopername + '</a></div>' + string3 + '</td><td><div name="costume1' + trooperid + '" signid="' + signid + '">' + $("#costume option:selected").text() + '</div><div name="costume2' + trooperid + '" signid="' + signid + '" style="display:none;">' + string1 + '</div></td><td><div name="backup1' + trooperid + '" signid="' + signid + '">' + ifEmpty($("#costumebackup option:selected").text()) + '</div><div name="backup2' + trooperid + '" signid="' + signid + '" style="display:none;">' + string2 + '</div></td><td><div name="status1' + trooperid + '" signid="' + signid + '">' + getStatus($("#status").val()) + '</div><div name="status2' + trooperid + '" signid="' + signid + '" style="display:none;"><select name="statusVal' + trooperid + '" signid="' + signid + '"><option value="0">Going</option><option value="1">Stand By</option><option value="2">Tentative</option><option value="3">Attended</option><option value="4">Canceled</option><option value="5">Pending</option><option value="6">Not Picked</option><option value="7">No Show</option></select></div></td></tr>');

                        // Select Options
                        $("select[name=statusVal" + trooperid + "][signid=" + signid + "]").val($("#status").val());

						// Make sure not placeholder
						if(trooperid != placeholder)
						{
							// Remove trooper
							$("#trooperSelect option[value='" + trooperid + "']").remove();
						}

                        // Reset
                        $("#costume").val("null").trigger('change');
                        $("#costumebackup").val("0").trigger('change');
                        $("#status").val("null");
                        $("input[id='trooperSearch']").val("").trigger("input");
                    }
                });
            }
        });
    });
});