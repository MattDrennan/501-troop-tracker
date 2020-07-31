<?php
include 'config.php';

// Change password
if($_GET['do'] == "changepassword")
{
	// Display submission for change your password, otherwise show the form
	if(isset($_POST['changePasswordSend']))
	{
		// Get data
		$query = "SELECT * FROM troopers WHERE id='".$_SESSION['id']."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Check credentials
				if(cleanInput(md5($_POST['oldpassword'])) == $db->password)
				{
					if($_POST['newpassword'] == $_POST['newpassword2'])
					{
						if(strlen($_POST['newpassword']) >= 6)
						{
							// Query the database
							$conn->query("UPDATE troopers SET password = '".md5(cleanInput($_POST['newpassword']))."' WHERE id = '".$_SESSION['id']."'");

							echo 'Your password has changed!';
						}
						else
						{
							echo 'Your password must be six (6) characters long.';
						}
					}
					else
					{
						echo 'Your passwords do not match.';
					}
				}
				else
				{
					echo 'Incorrect old password.';
				}
			}
		}
	}
}

// Change sign up
if($_GET['do'] == "modifysignup")
{
	// If modify sign up submit button pressed
	if($_POST['submitModifySignUp'])
	{
		// If multiple days
		if($_POST['days'] == 1)
		{
			if(!isset($_POST['shiftcheckbox']))
			{
				// Change status
				$conn->query("UPDATE event_sign_up SET reason = '".cleanInput($_POST['cancelReason'])."', status = '4' WHERE troopid = '".cleanInput($_POST['troopidC'])."' AND trooperid = '".$_SESSION['id']."'") or die($conn->error);

				// Delete all from shift trooper when canceling
				$conn->query("DELETE FROM shift_trooper WHERE troopid = '".cleanInput($_POST['troopidC'])."' AND trooperid = '".$_SESSION['id']."'") or die($conn->error);
			}
			else
			{
				// Query for shift boxes
				$shift = "";

				// Loop through check boxes to get data
				foreach($_POST['shiftcheckbox'] as $key)
				{
					$shift = $shift . ',' . cleanInput($key);
				}

				// Cut first comma out
				$shift = substr($shift, 1);

				// Clear it out to start all over
				$conn->query("DELETE FROM shift_trooper WHERE troopid = '".cleanInput($_POST['troopidC'])."' AND trooperid = '".$_SESSION['id']."'") or die($conn->error);

				// Insert into database
				$conn->query("INSERT INTO shift_trooper (troopid, trooperid, shift) VALUES ('".cleanInput($_POST['troopidC'])."', ".cleanInput($_SESSION['id']).", '".$shift."')") or die($conn->error);

				// Update event
				$conn->query("UPDATE event_sign_up SET reason = '', status = '".cleanInput($_POST['status'])."' WHERE troopid = '".cleanInput($_POST['troopidC'])."' AND trooperid = '".$_SESSION['id']."'") or die($conn->error);
			}

			// Query database for shift info
			$query3 = "SELECT shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid FROM shift_trooper WHERE shift_trooper.trooperid = '".$_SESSION['id']."' AND shift_trooper.troopid = '".cleanInput($_POST['troopidC'])."'";


			$date1 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['dateStart'])));
			$date2 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['dateEnd'])));

			$days = getDatesFromRange($date1, $date2);

			$l = 0;
			$data = "";

			if ($result3 = mysqli_query($conn, $query3))
			{
				while ($db3 = mysqli_fetch_object($result3))
				{
					$shiftString = explode(",", $db3->shift);

					for($n = 0; $n <= count($shiftString) - 1; $n += 2)
					{
						$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

						$shift = mysqli_fetch_array($shiftGet);

						// Convert times
						$readTime1 = date('h:i A', strtotime($shift[1]));
						$readTime2 = date('h:i A', strtotime($shift[2]));

						$data .= $days[$shiftString[$n + 1]] . '<br />' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';
					}

					$l++;
				}
			}

			// If no data
			if($l == 0)
			{
				$data = "Canceled";
			}

			// Send back data
			$array = array('success' => 'success', 'data' => $data, 'id' => $_SESSION['id']);
			echo json_encode($array);
		}
	}
}

// Approve troopers
if(isset($_GET['do']) && $_GET['do'] == "assignawards" && loggedIn() && isAdmin())
{
	// Award submitted for deletion...
	if(isset($_POST['submitDeleteAward']))
	{
		// Query the database
		$conn->query("DELETE FROM awards WHERE id = '".cleanInput($_POST['awardID'])."'");

		// Delete from the other database
		$conn->query("DELETE FROM award_troopers WHERE awardid = '".cleanInput($_POST['awardID'])."'");
	}

	// Add award...
	if(isset($_POST['submitAddAward']))
	{
		$message = "Award added";

		// Check if has value
		if(cleanInput($_POST['awardName']) == "")
		{
			$message = "Award must have a name.";
		}
		else
		{
			// Query the database
			$conn->query("INSERT INTO awards (title, icon) VALUES ('".cleanInput($_POST['awardName'])."', '".cleanInput($_POST['awardImage'])."')");
		}

		$array = array(array('message' => $message));
		echo json_encode($array);
	}

	if(isset($_POST['submitAward']))
	{
		// Check how many rewards
		$result = mysqli_query($conn, "SELECT count(*) FROM award_troopers WHERE trooperid = '".cleanInput($_POST['userIDAward'])."' AND awardid = '".cleanInput($_POST['awardIDAssign'])."'");
		$num_rows = mysqli_fetch_row($result)[0];

		$message = "The award was awarded successfully!";

		// If no duplicates
		if($num_rows == 0)
		{
			// Query the database
			$conn->query("INSERT INTO award_troopers (trooperid, awardid) VALUES ('".cleanInput($_POST['userIDAward'])."', '".cleanInput($_POST['awardIDAssign'])."')");
		}
		else
		{
			$message = "Trooper already has this award!";
		}

		$array = array(array('message' => $message));
		echo json_encode($array);
	}

	if(isset($_POST['submitEditAward']))
	{
		// Query the database
		$conn->query("UPDATE awards SET title = '".cleanInput($_POST['editAwardTitle'])."', icon = '".cleanInput($_POST['editAwardImage'])."' WHERE id = '".cleanInput($_POST['awardIDEdit'])."'");
	}
}

// Get trooper data
if(isset($_GET['do']) && $_GET['do'] == "getuser" && loggedIn())
{
	if(isset($_POST['getuser']))
	{
		$query = "SELECT * FROM troopers WHERE id = '".cleanInput($_POST['id'])."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$array = array('name' => $db->name, 'email' => $db->email, 'phone' => $db->phone, 'squad' => $db->squad, 'tkid' => $db->tkid);
			}
		}
	}

	echo json_encode($array);
}

// Get award data
if(isset($_GET['do']) && $_GET['do'] == "getawards" && loggedIn())
{
	if(isset($_POST['getawards']))
	{
		$query = "SELECT * FROM awards WHERE trooperid = '".cleanInput($_POST['id'])."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$array = array(array('title' => $db->title, 'id' => $db->id));
			}
		}

		echo json_encode($array);
	}
}

// Approve troopers
if(isset($_GET['do']) && $_GET['do'] == "approvetroopers" && loggedIn() && isAdmin())
{
	// User submitted for deletion...
	if(isset($_POST['submitDenyUser']))
	{
		// Query the database
		$conn->query("DELETE FROM troopers WHERE id = '".cleanInput($_POST['userID'])."'");
	}

	if(isset($_POST['submitApproveUser']))
	{
		// Query the database
		$conn->query("UPDATE troopers SET approved = 1 WHERE id = '".cleanInput($_POST['userID'])."'");
	}
}

