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
				if(password_verify(cleanInput($_POST['oldpassword']), $db->password))
				{
					if($_POST['newpassword'] == $_POST['newpassword2'])
					{
						if(strlen($_POST['newpassword']) >= 6)
						{
							// Query the database
							$conn->query("UPDATE troopers SET password = '".password_hash(cleanInput($_POST['newpassword']), PASSWORD_DEFAULT)."' WHERE id = '".$_SESSION['id']."'");

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

/******************** TROOPER CHECK *******************************/

// Set reserve
if(isset($_GET['do']) && $_GET['do'] == "troopercheckreserve" && loggedIn() && isAdmin())
{
	if(empty($_POST['trooper']))
	{
		// Send JSON
		$array = array('data' => 'No troopers selected!');
		echo json_encode($array);
	}
	else
	{
		foreach($_POST['trooper'] as $trooper)
		{
			$conn->query("UPDATE troopers SET permissions = '3' WHERE id = '".cleanInput($trooper)."'");
		}
		
		// Send JSON
		$array = array('data' => 'Success!');
		echo json_encode($array);
	}
}

// Set retired
if(isset($_GET['do']) && $_GET['do'] == "troopercheckretired" && loggedIn() && isAdmin())
{
	if(empty($_POST['trooper']))
	{
		// Send JSON
		$array = array('data' => 'No troopers selected!');
		echo json_encode($array);
	}
	else
	{
		foreach($_POST['trooper'] as $trooper)
		{
			$conn->query("UPDATE troopers SET permissions = '4' WHERE id = '".cleanInput($trooper)."'");
		}
		
		// Send JSON
		$array = array('data' => 'Success!');
		echo json_encode($array);
	}
}

/******************** PHOTOS *******************************/

if(isset($_GET['do']) && $_GET['do'] == "deletephoto" && loggedIn())
{
	// Query database for photos
	$query = "SELECT * FROM uploads WHERE id = '".cleanInput($_POST['photoid'])."'";
	
	// Count photos
	$i = 0;
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->trooperid == $_SESSION['id'] || isAdmin())
			{
				// Delete file
				unlink("images/uploads/" . $db->filename);
				
				// Query database
				$conn->query("DELETE FROM uploads WHERE id = '".cleanInput($_POST['photoid'])."'");
				
				// Increment
				$i++;
			}
		}
	}
	
	// If failed...
	if($i == 0)
	{
		// Send JSON
		$array = array('data' => 'Failed to delete photo');
		echo json_encode($array);
	}
	else
	{
		// Send JSON
		$array = array('data' => 'Deleted!');
		echo json_encode($array);
	}
}

/******************** MODIFY SIGN UP FROM EVENT PAGE *******************************/

