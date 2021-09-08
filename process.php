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

/******************** MODIFY SIGN UP FROM EVENT PAGE *******************************/

if(isset($_GET['do']) && $_GET['do'] == "modifysignup" && loggedIn())
{
	// Update SQL
	$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costume'])."', costume_backup = '".cleanInput($_POST['costume_backup'])."', status = '".cleanInput($_POST['status'])."' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($_POST['troopid'])."'");
}

/*********************** UNDO CANCEL *********************************************/

if(isset($_GET['do']) && $_GET['do'] == "undocancel" && loggedIn())
{
	// Prevent if troop full
	$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".cleanInput($_GET['troopid'])."' AND status != '4'");
	
	if($getNumOfTroopers->num_rows < $db->limitTotal)
	{
		// Message for user in their alert
		$message = "Please fill in the form fields.";
		
		$costume = '
		<select name="modifysignupFormCostume2" id="modifysignupFormCostume2">
			<option value="0" SELECTED>Please select a costume...</option>';

		$query3 = "SELECT * FROM costumes ORDER BY costume";
		if ($result3 = mysqli_query($conn, $query3))
		{
			while ($db3 = mysqli_fetch_object($result3))
			{
				// Default - Do not pre-select
				$costume .= '
				<option value="'. $db3->id .'">'.$db3->costume.'</option>';
			}
		}

		$costume .= '
		</select>';
		
		$backup = '
		<select name="modiftybackupcostumeForm2" id="modiftybackupcostumeForm2">
			<option value="99999" SELECTED>N/A</option>';

		// Display costumes
		$query3 = "SELECT * FROM costumes ORDER BY costume";
		
		// Amount of costumes
		if ($result3 = mysqli_query($conn, $query3))
		{
			while ($db3 = mysqli_fetch_object($result3))
			{
				$backup .= '
				<option value="'.$db3->id.'">'.$db3->costume.'</option>';
			}
		}

		$backup .= '
		</select>';
		
		if($_POST['limitedevent'] != 1)
		{
			$status = '
			<select name="modifysignupStatusForm2" id="modifysignupStatusForm2">
				<option value="-1" SELECTED>Please select an option...</option>
				<option value="0">I\'ll be there!</option>
				<option value="1">Tentative</option>
			</select>';
		}
		else
		{
			$status = '
			(Pending Command Staff Approval)';								
		}
		
		// Send JSON data
		$array = array(array('data' => $message, 'costume' => $costume, 'backup' => $backup, 'status' => $status));
		echo json_encode($array);
	}
}

// UNDO Cancel Save

if(isset($_GET['do']) && isset($_GET['do2']) && $_GET['do'] == "undocancel" && $_GET['do2'] == "undofinish" && loggedIn())
{
	// Prevent if troop full
	$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".cleanInput($_GET['troopid'])."' AND status != '4'");
	
	if($getNumOfTroopers->num_rows < $db->limitTotal)
	{
		// Update SQL
		$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costume'])."', costume_backup = '".cleanInput($_POST['costume_backup'])."', status = '".cleanInput($_POST['status'])."' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($_POST['troopid'])."'");
	}
}

/************************* Comments **************************************/