// Manage troopers
if(isset($_GET['do']) && $_GET['do'] == "managetroopers" && loggedIn() && isAdmin())
{
	// User submitted for deletion...
	if(isset($_POST['submitDeleteUser']))
	{
		// Query the database
		$conn->query("DELETE FROM troopers WHERE id = '".cleanInput($_POST['userID'])."'");
	}

	// User submitted for edit...
	if(isset($_POST['submitEditUser']))
	{
		// Load user info
		$query = "SELECT * FROM troopers WHERE id = '".cleanInput($_POST['userID'])."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$array = array('id' => $db->id, 'name' => $db->name, 'email' => $db->email, 'phone' => $db->phone, 'squad' => $db->squad, 'permissions' => $db->permissions, 'tkid' => $db->tkid);

				echo json_encode($array);
			}
		}
	}

	// User edit submitted
	if(isset($_POST['submitUserEdit']))
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
		if(cleanInput($_POST['user']) != "" && cleanInput($_POST['email']) != "" && cleanInput($_POST['squad']) != "" && cleanInput($_POST['permissions']) != "" && cleanInput($_POST['tkid']) != "")
		{
			include("script/lib/EmailAddressValidator.php");
			$validator = new EmailAddressValidator;
			if (!$validator->check_email_address(cleanInput($_POST['email'])))
			{
				$array = array('success' => 'failed', 'data' => 'Invalid e-mail.');
				echo json_encode($array);
			}
			else
			{
				// Query the database
				$conn->query("UPDATE troopers SET name = '".cleanInput($_POST['user'])."', email =  '".cleanInput($_POST['email'])."', phone = '".cleanInput(cleanInput($_POST['phone']))."', squad = '".cleanInput($_POST['squad'])."', permissions = '".cleanInput($_POST['permissions'])."', tkid = '".cleanInput($_POST['tkid'])."' WHERE id = '".cleanInput($_POST['userIDE'])."'") or die($conn->error);

				$array = array('success' => 'true', 'data' => 'User has been updated!');
				echo json_encode($array);
			}
		}
		else
		{
			$array = array('success' => 'failed', 'data' => '');
			echo json_encode($array);
		}
	}
}

// Create user
if(isset($_GET['do']) && $_GET['do'] == "createuser" && loggedIn())
{
	if(isset($_POST['submitUser']))
	{
		if(cleanInput($_POST['name']) != "" && cleanInput($_POST['email']) != "" && cleanInput($_POST['squad']) != "" && cleanInput($_POST['permissions']) != "" && cleanInput($_POST['tkid']) != "")
		{
			// Verify emails
			include("script/lib/EmailAddressValidator.php");

			$validator = new EmailAddressValidator;
			if (!$validator->check_email_address(cleanInput($_POST['email'])))
			{
				$array = array('success' => 'failed', 'data' => 'Invalid e-mail.');
				echo json_encode($array);
			}
			else
			{
				// Insert into database
				$conn->query("INSERT INTO troopers (name, email, phone, squad, permissions, tkid) VALUES ('".cleanInput($_POST['name'])."', '".cleanInput($_POST['email'])."', '".cleanInput($_POST['phone'])."', '".cleanInput($_POST['squad'])."', '".cleanInput($_POST['permissions'])."', '".cleanInput($_POST['tkid'])."')");

				$array = array('success' => 'success', 'data' => 'User created!');
				echo json_encode($array);
			}
		}
		else
		{
			$array = array('success' => 'failed', 'data' => 'A value is missing.');
			echo json_encode($array);
		}
	}
}

// Change phone
if(isset($_GET['do']) && $_GET['do'] == "changephone" && loggedIn())
{
	if(isset($_POST['phoneButton']))
	{
		$conn->query("UPDATE troopers SET phone = '".cleanInput($_POST['phone'])."' WHERE id = '".$_SESSION['id']."'");
		echo 'Phone number updated sucessfully!';
	}
}

// Change name
if(isset($_GET['do']) && $_GET['do'] == "changename" && loggedIn())
{
	if(isset($_POST['nameButton']))
	{
		$failed = false;

		if(cleanInput($_POST['name']) == "")
		{
			$failed = true;
			echo 'Please enter a name.';
		}

		if(!$failed)
		{
			$conn->query("UPDATE troopers SET name = '".cleanInput($_POST['name'])."' WHERE id = '".$_SESSION['id']."'");
			echo 'Name updated sucessfully!';
		}
	}
}

// Change e-mail
if(isset($_GET['do']) && $_GET['do'] == "changeemail" && loggedIn())
{
	if(isset($_POST['emailButton']))
	{
		$failed = false;

		if(cleanInput($_POST['email']) == "")
		{
			$failed = true;
			echo 'Please enter a valid e-mail address.';
		}

		// Verify emails
		include("script/lib/EmailAddressValidator.php");

		$validator = new EmailAddressValidator;
		if (!$validator->check_email_address(cleanInput($_POST['email'])))
		{
			$failed = true;
			echo '\n\n-Please enter a valid e-mail address.';
		}

		if(!$failed)
		{
			$conn->query("UPDATE troopers SET email = '".cleanInput($_POST['email'])."' WHERE id = '".$_SESSION['id']."'");
			echo 'E-mail updated sucessfully!';
		}
	}
}

// Unsubscribe / Subscribe
if(isset($_GET['do']) && $_GET['do'] == "unsubscribe" && loggedIn())
{
	if(isset($_POST['unsubscribeButton']))
	{
		$query = "SELECT subscribe FROM troopers WHERE id = '".$_SESSION['id']."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($db->subscribe == 1)
				{
					$conn->query("UPDATE troopers SET subscribe = 0 WHERE id = '".$_SESSION['id']."'");
					echo 'You are now unsubscribed from e-mails and will no longer receive notifications.';
				}
				else
				{
					$conn->query("UPDATE troopers SET subscribe = 1 WHERE id = '".$_SESSION['id']."'");
					echo 'You are now subscribed to e-mail notifications.';
				}
			}
		}
	}
}

// Request access
if(isset($_GET['do']) && $_GET['do'] == "requestaccess")
{
	if($_POST['submitRequest'])
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
		if($_POST['tkid'] != "" && $_POST['name'] != "" && $_POST['email'] != "" && $_POST['password'] != "" && $_POST['squad'] >= 0)
		{
			$failed = false;

			echo '<ul>';

			if(cleanInput($_POST['name']) == "")
			{
				$failed = true;
				echo '<li>Please enter your name.</li>';
			}

			$tkid = cleanInput($_POST['tkid']);

			if(cleanInput($_POST['squad']) == 6)
			{
				$tkid = "111111" . $tkid;
			}
			else if(cleanInput($_POST['squad']) == 7)
			{
				$tkid = "222222" . $tkid;
			}
			else if(cleanInput($_POST['squad']) == 8)
			{
				$tkid = "333333" . $tkid;
			}
			else if(cleanInput($_POST['squad']) == 9)
			{
				$tkid = "444444" . $tkid;
			}

			if(strlen($_POST['password']) < 6)
			{
				echo '<li>Password must be 6 (six) characters.</li>';
			}

			if(!is_numeric($tkid))
			{
				echo '<li>TKID must be an integer,</li>';
			}

			if(strlen($tkid) > 11)
			{
				echo '<li>TKID must be less than eleven (11) characters.</li>';
			}

			// Query ID database
			$idcheck = $conn->query("SELECT id FROM troopers WHERE tkid = '".$tkid."'") or die($conn->error);

			// Check if ID exists
			if($idcheck->num_rows > 0)
			{
				$failed = true;
				echo '<li>TKID is taken.</li>';
			}

			// Verify passwords
			if(cleanInput($_POST['password']) != cleanInput($_POST['passwordC']))
			{
				$failed = true;
				echo '<li>Password and password confirm do not match.</li>';
			}

			// Verify emails
			include("script/lib/EmailAddressValidator.php");

			$validator = new EmailAddressValidator;
			if (!$validator->check_email_address(cleanInput($_POST['email'])))
			{
				$failed = true;
				echo '<li>Please input a valid email address.</li>';
			}

			if(strlen(cleanInput($_POST['phone'])) < 10 && cleanInput($_POST['phone']) != "")
			{
				$failed = true;
				echo '<li>Enter a valid phone number.</li>';
			}

			// If failed
			if(!$failed)
			{
				$conn->query("INSERT INTO troopers (name, tkid, email, phone, squad, password) VALUES ('".cleanInput($_POST['name'])."', '".floatval($tkid)."', '".cleanInput($_POST['email'])."', '".cleanInput($_POST['phone'])."', '".cleanInput($_POST['squad'])."', '".md5(cleanInput($_POST['password']))."')") or die($conn->error);
				echo '<li>Request submitted! You will receive an e-mail when your request is approved or denied.</li>';
			}

			echo '</ul>';
		}
	}
}