if(isset($_GET['do']) && $_GET['do'] == "modifysignup" && loggedIn())
{
	// Prevent if troop full
	$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".cleanInput($_POST['troopid'])."' AND status != '4'");
	
	// Get limit total
	$limitTotalGet = $conn->query("SELECT SUM(limit501st) + SUM(limitRebels) + SUM(limitDroid) + SUM(limitMando) + SUM(limitOther) FROM events WHERE id = '".cleanInput($_POST['troopid'])."'") or die($conn->error);
	$limitTotalGetVal = $limitTotalGet->fetch_row();
	
	// Hack Check
	$query = "SELECT * FROM event_sign_up WHERE (trooperid = '".cleanInput($_SESSION['id'])."' OR addedby = '".cleanInput($_SESSION['id'])."') AND troopid = '".cleanInput($_POST['troopid'])."'";
	
	// Used to see if record exists
	$i = 0;
	
	// Used to determine if a friend
	$isFriend = false;
	
	// Used to set the reason for canceling
	$reason = "";
	
	// Output
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Increment
			$i++;
			
			// Check to see if a friend
			if($db->addedby == $_SESSION['id'])
			{
				$isFriend = true;
			}
		}
	}
	
	// Kill hack
	if($i == 0)
	{
		die("Can not do this.");
	}
	
	// Check to see if friend
	if($isFriend)
	{
		// Check to see if canceled
		if($_POST['status'] == 4)
		{
			$reason = "Canceled by friend.";
		}
	}
	
	// Check if troop is full
	if($getNumOfTroopers->num_rows < $limitTotalGetVal[0])
	{
		// Update SQL
		$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costume'])."', costume_backup = '".cleanInput($_POST['costume_backup'])."', status = '".cleanInput($_POST['status'])."', reason = '".$reason."' WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND troopid = '".cleanInput($_POST['troopid'])."'");
		
		// Send JSON
		$array = array('success' => 'true');
		echo json_encode($array);
	}
	else
	{
		// Troop is full
		$data = "This troop is now full!";
		
		// Send JSON
		$array = array('success' => 'failed', 'data' => $data);
		echo json_encode($array);
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

		// Set up query string
		$troops = "";
		
		// Make sure this is a linked event
		if(isset($link))
		{
			// Query database for shifts to display all comments for linked events
			$query = "SELECT * FROM events WHERE link = '".$link."'";
			
			// Set up count so we don't start with an operator
			$j = 0;
			
			// Query loop
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					// If not first result
					if($j != 0)
					{
						$troops .= "OR ";
					}
					
					// Add to query
					$troops .= "troopid = '".$db->id."' ";
					
					// Increment j
					$j++;
				}
			}
		}

		// Load comments for return data
		$query = "SELECT * FROM comments WHERE ".$troops."troopid = '".cleanInput($_POST['eventId'])."' ORDER BY posted DESC";

		// Count comments
		$i = 0;

		// Return data
		$data = "";
		
		// Set name comment var - used for e-mails
		$name = "";
		
		// Set comment var - used for e-mails
		$comment = "";

		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$data .= '
				<div style="overflow-x: auto;" style="text-align: center;">
				<table border="1" name="comment_'.$db->id.'" id="comment_'.$db->id.'">';
				
				// Set up admin variable
				$admin = '';
				
				// Set comment variables
				$name = getName($db->trooperid);
				$comment = $db->comment;
				
				// If is admin, set up admin options
				if(isAdmin())
				{
					$admin = '<span style="margin-right: 15px;"><a href="#" id="deleteComment_'.$db->id.'" name="'.$db->id.'"><img src="images/trash.png" alt="Delete Comment" /></a></span>';
				}

				// Convert date/time
				$date = strtotime($db->posted);
				$newdate = date("F j, Y, g:i a", $date);

				$data .= '
				<tr>
					<td><span style="float: left;">'.$admin.'<a href="#" id="quoteComment_'.$db->id.'" name="'.$db->id.'"><img src="images/quote.png" alt="Quote Comment"></a></span> <a href="index.php?profile='.$db->trooperid.'">'.$name.' - '.readTKNumber(getTKNumber($db->trooperid), getTrooperSquad($db->trooperid)).'</a><br />'.$newdate.'</td>
				</tr>
				
				<tr>
					<td name="insideComment">'.nl2br(isImportant($db->important, showBBcodes($comment))).'</td>
				</tr>

				</table>
				</div>

				<br />';

				// Increment
				$i++;
			}
		}
		
		// Notify e-mail for comments
		$query = "SELECT events.id AS eventId, events.name AS eventName, event_sign_up.id AS signupId, troopers.id AS trooperId, troopers.email AS email, troopers.name AS name FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE event_sign_up.troopid = '".cleanInput($_POST['eventId'])."' AND troopers.subscribe = '1' AND troopers.email != '' GROUP BY troopers.id";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Send E-mail
				sendEmail($db->email, $db->name, "Troop Tracker: A comment has posted on ".$db->eventName."!", getName(cleanInput($_SESSION['id'])) . ": " . cleanInput($_POST['comment']) . "\n\nYou can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com");
			}
		}

		if($i == 0)
		{
			$data .= '
			<br />
			<b>No discussion to display.</b>';
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
		
		// Update other databases that are affected
		$conn->query("UPDATE event_sign_up SET attended_costume = '0' WHERE attended_costume = '".cleanInput($_POST['costumeID'])."'");
		$conn->query("UPDATE event_sign_up SET costume_backup = '0' WHERE attended_costume = '".cleanInput($_POST['costumeID'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted costume: " . getCostume(cleanInput($_POST['costumeID'])), cleanInput($_SESSION['id']));
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
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has added costume: " . cleanInput($_POST['costumeName']), cleanInput($_SESSION['id']));
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
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has edited costume ID [" . cleanInput($_POST['costumeIDEdit']) . "] to " . cleanInput($_POST['costumeNameEdit']), cleanInput($_SESSION['id']));
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
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted award ID: " . cleanInput($_POST['awardID']), cleanInput($_SESSION['id']));
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
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has added award: " . cleanInput($_POST['awardName']), cleanInput($_SESSION['id']));
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
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has awarded ID [" . cleanInput($_POST['awardIDAssign']) . "] to " . getName(cleanInput($_POST['userIDAward'])), cleanInput($_SESSION['id']));
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
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has edited award ID [" . cleanInput($_POST['awardIDEdit']) . " to " . cleanInput($_POST['editAwardTitle']), cleanInput($_SESSION['id']));
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
				$array = array('name' => $db->name, 'email' => $db->email, 'forum' => $db->forum_id, 'rebelforum' => $db->rebelforum, 'phone' => $db->phone, 'squad' => getSquadName($db->squad), 'tkid' => readTKNumber($db->tkid, $db->squad), 'link' => get501Info($db->tkid, $db->squad)['link']);
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
					sendEmail($db->email, $db->name, "".garrison." Troop Tracker: Account Denied", "Your account has been denied. Please confirm that all your information is correct and try again. If you continue to have issues, please reach out on the boards.");
				}
				catch(Exception $e)
				{
					// Nothing
				}
			}
		}
		
		// Query the database
		$conn->query("DELETE FROM troopers WHERE id = '".cleanInput($_POST['userID2'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has denied user ID [" . cleanInput($_POST['userID2']) . "]", cleanInput($_SESSION['id']));
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
					sendEmail($db->email, $db->name, "".garrison." Troop Tracker: Account Approved", "Your account has been approved!");
				}
				catch(Exception $e)
				{
					// Nothing
				}
			}
		}
		
		// Query the database
		$conn->query("UPDATE troopers SET approved = 1 WHERE id = '".cleanInput($_POST['userID2'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has approved user ID [" . cleanInput($_POST['userID2']) . "]", cleanInput($_SESSION['id']));
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
		
		// Update other databases that will be affected
		$conn->query("DELETE FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		$conn->query("DELETE FROM award_troopers WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		$conn->query("DELETE FROM comments WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted user ID [" . cleanInput($_POST['userID']) . "]", cleanInput($_SESSION['id']));
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
				$array = array('id' => $db->id, 'name' => $db->name, 'email' => $db->email, 'phone' => $db->phone, 'squad' => $db->squad, 'permissions' => $db->permissions, 'tkid' => $db->tkid, 'forumid' => $db->forum_id, 'rebelforum' => $db->rebelforum, 'supporter' => $db->supporter);

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
				// Set up TKID
				$tkid = cleanInput($_POST['tkid']);
				
				// Query the database
				$conn->query("UPDATE troopers SET name = '".cleanInput($_POST['user'])."', email =  '".cleanInput($_POST['email'])."', phone = '".cleanInput(cleanInput($_POST['phone']))."', squad = '".cleanInput($_POST['squad'])."', permissions = '".cleanInput($_POST['permissions'])."', tkid = '".$tkid."', forum_id = '".cleanInput($_POST['forumid'])."', rebelforum = '".cleanInput($_POST['rebelforum'])."', supporter = '".cleanInput($_POST['supporter'])."' WHERE id = '".cleanInput($_POST['userIDE'])."'") or die($conn->error);
				
				// Send notification to command staff
				sendNotification(getName($_SESSION['id']) . " has updated user ID [" . cleanInput($_POST['userIDE']) . "]", cleanInput($_SESSION['id']));

				$array = array('success' => 'true', 'newname' => cleanInput($_POST['user']) . " - " . readTKNumber($tkid, getTrooperSquad(cleanInput($_POST['userIDE']))), 'data' => 'User has been updated!');
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
			
			// Set up error message
			$errorMessage = "";
			
			// Set up fail variable
			$failed = false;
			
			// Get TKID
			$tkid = cleanInput($_POST['tkid']);
			
			// Check length
			if(strlen($tkid) > 11)
			{
				$failed = true;
				$errorMessage .= 'TKID must be less than eleven (11) characters. ';
			}

			// Check password length
			if(strlen($_POST['password']) < 6)
			{
				$failed = true;
				$errorMessage .= 'Password must be 6 (six) characters. ';
			}

			// TKID is number check
			if(!is_numeric($tkid))
			{
				$failed = true;
				$errorMessage .= 'TKID must be an integer. ';
			}

			// Query ID database
			$idcheck = $conn->query("SELECT id FROM troopers WHERE tkid = '".$tkid."'") or die($conn->error);
			
			// Query 501st forum
			$forumcheck = $conn->query("SELECT forum_id FROM troopers WHERE forum_id = '".cleanInput($_POST['forumid'])."'") or die($conn->error);
			
			// Check if 501st forum exists
			if($forumcheck->num_rows > 0)
			{
				$failed = true;
				$errorMessage .= 'FL Garrison Forum Name is already taken. Please contact the '.garrison.' Webmaster for further assistance. ';
			}
			
			// Query Rebel forum - if specified
			if(cleanInput($_POST['rebelforum']) != "")
			{
				$rebelcheck = $conn->query("SELECT rebelforum FROM troopers WHERE rebelforum = '".cleanInput($_POST['rebelforum'])."'") or die($conn->error);
				
				// Check if Rebel exists
				if($rebelcheck->num_rows > 0)
				{
					$failed = true;
					$errorMessage .= 'Rebel Forum Name is already taken. Please contact the '.garrison.' Webmaster for further assistance. ';
				}
			}

			// Check E-mail
			$validator = new EmailAddressValidator;
			if (!$validator->check_email_address(cleanInput($_POST['email'])))
			{
				$failed = true;
				$errorMessage .= 'Invalid E-mail ';
			}
			
			if(!$failed)
			{
				// Insert into database
				$conn->query("INSERT INTO troopers (name, email, forum_id, rebelforum, phone, squad, permissions, tkid, password, approved) VALUES ('".cleanInput($_POST['name'])."', '".cleanInput($_POST['email'])."', '".cleanInput($_POST['forumid'])."', '".cleanInput($_POST['rebelforum'])."', '".cleanInput($_POST['phone'])."', '".cleanInput($_POST['squad'])."', '".cleanInput($_POST['permissions'])."', '".cleanInput($_POST['tkid'])."', '".password_hash(cleanInput($_POST['password']), PASSWORD_DEFAULT)."', 1)");
				
				// Send notification to command staff
				sendNotification(getName($_SESSION['id']) . " has added a user", cleanInput($_SESSION['id']));

				$array = array('success' => 'success', 'data' => 'User created!');
				echo json_encode($array);
			}
			else
			{
				$array = array('success' => 'failed', 'data' => $errorMessage);
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

			// If name empty
			if(cleanInput($_POST['name']) == "")
			{
				$failed = true;
				echo '<li>Please enter your name.</li>';
			}

			// Set TKID
			$tkid = cleanInput($_POST['tkid']);
			
			// If Forum ID empty
			if(cleanInput($_POST['forumid']) == "")
			{
				$failed = true;
				echo '<li>Please enter your FL 501st Forum Username.</li>';
			}
			
			// If TKID is greather than 11 characters
			if(strlen($tkid) > 11)
			{
				$failed = true;
				echo '<li>TKID must be less than eleven (11) characters.</li>';
			}

			// If password is less than six characters
			if(strlen($_POST['password']) < 6)
			{
				$failed = true;
				echo '<li>Password must be 6 (six) characters.</li>';
			}

			// If TKID is not numeric
			if(!is_numeric($tkid))
			{
				$failed = true;
				echo '<li>TKID must be an integer.</li>';
			}
			
			// Set squad variable
			$squad = cleanInput($_POST['squad']);
			
			// Check if 501st
			if($squad <= count($squadArray))
			{
				// Query ID database
				$idcheck = $conn->query("SELECT id FROM troopers WHERE tkid = '".$tkid."' AND squad <= ".count($squadArray)."") or die($conn->error);
			}
			else
			{
				// In a club - query by club
				$idcheck = $conn->query("SELECT id FROM troopers WHERE tkid = '".$tkid."' AND squad = ".$squad."") or die($conn->error);
			}
			
			// Query 501st forum
			$forumcheck = $conn->query("SELECT forum_id FROM troopers WHERE forum_id = '".cleanInput($_POST['forumid'])."'") or die($conn->error);
			
			// Check if 501st forum exists
			if($forumcheck->num_rows > 0)
			{
				$failed = true;
				echo '<li>FL Garrison Forum Name is already taken. Please contact the '.garrison.' Webmaster for further assistance.</li>';
			}
			
			// Query Rebel forum - if specified
			if(cleanInput($_POST['rebelforum']) != "")
			{
				$rebelcheck = $conn->query("SELECT rebelforum FROM troopers WHERE rebelforum = '".cleanInput($_POST['rebelforum'])."'") or die($conn->error);
				
				// Check if Rebel exists
				if($rebelcheck->num_rows > 0)
				{
					$failed = true;
					echo '<li>Rebel Forum Name is already taken. Please contact the '.garrison.' Webmaster for further assistance.</li>';
				}
			}

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
				$conn->query("INSERT INTO troopers (name, tkid, email, forum_id, rebelforum, phone, squad, password) VALUES ('".cleanInput($_POST['name'])."', '".floatval($tkid)."', '".cleanInput($_POST['email'])."', '".cleanInput($_POST['forumid'])."', '".cleanInput($_POST['rebelforum'])."', '".cleanInput($_POST['phone'])."', '".$squad."', '".password_hash(cleanInput($_POST['password']), PASSWORD_DEFAULT)."')") or die($conn->error);
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
			
			// Get support goal button pressed and need data
			if(isset($_POST['getSupportGoal']))
			{
				$array = array('data' => $db->supportgoal);
				echo json_encode($array);
			}
			
			// Save support goal
			if(isset($_POST['saveSupportGoal']))
			{
				// Check value
				if($_POST['supportgoal'] != "" && is_numeric($_POST['supportgoal']))
				{
					// Query
					$conn->query("UPDATE settings SET supportgoal = '".cleanInput($_POST['supportgoal'])."'");
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
			$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limitRebels, limit501st, limitMando, limitDroid, limitOther, squad) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".cleanInput($date1)."', '".cleanInput($date2)."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limitRebels'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitMando'])."', '".cleanInput($_POST['limitDroid'])."', '".cleanInput($_POST['limitOther'])."', '".cleanInput($_POST['squadm'])."')") or die($conn->error);
			
			// Event ID - Last insert from database
			$eventId = $conn->insert_id;
			
			// Loop through shifts
			foreach($_POST as $key => $value)
			{
				// Check if contains "shiftpost"
				if(strstr($key, 'shiftpost'))
				{
					// Get pair value from shiftpost
					$pair = $value;
					
					// Verify there is a value in both dates before inserting data
					if(cleanInput($_POST['adddateStart' . $pair]) != "" && cleanInput($_POST['adddateEnd' . $pair]) != "")
					{
						// Clean date input
						$date1 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['adddateStart' . $pair])));
						$date2 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['adddateEnd' . $pair])));
					
						// Query the database
						$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limitRebels, limit501st, limitMando, limitDroid, limitOther, squad, link) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".$date1."', '".$date2."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limitRebels'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitMando'])."', '".cleanInput($_POST['limitDroid'])."', '".cleanInput($_POST['limitOther'])."', '".cleanInput($_POST['squadm'])."', '".$eventId."')") or die($conn->error);
					}
				}
			}
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has added an event: [" . cleanInput($_POST['eventName']) . "]", cleanInput($_SESSION['id']));

			$array = array('success' => 'success', 'data' => 'Event created!');
			echo json_encode($array);
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
		// Filter out none
		if(cleanInput($_POST['reasonVal' . $_POST['trooperSelectEdit'] . '']) == "None")
		{
			// Set to blank
			$_POST['reasonVal' . $_POST['trooperSelectEdit'] . ''] = "";
		}
		
		// Query the database
		$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costumeValSelect' . $_POST['trooperSelectEdit'] . ''])."', costume_backup = '".cleanInput($_POST['costumeVal' . $_POST['trooperSelectEdit'] . ''])."', status = '".cleanInput($_POST['statusVal' . $_POST['trooperSelectEdit'] . ''])."', reason = '".cleanInput($_POST['reasonVal' . $_POST['trooperSelectEdit'] . ''])."', attended_costume = '".cleanInput($_POST['attendcostumeVal' . $_POST['trooperSelectEdit'] . ''])."' WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."'") or die($conn->error);

		// If set as attended, check trooper counts
		if(cleanInput($_POST['statusVal' . $_POST['trooperSelectEdit'] . '']) == 3)
		{
			// Check troops for notification
			troopCheck(cleanInput($_POST['trooperSelectEdit']));
		}

		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has edited event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));

		// Send back data
		$array = array('success' => 'success', 'id' => $_SESSION['id']);
		echo json_encode($array);
	}

	// Add a trooper to roster
	if(isset($_POST['troopRosterFormAdd']))
	{
		// Does this trooper already exist in roster?
		$query = "SELECT * FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperSelect'])."' AND troopid = '".cleanInput($_POST['troopid'])."'";
		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Increment
				$i++;
			}
		}
		
		if(cleanInput($_POST['costume']) != "null" && cleanInput($_POST['status']) != "null" && $i == 0)
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, costume_backup, reason, status, attended_costume) VALUES ('".cleanInput($_POST['trooperSelect'])."', '".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['costumebackup'])."', '".cleanInput($_POST['reason'])."', '".cleanInput($_POST['status'])."', '".cleanInput($_POST['attendedcostume'])."')") or die($conn->error);
			$last_id = $conn->insert_id;
			
			// If status is attended
			if(cleanInput($_POST['status']) == 3)
			{
				// Check troops for notification
				troopCheck(cleanInput($_POST['trooperSelect']));
			}
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has added trooper ID [".cleanInput($_POST['trooperSelect'])."] to event ID [" . cleanInput($_POST['troopid']) . "]", cleanInput($_SESSION['id']));
		}
	}

	// Event submitted for deletion...
	if(isset($_POST['submitDelete']))
	{
		// Get number of events with link
		$getNumOfLinks = $conn->query("SELECT id FROM events WHERE link = '".cleanInput($_POST['eventId'])."'");
		
		// Query the database
		$conn->query("DELETE FROM events WHERE id = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - event_sign_up
		$conn->query("DELETE FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'");
		
		// If this event is the main link to others
		if($getNumOfLinks->num_rows > 0)
		{
			// Get lowest event for link change
			$getLinkChange = $conn->query("SELECT id FROM events WHERE link = '".cleanInput($_POST['eventId'])."' ORDER BY id ASC LIMIT 1");
			$getLinkVal = $getLinkChange->fetch_row()[0];
			
			// Set link to new main event
			$conn->query("UPDATE events SET link = '".$getLinkVal."' WHERE link = '".cleanInput($_POST['eventId'])."'");
			
			// Remove link from main event
			$conn->query("UPDATE events SET link = '0' WHERE id = '".$getLinkVal."'");
		}
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
	}

	// Event submitted for cancelation...
	if(isset($_POST['submitCancel']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '2' WHERE id = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - event_sign_up
		$conn->query("DELETE FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has canceled event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
	}

	// Event submitted for completion...
	if(isset($_POST['submitFinish']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '1' WHERE id = '".cleanInput($_POST['eventId'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has finished event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
	}
	
	// Event submitted for open...
	if(isset($_POST['submitOpen']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '0' WHERE id = '".cleanInput($_POST['eventId'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has reopened event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
	}
	
	// Event submitted for completion...
	if(isset($_POST['submitCharity']))
	{
		// Query the database
		$conn->query("UPDATE events SET moneyRaised = '".cleanInput($_POST['charity'])."' WHERE id = '".cleanInput($_POST['eventId'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has set charity raised to [".cleanInput($_POST['charity'])."] on event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
	}

	// Remove trooper from roster
	if(isset($_POST['removetrooper']))
	{
		if(isset($_POST['trooperSelectEdit']) && $_POST['trooperSelectEdit'] >= 0)
		{
			$array = array('success' => 'true', 'data' => 'Trooper removed!');

			// Query the database
			$conn->query("DELETE FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."'");
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has removed trooper ID [".cleanInput($_POST['trooperSelectEdit'])."] on event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
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
		// Array of costumes
		$costumesName = array();
		$costumesID = array();
		
		// Display costumes
		$query2 = "SELECT * FROM costumes";
		$limitToGet = $conn->query("SELECT limitTo FROM events WHERE id = '".cleanInput($_POST['eventId'])."'") or die($conn->error);
		$limitToGetVal = $limitToGet->fetch_row();
		
		// If limited to certain costumes, only show certain costumes...
		if($limitToGetVal[0] < 4)
		{
			$query2 .= " WHERE era = '".$limitToGetVal[0]."' OR era = '4'";
		}
		
		$query2 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler') DESC, costume";
		
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				array_push($costumesID, $db2->id);
				array_push($costumesName, $db2->costume);
			}
		}
		
		// Convert to JavaScript array
		echo '
		<script type="text/javascript">

			var jArray1 = ' . json_encode($costumesName) . ';
			var jArray2 = ' . json_encode($costumesID) . ';
		</script>';
							
		// Load users assigned to event
		$query = "SELECT * FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."' ORDER BY id ASC";
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
								<th>Selection</th>	<th>Trooper TKID / Name</td>	<th>Trooper Costume</th>	<th>Trooper Backup Costume</th>	<th>Trooper Status</th>	<th>Trooper Comment</th>	<th>Attended With</th>';
				}

				// List troopers
				echo '
				<tr id="roster_'.$db->trooperid.'" name="roster_'.$db->trooperid.'">
					<td>
						<input type="hidden" name="tkid" id="tkid" value = "'.getTKNumber($db->trooperid).'" />
						<input type="hidden" name="troopername" id="troopername" value = "'.getName(cleanInput($db->trooperid)).'" />
						<input type="hidden" name="eventId" id="eventId" value = "'.cleanInput($_POST['eventId']).'" />
						<input type="radio" name="trooperSelectEdit" id="trooperSelectEdit" value="'.$db->trooperid.'" />
					</td>

					<td>
						<div name="tknumber1'.$db->trooperid.'" id="tknumber1'.$db->trooperid.'">'.getTKNumber($db->trooperid).' - '.getName($db->trooperid).'</div>
					</td>

					<td>
						<div name="costume1'.$db->trooperid.'" id="costume1'.$db->trooperid.'">'.ifEmpty(getCostume($db->costume), "N/A").'</div>
						<div name="costume2'.$db->trooperid.'" id="costume2'.$db->trooperid.'" style="display:none;">
							<select name="costumeValSelect'.$db->trooperid.'">';
							
							// Array count
							$a = 0;

							// Display costumes	
							foreach($costumesName as $key)
							{
								// Select the costume the user chose to wear
								if($db->costume == intval($costumesID[$a]))
								{
									echo '<option value="'.$costumesID[$a].'" SELECTED>'.$key.'</option>';
								}
								else
								{
									echo '<option value="'.$costumesID[$a].'">'.$key.'</option>';
								}
								
								$a++;
							}

							echo '
							</select>
						</div>
					</td>

					<td>
						<div name="backup1'.$db->trooperid.'" id="backup1'.$db->trooperid.'">'.ifEmpty(getCostume($db->costume_backup), "N/A").'</div>
						<div name="backup2'.$db->trooperid.'" id="backup2'.$db->trooperid.'" style="display:none;">

						<select name="costumeVal'.$db->trooperid.'" id="costumeVal'.$db->trooperid.'">';

						// Reset
						$a = 0;
						
						// Count costumes
						$c = 0;
						
						// Count picked
						$p = 0;
						
						// Display costumes
						foreach($costumesName as $key)
						{
							// Select the costume the user chose to wear
							if($db->costume_backup == intval($costumesID[$a]))
							{
								// The users costume
								echo '<option value="'.$costumesID[$a].'" SELECTED>'.$key.'</option>';
								$p++;
							}
							else
							{
								// Display costume
								echo '<option value="'.$costumesID[$a].'">'.$key.'</option>';
							}

							$c++;
							$a++;
						}

						if($c == 0 || $p == 0)
						{
							echo '<option value="0" SELECTED>None</option>';
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
						<div name="attendcostume1'.$db->trooperid.'" id="attendcostume1'.$db->trooperid.'">'.ifEmpty(getCostume($db->attended_costume), "Not Submitted").'</div>
						<div name="attendcostume2'.$db->trooperid.'" id="attendcostume2'.$db->trooperid.'" style="display:none;">
						<select name="attendcostumeVal'.$db->trooperid.'" id="attendcostumeVal'.$db->trooperid.'">';

						// Reset
						$a = 0;
						
						// Amount of costumes
						$c = 0;
						
						// Count picked
						$p = 0;

						// Display costumes
						foreach($costumesName as $key)
						{
							if($db->attended_costume == intval($costumesID[$a]))
							{
								// The users costume
								echo '<option value="'.$costumesID[$a].'" SELECTED>'.$key.'</option>';
								$p++;
							}
							else
							{
								// Display costume
								echo '<option value="'.$costumesID[$a].'">'.$key.'</option>';
							}

							$c++;
							$a++;
						}

						if($c == 0 || $p == 0)
						{
							echo '<option value="0" SELECTED>None</option>';
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
			echo '
			<form action="process.php?do=editevent" method="POST" name="troopRosterForm" id="troopRosterForm" style="display: none;">
				<div style="overflow-x: auto;">
				<table border="1" name="rosterTable" id="rosterTable">
					<tr>
						<th>Selection</th>	<th>Trooper TKID / Name</td>	<th>Trooper Costume</th>	<th>Trooper Backup Costume</th>	<th>Trooper Status</th>	<th>Trooper Comment</th>	<th>Attended With</th>
					</tr>
				</table>
				</div>
				
				<input type="submit" name="removetrooper" id="removetrooper" value="Remove Trooper" />	<input type="submit" name="edittrooper" id="edittrooper" value="Edit Trooper" />
			</form>';
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
		$query = "SELECT troopers.id AS troopida, troopers.name AS troopername, troopers.tkid, troopers.squad FROM troopers WHERE NOT EXISTS (SELECT event_sign_up.trooperid FROM event_sign_up WHERE event_sign_up.trooperid = troopers.id AND event_sign_up.troopid = '".cleanInput($_POST['eventId'])."') ORDER BY troopers.name";

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
						
						<br />
						
						Trooper Search: <input type="text" name="trooperSearch" id="trooperSearch" style="width: 50%;" />

						<p>Select a trooper to add:</p>
						<select name="trooperSelect" id="trooperSelect">';
				}
				
				// Get TKID
				$tkid = readTKNumber($db->tkid, $db->squad);

				// List troopers
				echo '
				<option value="'.$db->troopida.'" tkid="'.$tkid.'" troopername="'.$db->troopername.'">'.$db->troopername.' - '.$tkid.'</option>';
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
				<select name="costume" id="costume">
					<option value="null" SELECTED>Please choose an option...</option>';
					
				// Reset
				$a = 0;
					
				// Display costumes
				foreach($costumesName as $key)
				{
					echo '
					<option value="'. $costumesID[$a] .'">'.$key.'</option>';
					
					$a++;
				}

			echo '
				</select>

				<p>What is there backup costume if applicable:</p>

				<select name="costumebackup" id="costumebackup">';
				
				// Reset
				$a = 0;
				
				// Amount of costumes
				$c = 0;
				
				// Display costumes
				foreach($costumesName as $key)
				{
					// If first select option
					if($c == 0)
					{
						echo '<option value="0" SELECTED>Select a costume...</option>';
					}
					
					// Add costume
					echo '<option value="'.$costumesID[$a].'">'.$key.'</option>';
					
					$a++;
					$c++;
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
					
					<select name="attendedcostume" id="attendedcostume">';
					
					// Reset
					$a = 0;
					$c = 0;
					
					// Display costumes
					foreach($costumesName as $key)
					{
						// If first select option
						if($c == 0)
						{
							echo '<option value="0" SELECTED>Select a costume...</option>';
						}
						
						// Add costume
						echo '<option value="'.$costumesID[$a].'">'.$key.'</option>';
						
						$a++;
						$c++;
					}
					
					echo '
					</select>
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
				$array = array('id' => $db->id, 'name' => $db->name, 'venue' => $db->venue, 'dateStart' => $db->dateStart, 'dateEnd' => $db->dateEnd, 'website' => $db->website, 'numberOfAttend' => $db->numberOfAttend, 'requestedNumber' => $db->requestedNumber, 'requestedCharacter' => $db->requestedCharacter, 'secureChanging' => $db->secureChanging, 'blasters' => $db->blasters, 'lightsabers' => $db->lightsabers, 'parking' => $db->parking, 'mobility' => $db->mobility, 'amenities' => $db->amenities, 'referred' => $db->referred, 'comments' => $db->comments, 'location' => $db->location, 'squad' => $db->squad, 'label' => $db->label, 'postComment' => $db->postComment, 'notes' => $db->notes, 'limitedEvent' => $db->limitedEvent, 'limitTo' => $db->limitTo, 'limitRebels' => $db->limitRebels, 'limit501st' => $db->limit501st, 'limitMando' => $db->limitMando, 'limitDroid' => $db->limitDroid, 'limitOther' => $db->limitOther, 'closed' => $db->closed, 'moneyRaised' => $db->moneyRaised, 'eventLink' => $db->link);

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
			
			// Get number of events with link
			$getNumOfLinks = $conn->query("SELECT id FROM events WHERE link = '".cleanInput($_POST['eventIdE'])."'");
			
			// If event is linked
			if($_POST['eventLink'] > 0)
			{
				// Query the database - linked
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', limitRebels = '".cleanInput($_POST['limitRebels'])."', limit501st = '".cleanInput($_POST['limit501st'])."', limitMando = '".cleanInput($_POST['limitMando'])."', limitDroid = '".cleanInput($_POST['limitDroid'])."', limitOther = '".cleanInput($_POST['limitOther'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventLink'])."' OR id = '".cleanInput($_POST['eventLink'])."'") or die($conn->error);
				
				// Update date
				$conn->query("UPDATE events SET dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."' WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);
			}
			else if($getNumOfLinks->num_rows > 0)
			{
				// Query the database - linked
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', limitRebels = '".cleanInput($_POST['limitRebels'])."', limit501st = '".cleanInput($_POST['limit501st'])."', limitMando = '".cleanInput($_POST['limitMando'])."', limitDroid = '".cleanInput($_POST['limitDroid'])."', limitOther = '".cleanInput($_POST['limitOther'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);
				
				// Update date
				$conn->query("UPDATE events SET dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."' WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);
			}
			else
			{
				// Query the database - if not linked
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', limitRebels = '".cleanInput($_POST['limitRebels'])."', limit501st = '".cleanInput($_POST['limit501st'])."', limitMando = '".cleanInput($_POST['limitMando'])."', limitDroid = '".cleanInput($_POST['limitDroid'])."', limitOther = '".cleanInput($_POST['limitOther'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);
			}
			
			// Set up if we should send notification
			$sendNotificationCheck = true;
			
			// Loop through shifts - if exist
			foreach($_POST as $key => $value)
			{
				// Check if contains "shiftpost"
				if(strstr($key, 'shiftpost'))
				{
					// Get pair value from shiftpost
					$pair = $value;
					
					// Set up link value
					$link = -1;
					
					// If this is a linked event and not main
					if($_POST['eventLink'] > 0)
					{
						$link = cleanInput($_POST['eventLink']);
					}
					else
					{
						$link = cleanInput($_POST['eventIdE']);
					}
					
					// Verify there is a value in both dates before inserting data
					if(cleanInput($_POST['adddateStart' . $pair]) != "" && cleanInput($_POST['adddateEnd' . $pair]) != "")
					{
						// Clean date input
						$date1 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['adddateStart' . $pair])));
						$date2 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['adddateEnd' . $pair])));
					
						// Query the database
						$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limitRebels, limit501st, limitMando, limitDroid, limitOther, squad, link) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".$date1."', '".$date2."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limitRebels'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitMando'])."', '".cleanInput($_POST['limitDroid'])."', '".cleanInput($_POST['limitOther'])."', '".cleanInput($_POST['squadm'])."', '".$link."')") or die($conn->error);
						
						// Send notification to command staff
						sendNotification(getName($_SESSION['id']) . " has edited event ID and added a shift: [" . $link . "]", cleanInput($_SESSION['id']));
						
						// We just sent a notification, don't send another below
						$sendNotificationCheck = false;
					}
				}
			}
			
			// Send notification to command staff, if we have not already
			if($sendNotificationCheck)
			{
				sendNotification(getName($_SESSION['id']) . " has edited event ID: [" . cleanInput($_POST['eventIdE']) . "]", cleanInput($_SESSION['id']));
			}

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
		// Check if this is add friend
		if(isset($_POST['addfriend']))
		{
			// Prevent bug of getting signed up twice
			$eventCheck = inEvent(cleanInput($_POST['trooperSelect']), cleanInput($_POST['event']));
		}
		else
		{
			// Prevent bug of getting signed up twice
			$eventCheck = inEvent(cleanInput($_SESSION['id']), cleanInput($_POST['event']));
		}

		if($eventCheck['inTroop'] == 1)
		{
			die("ALREADY IN THIS TROOP!");
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
		
		// Check if this is add friend
		if(isset($_POST['addfriend']))
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup, addedby) VALUES ('".cleanInput($_POST['trooperSelect'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['status'])."', '".cleanInput($_POST['backupcostume'])."', '".cleanInput($_SESSION['id'])."')") or die($conn->error);
		}
		else
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup) VALUES ('".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['status'])."', '".cleanInput($_POST['backupcostume'])."')") or die($conn->error);
		}

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
				$query2 = "SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.reason, event_sign_up.attended_costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, troopers.id AS trooperId, troopers.name, troopers.tkid, troopers.squad FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".cleanInput($_POST['event'])."' ORDER BY event_sign_up.id ASC";
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
						if(loggedIn() && ($db2->trooperId == $_SESSION['id'] || $_SESSION['id'] == $db2->addedby) && $db->closed == 0 && $db2->status != 4)
						{
							$data .= '
							<tr>
								<td>
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>
								</td>
									
								<td>
									'.readTKNumber($db2->tkid, $db2->squad).'
								</td>
								
								<td name="trooperRosterCostume" id="trooperRosterCostume">
									<select name="modifysignupFormCostume2" id="modifysignupFormCostume2">';

									// Display costumes
									$query3 = "SELECT * FROM costumes";
									
									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
									}
									
									$query3 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler'".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume";
									
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
									$query3 = "SELECT * FROM costumes";
									
									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
									}
									
									$query3 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler'".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume";
									
									// Count results
									$c = 0;
									
									// Amount of costumes
									if ($result3 = mysqli_query($conn, $query3))
									{
										while ($db3 = mysqli_fetch_object($result3))
										{
											// If costume set to backup and first result
											if($db2->costume_backup == 0 && $c == 0)
											{
												$data .= '
												<option value="0" SELECTED>N/A</option>';
											}
											// Make sure this is a first result otherwise
											else if($c == 0)
											{
												$data .= '
												<option value="0">N/A</option>';
											}
											
											
											// If a costume matches
											if($db2->costume_backup == $db3->id)
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
											<option value="4" '.echoSelect(4, $db2->status).'>Cancel</option>	
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
						else if(loggedIn() && ($db2->trooperId == $_SESSION['id'] || $_SESSION['id'] == $db2->addedby) && $db->closed == 0 && $db2->status == 4)
						{
							$data .= '
							<tr>
								<td>
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>
								</td>
									
								<td>
									'.readTKNumber($db2->tkid, $db2->squad).'
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
									'.readTKNumber($db2->tkid, $db2->squad).'
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
							<td><a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a></td>	<td>'.readTKNumber($db2->tkid, $db2->squad).'</td>	<td>'.getCostume($db2->costume).'</td>	<td>'.getCostume($db2->costume_backup).'</td>	<td id="'.$db2->trooperId.'Status">'.getStatus($db2->status).'
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

				if(!isset($_POST['addfriend']))
				{
					$data .= '
					<div name="signeduparea" id="signeduparea">
						<p><b>You are signed up for this troop!</b></p>
					</div>';
				}

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

		// Make sure troop list and costume is not blank
		if(!empty($list) && $_POST['costume'] != "") 
		{
			$n = count($list);

			for($i = 0; $i < $n; $i++)
			{
				// Query the database
				$conn->query("UPDATE event_sign_up SET attended_costume = '".cleanInput($_POST['costume'])."', status = '3' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'") or die($conn->error);
			}
			
			// Check troops for notification
			troopCheck($_SESSION['id']);
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
				$conn->query("UPDATE event_sign_up SET status = '4' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'") or die($conn->error);
			}
		}
	}

	// Send back AJAX data

	// What we are going to send back
	$dataMain = "";

	// Load events that need confirmation
	$query = "SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd < NOW() AND status < 3 AND events.closed = 1";

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