// Enter comment into database
if(isset($_GET['do']) && $_GET['do'] == "postcomment" && isset($_POST['submitComment']) && loggedIn())
{
	if(strlen($_POST['comment']) > 0 && ($_POST['important'] == 0 || $_POST['important'] == 1))
	{
		// Query the database
		$conn->query("INSERT INTO comments (troopid, trooperid, comment, important) VALUES ('".cleanInput($_POST['eventId'])."', '".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['comment'])."', '".cleanInput($_POST['important'])."')") or die($conn->error);

		// Load comments for return data
		$query = "SELECT * FROM comments WHERE troopid = '".cleanInput($_POST['eventId'])."' ORDER BY posted DESC";

		// Count comments
		$i = 0;

		// Return data
		$data = "";

		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$data .= '
				<div style="overflow-x: auto;" style="text-align: center;">
				<table border="1" name="comment_'.$db->id.'" id="comment_'.$db->id.'">';

				$data .= '
				<tr>
					<td><a href="index.php?profile='.$db->trooperid.'">'.getName($db->trooperid).' - '.getTKNumber($db->trooperid).'</a></td>
				</tr>';

				if(isAdmin())
				{
					$data .= '
					<tr>
						<td><a href="#" id="deleteComment_'.$db->id.'" name="'.$db->id.'" class="button">Delete Comment</a></td>
					</tr>';
				}

				$data .= '
				<tr>
					<td>'.$db->posted.'</td>
				</tr>

				<tr>
					<td>'.isImportant($db->important, $db->comment).'</td>
				</tr>

				</table>
				</div>

				<br />';

				// Increment
				$i++;
			}
		}

		if($i == 0)
		{
			$data .= '
			<br />
			<b>No comments to display.</b>';
		}

		$array = array('data' => $data);
		echo json_encode($array);
	}
}


/************************ Costumes ***************************************/
// Costume management - add, delete, edit
if(isset($_GET['do']) && $_GET['do'] == "managecostumes" && loggedIn() && isAdmin())
{
	// Costume submitted for deletion...
	if(isset($_POST['submitDeleteCostume']))
	{
		// Query the database
		$conn->query("DELETE FROM costumes WHERE id = '".cleanInput($_POST['costumeID'])."'");
	}
	
	// Add costume...
	if(isset($_POST['addCostumeButton']))
	{
		$message = "Costume added";

		// Check if has value
		if(cleanInput($_POST['costumeName']) == "")
		{
			$message = "Costume must have a name.";
		}
		else
		{
			// Query the database
			$conn->query("INSERT INTO costumes (costume, era, club) VALUES ('".cleanInput($_POST['costumeName'])."', '".cleanInput($_POST['costumeEra'])."', '".cleanInput($_POST['costumeClub'])."')");
			$last_id = $conn->insert_id;
		}
		
		$returnMessage = '
		<h3>Edit Costume</h3>';

		// Get data
		$query = "SELECT * FROM costumes ORDER BY costume";

		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Formatting
				if($i == 0)
				{
					$returnMessage .= '
					<form action="process.php?do=managecostumes" method="POST" name="costumeEditForm" id="costumeEditForm">

					<select name="costumeIDEdit" id="costumeIDEdit">

						<option value="0" SELECTED>Please select a costume...</option>';
				}

				$returnMessage .= '
				<option value="'.$db->id.'" costumeName="'.$db->costume.'" costumeID="'.$db->id.'" costumeEra="'.$db->era.'" costumeClub="'.$db->club.'">'.$db->costume.'</option>';

				// Increment
				$i++;
			}
		}

		if($i == 0)
		{
			$returnMessage .= 'No costumes to display.';
		}
		else
		{
			$returnMessage .= '
			</select>

			<div id="editCostumeList" name="editCostumeList" style="display: none;">

			<b>Costume Name:</b></br />
			<input type="text" name="costumeNameEdit" id="costumeNameEdit" />
			
			<b>Costume Era:</b></br />
			<select name="costumeEraEdit" id="costumeEraEdit">
				<option value="0">Prequel</option>
				<option value="1" SELECTED>Original</option>
				<option value="2">Sequel</option>
				<option value="3">Expanded</option>
				<option value="4">All</option>
			</select>
			
			<b>Costume Club:</b></br />
			<select name="costumeClubEdit" id="costumeClubEdit">
				<option value="0" SELECTED>501st Legion</option>
				<option value="1">Rebel Legion</option>
				<option value="2">Mando Mercs</option>
				<option value="3">Droid Builders</option>
				<option value="4">Rebel + 501st</option>
				<option value="5">Other</option>
				<option value="6">All</option>
			</select>

			<input type="submit" name="submitEditCostume" id="submitEditCostume" value="Edit Costume" />

			</div>
			</form>';
		}
		
		$returnMessage .= '
		<br />
		<hr />
		<br />
		
		<h3>Delete Costume</h3>';

		// Get data
		$query = "SELECT * FROM costumes ORDER BY costume";

		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Formatting
				if($i == 0)
				{
					$returnMessage .= '
					<form action="process.php?do=managecostumes" method="POST" name="costumeDeleteForm" id="costumeDeleteForm">

					<select name="costumeID" id="costumeID">';
				}

				$returnMessage .= '<option value="'.$db->id.'">'.$db->costume.'</option>';

				// Increment
				$i++;
			}
		}

		if($i == 0)
		{
			$returnMessage .= 'No costumes to display.';
		}
		else
		{
			$returnMessage .= '
			<input type="submit" name="submitDeleteCostume" id="submitDeleteCostume" value="Delete Costume" />
			</form>';
		}
		
		$array = array(array('message' => $message, 'id' => $last_id, 'result' => $returnMessage));
		echo json_encode($array);
	}
	
	// Edit costume
	if(isset($_POST['submitEditCostume']))
	{
		// Query the database
		$conn->query("UPDATE costumes SET costume = '".cleanInput($_POST['costumeNameEdit'])."', era = '".cleanInput($_POST['costumeEraEdit'])."', club = '".cleanInput($_POST['costumeClubEdit'])."' WHERE id = '".cleanInput($_POST['costumeIDEdit'])."'");
	}
}