// Create event
if(isset($_GET['do']) && $_GET['do'] == "createevent" && loggedIn() && isAdmin())
{
	// Event submitted...
	if(isset($_POST['submitEvent']))
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
		if($_POST['eventName'] != "" && $_POST['eventVenue'] != "" && $_POST['location'] != "" && $_POST['dateStart'] != "" && $_POST['dateEnd'] != "" && $_POST['numberOfAttend'] != "" && $_POST['requestedNumber'] != "" && $_POST['secure'] != "" && $_POST['blasters'] != "" && $_POST['lightsabers'] != "" && $_POST['parking'] != "null" && $_POST['mobility'] != "null" && $_POST['label'] != "null" && $_POST['limitedEvent'] != "null")
		{
			// Convert date
			$date1 = date('Y-m-d H:i:s', strtotime($_POST['dateStart']));
			$date2 = date('Y-m-d H:i:s', strtotime($_POST['dateEnd']));

			$days = getDatesFromRange($date1, $date2);

			if(count($days) > 1 && $_POST['time1a'] == "" && $_POST['time1b'] == "")
			{
				$array = array('success' => 'failed', 'data' => 'You must insert shifts on multi day events');
				echo json_encode($array);
			}
			else
			{
				// Query the database
				$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limitRebels, limit501st, limitMando, limitDroid, squad) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".cleanInput($date1)."', '".cleanInput($date2)."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limitRebels'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitMando'])."', '".cleanInput($_POST['limitDroid'])."', '".getSquad(cleanInput($_POST['location']))."')") or die($conn->error);

				$array = array('success' => 'success', 'data' => 'Event created!');
				echo json_encode($array);
			}

			// Event ID - Last insert from database
			$eventId = $conn->insert_id;

			// Insert shifts into database
			if($_POST['time1a'] != "" && $_POST['time1b'] != "")
			{
				$conn->query("INSERT INTO shifts (troopid, starttime, endtime) VALUES ('".$eventId."', '".cleanInput($_POST['time1a'])."', '".cleanInput($_POST['time1b'])."')");

				if($_POST['time2a'] != "" && $_POST['time2b'] != "")
				{
					$conn->query("INSERT INTO shifts (troopid, starttime, endtime) VALUES ('".$eventId."', '".cleanInput($_POST['time2a'])."', '".cleanInput($_POST['time2b'])."')");

					if($_POST['time3a'] != "" && $_POST['time3b'] != "")
					{
						$conn->query("INSERT INTO shifts (troopid, starttime, endtime) VALUES ('".$eventId."', '".cleanInput($_POST['time3a'])."', '".cleanInput($_POST['time3b'])."')");
					}
				}
			}
		}
		else
		{
			$array = array('success' => 'failed', 'data' => 'Some fields are missing!');
			echo json_encode($array);
		}
	}
}

