// Wait for the DOM to be ready
$(function()
{	
    // Initialize form validation on the registration form.
    // It has the name attribute "registration"
    $("form[name='createEventForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
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
                range: [0, 10]
            },
            limitedEvent:
            {
                required: true,
                range: [0, 1]
            },
            limit501st:
            {
                required: false,
                digits: true
            },
            limitDroid:
            {
                required: false,
                digits: true
            },
            limitOther:
            {
                required: false,
                digits: true
            },
            limitRebels:
            {
                required: false,
                digits: true
            },
            limitMando:
            {
                required: false,
                digits: true
            }
        },
        // Specify validation error messages
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
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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
                            $("#limitedEvent").val("null");
                            $("#era").val("4");
                            $("#limit501st").val("500");
                            $("#limitRebels").val("500");
                            $("#limitMando").val("500");
                            $("#limitDroid").val("500");
							$("#limitOther").val("500");
                            $("#referred").val("");
                            $("#options").show();

                            // Remove all shift boxes
                            $("div[name*='pair']").each(function()
                            {
                                $(this).remove();
                            });
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
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            user: "required",
            forumid: "required",
            email:
            {
                //required: true,
                email: true
            },
            squad:
            {
                required: true,
                range: [1, 9]
            },
            permissions:
            {
                required: true,
                range: [0, 2]
            },
            p501:
            {
                required: true,
                range: [0, 4]
            },
            pRebel:
            {
                required: true,
                range: [0, 4]
            },
            pDroid:
            {
                required: true,
                range: [0, 4]
            },
            pMando:
            {
                required: true,
                range: [0, 4]
            },
            pOther:
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
        // Specify validation error messages
        messages:
        {
            user: "Please enter a name.",
            squad: "Please enter a squad.",
            permissions: "Please enter permissions.",
            tkid: "Please enter a TKID.",
            forumid: "Please enter a forum ID."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form)
        {
            var r = confirm("Are you sure you want to edit this user?");

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

    $("form[name='editEventForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
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
                range: [0, 10]
            },
            limitedEvent:
            {
                required: true,
                range: [0, 1]
            },
            limit501st:
            {
                required: false,
                digits: true
            },
            limitDroid:
            {
                required: false,
                digits: true
            },
            limitOther:
            {
                required: false,
                digits: true
            },
            limitRebels:
            {
                required: false,
                digits: true
            },
            limitMando:
            {
                required: false,
                digits: true
            }
        },
        // Specify validation error messages
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
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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
                        console.log(data);
                        var json = JSON.parse(data);

                        // Hide interface
                        $("#submitEdit").val("Edit");
                        $("#options").show();
                        $("#editEventInfo").hide();

                        // Fix text when changing the title
                        $("#eventId option:selected").text($("#eventName").val());
                        $("#eventId").select2();
						
						// Remove all shift boxes
						$("div[name*='pair']").each(function()
						{
							$(this).remove();
						});

                        // Alert to success
                        alert(json.data);
                    }
                });
            }
        }
    });

    $("form[name='loginForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            tkid: "required",
            password: "required"
        },
        // Specify validation error messages
        messages:
        {
            tkid: "Please enter your TKID or forum username.",
            password: "Please enter your password."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form)
        {
            form.submit();
        }
    });

    $("form[name='changeEmailForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            email:
            {
                required: true,
                email: true
            }
        },
        // Specify validation error messages
        messages:
        {
            email: "Please enter a valid e-mail."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form)
        {
            var form = $("#changeEmailForm");
            var url = form.attr("action");

            $.ajax(
            {
                type: "POST",
                url: url,
                data: form.serialize() + "&emailButton=1",
                success: function(data)
                {
                    alert(data);
                }
            });
        }
    });

    $("form[name='changePhoneForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            //phone: "required"
        },
        // Specify validation error messages
        messages:
        {
            //phone: "Please enter a phone number."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            name: "required"
        },
        // Specify validation error messages
        messages:
        {
            name: "Please enter a name."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            tkid:
            {
                required: true,
                digits: true,
                maxlength: 11
            },
            email:
            {
                required: true,
                email: true
            },
			rebelforum:
			{
				required: function()
				{
					return $('#squad').val() == 6;
				}				
			},
			mandoid:
			{
				digits: true,
				required: function()
				{
					return $('#squad').val() == 8;
				}				
			},
            sgid:
            {
				required: false,
                digits: true          
            },
            forumid: "required",
            password: "required",
            passwordC: "required",
            name: "required"
        },
        // Specify validation error messages
        messages:
        {
            tkid: "Please enter your TKID and make sure it is no more than eleven (11) characters.",
            forumid: "Please enter your FL 501st Forum Username.",
            password: "Please enter your password.",
            passwordC: "Please re-enter your password to confirm it.",
            email: "Please enter a valid e-mail.",
            name: "Please enter your name.",
			rebelforum: "Please enter your Rebel Legion forum username.",
			mandoid: "Please enter your Mando Mercs CAT #."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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

    $("form[name='signupForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
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
        // Specify validation error messages
        messages:
        {
            costume: "Please choose a costume.",
            status: "Please choose your status."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
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
        // Specify validation error messages
        messages:
        {
            costume: "Please choose a costume.",
            status: "Please choose your status."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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
                    else
                    {
                        // Put data in html
                        $("#signuparea").html(json.data);
                        selectAdd();

                        // Do the rest...
                        $("#signeduparea").show();
                        $("#addfriend").show();
                        selectAdd();
                        $("#signuparea1").hide();
                        $("#hr1").hide();
                        $("#rosterTableNoData").hide();

                        // Update troopers remaining on the page
                        $("div[name=troopersRemainingDisplay]").html(json.troopersRemaining);
                    }
                }
            });
        }
    });

    $("form[name='signupForm3']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
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
        // Specify validation error messages
        messages:
        {
            costume: "Please choose a costume.",
            status: "Please choose your status."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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

                    if (json.success == "failed")
                    {
                        alert(json.data);
                    }
                    else
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
                    }
                }
            });
        }
    });

    $("form[name='createUserForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            name: "required",
            forumid: "required",
            email:
            {
                required: true,
                email: true
            },
            squad:
            {
                required: true,
                range: [0, 1]
            },
            permissions:
            {
                required: true,
                range: [0, 2]
            },
            p501:
            {
                required: true,
                range: [0, 4]
            },
            pRebel:
            {
                required: true,
                range: [0, 4]
            },
            pDroid:
            {
                required: true,
                range: [0, 4]
            },
            pMando:
            {
                required: true,
                range: [0, 4]
            },
            pOther:
            {
                required: true,
                range: [0, 4]
            },
            tkid:
            {
                required: true,
                digits: true
            },
			mandoid:
			{
				digits: true
			},
            sgid:
            {
                digits: true
            },
            password:
            {
                required: true,
                minlength: 6
            }
        },
        // Specify validation error messages
        messages:
        {
            name: "Please enter a name.",
            forumid: "Please enter the FL 501st Username.",
            email: "Please enter a valid e-mail address.",
            squad: "Please choose a squad.",
            tkid: "Please enter a valid ID.",
            password: "Please enter a valid password."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form)
        {
            var r = confirm("Are you sure you want to create this user?");

            if (r == true)
            {
                $.ajax(
                {
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize() + "&submitUser=1",
                    success: function(data)
                    {
                        var json = JSON.parse(data);

                        // If success
                        if (json.success == "success")
                        {
                            // Clear Form
                            $("#name").val("");
                            $("#email").val("");
                            $("#forumid").val("");
							$("#rebelforum").val("");
							$("#mandoid").val("");
                            $("#sgid").val("");
                            $("#phone").val("");
                            $("#squad").val("1");
                            $("#permissions").val("0");
                            $("#tkid").val("");
                            $("#password").val("");
                        }

                        // Alert to success
                        alert(json.data);
                    }
                });
            }
        }
    });

    $("form[name='commentForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
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
        // Specify validation error messages
        messages:
        {
            comment: "Please enter a comment.",
            important: "Please pick an importance."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
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
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            tkid:
            {
                required: true
            },
            email:
            {
                required: true,
                email: true
            },
            password:
            {
                required: true,
                minlength: 6
            },
            password2:
            {
                required: true,
                minlength: 6
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
        // Specify validation error messages
        messages:
        {
            tkid: "Please enter your TKID.",
            password: "Please enter a six (6) character password.",
            password2: "Please enter a matching six (6) character password.",
            email: "Please enter a valid e-mail address.",
            tkid2: "Please enter an ID."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form)
        {
            form.submit();
        }
    });

    $("body").on("click", "#troopRosterFormAdd", function(e)
    {
        $("form[name='troopRosterFormAdd']").validate(
        {
            // Specify validation rules
            rules:
            {
                // The key name on the left side is the name attribute
                // of an input field. Validation rules are defined
                // on the right side
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
            // Specify validation error messages
            messages:
            {
                costume: "Please select a costume.",
                status: "Please select a status."
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid
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

                        // Show form / table
                        $('#rosterTable').append('<tr name="roster_' + trooperid + '" signid="' + signid + '"><td><input type="hidden" name="tkid" signid="' + signid + '" value = "' + tkid + '" /><input type="hidden" name="troopername" signid="' + signid + '" value = "' + troopername + '" /><input type="hidden" name="eventId" signid="' + signid + '" value = "' + $("#troopid").val() + '" /><input type="radio" name="trooperSelectEdit" signid="' + signid + '" value="' + trooperid + '" /></td><td><div name="tknumber1' + trooperid + '" signid="' + signid + '"><a href="index.php?profile=' + trooperid + '" target="_blank">' + tkid + ' - ' + troopername + '</a></div></td><td><div name="costume1' + trooperid + '" signid="' + signid + '">' + $("#costume option:selected").text() + '</div><div name="costume2' + trooperid + '" signid="' + signid + '" style="display:none;">' + string1 + '</div></td><td><div name="backup1' + trooperid + '" signid="' + signid + '">' + ifEmpty($("#costumebackup option:selected").text()) + '</div><div name="backup2' + trooperid + '" signid="' + signid + '" style="display:none;">' + string2 + '</div></td><td><div name="status1' + trooperid + '" signid="' + signid + '">' + getStatus($("#status").val()) + '</div><div name="status2' + trooperid + '" signid="' + signid + '" style="display:none;"><select name="statusVal' + trooperid + '" signid="' + signid + '"><option value="0">Going</option><option value="1">Stand By</option><option value="2">Tentative</option><option value="3">Attended</option><option value="4">Canceled</option><option value="5">Pending</option><option value="6">Not Picked</option></select></div></td></tr>');

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

    $("form[name='changePasswordForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            oldpassword: "required",
            newpassword:
            {
                required: true,
                minlength: 6
            },
            newpassword2:
            {
                required: true,
                minlength: 6
            }
        },
        // Specify validation error messages
        messages:
        {
            oldpassword: "Please enter your old password.",
            newpassword: "Please enter a six (6) character password.",
            newpassword2: "Please enter a matching six (6) character password."
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form)
        {
            var form = $("#changePasswordForm");
            var url = form.attr("action");

            $.ajax(
            {
                type: "POST",
                url: url,
                data: form.serialize() + "&changePasswordSend=1",
                success: function(data)
                {
                    $("#oldpassword").val("");
                    $("#newpassword").val("");
                    $("#newpassword2").val("");
                    alert(data);
                }
            });
        }
    });

    $("form[name='forgotPasswordForm']").validate(
    {
        // Specify validation rules
        rules:
        {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side
            tkid: "required"
        },
        // Specify validation error messages
        messages:
        {
            tkid: "Please enter your TK ID or forum username.",
            email: "Please enter a valid e-mail address.",
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form)
        {
            $.ajax(
            {
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(data)
                {
                    //alert(data); // show response from the php script.
                }
            });
            form.submit();
        }
    });
});