/************************ AWARDS ***************************************/
// Awards to troopers
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
			$last_id = $conn->insert_id;
		}
		
		$returnMessage = '<br /><hr /><br /><h3>Edit Award</h3>';

		// Get data
		$query = "SELECT * FROM awards ORDER BY title";

		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Formatting
				if($i == 0)
				{
					$returnMessage .= '
					<form action="process.php?do=assignawards" method="POST" name="awardEdit" id="awardEdit">

					<select name="awardIDEdit" id="awardIDEdit">

						<option value="0" SELECTED>Please select an award...</option>';
				}

				$returnMessage .= '<option value="'.$db->id.'" awardTitle="'.$db->title.'" awardID="'.$db->id.'" awardImage="'.$db->icon.'">'.$db->title.'</option>';

				// Increment
				$i++;
			}
		}

		if($i == 0)
		{
			$returnMessage .= 'No awards to display.';
		}
		else
		{
			$returnMessage .= '
			</select>

			<div id="editAwardList" name="editAwardList" style="display: none;">

			<b>Award Title:</b><br />
			<input type="text" name="editAwardTitle" id="editAwardTitle" />

			<br /><b>Award Image:</b><br />
			<input type="text" name="editAwardImage" id="editAwardImage" />

			<br />

			<input type="submit" name="submitEditAward" id="submitEditAward" value="Edit Award" />

			</div>
			</form>';
		}

		$returnMessage .= '<br /><hr /><br /><h3>Delete Award</h3>';

		// Get data
		$query = "SELECT * FROM awards ORDER BY title";

		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Formatting
				if($i == 0)
				{
					$returnMessage .= '
					<form action="process.php?do=assignawards" method="POST" name="awardUserDelete" id="awardUserDelete">

					<select name="awardID" id="awardID">';
				}

				$returnMessage .= '<option value="'.$db->id.'">'.$db->title.'</option>';

				// Increment
				$i++;
			}
		}

		if($i == 0)
		{
			$returnMessage .= 'No awards to display.';
		}
		else
		{
			$returnMessage .= '
			<input type="submit" name="submitDeleteAward" id="submitDeleteAward" value="Delete Award" />
			</form>';
		}
		
		$returnMessage2 = '';
		
		// Get data
		$query = "SELECT * FROM troopers WHERE approved = 1 ORDER BY name";

		// Amount of users
		$i = 0;
		$getId = 0;

		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Formatting
				if($i == 0)
				{
					$getId = $db->id;

					$returnMessage2 .= '
					<form action="process.php?do=assignawards" method="POST" name="awardUser" id="awardUser">

					<select name="userIDAward" id="userIDAward">';
				}

					$returnMessage2 .= '<option value="'.$db->id.'">'.$db->name.'</option>';

				// Increment
				$i++;
			}
		}

		// If no events
		if($i == 0)
		{
				$returnMessage2 .= 'There are no troopers to display.';
		}
		else
		{
				$returnMessage2 .= '
			</select>

			<br /><br />';

			// Get data
			$query2 = "SELECT * FROM awards ORDER BY title";

			// Amount of awards
			$j = 0;

			if ($result2 = mysqli_query($conn, $query2))
			{
				while ($db = mysqli_fetch_object($result2))
				{
					// Formatting
					if($j == 0)
					{
						$getId = $db->id;

							$returnMessage2 .= '<select id="awardIDAssign" name="awardIDAssign">';
					}

						$returnMessage2 .= '<option value="'.$db->id.'">'.$db->title.'</option>';

					// Increment $j
					$j++;
				}
			}

			// If awards exist
			if($j > 0)
			{
					$returnMessage2 .= '
				</select>

				<input type="submit" name="award" id="award" value="Assign!" />';
			}
			else
			{
					$returnMessage2 .= 'No awards to display.';
			}
		}

		$returnMessage2 .= '</form>';

		$array = array(array('message' => $message, 'id' => $last_id, 'result' => $returnMessage, 'result2' => $returnMessage2));
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
				$array = array('name' => $db->name, 'email' => $db->email, 'forum' => $db->forum_id, 'phone' => $db->phone, 'squad' => $db->squad, 'tkid' => $db->tkid);
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
		// Query for user info
		$query = "SELECT * FROM troopers WHERE id = '".cleanInput($_POST['userID2'])."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Send e-mail
				try
				{
					sendEmail($db->email, $db->name, "Florida Garrison Troop Tracker: Account Denied", "Your account has been denied. Please confirm that all your information is correct and try again. If you continue to have issues, please reach out on the boards.");
				}
				catch(Exception $e)
				{
					// Nothing
				}
			}
		}
		
		// Query the database
		$conn->query("DELETE FROM troopers WHERE id = '".cleanInput($_POST['userID2'])."'");
	}

	if(isset($_POST['submitApproveUser']))
	{
		// Query for user info
		$query = "SELECT * FROM troopers WHERE id = '".cleanInput($_POST['userID2'])."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				try
				{
					// Send e-mail
					sendEmail($db->email, $db->name, "Florida Garrison Troop Tracker: Account Approved", "Your account has been approved!");
				}
				catch(Exception $e)
				{
					// Nothing
				}
			}
		}
		
		// Query the database
		$conn->query("UPDATE troopers SET approved = 1 WHERE id = '".cleanInput($_POST['userID2'])."'");
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
				$array = array('id' => $db->id, 'name' => $db->name, 'email' => $db->email, 'phone' => $db->phone, 'squad' => $db->squad, 'permissions' => $db->permissions, 'tkid' => $db->tkid, 'forumid' => $db->forum_id);

				echo json_encode($array);
			}
		}
	}

	// User edit submitted
	if(isset($_POST['submitUserEdit']))
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
		if(cleanInput($_POST['user']) != "" && cleanInput($_POST['email']) != "" && cleanInput($_POST['squad']) != "" && cleanInput($_POST['permissions']) != "" && cleanInput($_POST['tkid']) != "" && cleanInput($_POST['forumid']) != "")
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
				$conn->query("UPDATE troopers SET name = '".cleanInput($_POST['user'])."', email =  '".cleanInput($_POST['email'])."', phone = '".cleanInput(cleanInput($_POST['phone']))."', squad = '".cleanInput($_POST['squad'])."', permissions = '".cleanInput($_POST['permissions'])."', tkid = '".cleanInput($_POST['tkid'])."', forum_id = '".cleanInput($_POST['forumid'])."' WHERE id = '".cleanInput($_POST['userIDE'])."'") or die($conn->error);

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
		if(cleanInput($_POST['name']) != "" && cleanInput($_POST['email']) != "" && cleanInput($_POST['squad']) != "" && cleanInput($_POST['permissions']) != "" && cleanInput($_POST['tkid']) != "" && cleanInput($_POST['password']) != "")
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
				$conn->query("INSERT INTO troopers (name, email, forum_id, phone, squad, permissions, tkid, password, approved) VALUES ('".cleanInput($_POST['name'])."', '".cleanInput($_POST['email'])."', '".cleanInput($_POST['forumid'])."', '".cleanInput($_POST['phone'])."', '".cleanInput($_POST['squad'])."', '".cleanInput($_POST['permissions'])."', '".cleanInput($_POST['tkid'])."', '".cleanInput(md5($_POST['password']))."', 1)");

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
			
			if(cleanInput($_POST['forumid']) == "")
			{
				$failed = true;
				echo '<li>Please enter your FL 501st Forum Username.</li>';
			}

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
				echo '<li>TKID is taken. If you have troops on the old troop tracker, <a href="index.php?action=setup">click here to request access</a>.</li>';
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
				$conn->query("INSERT INTO troopers (name, tkid, email, forum_id, phone, squad, password) VALUES ('".cleanInput($_POST['name'])."', '".floatval($tkid)."', '".cleanInput($_POST['email'])."', '".cleanInput($_POST['forumid'])."', '".cleanInput($_POST['phone'])."', '".cleanInput($_POST['squad'])."', '".md5(cleanInput($_POST['password']))."')") or die($conn->error);
				echo '<li>Request submitted! You will receive an e-mail when your request is approved or denied.</li>';
			}

			echo '</ul>';
		}
	}
}