// Edit Event
if(isset($_GET['do']) && $_GET['do'] == "editevent" && loggedIn() && isAdmin())
{
	// Edit a trooper from roster
	if(isset($_POST['submitEditRoster']))
	{
		// Query the database
		$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costumeValSelect' . $_POST['trooperSelectEdit'] . ''])."', costume_backup = '".cleanInput($_POST['costumeVal' . $_POST['trooperSelectEdit'] . ''])."', status = '".cleanInput($_POST['statusVal' . $_POST['trooperSelectEdit'] . ''])."', reason = '".cleanInput($_POST['reasonVal' . $_POST['trooperSelectEdit'] . ''])."', attend = '".cleanInput($_POST['attendVal' . $_POST['trooperSelectEdit'] . ''])."', attended_costume = '".cleanInput($_POST['attendcostumeVal' . $_POST['trooperSelectEdit'] . ''])."' WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."'") or die($conn->error);

		// Attending On - Update multiple day events (Maybe fixed this?? Was changing all values to 4 (canceled) even on single day events. This will change all to 4 only when canceling now)
		if(!isset($_POST['shiftcheckbox' . $_POST['trooperSelectEdit'] . '']))
		{
			if(cleanInput($_POST['statusVal' . $_POST['trooperSelectEdit'] . '']) == 4)
			{
				// Change status
				$conn->query("UPDATE event_sign_up SET status = '4' WHERE troopid = '".cleanInput($_POST['eventId'])."' AND trooperid = '".cleanInput($_POST['trooperSelectEdit'])."'") or die($conn->error);

				// Delete all from shift trooper when canceling
				$conn->query("DELETE FROM shift_trooper WHERE troopid = '".cleanInput($_POST['eventId'])."' AND trooperid = '".cleanInput($_POST['trooperSelectEdit'])."'") or die($conn->error);
			}
		}
		else
		{
			// Query for shift boxes
			$shift = "";

			// Loop through check boxes to get data
			foreach($_POST['shiftcheckbox' . $_POST['trooperSelectEdit'] . ''] as $key)
			{
				$shift = $shift . ',' . cleanInput($key);
			}

			// Cut first comma out
			$shift = substr($shift, 1);

			// Clear it out to start all over
			$conn->query("DELETE FROM shift_trooper WHERE troopid = '".cleanInput($_POST['eventId'])."' AND trooperid = '".cleanInput($_POST['trooperSelectEdit'])."'") or die($conn->error);

			// Insert into database
			$conn->query("INSERT INTO shift_trooper (troopid, trooperid, shift) VALUES ('".cleanInput($_POST['eventId'])."', ".cleanInput($_POST['trooperSelectEdit']).", '".$shift."')") or die($conn->error);

			// Update event
			$conn->query("UPDATE event_sign_up SET reason = '', status = '".cleanInput($_POST['statusVal' . cleanInput($_POST['trooperSelectEdit']) . ''])."' WHERE troopid = '".cleanInput($_POST['eventId'])."' AND trooperid = '".cleanInput($_POST['trooperSelectEdit'])."'") or die($conn->error);
		}

		/********************************************************/

		// Attended On - Update multiple day events
		// ?? May need to update this
		if(!isset($_POST['shiftcheckbox2' . $_POST['trooperSelectEdit'] . '']))
		{
			// Delete all from shift trooper when canceling
			$conn->query("UPDATE shift_trooper SET attend = '-1' WHERE troopid = '".cleanInput($_POST['eventId'])."' AND trooperid = '".cleanInput($_POST['trooperSelectEdit'])."'") or die($conn->error);
		}
		else
		{
			// Query for shift boxes
			$shift = "";

			// Loop through check boxes to get data
			foreach($_POST['shiftcheckbox2' . $_POST['trooperSelectEdit'] . ''] as $key)
			{
				$shift = $shift . ',' . cleanInput($key);
			}

			// Cut first comma out
			$shift = substr($shift, 1);

			// Update database
			$conn->query("UPDATE shift_trooper SET attend = '-1,".$shift."' WHERE troopid = '".cleanInput($_POST['eventId'])."' AND trooperid = '".cleanInput($_POST['trooperSelectEdit'])."'") or die($conn->error);
		}

		/***************************************************************/

		// Retrieve data to update
		// Query database for shift info
		$query3 = "SELECT shift_trooper.attend, shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid, events.dateStart, events.dateEnd FROM shift_trooper LEFT JOIN events ON events.id = shift_trooper.troopid WHERE shift_trooper.trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND shift_trooper.troopid = '".cleanInput($_POST['eventId'])."'";

		$l = 0;
		$data = "";
		$data2 = "";

		if ($result3 = mysqli_query($conn, $query3))
		{
			while ($db3 = mysqli_fetch_object($result3))
			{
				// Get date data
				$date1 = date('Y-m-d H:i:s', strtotime($db3->dateStart));
				$date2 = date('Y-m-d H:i:s', strtotime($db3->dateEnd));

				// Get datas ranges
				$days = getDatesFromRange($date1, $date2);

				// Make a shift string
				$shiftString = explode(",", $db3->shift);
				// Get attend data - Read the data, use substr to remove the -1 at the start
				$shiftString2 = explode(",", substr($db3->attend, 3));

				for($n = 0; $n <= count($shiftString) - 1; $n += 2)
				{
					$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

					$shift = mysqli_fetch_array($shiftGet);

					// Convert times
					$readTime1 = date('h:i A', strtotime($shift[1]));
					$readTime2 = date('h:i A', strtotime($shift[2]));

					$data .= $days[$shiftString[$n + 1]] . '<br />' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';
				}

				if($db3->attend != "-1")
				{
					for($n = 0; $n <= count($shiftString2) - 1; $n += 2)
					{
						$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString2[$n]."'") or die($conn->error);

						$shift = mysqli_fetch_array($shiftGet);

						// Convert times
						$readTime1 = date('h:i A', strtotime($shift[1]));
						$readTime2 = date('h:i A', strtotime($shift[2]));

						$data2 .= $days[$shiftString2[$n + 1]] . '<br />' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';
					}
				}

				$l++;
			}
		}

		// If no data
		if($l == 0)
		{
			// If multiple day
			if(count($days) > 1)
			{
				$data = "Canceled";
				$data2 = "";
			}
			else
			{
				// If single day
				$data = $days[0];
				$data2 = "";
			}
		}

		// Send back data
		$array = array('success' => 'success', 'data' => $data, 'data2' => $data2, 'id' => $_SESSION['id']);
		echo json_encode($array);
	}

	// Add a trooper to roster
	if(isset($_POST['troopRosterFormAdd']))
	{
		if(cleanInput($_POST['costume']) != "null" && cleanInput($_POST['status']) != "null")
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, costume_backup, reason, status, attended_costume) VALUES ('".cleanInput($_POST['trooperSelect'])."', '".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['costumebackup'])."', '".cleanInput($_POST['reason'])."', '".cleanInput($_POST['status'])."', '".cleanInput($_POST['attendedcostume'])."')") or die($conn->error);
		}
	}

	// Event submitted for deletion...
	if(isset($_POST['submitDelete']))
	{
		// Query the database
		$conn->query("DELETE FROM events WHERE id = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - event_sign_up
		$conn->query("DELETE FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - shift trooper
		$conn->query("DELETE FROM shift_trooper WHERE troopid = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - shifts
		$conn->query("DELETE FROM shifts WHERE troopid = '".cleanInput($_POST['eventId'])."'");
	}

	// Event submitted for cancelation...
	if(isset($_POST['submitCancel']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '2' WHERE id = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - event_sign_up
		$conn->query("DELETE FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - shift trooper
		$conn->query("DELETE FROM shift_trooper WHERE troopid = '".cleanInput($_POST['eventId'])."'");
	}

	// Event submitted for completion...
	if(isset($_POST['submitFinish']))
	{
		// Query the database
		$conn->query("UPDATE events SET moneyRaised = '".cleanInput($_POST['charity'])."', closed = '1' WHERE id = '".cleanInput($_POST['eventId'])."'");
	}

	// Remove trooper from roster
	if(isset($_POST['removetrooper']))
	{
		if(isset($_POST['trooperSelectEdit']) && $_POST['trooperSelectEdit'] >= 0)
		{
			$array = array('success' => 'true', 'data' => 'Trooper removed!');

			// Query the database
			$conn->query("DELETE FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."'");
		}
		else
		{
			$array = array('success' => 'false', 'data' => 'Please select a trooper.');
		}

		echo json_encode($array);
	}

	// Roster
	if(isset($_POST['submitRoster']))
	{
		// Load users assigned to event
		$query = "SELECT * FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'";
		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// First add this to make a list
				if($i == 0)
				{
					echo '
					<form action="process.php?do=editevent" method="POST" name="troopRosterForm" id="troopRosterForm">
						<div style="overflow-x: auto;">
						<table border="1" name="rosterTable" id="rosterTable">
							<tr>
								<th>Selection</th>	<th>Trooper TKID</td>	<th>Trooper Costume</th>	<th>Trooper Backup Costume</th>	<th>Trooper Status</th>	<th>Trooper Comment</th>	<th>Trooper Attended</th>	<th>Attended With</th>	<th>Dates Attending</th>	<th>Dates Attended</th>';
				}

				// List troopers
				echo '
				<tr id="roster_'.$db->trooperid.'" name="roster_'.$db->trooperid.'">
					<td>
						<input type="hidden" name="eventId" id="eventId" value = "'.cleanInput($_POST['eventId']).'" />
						<input type="radio" name="trooperSelectEdit" id="trooperSelectEdit" value="'.$db->trooperid.'" />
					</td>

					<td>
						<div name="tknumber1'.$db->trooperid.'" id="tknumber1'.$db->trooperid.'">'.getTKNumber($db->trooperid).'</div>
					</td>

					<td>
						<div name="costume1'.$db->trooperid.'" id="costume1'.$db->trooperid.'">'.getCostume($db->costume).'</div>
						<div name="costume2'.$db->trooperid.'" id="costume2'.$db->trooperid.'" style="display:none;">
							<select name="costumeValSelect'.$db->trooperid.'">';

							// Display costumes
							$query2 = "SELECT * FROM costumes ORDER BY costume";
							if ($result2 = mysqli_query($conn, $query2))
							{
								while ($db2 = mysqli_fetch_object($result2))
								{
									// Select the costume the user chose to wear
									if($db->costume == $db2->id)
									{
										echo '<option value="'.$db2->id.'" SELECTED>'.$db2->costume.'</option>';
									}
									else
									{
										echo '<option value="'.$db2->id.'">'.$db2->costume.'</option>';
									}
								}
							}

							echo '
							</select>
						</div>
					</td>

					<td>
						<div name="backup1'.$db->trooperid.'" id="backup1'.$db->trooperid.'">'.getCostume($db->costume_backup).'</div>
						<div name="backup2'.$db->trooperid.'" id="backup2'.$db->trooperid.'" style="display:none;">

						<select name="costumeVal'.$db->trooperid.'" id="costumeVal'.$db->trooperid.'">';

						// Display costumes
						$query2 = "SELECT * FROM costumes ORDER BY costume";
						// Count costumes
						$c = 0;
						// Count picked
						$p = 0;
						if ($result2 = mysqli_query($conn, $query2))
						{
							while ($db2 = mysqli_fetch_object($result2))
							{
								// Select the costume the user chose to wear
								if($db->costume_backup == $db2->id)
								{
									// The users costume
									echo '<option value="'.$db2->costume.'" SELECTED>'.$db2->costume.'</option>';
									$p++;
								}
								else
								{
									// Display costume
									echo '<option value="'.$db2->id.'">'.$db2->costume.'</option>';
								}

								$c++;
							}
						}

						if($c == 0 || $p == 0)
						{
							echo '<option value="99999" SELECTED>None</option>';
						}

						echo '
						</select>

						</div>
					</td>

					<td>
						<div name="status1'.$db->trooperid.'" id="status1'.$db->trooperid.'">'.getStatus($db->status).'</div>
						<div name="status2'.$db->trooperid.'" id="status2'.$db->trooperid.'" style="display:none;">
							<select name="statusVal'.$db->trooperid.'">
								<option value="0" '.echoSelect(0, $db->status).'>Going</option>
								<option value="1" '.echoSelect(1, $db->status).'>Stand By</option>
								<option value="2" '.echoSelect(2, $db->status).'>Tentative</option>
								<option value="3" '.echoSelect(3, $db->status).'>Attended</option>
								<option value="4" '.echoSelect(4, $db->status).'>Canceled</option>
								<option value="5" '.echoSelect(5, $db->status).'>Pending</option>
								<option value="6" '.echoSelect(6, $db->status).'>Not Picked</option>
							</select>
						</div>
					</td>

					<td>
						<div name="reason1'.$db->trooperid.'" id="reason1'.$db->trooperid.'">'.$db->reason.'</div>
						<div name="reason2'.$db->trooperid.'" id="reason2'.$db->trooperid.'" style="display:none;"><input type="text" id="reasonVal'.$db->trooperid.'" name="reasonVal'.$db->trooperid.'" value="'.$db->reason.'" /></div>
					</td>

					<td>
						<div name="attend1'.$db->trooperid.'" id="attend1'.$db->trooperid.'">'.didAttend($db->attend).'</div>
						<div name="attend2'.$db->trooperid.'" id="attend2'.$db->trooperid.'" style="display:none;">
							<select name="attendVal'.$db->trooperid.'">
								<option value="0" '.echoSelect(0, $db->attend).'>Did not attend</option>
								<option value="1" '.echoSelect(1, $db->attend).'>Attended</option>
							</select>
						</div>
					</td>

					<td>
						<div name="attendcostume1'.$db->trooperid.'" id="attendcostume1'.$db->trooperid.'">'.getCostume($db->attended_costume).'</div>
						<div name="attendcostume2'.$db->trooperid.'" id="attendcostume2'.$db->trooperid.'" style="display:none;">
						<select name="attendcostumeVal'.$db->trooperid.'" id="attendcostumeVal'.$db->trooperid.'">';

						// Display costumes
						$query2 = "SELECT * FROM costumes ORDER BY costume";
						// Amount of costumes
						$c = 0;
						// Count picked
						$p = 0;
						if ($result2 = mysqli_query($conn, $query2))
						{
							while ($db2 = mysqli_fetch_object($result2))
							{
								if($db->attended_costume == $db2->id)
								{
									// The users costume
									echo '<option value="'.$db2->id.'" SELECTED>'.$db2->costume.'</option>';
									$p++;
								}
								else
								{
									// Display costume
									echo '<option value="'.$db2->id.'">'.$db2->costume.'</option>';
								}

								$c++;
							}
						}

						if($c == 0 || $p == 0)
						{
							echo '<option value="99999" SELECTED>None</option>';
						}

						echo '
						</select>
						</div>
					</td>

					<td>

						<div id="dateAttending'.$db->trooperid.'">';

					// Query events
					$query2 = "SELECT * FROM events WHERE id = '".$db->troopid."'";
					if ($result2 = mysqli_query($conn, $query2))
					{
						while ($db2 = mysqli_fetch_object($result2))
						{
							// Query database for shift info
							$query3 = "SELECT shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid FROM shift_trooper WHERE shift_trooper.trooperid = '".$db->trooperid."' AND shift_trooper.troopid = '".$db->troopid."'";


							$date1 = date('Y-m-d H:i:s', strtotime($db2->dateStart));
							$date2 = date('Y-m-d H:i:s', strtotime($db2->dateEnd));

							$days = getDatesFromRange($date1, $date2);

							$l = 0;
							$data = "";

							if ($result3 = mysqli_query($conn, $query3))
							{
								while ($db3 = mysqli_fetch_object($result3))
								{
									$shiftString = explode(",", $db3->shift);

									for($n = 0; $n <= count($shiftString) - 1; $n += 2)
									{
										$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

										$shift = mysqli_fetch_array($shiftGet);

										// Convert times
										$readTime1 = date('h:i A', strtotime($shift[1]));
										$readTime2 = date('h:i A', strtotime($shift[2]));

										$data .= $days[$shiftString[$n + 1]] . '<br />' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';
									}

									// Increment
									$l++;
								}
							}

							// If no data
							if($l == 0)
							{
								if(count($days) > 1)
								{
									$data = "Canceled";
								}
								else
								{
									$data = $date1;
									$data = date('m-d-Y', strtotime($date1));
								}
							}

							echo $data;
						}
					}

					echo '
						</div>

						<div id="dateAttending'.$db->trooperid.'Edit" style="display: none;">';

					$j = 0;

					foreach ($days as $key => $value)
					{
					    echo '<p><b>' . $value . '</b></p>';

						$query3 = "SELECT shifts.id, shifts.starttime, shifts.endtime, shifts.troopid AS shiftTroop, shift_trooper.troopid, shift_trooper.shift, shift_trooper.trooperid FROM shifts LEFT JOIN shift_trooper ON shifts.troopid = shift_trooper.troopid WHERE shifts.troopid = '".$db->troopid."' AND shift_trooper.trooperid = '".$db->trooperid."' GROUP BY shifts.id";

						if ($result3 = mysqli_query($conn, $query3))
						{
							while ($db3 = mysqli_fetch_object($result3))
							{
								// The dates
								$readTime1 = date('h:i A', strtotime($db3->starttime));
								$readTime2 = date('h:i A', strtotime($db3->endtime));

								// Our string of choices from databases
								$shiftString = explode(",", $db3->shift);

								// Was this choice picked?
								$pickedChoice = false;

								// loop through and see if checked
								if(!is_null($db3->trooperid) && $db3->trooperid == $db->trooperid)
								{
									for($o = 0; $o <= count($shiftString) - 1; $o += 2)
									{
										// Check the choice
										if($key == $shiftString[$o + 1] && $shiftString[$o] == $db3->id)
										{
											// This is a picked choice
											$pickedChoice = true;
										}
									}
								}

								// Alrerady existing elements to display
								$alreadyExists = [];

								// Ensure data is assigned to this ID
								if($db3->trooperid == $db->trooperid)
								{
									// If picked
									if($pickedChoice)
									{
										echo '
										<input type="checkbox" name="shiftcheckbox'.$db->trooperid.'[]" id="shiftcheckbox" value="'. $db3->id .','.$key.'" CHECKED />'.$readTime1.' - '.$readTime2.'<br />';
									}
									else
									{
										// If not picked
										echo '
										<input type="checkbox" name="shiftcheckbox'.$db->trooperid.'[]" id="shiftcheckbox" value="'. $db3->id .','.$key.'" />'.$readTime1.' - '.$readTime2.'<br />';				
									}

									array_push($alreadyExists, $db3->id);
								}
								else
								{
									// If not in array
									if(!in_array($db3->id, $alreadyExists))
									{
										// If not picked
										echo '
										<input type="checkbox" name="shiftcheckbox'.$db->trooperid.'[]" id="shiftcheckbox" value="'. $db3->id .','.$key.'" />'.$readTime1.' - '.$readTime2.'<br />';			
									}
								}

								// Increment
								$j += 2;
							}
						}
					}

					echo '
						</div>
					</td>

					<td>

						<div id="dateAttended'.$db->trooperid.'">';

					// Query events
					$query2 = "SELECT * FROM events WHERE id = '".$db->troopid."'";
					if ($result2 = mysqli_query($conn, $query2))
					{
						while ($db2 = mysqli_fetch_object($result2))
						{
							// Query database for shift info
							$query3 = "SELECT shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid, shift_trooper.attend FROM shift_trooper WHERE shift_trooper.trooperid = '".$db->trooperid."' AND shift_trooper.troopid = '".$db->troopid."' AND shift_trooper.attend != '-1'";


							$date1 = date('Y-m-d H:i:s', strtotime($db2->dateStart));
							$date2 = date('Y-m-d H:i:s', strtotime($db2->dateEnd));

							$days = getDatesFromRange($date1, $date2);

							$l = 0;
							$data = "";

							if(count($days) > 1)
							{
								if ($result3 = mysqli_query($conn, $query3))
								{
									while ($db3 = mysqli_fetch_object($result3))
									{
										// Read the data, use substr to remove the -1 at the start
										$shiftString = explode(",", substr($db3->attend, 3));

										for($n = 0; $n <= count($shiftString) - 1; $n += 2)
										{
											$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

											$shift = mysqli_fetch_array($shiftGet);

											// Convert times
											$readTime1 = date('h:i A', strtotime($shift[1]));
											$readTime2 = date('h:i A', strtotime($shift[2]));

											$data .= $days[$shiftString[$n + 1]] . '<br />' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';
										}

										// Increment
										$l++;
									}
								}

								// If no data
								if($l == 0)
								{
									$data = "";
								}
							}
							else
							{
								// If single day event and attended
								if($db->attend == 1)
								{
									$data = $days[0];
								}
								else
								{
									$data = "";
								}
							}

							echo $data;
						}
					}

					echo '
						</div>

						<div id="dateAttended'.$db->trooperid.'Edit" style="display: none;">';


					$j = 0;

					// If multiple day event
					if(count($days) > 1)
					{
						foreach ($days as $key => $value)
						{
						    echo '<p><b>' . $value . '</b></p>';

							$query3 = "SELECT shifts.id, shifts.starttime, shifts.endtime, shifts.troopid AS shiftTroop, shift_trooper.attend, shift_trooper.troopid, shift_trooper.shift, shift_trooper.trooperid FROM shifts LEFT JOIN shift_trooper ON shifts.troopid = shift_trooper.troopid WHERE shifts.troopid = '".$db->troopid."' AND shift_trooper.attend != '-1'";

							if ($result3 = mysqli_query($conn, $query3))
							{
								while ($db3 = mysqli_fetch_object($result3))
								{
									// The dates
									$readTime1 = date('h:i A', strtotime($db3->starttime));
									$readTime2 = date('h:i A', strtotime($db3->endtime));

									// Our string of choices from databases
									//$shiftString = explode(",", $db3->shift);
									$shiftString = explode(",", substr($db3->attend, 3));

									// Was this choice picked?
									$pickedChoice = false;

									// loop through and see if checked
									if(!is_null($db3->trooperid) && $db3->trooperid == $db->trooperid)
									{
										for($o = 0; $o <= count($shiftString) - 1; $o += 2)
										{
											// Check the choice
											if($key == $shiftString[$o + 1] && $shiftString[$o] == $db3->id)
											{
												// This is a picked choice
												$pickedChoice = true;
											}
										}
									}

									// Alrerady existing elements to display
									$alreadyExists = [];

									// Ensure data is assigned to this ID
									if($db3->trooperid == $db->trooperid)
									{
										// If picked
										if($pickedChoice)
										{
											echo '
											<input type="checkbox" name="shiftcheckbox2'.$db->trooperid.'[]" id="shiftcheckbox2" value="'. $db3->id .','.$key.'" CHECKED />'.$readTime1.' - '.$readTime2.'<br />';
										}
										else
										{
											// If not picked
											echo '
											<input type="checkbox" name="shiftcheckbox2'.$db->trooperid.'[]" id="shiftcheckbox2" value="'. $db3->id .','.$key.'" />'.$readTime1.' - '.$readTime2.'<br />';				
										}

										array_push($alreadyExists, $db3->id);
									}
									else
									{
										// If not in array
										if(!in_array($db3->id, $alreadyExists))
										{
											// If not picked
											echo '
											<input type="checkbox" name="shiftcheckbox2'.$db->trooperid.'[]" id="shiftcheckbox2" value="'. $db3->id .','.$key.'" />'.$readTime1.' - '.$readTime2.'<br />';			
										}
									}

									// Increment
									$j += 2;
								}
							}
						}
					}
					else
					{
						// If a single day event, let's see if they actually attended before showing
						if($db->attend == 1)
						{
							echo '<p><b>' . $days[0] . '</b></p>';
						}
					}

					echo '
					</td>
				</tr>';

				$i++;
			}
		}

		// If not users assigned
		if($i == 0)
		{
			//echo 'There are no troopers assigned to this troop!';
		}
		else
		{
			echo '
				</table>
				</div>

				<input type="submit" name="removetrooper" id="removetrooper" value="Remove Trooper" />	<input type="submit" name="edittrooper" id="edittrooper" value="Edit Trooper" />
			</form>';

		}

		// Load all users
		$query = "SELECT troopers.id AS troopida, troopers.name AS troopername, event_sign_up.trooperid, event_sign_up.troopid AS troopid FROM troopers LEFT JOIN event_sign_up ON troopers.id = event_sign_up.trooperid WHERE event_sign_up.troopid != '".cleanInput($_POST['eventId'])."' AND troopers.id NOT IN (SELECT event_sign_up.trooperid FROM event_sign_up WHERE event_sign_up.troopid = '".cleanInput($_POST['eventId'])."') OR event_sign_up.troopid IS NULL GROUP BY troopers.id";

		$i = 0;
		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// First add this to make a list
				if($i == 0)
				{
					echo '
					<form action="process.php?do=editevent" method="POST" name="troopRosterFormAdd" id="troopRosterFormAdd">
						<input type="hidden" name="troopid" id="troopid" value="'.cleanInput($_POST['eventId']).'" />

						<p>Select a trooper to add:</p>
						<select name="trooperSelect" id="trooperSelect">';
				}

				// List troopers
				echo '
				<option value="'.$db->troopida.'">'.$db->troopername.'</option>';
				$i++;
			}
		}

		// If no troopers
		if($i == 0)
		{
			echo 'No troopers to add.';
		}
		else
		{
			echo '
				</select>

				<p>What costume are they wearing?</p>
				<select name="costume">
					<option value="null" SELECTED>Please choose an option...</option>';

				$query2 = "SELECT * FROM costumes";
				if ($result2 = mysqli_query($conn, $query2))
				{
					while ($db2 = mysqli_fetch_object($result2))
					{
						echo '
						<option value="'. $db2->id .'">'.$db2->costume.'</option>';
					}
				}

			echo '
				</select>

				<p>What is there backup costume if applicable:</p>

				<select name="costumebackup" id="costumebackup">';

				// Display costumes
				$query2 = "SELECT * FROM costumes ORDER BY costume";
				// Amount of costumes
				$c = 0;
				if ($result2 = mysqli_query($conn, $query2))
				{
					while ($db2 = mysqli_fetch_object($result2))
					{
						// If first select option
						if($c == 0)
						{
							echo '<option value="99999" SELECTED>Select a costume...</option>';
						}

						// Display costume
						echo '<option value="'.$db2->id.'">'.$db2->costume.'</option>';

						$c++;
					}
				}

				echo '
				</select>

				<p>Status</p>
				<select name="status" id="status">
					<option value="null" SELECTED>Please choose an option</option>
					<option value="0">Going</option>
					<option value="1">Stand By</option>
					<option value="2">Tentative</option>
					<option value="3">Attended</option>
					<option value="4">Canceled</option>
					<option value="5">Pending</option>
				</select>

				<div id="reasonBlock" name="reasonBlock" style="display:none;">
					<p>Reason:</p>
					<input type="text" name="reason" id="reason" />
				</div>

				<div id="attendBlock" name="attendBlock" style="display:none;">
					<p>Attended event in the following costume:</p>
					<input type="text" name="attendedcostume" id="attendedcostume" />
				</div>

				<input type="submit" name="submitAddOn" id="submitAddOn" value="Add!" />
			</form>';
		}
	}

	// Event submitted for edit...
	if(isset($_POST['submitEdit']))
	{
		// Load event info
		$query = "SELECT * FROM events WHERE id = '".cleanInput($_POST['eventId'])."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$array = array('id' => $db->id, 'name' => $db->name, 'venue' => $db->venue, 'dateStart' => $db->dateStart, 'dateEnd' => $db->dateEnd, 'website' => $db->website, 'numberOfAttend' => $db->numberOfAttend, 'requestedNumber' => $db->requestedNumber, 'requestedCharacter' => $db->requestedCharacter, 'secureChanging' => $db->secureChanging, 'blasters' => $db->blasters, 'lightsabers' => $db->lightsabers, 'parking' => $db->parking, 'mobility' => $db->mobility, 'amenities' => $db->amenities, 'referred' => $db->referred, 'comments' => $db->comments, 'location' => $db->location, 'label' => $db->label, 'postComment' => $db->postComment, 'notes' => $db->notes, 'limitedEvent' => $db->limitedEvent, 'limitTo' => $db->limitTo, 'limitRebels' => $db->limitRebels, 'limit501st' => $db->limit501st, 'limitMando' => $db->limitMando, 'limitDroid' => $db->limitDroid, 'closed' => $db->closed, 'moneyRaised' => $db->moneyRaised);

				echo json_encode($array);
			}
		}
	}

	// Event edit submitted
	if(isset($_POST['submitEventEdit']))
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
		if($_POST['eventName'] != "" && $_POST['eventVenue'] != "" && $_POST['location'] != "" && $_POST['dateStart'] != "" && $_POST['dateEnd'] != "" && $_POST['numberOfAttend'] != "" && $_POST['requestedNumber'] != "" && $_POST['secure'] != "" && $_POST['blasters'] != "" && $_POST['lightsabers'] != "" && $_POST['parking'] != "null" && $_POST['mobility'] != "null" && $_POST['label'] != "null" && $_POST['limitedEvent'] != "null")
		{
			// Convert date
			$date1 = date('Y-m-d H:i:s', strtotime($_POST['dateStart']));
			$date2 = date('Y-m-d H:i:s', strtotime($_POST['dateEnd']));

			// Load event info - we are going to check to see if the dates have changed
			$query = "SELECT * FROM events WHERE id = '".cleanInput($_POST['eventId'])."'";
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Convert date database values
					$date1DB = date('Y-m-d H:i:s', strtotime($db->dateStart));
					$date2DB = date('Y-m-d H:i:s', strtotime($db->dateEnd));

					if($date1 != $date1DB || $date2 != $date2DB)
					{
						// If dates have been changed, remove users
						// Delete from sign ups - event_sign_up
						$conn->query("DELETE FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'");

						// Delete from sign ups - shift trooper
						$conn->query("DELETE FROM shift_trooper WHERE troopid = '".cleanInput($_POST['eventId'])."'");
					}
				}
			}

			// Query the database
			$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', limitRebels = '".cleanInput($_POST['limitRebels'])."', limit501st = '".cleanInput($_POST['limit501st'])."', limitMando = '".cleanInput($_POST['limitMando'])."', limitDroid = '".cleanInput($_POST['limitDroid'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);

			$array = array('success' => 'true', 'data' => 'Event has been updated!');
			echo json_encode($array);
		}
		else
		{
			$array = array('success' => 'failed', 'data' => '');
			echo json_encode($array);
		}
	}
}