// Get Location Button
if(isset($_GET['do']) && $_GET['do'] == "getlocation" && loggedIn() && isAdmin())
{
	if(isset($_POST['location']) && $_POST['location'] != "")
	{
		$array = array('squad' => getSquad($_POST['location']));
		echo json_encode($array);
	}
}

// Edit Settings
if(isset($_GET['do']) && $_GET['do'] == "changesettings" && loggedIn() && isAdmin())
{
	$query = "SELECT * FROM settings LIMIT 1";
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Close site button pressed
			if(isset($_POST['submitCloseSite']))
			{
				// If site closed, show button
				if($db->siteclosed == 0)
				{
					// Close website button
					$conn->query("UPDATE settings SET siteclosed = '1'");
				}
				else
				{
					// Open website button
					$conn->query("UPDATE settings SET siteclosed = '0'");
				}
			}
			
			// Close site button pressed
			if(isset($_POST['submitCloseSignUps']))
			{
				// If sign up closed, show button
				if($db->signupclosed == 0)
				{
					// Close sign up button
					$conn->query("UPDATE settings SET signupclosed = '1'");
				}
				else
				{
					// Open sign up button
					$conn->query("UPDATE settings SET signupclosed = '0'");
				}
			}
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
			// Clean date input
			$date1 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['dateStart'])));
			$date2 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['dateEnd'])));
			
			// Query the database
			$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limitRebels, limit501st, limitMando, limitDroid, limitTotal, squad) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".cleanInput($date1)."', '".cleanInput($date2)."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limitRebels'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitMando'])."', '".cleanInput($_POST['limitDroid'])."', '".cleanInput($_POST['limitTotal'])."', '".cleanInput($_POST['squadm'])."')") or die($conn->error);

			$array = array('success' => 'success', 'data' => 'Event created!');
			echo json_encode($array);

			// Event ID - Last insert from database
			$eventId = $conn->insert_id;
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

		// Send back data
		$array = array('success' => 'success', 'id' => $_SESSION['id']);
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
	}

	// Event submitted for cancelation...
	if(isset($_POST['submitCancel']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '2' WHERE id = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - event_sign_up
		$conn->query("DELETE FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'");
	}

	// Event submitted for completion...
	if(isset($_POST['submitFinish']))
	{
		// Query the database
		$conn->query("UPDATE events SET moneyRaised = '".cleanInput($_POST['charity'])."', closed = '1' WHERE id = '".cleanInput($_POST['eventId'])."'");
	}
	
	// Event submitted for open...
	if(isset($_POST['submitOpen']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '0' WHERE id = '".cleanInput($_POST['eventId'])."'");
	}
	
	// Event submitted for completion...
	if(isset($_POST['submitCharity']))
	{
		// Query the database
		$conn->query("UPDATE events SET moneyRaised = '".cleanInput($_POST['charity'])."' WHERE id = '".cleanInput($_POST['eventId'])."'");
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
								<th>Selection</th>	<th>Trooper TKID</td>	<th>Trooper Costume</th>	<th>Trooper Backup Costume</th>	<th>Trooper Status</th>	<th>Trooper Comment</th>	<th>Trooper Attended</th>	<th>Attended With</th>';
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
						<div name="backup1'.$db->trooperid.'" id="backup1'.$db->trooperid.'">'.ifEmpty(getCostume($db->costume_backup), "N/A").'</div>
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
						<div name="reason1'.$db->trooperid.'" id="reason1'.$db->trooperid.'">'.ifEmpty($db->reason, "None").'</div>
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
						<div name="attendcostume1'.$db->trooperid.'" id="attendcostume1'.$db->trooperid.'">'.ifEmpty(getCostume($db->attended_costume), "Not Submitted").'</div>
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
		$query = "SELECT troopers.id AS troopida, troopers.name AS troopername, troopers.tkid, event_sign_up.trooperid, event_sign_up.troopid AS troopid FROM troopers LEFT JOIN event_sign_up ON troopers.id = event_sign_up.trooperid WHERE event_sign_up.troopid != '".cleanInput($_POST['eventId'])."' AND troopers.id NOT IN (SELECT event_sign_up.trooperid FROM event_sign_up WHERE event_sign_up.troopid = '".cleanInput($_POST['eventId'])."') OR event_sign_up.troopid IS NULL GROUP BY troopers.id ORDER BY troopers.name";

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
				<option value="'.$db->troopida.'">'.$db->troopername.' - '.readTKNumber($db->tkid).'</option>';
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

				$query2 = "SELECT * FROM costumes ORDER BY costume";
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
				$array = array('id' => $db->id, 'name' => $db->name, 'venue' => $db->venue, 'dateStart' => $db->dateStart, 'dateEnd' => $db->dateEnd, 'website' => $db->website, 'numberOfAttend' => $db->numberOfAttend, 'requestedNumber' => $db->requestedNumber, 'requestedCharacter' => $db->requestedCharacter, 'secureChanging' => $db->secureChanging, 'blasters' => $db->blasters, 'lightsabers' => $db->lightsabers, 'parking' => $db->parking, 'mobility' => $db->mobility, 'amenities' => $db->amenities, 'referred' => $db->referred, 'comments' => $db->comments, 'location' => $db->location, 'squad' => $db->squad, 'label' => $db->label, 'postComment' => $db->postComment, 'notes' => $db->notes, 'limitedEvent' => $db->limitedEvent, 'limitTo' => $db->limitTo, 'limitRebels' => $db->limitRebels, 'limit501st' => $db->limit501st, 'limitMando' => $db->limitMando, 'limitDroid' => $db->limitDroid, 'limitTotal' => $db->limitTotal, 'closed' => $db->closed, 'moneyRaised' => $db->moneyRaised);

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

			// Query the database
			$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', limitRebels = '".cleanInput($_POST['limitRebels'])."', limit501st = '".cleanInput($_POST['limit501st'])."', limitMando = '".cleanInput($_POST['limitMando'])."', limitDroid = '".cleanInput($_POST['limitDroid'])."', limitTotal = '".cleanInput($_POST['limitTotal'])."'  WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);

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
		
		// Check to see if this event is limited to an era
		if(eraCheck(cleanInput($_POST['event']), cleanInput($_POST['costume'])))
		{
			// Message to users
			$data = "This costume is not allowed for this event.";

			// Send back data
			$array = array('success' => 'failed', 'data' => $data, 'id' => $_SESSION['id']);
			echo json_encode($array);

			// DO NOT CONTINUE
			die("");
		}

		// End of check to see if this event is full

		// Query the database
		$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup) VALUES ('".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['status'])."', '".cleanInput($_POST['backupcostume'])."')") or die($conn->error);

		// Define data variable for below code
		$data = "";

		// Get data to send back - query the event data for the information

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
							<form action="process.php?do=modifysignup" method="POST" name="modifysignupForm" id="modifysignupForm">
							
							<!-- Hidden variables -->
							<input type="hidden" name="modifysignupTroopIdForm" id="modifysignupTroopIdForm" value="'.$db->id.'" />
							<input type="hidden" name="limitedEventCancel" id="limitedEventCancel" value="'.$db->limitedEvent.'" />
							<input type="hidden" name="troopidC" id="troopidC" value="'.strip_tags(addslashes($_POST['event'])).'" />
							<input type="hidden" name="myId" id="myId" value="'.strip_tags(addslashes($_SESSION['id'])).'" />

							<div style="overflow-x: auto;">
							<table border="1">
							<tr>
								<th>Trooper Name</th>	<th>TKID</th>	<th>Costume</th>	<th>Backup Costume</th>	<th>Status</th>
							</tr>';
						}

						// Allow for users to edit their status from the event, and make sure the event is not closed, and the user did not cancel
						if(loggedIn() && $db2->trooperId == $_SESSION['id'] && $db->closed == 0 && $db2->status != 4)
						{
							$data .= '
							<tr>
								<td>
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>
								</td>
									
								<td>
									'.readTKNumber($db2->tkid).'
								</td>
								
								<td name="trooperRosterCostume" id="trooperRosterCostume">
									<select name="modifysignupFormCostume2" id="modifysignupFormCostume2">';

									$query3 = "SELECT * FROM costumes ORDER BY costume";
									if ($result3 = mysqli_query($conn, $query3))
									{
										while ($db3 = mysqli_fetch_object($result3))
										{
											if($db2->costume == $db3->id)
											{
												// If this is the selected costume, make it selected
												$data .= '
												<option value="'. $db3->id .'" SELECTED>'.$db3->costume.'</option>';
											}
											else
											{
												// Default
												$data .= '
												<option value="'. $db3->id .'">'.$db3->costume.'</option>';
											}
										}
									}

									$data .= '
									</select>
								</td>
								
								<td name="trooperRosterBackup" id="trooperRosterBackup">
									<select name="modiftybackupcostumeForm2" id="modiftybackupcostumeForm2">';

									// Display costumes
									$query3 = "SELECT * FROM costumes ORDER BY costume";
									
									// Count results
									$c = 0;
									
									// Amount of costumes
									if ($result3 = mysqli_query($conn, $query3))
									{
										while ($db3 = mysqli_fetch_object($result3))
										{
											// If costume set to backup and first result
											if($db2->costume_backup == 99999 && $c == 0)
											{
												$data .= '
												<option value="99999" SELECTED>N/A</option>';
											}
											// Make sure this is a first result otherwise
											else if($c == 0)
											{
												$data .= '
												<option value="99999">N/A</option>';
											}
											// If a costume matches
											else if($db2->costume_backup == $db3->id)
											{
												$data .= '
												<option value="'.$db3->id.'" SELECTED>'.$db3->costume.'</option>';
											}
											// Start showing costumes
											else
											{
												$data .= '
												<option value="'.$db3->id.'">'.$db3->costume.'</option>';
											}
											
											// Increment
											$c++;
										}
									}

									$data .= '
									</select>
								</td>
								
								<td id="'.$db2->trooperId.'Status">
								<div name="trooperRosterStatus2" id="trooperRosterStatus2">';
								
									if($db->limitedEvent != 1)
									{
										$data .= '
										<select name="modifysignupStatusForm" id="modifysignupStatusForm">
											<option value="0" '.echoSelect(0, $db2->status).'>I\'ll be there!</option>
											<option value="1" '.echoSelect(1, $db2->status).'>Tentative</option>
										</select>';
									}
									else
									{
										$data .= '
										(Pending Command Staff Approval)';								
									}

								$data .= '
								</div>
								</td>
							</tr>';
						}
						// If this is the user, and the user canceled, allow to be edited
						else if(loggedIn() && $db2->trooperId == $_SESSION['id'] && $db->closed == 0 && $db2->status == 4)
						{
							$data .= '
							<tr>
								<td>
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>
								</td>
									
								<td>
									'.readTKNumber($db2->tkid).'
								</td>
								
								<td name="trooperRosterCostume" id="trooperRosterCostume">
									'.getCostume($db2->costume).'
								</td>
								
								<td name="trooperRosterBackup" id="trooperRosterBackup">
									'.ifEmpty(getCostume($db2->costume_backup), "N/A").'
								</td>
								
								<td id="'.$db2->trooperId.'Status">
								<div name="trooperRosterStatus" id="trooperRosterStatus">
									'.getStatus($db2->status).'
								</div>
								</td>
							</tr>';
						}
						else
						{
							// If a user other than the current user
							$data .= '
							<tr>
								<td>
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>
								</td>
									
								<td>
									'.readTKNumber($db2->tkid).'
								</td>
								
								<td>
									'.getCostume($db2->costume).'
								</td>
								
								<td>
									'.ifEmpty(getCostume($db2->costume_backup), "N/A").'
								</td>
								
								<td id="'.$db2->trooperId.'Status">
									'.getStatus($db2->status).'
								</td>
							</tr>';
						}

						/*$data .= '
						<tr>
							<td><a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a></td>	<td>'.readTKNumber($db2->tkid).'</td>	<td>'.getCostume($db2->costume).'</td>	<td>'.getCostume($db2->costume_backup).'</td>	<td id="'.$db2->trooperId.'Status">'.getStatus($db2->status).'
						</td>
						
						</tr>';*/

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
					</div>
					</form>';
				}

				$data .= '
				<div name="signeduparea" id="signeduparea">
					<p><b>You are signed up for this troop!</b></p>

					<form action="index.php" method="POST" name="cancelForm" id="cancelForm">
						<p>Reason why you are canceling:</p>
						<input type="text" name="cancelReason" id="cancelReason" />
						<input type="submit" name="submitCancelTroop" id="submitCancelTroop" value="Cancel Troop" />
					</form>
				</div>';

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

		// If set to avoid error
		if(isset($_POST['confirmList']))
		{
			$list = $_POST['confirmList'];
		}

		if(!empty($list)) 
		{
			$n = count($list);

			for($i = 0; $i < $n; $i++)
			{
				// Query the database
				$conn->query("UPDATE event_sign_up SET attended_costume = '".cleanInput($_POST['costume'])."', attend = '1', status = '3' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'") or die($conn->error);
			}
			
			// Notify how many troops did a trooper attend
			$trooperCount_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE trooperid = '".$_SESSION['id']."' AND attend = '1'") or die($conn->error);
			$count = $trooperCount_get->fetch_row();
			
			if($count[0] == 1 || $count[0] == 10 || $count[0] == 25 || $count[0] == 50 || $count[0] == 75 || $count[0] == 100 || $count[0] == 150 || $count[0] == 200 || $count[0] == 250 || $count[0] == 300 || $count[0] == 400 || $count[0] == 500 || $count[0] == 501)
			{
				$conn->query("INSERT INTO notifications (message, trooperid) VALUES ('".getName($_SESSION['id'])." now has ".$count[0]." troop(s)', '".$_SESSION['id']."')");
			}
		}
	}

	// Delete from confirm list
	if(isset($_POST['submitConfirmListDelete']))
	{
		// Create arrays
		$list = [];

		// If set to avoid error
		if(isset($_POST['confirmList']))
		{
			$list = $_POST['confirmList'];
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

		while ($db = mysqli_fetch_object($result))
		{
			// If a shift exists to attest to
			$i++;

			// Show confirm
			$dataMain .= '
			<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">
				<input type="checkbox" name="confirmList[]" id="confirmList_'.$db->eventId.'" value="'.$db->eventId.'" /> '.$db->name.'<br /><br />';

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