if(isset($_GET['do']) && $_GET['do'] == "signup")
{
	// When we receive a submission for an event sign up...
	if(isset($_POST['submitSignUp']))
	{
		// Prevent bug of getting signed up twice
		$eventCheck = inEvent(cleanInput($_SESSION['id']), cleanInput($_POST['event']));

		if($eventCheck['inTroop'] == 1)
		{
			die("YOU ARE ALREADY IN THIS TROOP!");
		}

		// End prevent bug of getting signed up twice

		// Check to see if this event is full

		// If multiple day event
		if(isset($_POST['shiftcheckbox']))
		{
			// Loop through check boxes to get data
			foreach($_POST['shiftcheckbox'] as $key)
			{
				// Convert to readable
				$shift = explode(",", $key);

				// Run through isEventFull
				if(isEventFull(cleanInput($_POST['event']), cleanInput($_POST['costume']), $shift[0], $shift[1]))
				{
					// Message to users
					$data = "This event is full for the costume type selected.";

					// Send back data
					$array = array('success' => 'failed', 'data' => $data, 'id' => $_SESSION['id']);
					echo json_encode($array);

					// DO NOT CONTINUE
					die("");
				}
			}
		}
		else
		{
			// If single day event
			if(isEventFull(cleanInput($_POST['event']), cleanInput($_POST['costume'])))
			{
				// Message to users
				$data = "This event is full for the costume type selected.";

				// Send back data
				$array = array('success' => 'failed', 'data' => $data, 'id' => $_SESSION['id']);
				echo json_encode($array);

				// DO NOT CONTINUE
				die("");
			}
		}

		// End of check to see if this event is full

		// Query the database
		$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup) VALUES ('".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['status'])."', '".cleanInput($_POST['backupcostume'])."')") or die($conn->error);

		// If a shift is checked
		// The isset prevents an notice error, where it would show if their wasn't a shift to select
		if(isset($_POST['shiftcheckbox']))
		{
			if($_POST['shiftcheckbox'] != "")
			{
				// Query for shift boxes
				$shift = "";

				// Loop through check boxes to get data
				foreach($_POST['shiftcheckbox'] as $key)
				{
					$shift = $shift . ',' . cleanInput($key);
				}

				// Cut first comma out
				$shift = substr($shift, 1);

				// Insert into database
				$conn->query("INSERT INTO shift_trooper (troopid, trooperid, shift) VALUES ('".cleanInput($_POST['event'])."', ".cleanInput($_SESSION['id']).", '".$shift."')") or die($conn->error);
			}
		}

		// Define data variable for below code
		$data = "";

		// Get data to send back - query the event data for the information

		// Start Test Code

		// Query database for event info
		$query = "SELECT * FROM events WHERE id = '".cleanInput($_POST['event'])."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{

				// Query database for roster info
				$query2 = "SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.reason, event_sign_up.attend, event_sign_up.attended_costume, event_sign_up.status, event_sign_up.troopid, troopers.id AS trooperId, troopers.name, troopers.tkid FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".cleanInput($_POST['event'])."' ORDER BY status";
				$i = 0;

				if ($result2 = mysqli_query($conn, $query2))
				{
					while ($db2 = mysqli_fetch_object($result2))
					{
						// Use this for later to determine which select box to show...
						$status = $db2->status;

						// If no events to show...
						if($i == 0)
						{
							$data .= '
							<div style="overflow-x: auto;">
							<table border="1">
							<tr>
								<th>Trooper Name</th>	<th>TKID</th>	<th>Costume</th>	<th>Backup Costume</th>	<th>Status</th>	<th>When</th>
							</tr>';
						}

						$data .= '
						<tr>
							<td><a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a></td>	<td>'.readTKNumber($db2->tkid).'</td>	<td>'.getCostume($db2->costume).'</td>	<td>'.getCostume($db2->costume_backup).'</td>	<td id="'.$db2->trooperId.'Status">'.getStatus($db2->status).'</td>';

						// Query database for shift info
						$query3 = "SELECT shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid FROM shift_trooper WHERE shift_trooper.trooperid = '".$db2->trooperId."' AND shift_trooper.troopid = '".$db2->troopid."'";


						$date1 = date('Y-m-d H:i:s', strtotime($db->dateStart));
						$date2 = date('Y-m-d H:i:s', strtotime($db->dateEnd));

						$days = getDatesFromRange($date1, $date2);

						$l = 0;

						if ($result3 = mysqli_query($conn, $query3))
						{
							while ($db3 = mysqli_fetch_object($result3))
							{
								// Formatting
								if($l == 0)
								{
									$data .= '<td id="when'.$db3->trooperid.'">';
								}

								$shiftString = explode(",", $db3->shift);

								for($n = 0; $n <= count($shiftString) - 1; $n += 2)
								{
									$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

									$shift = mysqli_fetch_array($shiftGet);

									$readTime1 = date('h:i A', strtotime($shift[1]));
									$readTime2 = date('h:i A', strtotime($shift[2]));

									$data .= $days[$shiftString[$n + 1]] . '<br />' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';
								}

								$l++;
							}
						}

						// Formatting
						if($l == 0)
						{
							// Format for multiple days
							if(count($days) > 1)
							{
								$data .= '<td>Canceled</td>';
							}
							else
							{
								$data .= '
								<td>'.$days[0].'</td>';
							}
						}
						else
						{
							$data .= '</td>';
						}

						$data .= '
						</tr>';

						$i++;
					}
				}

				if($i == 0)
				{
					$data .= '
					<b>No troopers have signed up for this event!</b>
					<br />
					<br />';
				}
				else
				{
					$data .= '</table>
					</div>';
				}

				// End Test Code

				// Send back data
				$array = array('success' => 'success', 'data' => $data, 'id' => $_SESSION['id']);
				echo json_encode($array);
			}
		}
	}
}

if(isset($_GET['do']) && $_GET['do'] == "confirmList")
{
	// Confirm from confirm list
	if(isset($_POST['submitConfirmList']))
	{
		// Create arrays
		$list = [];
		$list2 = [];

		// If set to avoid error
		if(isset($_POST['confirmList']))
		{
			$list = $_POST['confirmList'];
		}

		// If set to avoid error
		if(isset($_POST['confirmShift']))
		{
			$list2 = $_POST['confirmShift'];
		}

		if(!empty($list)) 
		{
			$n = count($list);

			for($i = 0; $i < $n; $i++)
			{
				// Query the database
				$conn->query("UPDATE event_sign_up SET attended_costume = '".cleanInput($_POST['costume'])."', attend = '1', status = '3' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'") or die($conn->error);
			}
		}

		if(!empty($list2)) 
		{
			// Get array count
			$n = count($list2);

			for($i = 0; $i < $n; $i++)
			{
				// Make the value readable
				$arraySplit = explode(",", $list2[$i]);

				// Query the shift database
				$conn->query("UPDATE shift_trooper SET costume = CONCAT(costume, ',".$arraySplit[0].",".$arraySplit[1].",".cleanInput($_POST['costume'])."'), attend = CONCAT(attend, ',".$arraySplit[0].",".$arraySplit[1]."') WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);

				// Load shift trooper to get data
				$shiftGet = $conn->query("SELECT * FROM shift_trooper WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);
				$shift = mysqli_fetch_array($shiftGet);

				// Count values in shift
				$shiftCount = floor(count(explode(",", $shift['shift']))/2);
				$attendCount = floor(count(explode(",", substr($shift['attend'], 3)))/2);
				$didNotCount = floor(count(explode(",", substr($shift['didNotAttend'], 3)))/2);

				// Process the data / Did they attend the troop in a multi-troop?
				// Add all troops together
				$allTroops = $attendCount + $didNotCount;

				// All troops accounted for
				if($allTroops == $shiftCount)
				{
					if($attendCount == 0)
					{
						// Query the event sign up database to say did not attend
						$conn->query("UPDATE event_sign_up SET attend = '2', status = '4' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);
					}
					else
					{
						// Query the event sign up database to say did not attend
						$conn->query("UPDATE event_sign_up SET attend = '1', status = '3' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);
					}
				}
			}
		}
	}

	// Delete from confirm list
	if(isset($_POST['submitConfirmListDelete']))
	{
		// Create arrays
		$list = [];
		$list2 = [];

		// If set to avoid error
		if(isset($_POST['confirmList']))
		{
			$list = $_POST['confirmList'];
		}

		// If set to avoid error
		if(isset($_POST['confirmShift']))
		{
			$list2 = $_POST['confirmShift'];
		}

		if(!empty($list)) 
		{
			$n = count($list);

			for($i = 0; $i < $n; $i++)
			{
				// Query the database
				$conn->query("UPDATE event_sign_up SET attend = '2', status = '4' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'") or die($conn->error);
			}
		}

		if(!empty($list2)) 
		{
			// Get array count
			$n = count($list2);

			for($i = 0; $i < $n; $i++)
			{
				// Make the value readable
				$arraySplit = explode(",", $list2[$i]);

				// Query the shift database add did not attend to database
				$conn->query("UPDATE shift_trooper SET didNotAttend = CONCAT(didNotAttend, ',".$arraySplit[0].",".$arraySplit[1]."') WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);

				// Load shift trooper to get data
				$shiftGet = $conn->query("SELECT * FROM shift_trooper WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);
				$shift = mysqli_fetch_array($shiftGet);

				// Count values in shift
				$shiftCount = floor(count(explode(",", $shift['shift']))/2);
				$attendCount = floor(count(explode(",", substr($shift['attend'], 3)))/2);
				$didNotCount = floor(count(explode(",", substr($shift['didNotAttend'], 3)))/2);

				// Process the data / Did they attend the troop in a multi-troop?
				// Add all troops together
				$allTroops = $attendCount + $didNotCount;

				// All troops accounted for
				if($allTroops == $shiftCount)
				{
					if($attendCount == 0)
					{
						// Query the event sign up database to say did not attend
						$conn->query("UPDATE event_sign_up SET attend = '2', status = '4' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);
					}
					else
					{
						// Query the event sign up database to say did not attend
						$conn->query("UPDATE event_sign_up SET attend = '1', status = '3' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($arraySplit[2])."'") or die($conn->error);
					}
				}
			}
		}
	}

	// Send back AJAX data

	// What we are going to send back
	$dataMain = "";

	// Load events that need confirmation
	$query = "SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd < NOW() AND attend = 0 AND events.closed = 1";

	if ($result = mysqli_query($conn, $query))
	{
		// Number of results total
		$i = 0;

		// Number of results within shift_trooper
		$l = 0;

		// Count how many have data from user
		$m = 0;

		while ($db = mysqli_fetch_object($result))
		{
			// Load shifts if applicable
			// Query database for shift info
			$query3 = "SELECT shift_trooper.id, shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid, shift_trooper.attend, shift_trooper.didNotAttend FROM shift_trooper WHERE shift_trooper.trooperid = '".$_SESSION['id']."' AND shift_trooper.troopid = '".$db->eventId."'";


			$date1 = date('Y-m-d H:i:s', strtotime($db->dateStart));
			$date2 = date('Y-m-d H:i:s', strtotime($db->dateEnd));

			$days = getDatesFromRange($date1, $date2);

			$data = "";

			if($result3 = mysqli_query($conn, $query3))
			{
				while ($db3 = mysqli_fetch_object($result3))
				{
					// Array of shifts
					$shiftString = explode(",", $db3->shift);

					for($n = 0; $n <= count($shiftString) - 1; $n += 2)
					{
						$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

						$shift = mysqli_fetch_array($shiftGet);

						// The dates
						$readTime1 = date('h:i A', strtotime($shift[1]));
						$readTime2 = date('h:i A', strtotime($shift[2]));

						// This may be able to be deleted - keep this until further notice - 07/26/2020
						//$data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $days[$shiftString[$n + 1]] . '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="confirmShift[]" id="confirmShift_'.$shift[0].'" value="'.$shift[0].','.str_replace(" ", "", $shiftString[$n + 1]).','.$db->eventId.'" /> ' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';

						$checkAttend = explode(",", substr($db3->attend, 3));
						$checkDidNotAttend = explode(",", substr($db3->didNotAttend, 3));

						// Check 1 - check attend data
						$check1 = true;
						// Check 2 - check did not attend data
						$check2 = true;

						// Do not loop through did not attend, if no data
						if(count($checkAttend) > 1)
						{
							// Loop through attend
							for($j = 0; $j <= count($checkAttend) - 1; $j += 2)
							{
								// Inside shift
								if($checkAttend[$j] == $shiftString[$n] && $checkAttend[$j + 1] == $shiftString[$n + 1])
								{
									$check1 = false;
								}
							}
						}

						// Do not loop through did not attend, if no data
						if(count($checkDidNotAttend) > 1)
						{
							// Loop through did not attend
							for($j = 0; $j <= count($checkDidNotAttend) - 1; $j += 2)
							{
								// Inside shift
								if($checkDidNotAttend[$j] == $shiftString[$n] && $checkDidNotAttend[$j + 1] == $shiftString[$n + 1])
								{
									$check2 = false;
								}
							}
						}

						if($check1 && $check2)
						{
							$data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $days[$shiftString[$n + 1]] . '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="confirmShift[]" id="confirmShift_'.$shift[0].'" value="'.$shift[0].','.str_replace(" ", "", $shiftString[$n + 1]).','.$db->eventId.'" /> ' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';

							$m++;
						}
					}

					$l++;
				}
			}

			// If a shift exists to attest to
			$i++;

			// If a multiple day troop
			if($l > 0)
			{
				// If has data
				if($m > 0)
				{
					$dataMain .= '
					<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">'.$db->name.'<br /><br />' . $data;
				}
			}
			else
			{
				// If not a multiple day troop
				$dataMain .= '
				<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">
					<input type="checkbox" name="confirmList[]" id="confirmList_'.$db->eventId.'" value="'.$db->eventId.'" /> '.$db->name.'<br /><br />';
			}

			// End load shifts

			$dataMain .= '
			</div>';
		}
	}

	// Send back data
	$array = array('success' => 'success', 'data' => $dataMain, 'id' => $_SESSION['id']);
	echo json_encode($array);

	// End send back AJAX data
}

?>
