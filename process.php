<?php

/**
 * This file is used for processing AJAX requests.
 *
 * @author  Matthew Drennan
 *
 */

include 'config.php';

/******************** SAVE PLACEHOLDER *******************************/

if(isset($_GET['do']) && $_GET['do'] == "saveplaceholder" && loggedIn())
{
	// Query database for placeholder
	$query = "SELECT * FROM event_sign_up WHERE id = '".cleanInput($_POST['signid'])."' AND addedby = '".$_SESSION['id']."' AND trooperid = '".placeholder."'";
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			$conn->query("UPDATE event_sign_up SET note = '".cleanInput($_POST['note'])."' WHERE id = '".cleanInput($_POST['signid'])."'");
		}
	}
}

/******************** SMILEY EDITOR *******************************/

if(isset($_GET['do']) && $_GET['do'] == "smileyeditor")
{
	// Send JSON
	$array = array('data' => smileyEditor());
	echo json_encode($array);
}

/******************** EDIT COMMENT *******************************/

if(isset($_GET['do']) && isset($_POST['commentid']) && isset($_POST['comment']) && $_GET['do'] == "editcomment")
{
	// Data to send back
	$data = "";

	// Query database for comment
	$query = "SELECT * FROM comments WHERE id = '".cleanInput($_POST['commentid'])."'";
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Check comment matches ID of trooper
			if($_SESSION['id'] == $db->trooperid)
			{
				// Update comment
				$conn->query("UPDATE comments SET comment = '".cleanInput($_POST['comment'])."' WHERE id = '".cleanInput($_POST['commentid'])."' AND trooperid = '".cleanInput($_SESSION['id'])."'");

				// Edit comment
				editPost(getCommentPostID(cleanInput($_POST['commentid'])), cleanInput($_POST['comment']));

				// Set up return data
				$data = nl2br(isImportant($db->important, showBBcodes(cleanInput($_POST['comment']))));
			}
		}
	}

	// Send JSON
	$array = array('data' => $data);
	echo json_encode($array);
}

/******************** ADD MASTER ROSTER *******************************/

if(isset($_GET['do']) && isset($_POST['userID']) && isset($_POST['squad']) && $_GET['do'] == "addmasterroster" && isAdmin())
{
	// Set up query add
	$queryAdd = "";
	$queryAdd2 = "";
	
	// Which club to get
	if($_POST['squad'] <= count($squadArray))
	{
		$queryAdd = "p501";
	}
	
	// Set up count
	$clubCount = count($squadArray) + 1;
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Match
		if($_POST['squad'] == $clubCount)
		{
			// Add to query
			$queryAdd = "".$club_value['db']."";
		}
		
		// Increment
		$clubCount++;
	}
	
	// Check if 501st squad
	if($_POST['squad'] <= count($squadArray))
	{
		// Add this query to set squad
		$queryAdd2 = ", squad = " . cleanInput($_POST['squad']);
	}
	
	// Query trooper
	$conn->query("UPDATE troopers SET ".$queryAdd." = 1".$queryAdd2." WHERE id = '".cleanInput($_POST['userID'])."'");
	
	// Send JSON
	$array = array('data' => 'Trooper added!');
	echo json_encode($array);
}

/******************** CHANGE PERMISSION *******************************/

if(isset($_GET['do']) && isset($_POST['trooperid']) && isset($_POST['permission']) && $_GET['do'] == "changepermission" && isAdmin())
{
	$queryAdd = "";
	
	// Which club to get
	if($_POST['club'] <= count($squadArray))
	{
		$queryAdd = "p501";
	}
	
	// Set up count
	$clubCount = count($squadArray) + 1;
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Match
		if($_POST['club'] == $clubCount)
		{
			// Add to query
			$queryAdd = "".$club_value['db']."";
		}
		
		// Increment
		$clubCount++;
	}
				
	// Query the database
	$conn->query("UPDATE troopers SET ".$queryAdd." = '".cleanInput($_POST['permission'])."' WHERE id = '".cleanInput($_POST['trooperid'])."'");
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
		$queryAdd = "";
		
		// Which club to get
		if($_POST['club'] <= count($squadArray))
		{
			$queryAdd = "p501";
		}

		// Set up count
		$clubCount = count($squadArray) + 1;
		
		// Loop through clubs
		foreach($clubArray as $club => $club_value)
		{
			// Match
			if($_POST['club'] == $clubCount)
			{
				// Add to query
				$queryAdd = "".$club_value['db']."";
			}
			
			// Increment
			$clubCount++;
		}
	
		foreach($_POST['trooper'] as $trooper)
		{
			$conn->query("UPDATE troopers SET ".$queryAdd." = '2' WHERE id = '".cleanInput($trooper)."'");
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
			$queryAdd = "";
			
			// Which club to get
			if($_POST['club'] <= count($squadArray))
			{
				$queryAdd = "p501";
			}
			
			// Set up count
			$clubCount = count($squadArray) + 1;
			
			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Match
				if($_POST['club'] == $clubCount)
				{
					// Add to query
					$queryAdd = "".$club_value['db']."";
				}
				
				// Increment
				$clubCount++;
			}
			
			$conn->query("UPDATE troopers SET ".$queryAdd." = '3' WHERE id = '".cleanInput($trooper)."'");
		}
		
		// Send JSON
		$array = array('data' => 'Success!');
		echo json_encode($array);
	}
}

/******************** PHOTOS *******************************/

// Delete Photo
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
			// If is trooper or is admin
			if($db->trooperid == $_SESSION['id'] || isAdmin())
			{
				// Delete file if exist
				if(file_exists("images/uploads/" . $db->filename))
				{
					unlink("images/uploads/" . $db->filename);
				}
				
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
		$array = array('data' => 'Failed to delete photo!');
		echo json_encode($array);
	}
	else
	{
		// Send JSON
		$array = array('data' => 'Deleted!');
		echo json_encode($array);
	}
}

// Make Admin / Regular
if(isset($_GET['do']) && $_GET['do'] == "adminphoto" && loggedIn())
{
	// Query database for photos
	$query = "SELECT * FROM uploads WHERE id = '".cleanInput($_POST['photoid'])."'";
	
	// Count photos
	$i = 0;
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->admin == 0)
			{
				// Query database
				$conn->query("UPDATE uploads SET admin = '1' WHERE id = '".cleanInput($_POST['photoid'])."'");
			}
			else
			{
				// Query database
				$conn->query("UPDATE uploads SET admin = '0' WHERE id = '".cleanInput($_POST['photoid'])."'");
			}
			
			// Increment
			$i++;
		}
	}
	
	// If failed...
	if($i == 0)
	{
		// Send JSON
		$array = array('data' => 0);
		echo json_encode($array);
	}
	else
	{
		// Send JSON
		$array = array('data' => 1);
		echo json_encode($array);
	}
}

/******************** MODIFY SIGN UP FROM EVENT PAGE *******************************/

if(isset($_GET['do']) && $_GET['do'] == "modifysignup" && loggedIn())
{
	// Prevent if troop full
	$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".cleanInput($_POST['troopid'])."' AND status != '4' AND status != '1'");

	// Set up add to query
	$addToQuery = "";

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Add
		$addToQuery .= " + SUM(".$club_value['dbLimit'].")";
	}
	
	// Hack Check
	$query = "SELECT * FROM event_sign_up WHERE (trooperid = '".cleanInput($_SESSION['id'])."' OR addedby = '".cleanInput($_SESSION['id'])."') AND troopid = '".cleanInput($_POST['troopid'])."'";
	
	// Used to see if record exists
	$i = 0;
	
	// Used to determine if a friend
	$isFriend = false;
	
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

	// Set up limits
	$limit501st = "";

	// Set up limit totals
	$limit501stTotal = eventClubCount(cleanInput($_POST['troopid']), 0);

	// Set up club count
	$clubCount = 1;

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Set up limits
		${$club_value['dbLimit']} = "";

		// Set up limit totals
		${$club_value['dbLimit'] . "Total"} = eventClubCount(cleanInput($_POST['troopid']), $clubCount);

		// Increment club count
		$clubCount++;
	}

	// Set total
	$limitTotal = 0;

	// Is this a total trooper event?
	$totalTrooperEvent = false;

	// Query to get limits
	$query = "SELECT * FROM events WHERE id = '".cleanInput($_POST['troopid'])."'";

	// Output
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Add total
			$limitTotal += $db->limit501st;

			// Set 501
			$limit501st = $db->limit501st;

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Set variables
				${$club_value['dbLimit']} = $db->{$club_value['dbLimit']};

				// Add variables
				$limitTotal += $db->{$club_value['dbLimit']};
			}

			// Check for total limit set, if it is, replace limit with it
			if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
			{
				$limitTotal = $db->limitTotalTroopers;
				$totalTrooperEvent = true;
			}
		}
	}
	
	// Kill hack
	if($i == 0)
	{
		die("Can not do this.");
	}

	// Set status from post
	$status = cleanInput($_POST['status']);

	// Set troop full - not used at the moment, but will keep it here for now
	$troopFull = false;

	// Check for total limit set, if it is, check if troop is full based on total
	if($totalTrooperEvent)
	{
		/* TOTAL TROOPERS */

		if($limitTotal - eventClubCount(cleanInput($_POST['troopid']), "all") <= 0 && $status != 4 && inEvent(cleanInput($_POST['trooperid']), cleanInput($_POST['troopid']))['inTroop'] != 1)
		{
			// Troop is full, set to stand by
			$status = 1;

			// Set troop full
			$troopFull = true;
		}
	}
	else
	{
		/* CHECK IF SQUADS / CLUB ARE FULL */

		// 501
		if((getCostumeClub(cleanInput($_POST['costume'])) == 0 && ($limit501st - eventClubCount(cleanInput($_POST['troopid']), 0)) <= 0) && $status != 4 && inEvent(cleanInput($_POST['trooperid']), cleanInput($_POST['troopid']))['inTroop'] != 1)
		{
			// Troop is full, set to stand by
			$status = 1;

			// Set troop full
			$troopFull = true;
		}

		// Loop through clubs
		foreach($clubArray as $club => $club_value)
		{
			// Loop through costumes
			foreach($club_value['costumes'] as $costume)
			{
				// Make sure not a dual costume
				if($costume != $dualCostume)
				{
					// Check club
					if((getCostumeClub(cleanInput($_POST['costume'])) == $costume && (${$club_value['dbLimit']} - eventClubCount(cleanInput($_POST['troopid']), $costume)) <= 0) && $status != 4 && inEvent(cleanInput($_POST['trooperid']), cleanInput($_POST['troopid']))['inTroop'] != 1)
					{
						// Troop is full, set to stand by
						$status = 1;

						// Set troop full
						$troopFull = true;
					}
				}
			}
		}
	}

	// Update SQL
	$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costume'])."', costume_backup = '".cleanInput($_POST['costume_backup'])."', status = '".$status."' WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND troopid = '".cleanInput($_POST['troopid'])."' AND id = '".cleanInput($_POST['signid'])."'");
	
	// Update notifications
	// Going
	if($status == 0)
	{
		// Send to database to send out notifictions later
		$conn->query("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES ('".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['trooperid'])."', '1')");
	}
	// Cancel
	else if($status == 4)
	{
		// Send to database to send out notifictions later
		$conn->query("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES ('".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['trooperid'])."', '2')");
	}

	// If cancel
	if($status == 4 && !isEventFull(cleanInput($_POST['troopid']), cleanInput($_POST['costume'])))
	{
		// Check if someone is on the wait list, and set to going
		$conn->query("UPDATE event_sign_up SET status = '0' WHERE troopid = '".cleanInput($_POST['troopid'])."' AND status = '1' AND ".getCostumeClub(cleanInput($_POST['costume']))." = (SELECT club FROM costumes WHERE id = event_sign_up.costume) ORDER BY signuptime DESC LIMIT 1");
	}

	// Update troopers remaining
	$data = '
	<ul>
		<li>This event is limited to '.$limitTotal.' troopers. ';

		// Check for total limit set, if it is, add remaining troopers
		if($totalTrooperEvent)
		{
			$data .= '
			' . troopersRemaining($limitTotal, eventClubCount($limitTotal, "all")) . '</li>';
		}
		else
		{
			$data .= '
			</li>

			<li>This event is limited to '.$limit501st.' 501st troopers. '.troopersRemaining($limit501st, eventClubCount(cleanInput($_POST['troopid']), 0)).' </li>';


			// Set up club count
			$clubCount = 1;

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				$data .= '
				<li>This event is limited to '.${$club_value['dbLimit']}.' '. $club_value['name'] .' troopers. '.troopersRemaining(${$club_value['dbLimit']}, eventClubCount(cleanInput($_POST['troopid']), $clubCount)).'</li>';

				// Increment club count
				$clubCount++;
			}
		}

	$data .= '
	</ul>';

	// Send JSON
	$array = array('success' => 'true', 'status' => $status, 'troopFull' => $troopFull, 'limit501st' => $limit501st, 'limit501stTotal' => $limit501stTotal, 'troopersRemaining' => $data);

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		$array[$club_value['dbLimit']] = ${$club_value['dbLimit']};
		$array[$club_value['dbLimit'] . "Total"] = ${$club_value['dbLimit'] . "Total"};
	}

	echo json_encode($array);
}

/************************* Comments **************************************/

// Enter comment into database
if(isset($_GET['do']) && $_GET['do'] == "postcomment" && isset($_POST['submitComment']) && loggedIn())
{
	if(strlen(trim($_POST['comment'])) > 0 && ($_POST['important'] == 0 || $_POST['important'] == 1))
	{
		// Query - check if a comment has been recently posted by this trooper
		$commentCheck = $conn->query("SELECT id FROM comments WHERE comment = '".cleanInput($_POST['comment'])."' AND posted > NOW() - INTERVAL 5 MINUTE") or die($conn->error);

		// Check comment check
		if($commentCheck->num_rows == 0)
		{
			// Query the database
			$conn->query("INSERT INTO comments (troopid, trooperid, comment, important) VALUES ('".cleanInput($_POST['eventId'])."', '".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['comment'])."', '".cleanInput($_POST['important'])."')") or die($conn->error);

			// Get last ID of comment
			$last_id = $conn->insert_id;

			// Get thread ID
			$thread_id = getEventThreadID(cleanInput($_POST['eventId']));

			// Check if there is a forum thread
			if($thread_id > 0)
			{
				// Post to forum
				$post = createPost($thread_id, cleanInput($_POST['comment']), getUserID($_SESSION['id']));

				// Update comment
				$conn->query("UPDATE comments SET post_id = '".$post['post']['post_id']."' WHERE id = '".$last_id."'");
			}
		}

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

			// Add a last OR at end if linked data
			if($j > 0)
			{
				$troops .= "OR ";
			}
		}
		
		// Check which type of link
		if(!isset($link) || isset($link) && $link <= 0)
		{
			$link = cleanInput($_POST['eventId']);
		}

		// Load comments for return data
		$query = "SELECT * FROM comments WHERE ".$troops."troopid = '".$link."' ORDER BY posted DESC";

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
					$admin = '<span style="margin-right: 15px;"><a href="#/" id="deleteComment_'.$db->id.'" name="'.$db->id.'"><img src="images/trash.png" alt="Delete Comment" /></a></span>';
				}
				
				// If is trooper, set up edit option
				if($db->trooperid == $_SESSION['id'])
				{
					$admin .= '<span style="margin-right: 15px;"><a href="#/" id="editComment_'.$db->id.'" name="'.$db->id.'"><img src="images/edit.png" alt="Edit Comment" /></a></span>';
				}

				// Convert date/time
				$newdate = formatTime($db->posted, "F j, Y, g:i a");

				$data .= '
				<tr>
					<td><span style="float: left;">'.$admin.'<a href="#/" id="quoteComment_'.$db->id.'" name="'.$db->id.'" troopername="'.getName($db->trooperid).'" tkid="'.getTKNumber($db->trooperid, true).'" trooperid="'.$db->trooperid.'"><img src="images/quote.png" alt="Quote Comment"></a></span> <a href="index.php?profile='.$db->trooperid.'">'.$name.' - '.readTKNumber(getTKNumber($db->trooperid), getTrooperSquad($db->trooperid)).'</a><br />'.$newdate.'</td>
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
		
		// Check comment check
		if($commentCheck->num_rows == 0)
		{
			// Send to database to send out notifictions later
			$conn->query("INSERT INTO notification_check (troopid, commentid) VALUES ('".cleanInput($_POST['eventId'])."', '".$last_id."')");
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
		// Don't allow delete N/A costume
		foreach($clubArray as $club => $club_value)
		{
			if(cleanInput($_POST['costumeID']) == $club_value['naCostume'])
			{
				$array = array('success' => 'fail');
				echo json_encode($array);

				return false;
			}
		}

		if(cleanInput($_POST['costumeID']) == $dualNA)
		{
			$array = array('success' => 'fail');
			echo json_encode($array);

			return false;
		}

		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted costume ID: " . cleanInput($_POST['costumeID']) . "", cleanInput($_SESSION['id']), 2, convertDataToJSON("SELECT * FROM costumes WHERE id = '".cleanInput($_POST['costumeID'])."'"));

		// Query the database
		$conn->query("DELETE FROM costumes WHERE id = '".cleanInput($_POST['costumeID'])."'");
		
		// Update other databases that are affected
		$conn->query("UPDATE event_sign_up SET costume = '".replaceCostumeID(cleanInput($_POST['costumeID']))."' WHERE costume = '".cleanInput($_POST['costumeID'])."'");
		$conn->query("UPDATE event_sign_up SET costume_backup = '".replaceCostumeID(cleanInput($_POST['costumeID']))."' WHERE costume_backup = '".cleanInput($_POST['costumeID'])."'");

		$array = array('success' => 'pass');
		echo json_encode($array);
	}
	
	// Add costume...
	if(isset($_POST['addCostumeButton']))
	{
		$message = "Costume added!";

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
			sendNotification(getName($_SESSION['id']) . " has added costume: " . cleanInput($_POST['costumeName']), cleanInput($_SESSION['id']), 1, convertDataToJSON("SELECT * FROM costumes WHERE id = '".$last_id."'"));
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
				<option value="4">Other</option>
				<option value="5">Dual (501st + Rebel)</option>
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
			</select>

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
		sendNotification(getName($_SESSION['id']) . " has edited costume ID [" . cleanInput($_POST['costumeIDEdit']) . "] to " . cleanInput($_POST['costumeNameEdit']), cleanInput($_SESSION['id']), 3, convertDataToJSON("SELECT * FROM costumes WHERE id = '".cleanInput($_POST['costumeIDEdit'])."'"));
	}
}

/************************ EVENT NOTIFICATIONS ***************************************/

// Event notifications button pressed
if(isset($_GET['do']) && $_GET['do'] == "eventsubscribe" && isset($_POST['eventsubscribe']) && loggedIn())
{
	// Set up variable
	$message = "";

	// Query to see if trooper is subscribed already
	$isSubscribed = $conn->query("SELECT * FROM event_notifications WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($_POST['event'])."'");

	// Check if subscribed
	if($isSubscribed->num_rows > 0)
	{
		// Already subscribed, delete information
		$conn->query("DELETE FROM event_notifications WHERE trooperid = '".cleanInput($_SESSION['id'])."' AND troopid = '".cleanInput($_POST['event'])."'");

		// Message
		$message = "You are now unsubscribed from this event.";
	}
	else
	{
		// Subscribe
		$conn->query("INSERT INTO event_notifications (trooperid, troopid) VALUES ('".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['event'])."')");

		// Message
		$message = "You are now subscribed to this event and will receive e-mail notifications.";
	}

	// Send JSON
	$array = array('message' => $message);
	echo json_encode($array);
}

/************************ TITLES ***************************************/
// Titles to troopers
if(isset($_GET['do']) && $_GET['do'] == "assigntitles" && loggedIn() && isAdmin())
{
	// Get title details
	if(isset($_POST['gettitle']))
	{
		// Set up return variable
		$hasTitle = 0;

		// Get data
		$query = "SELECT * FROM title_troopers WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND titleid = '".cleanInput($_POST['titleid'])."'";
		
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Set
				$hasTitle = 1;
			}
		}
		
		// Output
		$array = array('hasTitle' => $hasTitle);
		echo json_encode($array);
	}
	
	// Title submitted for deletion...
	if(isset($_POST['submitDeleteTitle']))
	{
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted title ID: " . cleanInput($_POST['titleID']), cleanInput($_SESSION['id']), 21, convertDataToJSON("SELECT * FROM titles WHERE id = '".cleanInput($_POST['titleID'])."'"));

		// Query the database
		$conn->query("DELETE FROM titles WHERE id = '".cleanInput($_POST['titleID'])."'");

		// Delete from the other database
		$conn->query("DELETE FROM title_troopers WHERE titleid = '".cleanInput($_POST['titleID'])."'");
	}

	// Add title...
	if(isset($_POST['submitAddTitle']))
	{
		$message = "Title added!";

		// Set up in advance to prevent error
		$last_id = 0;

		// Check if has value
		if(cleanInput($_POST['titleName']) == "")
		{
			$message = "Title must have a name.";
		}
		else
		{
			// Query the database
			$conn->query("INSERT INTO titles (title, icon, forum_id) VALUES ('".cleanInput($_POST['titleName'])."', '".cleanInput($_POST['titleImage'])."', '".cleanInput($_POST['titleForumID'])."')");

			$last_id = $conn->insert_id;
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has added title: " . cleanInput($_POST['titleName']), cleanInput($_SESSION['id']), 20, convertDataToJSON("SELECT * FROM titles WHERE id = '".$last_id."'"));
		}
		
		$returnMessage = '<br /><hr /><br /><h3>Edit Title</h3>';

		// Get data
		$query = "SELECT * FROM titles ORDER BY title";

		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Formatting
				if($i == 0)
				{
					$returnMessage .= '
					<form action="process.php?do=assigntitles" method="POST" name="titleEdit" id="titleEdit">

					<select name="titleIDEdit" id="titleIDEdit">

						<option value="0" SELECTED>Please select a title...</option>';
				}

				$returnMessage .= '<option value="'.$db->id.'" title="'.$db->title.'" titleID="'.$db->id.'" titleImage="'.$db->icon.'">'.$db->title.'</option>';

				// Increment
				$i++;
			}
		}

		if($i == 0)
		{
			$returnMessage .= 'No titles to display.';
		}
		else
		{
			$returnMessage .= '
			</select>

			<div id="editTitleList" name="editTitleList" style="display: none;">

			<p>
				<b>Title:</b>
				<input type="text" name="editTitle" id="editTitle" />
			</p>

			<p>
				<b>Title Image:</b>
				<input type="text" name="editTitleImage" id="editTitleImage" />
			</p>

			<input type="submit" name="submitEditTitle" id="submitEditTitle" value="Edit Title" />

			</div>
			</form>';
		}

		$returnMessage .= '<br /><hr /><br /><h3>Delete Title</h3>';

		// Get data
		$query = "SELECT * FROM titles ORDER BY title";

		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Formatting
				if($i == 0)
				{
					$returnMessage .= '
					<form action="process.php?do=assigntitles" method="POST" name="titleUserDelete" id="titleUserDelete">

					<select name="titleID" id="titleID">';
				}

				$returnMessage .= '<option value="'.$db->id.'">'.$db->title.'</option>';

				// Increment
				$i++;
			}
		}

		if($i == 0)
		{
			$returnMessage .= 'No titles to display.';
		}
		else
		{
			$returnMessage .= '
			</select>

			<input type="submit" name="submitDeleteTitle" id="submitDeleteTitle" value="Delete Title" />
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
					<form action="process.php?do=assigntitles" method="POST" name="titleUser" id="titleUser">

					<select name="userIDTitle" id="userIDTitle">';
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
			$query2 = "SELECT * FROM titles ORDER BY title";

			// Amount of titles
			$j = 0;

			if ($result2 = mysqli_query($conn, $query2))
			{
				while ($db = mysqli_fetch_object($result2))
				{
					// Formatting
					if($j == 0)
					{
						$getId2 = $db->id;
						$returnMessage2 .= '<select id="titleIDAssign" name="titleIDAssign">';
					}

					$returnMessage2 .= '<option value="'.$db->id.'">'.$db->title.'</option>';

					// Increment $j
					$j++;
				}
			}

			// If title exist
			if($j > 0)
			{
				$returnMessage2 .= '
				</select>

				<input type="submit" name="title" id="title" value="Assign" '.hasTitle($getId, $getId2, true).' />
				<input type="submit" name="titleRemove" id="titleRemove" value="Remove" '.hasTitle($getId, $getId2, true, true).' />';
			}
			else
			{
				$returnMessage2 .= 'No titles to display.';
			}
		}

		$returnMessage2 .= '</form>';

		$array = array(array('message' => $message, 'id' => $last_id, 'result' => $returnMessage, 'result2' => $returnMessage2));
		echo json_encode($array);
	}

	// Add's title to trooper
	if(isset($_POST['submitTitle']))
	{
		// Check how many rewards
		$result = mysqli_query($conn, "SELECT count(*) FROM title_troopers WHERE trooperid = '".cleanInput($_POST['userIDTitle'])."' AND titleid = '".cleanInput($_POST['titleIDAssign'])."'");
		$num_rows = mysqli_fetch_row($result)[0];

		$message = "The title was given successfully!";

		// If no duplicates
		if($num_rows == 0)
		{
			// Check title exists query
			$checkExist = $conn->query("SELECT * FROM titles WHERE id = '".cleanInput($_POST['titleIDAssign'])."'");

			// Check if title exists
			if($checkExist->num_rows > 0)
			{
				// Query the database
				$conn->query("INSERT INTO title_troopers (trooperid, titleid) VALUES ('".cleanInput($_POST['userIDTitle'])."', '".cleanInput($_POST['titleIDAssign'])."')");
			
				// Send notification to command staff
				sendNotification(getName($_SESSION['id']) . " has given title ID [" . cleanInput($_POST['titleIDAssign']) . "] to " . getName(cleanInput($_POST['userIDTitle'])), cleanInput($_SESSION['id']), 22, json_encode(array("trooperid" => cleanInput($_POST['userIDTitle']), "titleid" => cleanInput($_POST['titleIDAssign']))));
			}
			else
			{
				$message = "This title does not exist!";
			}
		}
		else
		{
			$message = "Trooper already has this title!";
		}

		$array = array(array('message' => $message));
		echo json_encode($array);
	}
	
	// Remove's title from trooper
	if(isset($_POST['removeTitle']))
	{
		// Check how many rewards
		$result = mysqli_query($conn, "SELECT count(*) FROM title_troopers WHERE trooperid = '".cleanInput($_POST['userIDTitle'])."' AND titleid = '".cleanInput($_POST['titleIDAssign'])."'");
		$num_rows = mysqli_fetch_row($result)[0];

		$message = "The title was removed successfully!";

		// If no duplicates
		if($num_rows > 0)
		{
			// Remove
			$conn->query("DELETE FROM title_troopers WHERE trooperid = '".cleanInput($_POST['userIDTitle'])."' AND titleid = '".cleanInput($_POST['titleIDAssign'])."'");
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has removed title ID [" . cleanInput($_POST['titleIDAssign']) . "] from " . getName(cleanInput($_POST['userIDTitle'])), cleanInput($_SESSION['id']), 24, json_encode(array("trooperid" => cleanInput($_POST['userIDTitle']), "titleid" => cleanInput($_POST['titleIDAssign']))));
		}
		else
		{
			$message = "Trooper does not have this title!";
		}

		$array = array(array('message' => $message));
		echo json_encode($array);
	}

	if(isset($_POST['submitEditTitle']))
	{
		// Query the database
		$conn->query("UPDATE titles SET title = '".cleanInput($_POST['editTitleTitle'])."', icon = '".cleanInput($_POST['editTitleImage'])."', forum_id = '".cleanInput($_POST['editTitleForumID'])."' WHERE id = '".cleanInput($_POST['titleIDEdit'])."'");

		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has edited title ID [" . cleanInput($_POST['titleIDEdit']) . "] to " . cleanInput($_POST['editTitle']), cleanInput($_SESSION['id']), 23, convertDataToJSON("SELECT * FROM titles WHERE id = '".cleanInput($_POST['titleIDEdit'])."'"));
	}
}

/************************ AWARDS ***************************************/
// Awards to troopers
if(isset($_GET['do']) && $_GET['do'] == "assignawards" && loggedIn() && isAdmin())
{
	// Get award details
	if(isset($_POST['getaward']))
	{
		// Set up return variable
		$hasAward = 0;

		// Get data
		$query = "SELECT * FROM award_troopers WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND awardid = '".cleanInput($_POST['awardid'])."'";
		
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Set
				$hasAward = 1;
			}
		}
		
		// Output
		$array = array('hasAward' => $hasAward);
		echo json_encode($array);
	}
	
	// Award submitted for deletion...
	if(isset($_POST['submitDeleteAward']))
	{
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted award ID: " . cleanInput($_POST['awardID']), cleanInput($_SESSION['id']), 4, convertDataToJSON("SELECT * FROM awards WHERE id = '".cleanInput($_POST['awardID'])."'"));

		// Query the database
		$conn->query("DELETE FROM awards WHERE id = '".cleanInput($_POST['awardID'])."'");

		// Delete from the other database
		$conn->query("DELETE FROM award_troopers WHERE awardid = '".cleanInput($_POST['awardID'])."'");
	}

	// Add award...
	if(isset($_POST['submitAddAward']))
	{
		$message = "Award added!";

		// Set up in advance to prevent error
		$last_id = 0;

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
			sendNotification(getName($_SESSION['id']) . " has added award: " . cleanInput($_POST['awardName']), cleanInput($_SESSION['id']), 5, convertDataToJSON("SELECT * FROM awards WHERE id = '".$last_id."'"));
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
			</select>

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
						$getId2 = $db->id;
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

				<input type="submit" name="award" id="award" value="Assign" '.hasAward($getId, $getId2, true).' />
				<input type="submit" name="awardRemove" id="awardRemove" value="Remove" '.hasAward($getId, $getId2, true, true).' />';
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

	// Give award to trooper
	if(isset($_POST['submitAward']))
	{
		// Check how many rewards
		$result = mysqli_query($conn, "SELECT count(*) FROM award_troopers WHERE trooperid = '".cleanInput($_POST['userIDAward'])."' AND awardid = '".cleanInput($_POST['awardIDAssign'])."'");
		$num_rows = mysqli_fetch_row($result)[0];

		$message = "The award was awarded successfully!";

		// If no duplicates
		if($num_rows == 0)
		{
			// Check title exists query
			$checkExist = $conn->query("SELECT * FROM awards WHERE id = '".cleanInput($_POST['awardIDAssign'])."'");

			// Check if title exists
			if($checkExist->num_rows > 0)
			{
				// Query the database
				$conn->query("INSERT INTO award_troopers (trooperid, awardid) VALUES ('".cleanInput($_POST['userIDAward'])."', '".cleanInput($_POST['awardIDAssign'])."')");
				
				// Send notification to command staff
				sendNotification(getName($_SESSION['id']) . " has awarded ID [" . cleanInput($_POST['awardIDAssign']) . "] to " . getName(cleanInput($_POST['userIDAward'])), cleanInput($_SESSION['id']), 6, json_encode(array("trooperid" => cleanInput($_POST['userIDAward']), "awardid" => cleanInput($_POST['awardIDAssign']))));
			}
			else
			{
				$message = "This award does not exist!";
			}
		}
		else
		{
			$message = "Trooper already has this award!";
		}

		$array = array(array('message' => $message));
		echo json_encode($array);
	}
	
	// Remove's award from trooper
	if(isset($_POST['removeAward']))
	{
		// Check how many rewards
		$result = mysqli_query($conn, "SELECT count(*) FROM award_troopers WHERE trooperid = '".cleanInput($_POST['userIDAward'])."' AND awardid = '".cleanInput($_POST['awardIDAssign'])."'");
		$num_rows = mysqli_fetch_row($result)[0];

		$message = "The award was removed successfully!";

		// If no duplicates
		if($num_rows > 0)
		{
			// Remove
			$conn->query("DELETE FROM award_troopers WHERE trooperid = '".cleanInput($_POST['userIDAward'])."' AND awardid = '".cleanInput($_POST['awardIDAssign'])."'");
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has removed award ID [" . cleanInput($_POST['awardIDAssign']) . "] from " . getName(cleanInput($_POST['userIDAward'])), cleanInput($_SESSION['id']), 25, json_encode(array("trooperid" => cleanInput($_POST['userIDAward']), "awardid" => cleanInput($_POST['awardIDAssign']))));
		}
		else
		{
			$message = "Trooper does not have this award!";
		}

		$array = array(array('message' => $message));
		echo json_encode($array);
	}

	// Edit an award
	if(isset($_POST['submitEditAward']))
	{
		// Query the database
		$conn->query("UPDATE awards SET title = '".cleanInput($_POST['editAwardTitle'])."', icon = '".cleanInput($_POST['editAwardImage'])."' WHERE id = '".cleanInput($_POST['awardIDEdit'])."'");

		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has edited award ID [" . cleanInput($_POST['awardIDEdit']) . "] to " . cleanInput($_POST['editAwardTitle']), cleanInput($_SESSION['id']), 7, convertDataToJSON("SELECT * FROM awards WHERE id = '".cleanInput($_POST['awardIDEdit'])."'"));
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
				// Set up link
				$link = '';

				// Create link
				if(get501Info($db->tkid, $db->squad)['link'] != "")
				{
					$link = get501Info($db->tkid, $db->squad)['link'];
				}
				else if($db->squad <= count($squadArray))
				{
					$link = 'https://www.501st.com/memberAPI/v3/legionId/' . $db->tkid;
				}
				
				// Array variables
				$array = array('name' => $db->name, 'email' => $db->email, 'forum' => $db->forum_id, 'phone' => $db->phone, 'squad' => getSquadName($db->squad), 'tkid' => readTKNumber($db->tkid, $db->squad), 'link' => $link);

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// If DB3 defined
					if($club_value['db3Name'] != "")
					{
						$array[$club_value['db3']] = $db->{$club_value['db3']}; 
					}
				}
			}
		}
	}

	echo json_encode($array);
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

		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has denied user ID [" . cleanInput($_POST['userID2']) . "]", cleanInput($_SESSION['id']), 8, convertDataToJSON("SELECT * FROM troopers WHERE id = '".cleanInput($_POST['userID2'])."'"));
		
		// Query the database - troopers
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
		sendNotification(getName($_SESSION['id']) . " has approved user ID [" . cleanInput($_POST['userID2']) . "]", cleanInput($_SESSION['id']), 9, convertDataToJSON("SELECT * FROM troopers WHERE id = '".cleanInput($_POST['userID2'])."'"));
	}
}

// Manage troopers
if(isset($_GET['do']) && $_GET['do'] == "managetroopers" && loggedIn() && isAdmin())
{
	// User submitted for deletion...
	if(isset($_POST['submitDeleteUser']))
	{
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted user ID [" . cleanInput($_POST['userID']) . "]", cleanInput($_SESSION['id']), 10, convertDataToJSON("SELECT * FROM troopers WHERE id = '".cleanInput($_POST['userID'])."'"));

		// Query the database
		$conn->query("DELETE FROM troopers WHERE id = '".cleanInput($_POST['userID'])."'");
		
		// Update other databases that will be affected
		$conn->query("DELETE FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		$conn->query("DELETE FROM award_troopers WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		$conn->query("DELETE FROM comments WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		$conn->query("DELETE FROM event_notifications WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		$conn->query("DELETE FROM notification_check WHERE trooperid = '".cleanInput($_POST['userID'])."'");
		$conn->query("DELETE FROM title_troopers WHERE trooperid = '".cleanInput($_POST['userID'])."'");
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
				$array = array('id' => $db->id, 'name' => $db->name, 'phone' => $db->phone, 'squad' => $db->squad, 'permissions' => $db->permissions, 'p501' => $db->p501, 'tkid' => $db->tkid, 'forumid' => $db->forum_id, 'supporter' => $db->supporter, 'spTrooper' => $db->spTrooper, 'spCostume' => $db->spCostume, 'spAward' => $db->spAward);

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// Push to array
					$array[$club_value['db']] = $db->{$club_value['db']};

					// If DB3 defined
					if($club_value['db3Name'] != "")
					{
						$array[$club_value['db3']] = $db->{$club_value['db3']}; 
					}
				}

				echo json_encode($array);
			}
		}
	}

	// User edit submitted
	if(isset($_POST['submitUserEdit']))
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
		if(cleanInput($_POST['user']) != "" && cleanInput($_POST['squad']) != "" && cleanInput($_POST['tkid']) != "" && cleanInput($_POST['forumid']) != "")
		{
			// Set up message
			$message = "Trooper has been updated!";
			
			// **CUSTOM**
			// Check if Rebel has a Rebel Forum to change status
			if(cleanInput($_POST['pRebel']) != 0 && cleanInput($_POST['rebelforum']) == "")
			{
				// Reset Rebel due to it not being able to put value in spreadsheet
				$_POST['pRebel'] = 0;
				
				// Add to message
				$message = "Rebel Legion member status can not be changed, unless a Rebel Legion Forum username is set.";
			}
			
			// Set up TKID
			$tkid = cleanInput($_POST['tkid']);

			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has updated user ID [" . cleanInput($_POST['userIDE']) . "]", cleanInput($_SESSION['id']), 11, convertDataToJSON("SELECT * FROM troopers WHERE id = '".cleanInput($_POST['userIDE'])."'"));

			// Set up add to query
			$addToQuery = "";

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Add
				$addToQuery .= "".$club_value['db']." = '".cleanInput($_POST[$club_value['db']])."', ";

				// If DB3 defined
				if($club_value['db3Name'] != "")
				{
					$addToQuery .= "".$club_value['db3']." = '".cleanInput($_POST[$club_value['db3']])."', ";
				}
			}
			
			// Query the database
			$conn->query("UPDATE troopers SET name = '".cleanInput($_POST['user'])."', phone = '".cleanInput(cleanInput($_POST['phone']))."', squad = '".cleanInput($_POST['squad'])."', p501 = '".cleanInput($_POST['p501'])."', ".$addToQuery." tkid = '".$tkid."', forum_id = '".cleanInput($_POST['forumid'])."', supporter = '".cleanInput($_POST['supporter'])."' WHERE id = '".cleanInput($_POST['userIDE'])."'") or die($conn->error);

			// If super user, update special permissions
			if(hasPermission(1))
			{
				// Give default value if null
				if(!isset($_POST['spTrooper'])) { $_POST['spTrooper'] = 0; } else { $_POST['spTrooper'] = 1; }
				if(!isset($_POST['spCostume'])) { $_POST['spCostume'] = 0; } else { $_POST['spCostume'] = 1; }
				if(!isset($_POST['spAward'])) { $_POST['spAward'] = 0; } else { $_POST['spAward'] = 1; }

				// Query the database
				$conn->query("UPDATE troopers SET spTrooper = '".cleanInput($_POST['spTrooper'])."', spCostume = '".cleanInput($_POST['spCostume'])."', spAward = '".cleanInput($_POST['spAward'])."', permissions = '".cleanInput($_POST['permissions'])."' WHERE id = '".cleanInput($_POST['userIDE'])."'") or die($conn->error);
			}

			$array = array('success' => 'true', 'newname' => cleanInput($_POST['user']) . " - " . readTKNumber($tkid, getTrooperSquad(cleanInput($_POST['userIDE']))), 'data' => $message);
			echo json_encode($array);
		}
		else
		{
			$array = array('success' => 'failed', 'data' => '');
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

// E-mail settings
if(isset($_GET['do']) && $_GET['do'] == "emailsettings" && loggedIn())
{
	// Check for post request
	if(isset($_POST['setemailsettings']))
	{
		// Check which setting we are changing
		$conn->query("UPDATE troopers SET " . cleanInput($_POST['setting']) . " = CASE " . cleanInput($_POST['setting']) . " WHEN 1 THEN 0 WHEN 0 THEN 1 END WHERE id = '".$_SESSION['id']."'") or die($conn->error);
	}
}

// Request access
if(isset($_GET['do']) && $_GET['do'] == "requestaccess")
{
	if($_POST['submitRequest'])
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
		if($_POST['tkid'] != "" && $_POST['name'] != "" && $_POST['forum_id'] != "" && $_POST['password'] != "" && $_POST['squad'] >= 0)
		{
			$failed = false;

			echo '<ul>';
			
			// Check to see if a forum username exists
			$forum_name_exists = getUserForum(cleanInput($_POST['forumid']));
			
			// Check forum name
			if(isset($forum_name_exists['exact']))
			{
				$failed = true;
				echo '<li>An account was not found on the FL 501st Boards. <a href="https://www.fl501st.com/boards/">Register here</a>.</li>';
			}

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
			
			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// If DB3 defined
				if($club_value['db3Name'] != "")
				{
					// Query Club - if specified
					if(cleanInput($_POST[$club_value['db3']]) != "")
					{
						$clubid = $conn->query("SELECT ".$club_value['db3']." FROM troopers WHERE ".$club_value['db3']." = '".cleanInput($_POST[$club_value['db3']])."'") or die($conn->error);
						
						// Check if club ID exists
						if($clubid->num_rows > 0)
						{
							$failed = true;
							echo '<li>'.$club_value['name'].' ID is already taken. Please contact the '.garrison.' Webmaster for further assistance.</li>';
						}
					}
				}
			}

			// Check if ID exists
			if($idcheck->num_rows > 0)
			{
				$failed = true;
				echo '<li>TKID is taken. If you have troops on the old troop tracker, <a href="index.php?action=setup">click here to request access</a>.</li>';
			}

			// Login with forum
			$forumLogin = loginWithForum(cleanInput($_POST['forumid']), $_POST['password']);

			// Verify forum and password
			if(isset($forumLogin['success']) && $forumLogin['success'] != 1)
			{
				$failed = true;
				echo '<li>Incorrect '.garrison.' Board username and password.</li>';
			}

			if(strlen(cleanInput($_POST['phone'])) < 10 && cleanInput($_POST['phone']) != "")
			{
				$failed = true;
				echo '<li>Enter a valid phone number.</li>';
			}

			// If failed
			if(!$failed)
			{
				// Set up permission vars
				$p501 = 0;

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// Add permission vars
					${$club_value['db']} = 0;
				}
				
				// Set permissions
				// 501
				if(cleanInput($_POST['squad']) <= count($squadArray))
				{
					$p501 = 1;
				}

				// Add to query set up
				$addToQuery1 = "";
				$addToQuery2 = "";
				$addToQuery3 = "";
				$addToQuery4 = "";

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// If has value
					if(isset($_POST[$club_value['db3']]) && (cleanInput($_POST[$club_value['db3']]) != "" || cleanInput($_POST[$club_value['db3']]) > 0))
					{
						// Change value
						${$club_value['db']} = 1;
					}
					else
					{
						// If contains ID
						if (isset($_POST[$club_value['db3']]) && strpos($club_value['db3'], "id") !== false)
						{
							// Set as int
							$_POST[$club_value['db3']] = 0;
						}
					}

					// If database 3 set
					if($club_value['db3'] != "")
					{
						// Add to query
						$addToQuery1 .= "".$club_value['db3'].", ";
						$addToQuery2 .= "'".cleanInput($_POST[$club_value['db3']])."', ";
						$addToQuery3 .= "".$club_value['db'].", ";
						$addToQuery4 .= "'".${$club_value['db']}."', ";
					}
				}
				
				// Insert
				$conn->query("INSERT INTO troopers (name, tkid, email, forum_id, ".$addToQuery1."p501,".$addToQuery3."phone, squad, password) VALUES ('".cleanInput($_POST['name'])."', '".floatval($tkid)."', '".$forumLogin['user']['email']."', '".cleanInput($_POST['forumid'])."',".$addToQuery2."'".$p501."',".$addToQuery4."'".cleanInput($_POST['phone'])."', '".$squad."', '".password_hash($_POST['password'], PASSWORD_DEFAULT)."')") or die($conn->error);
				
				// Last ID
				$last_id = $conn->insert_id;
				
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

// Event Page - Change Status
if(isset($_GET['do']) && $_GET['do'] == "changestatus" && loggedIn() && isAdmin())
{
	// Set up return data
	$message = "";
	$message2 = "";

	// Load event sign up in roster
	$query = "SELECT * FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND troopid = '".cleanInput($_POST['eventid'])."' AND id = ".cleanInput($_POST['signid'])."";

	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// If set to going
			if($db->status == 0)
			{
				// Set to not picked
				$conn->query("UPDATE event_sign_up SET status = '6' WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND troopid = '".cleanInput($_POST['eventid'])."' AND id = ".cleanInput($_POST['signid'])."");

				// Set return data
				$message = '
				Not Picked
				<br />
				<a href="#/" class="button" name="changestatus" trooperid="'.$db->trooperid.'" signid="'.$db->id.'" buttonid="1">Approve</a>';
			}
			// If set to pending
			if($db->status == 5)
			{
				// Set up variables
				$status = 0;
				$text = "Reject";
				$buttonid = 0;

				// Get button type
				if($_POST['buttonid'] == 0)
				{
					// Not picked
					$status = 6;
					$text = "Approve";
					$buttonid = 1;
				}
				else
				{
					// Going
					$status = 0;
					$text = "Reject";
					$buttonid = 0;
				}

				// Set to not picked
				$conn->query("UPDATE event_sign_up SET status = '".$status."' WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND troopid = '".cleanInput($_POST['eventid'])."' AND id = ".cleanInput($_POST['signid'])."");

				// Set return data
				$message = '
				'.getStatus($status).'
				<br />
				<a href="#/" class="button" name="changestatus" trooperid="'.$db->trooperid.'" signid="'.$db->id.'" buttonid="'.$buttonid.'">'.$text.'</a>';
			}
			// If set to not picked
			else if($db->status == 6)
			{
				// Set to going
				$conn->query("UPDATE event_sign_up SET status = '0' WHERE trooperid = '".cleanInput($_POST['trooperid'])."' AND troopid = '".cleanInput($_POST['eventid'])."' AND id = ".cleanInput($_POST['signid'])."");

				// Set return data
				$message = '
				Going
				<br />
				<a href="#/" class="button" name="changestatus" trooperid="'.$db->trooperid.'" signid="'.$db->id.'" buttonid="0">Reject</a>';
			}

			// If is admin
			if(isAdmin())
			{
				// Load trooper counts
				$message2 .= '
				<h3>Admin Trooper Counts</h3>

				<ul style="display:inline-table;">
					<li>501st troopers: '.eventClubCount(cleanInput($_POST['eventid']), 0).' </li>
					<li>Rebel Legion: '.eventClubCount(cleanInput($_POST['eventid']), 1).' </li>
					<li>Mando Mercs: '.eventClubCount(cleanInput($_POST['eventid']), 2).' </li>
					<li>Droid Builders: '.eventClubCount(cleanInput($_POST['eventid']), 3).' </li>
					<li>Other troopers: '.eventClubCount(cleanInput($_POST['eventid']), 4).' </li>
				</ul>';
			}
		}
	}

	$array = array('message' => $message, 'message2' => $message2);
	echo json_encode($array);
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

			// Set up add to query
			$addToQuery1 = "";
			$addToQuery2 = "";

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Add
				$addToQuery1 .= "".$club_value['dbLimit'].", ";
				$addToQuery2 .= "'".cleanInput($_POST[$club_value['dbLimit']])."', ";
			}
			
			// Query the database
			$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limit501st, limitTotalTroopers, ".$addToQuery1."squad) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".cleanInput($date1)."', '".cleanInput($date2)."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitTotalTroopers'])."', ".$addToQuery2."'".cleanInput($_POST['squadm'])."')") or die($conn->error);
			
			// Event ID - Last insert from database
			$eventId = $conn->insert_id;

			// Only create thread if we can and admin allows
			if($_POST['postToBoards'] == 1 && $_POST['squadm'] != 0)
			{
				// Make thread body
				$thread_body = threadTemplate($_POST['eventName'], $_POST['eventVenue'], $_POST['location'], $date1, $date2, $_POST['website'], $_POST['numberOfAttend'], $_POST['requestedNumber'], $_POST['requestedCharacter'], $_POST['secure'], $_POST['blasters'], $_POST['lightsabers'], $_POST['parking'], $_POST['mobility'], $_POST['amenities'], $_POST['comments'], $_POST['referred'], $eventId);

				// Create thread on forum
				$thread = createThread($squadArray[intval($_POST['squadm'] - 1)]['eventForum'], date("m/d/y", strtotime($date1)) . " " . $_POST['eventName'], $thread_body);

				// Lock the thread
				lockThread($thread['thread']['thread_id']);

				// Update event with thread and post IDs
				$conn->query("UPDATE events SET thread_id = '".$thread['thread']['thread_id']."', post_id = '".$thread['thread']['last_post_id']."' WHERE id = '".$eventId."'");
			}

			// Only notify if event is in the future
			if(strtotime($date1) > strtotime("now"))
			{
				// Send to database to send out notifictions later
				$conn->query("INSERT INTO notification_check (troopid) VALUES ($eventId)");
			}
			
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

						// Set up add to query
						$addToQuery1 = "";
						$addToQuery2 = "";

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							// Add
							$addToQuery1 .= "".$club_value['dbLimit'].", ";
							$addToQuery2 .= "'".cleanInput($_POST[$club_value['dbLimit']])."', ";
						}
					
						// Query the database
						$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limit501st, limitTotalTroopers, ".$addToQuery1."squad, link) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".$date1."', '".$date2."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitTotalTroopers'])."', ".$addToQuery2."'".cleanInput($_POST['squadm'])."', '".$eventId."')") or die($conn->error);

						// Last ID
						$last_id = $conn->insert_id;

						// Only create thread if we can and admin allows
						if($_POST['postToBoards'] == 1 && $_POST['squadm'] != 0)
						{
							// Make thread body
							$thread_body = threadTemplate($_POST['eventName'], $_POST['eventVenue'], $_POST['location'], $date1, $date2, $_POST['website'], $_POST['numberOfAttend'], $_POST['requestedNumber'], $_POST['requestedCharacter'], $_POST['secure'], $_POST['blasters'], $_POST['lightsabers'], $_POST['parking'], $_POST['mobility'], $_POST['amenities'], $_POST['comments'], $_POST['referred'], $last_id);

							// Create thread on forum
							$thread = createThread($squadArray[intval($_POST['squadm'] - 1)]['eventForum'], date("m/d/y", strtotime($date1)) . " " . $_POST['eventName'], $thread_body);

							// Lock the thread
							lockThread($thread['thread']['thread_id']);

							// Update event
							$conn->query("UPDATE events SET thread_id = '".$thread['thread']['thread_id']."', post_id = '".$thread['thread']['last_post_id']."' WHERE id = '".$last_id."'");
						}

						// Send notification to command staff
						sendNotification(getName($_SESSION['id']) . " has added an event: [" . $last_id . "][" . cleanInput($_POST['eventName']) . "]", cleanInput($_SESSION['id']), 13, convertDataToJSON("SELECT * FROM events WHERE id = '".$last_id."'"));
					}
				}
			}

			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has added an event: [" . $eventId . "][" . cleanInput($_POST['eventName']) . "]", cleanInput($_SESSION['id']), 13, convertDataToJSON("SELECT * FROM events WHERE id = '".$eventId."'"));

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
		// Query the database
		$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costumeValSelect' . $_POST['trooperSelectEdit'] . ''])."', costume_backup = '".cleanInput($_POST['costumeVal' . $_POST['trooperSelectEdit'] . ''])."', status = '".cleanInput($_POST['statusVal' . $_POST['trooperSelectEdit'] . ''])."' WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."' AND id = '".cleanInput($_POST['signid'])."'") or die($conn->error);

		// If set as attended, check trooper counts
		if(cleanInput($_POST['statusVal' . $_POST['trooperSelectEdit'] . '']) == 3)
		{
			// Check troops for notification
			troopCheck(cleanInput($_POST['trooperSelectEdit']));
		}

		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has edited event ID: [" . cleanInput($_POST['eventId']) . "] by updating trooper ID: [" . cleanInput($_POST['trooperSelectEdit']) . "].", cleanInput($_SESSION['id']), 14, convertDataToJSON("SELECT * FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."' AND id = '".cleanInput($_POST['signid'])."'"));

		// Send back data
		$array = array('success' => 'success', 'id' => $_SESSION['id']);
		echo json_encode($array);
	}

	// Add a trooper to roster
	if(isset($_POST['troopRosterFormAdd']))
	{
		// Does this trooper already exist in roster?
		$query = "SELECT * FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperSelect'])."' AND troopid = '".cleanInput($_POST['troopid'])."' AND trooperid != ".placeholder."";
		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Increment
				$i++;
			}
		}
		
		// Final check before adding to roster
		if(cleanInput($_POST['costume']) != "null" && cleanInput($_POST['status']) != "null" && $i == 0)
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, costume_backup, status) VALUES ('".cleanInput($_POST['trooperSelect'])."', '".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['costumebackup'])."', '".cleanInput($_POST['status'])."')") or die($conn->error);
			$last_id = $conn->insert_id;
			
			// If status is attended
			if(cleanInput($_POST['status']) == 3)
			{
				// Check troops for notification
				troopCheck(cleanInput($_POST['trooperSelect']));
			}
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has added trooper ID [".cleanInput($_POST['trooperSelect'])."] to event ID [" . cleanInput($_POST['troopid']) . "]", cleanInput($_SESSION['id']), 15, convertDataToJSON("SELECT * FROM event_sign_up WHERE id = '".$last_id."'"));
			
			// Send back data
			$array = array('success' => 'success', 'signid' => $last_id);
			echo json_encode($array);
		}
	}

	// Event submitted for deletion...
	if(isset($_POST['submitDelete']))
	{
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has deleted event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']), 16, convertDataToJSON("SELECT * FROM events WHERE id = '".cleanInput($_POST['eventId'])."'"));

		// Get number of events with link
		$getNumOfLinks = $conn->query("SELECT id FROM events WHERE link = '".cleanInput($_POST['eventId'])."'");

		// Delete thread on forum
		if(getEventThreadID(cleanInput($_POST['eventId'])) != 0)
		{
			deleteThread(getEventThreadID(cleanInput($_POST['eventId'])), true);
		}
		
		// Query the database
		$conn->query("DELETE FROM events WHERE id = '".cleanInput($_POST['eventId'])."'");

		// Delete from sign ups - event_sign_up
		$conn->query("DELETE FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'");
		
		// Delete from event_notifications
		$conn->query("DELETE FROM event_notifications WHERE troopid = '".cleanInput($_POST['eventId'])."'");
		
		// Delete from notification_check
		$conn->query("DELETE FROM notification_check WHERE troopid = '".cleanInput($_POST['eventId'])."'");
		
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
	}
	
	// Event submitted for lock...
	if(isset($_POST['submitLock']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '3' WHERE id = '".cleanInput($_POST['eventId'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has locked event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
	}

	// Event submitted for cancelation...
	if(isset($_POST['submitCancel']))
	{
		// Query the database
		$conn->query("UPDATE events SET closed = '2' WHERE id = '".cleanInput($_POST['eventId'])."'");
		
		// Prepare notification
		$query = "SELECT * FROM event_sign_up WHERE troopid = '".cleanInput($_POST['eventId'])."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Insert into notification_check
				$conn->query("INSERT INTO notification_check (troopid, trooperid, troopstatus) VALUES ('".$db->troopid."', '".$db->trooperid."', 2)");
			}
		}

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
	
	// Charity submitted for event...
	if(isset($_POST['submitCharity']))
	{
		// Form Check
		if(!is_numeric($_POST['charityDirectFunds'])) { return false; }
		if(!is_numeric($_POST['charityIndirectFunds'])) { return false; }
		if(!is_numeric($_POST['charityAddHours'])) { return false; }

		// Query the database
		$conn->query("UPDATE events SET charityDirectFunds = '".cleanInput($_POST['charityDirectFunds'])."', charityIndirectFunds = '".cleanInput($_POST['charityIndirectFunds'])."', charityName = '".cleanInput($_POST['charityName'])."', charityAddHours = '".cleanInput($_POST['charityAddHours'])."', charityNote = '".cleanInput($_POST['charityNote'])."' WHERE id = '".cleanInput($_POST['eventId'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has updated charity on event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']), 17, json_encode(array("id" => cleanInput($_POST['eventId']), "charityDirectFunds" => cleanInput($_POST['charityDirectFunds']), "charityIndirectFunds" => cleanInput($_POST['charityIndirectFunds']), "charityName" => cleanInput($_POST['charityName']), "charityAddHours" => cleanInput($_POST['charityAddHours']), "charityNote" => cleanInput($_POST['charityNote']))));
	}

	// Advanced options set...
	if(isset($_POST['submitAdvanced']))
	{
		// Query the database
		$conn->query("UPDATE events SET thread_id = '".cleanInput($_POST['threadIDA'])."', post_id = '".cleanInput($_POST['postIDA'])."' WHERE id = '".cleanInput($_POST['eventId'])."'");
		
		// Send notification to command staff
		sendNotification(getName($_SESSION['id']) . " has set thread ID to [".cleanInput($_POST['threadIDA'])."], post ID to [".cleanInput($_POST['postIDA'])."] on event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']), 26, json_encode(array("id" => cleanInput($_POST['eventId']), "thread_id" => cleanInput($_POST['threadIDA']), "post_id" => cleanInput($_POST['postIDA']))));
	}

	// Remove trooper from roster
	if(isset($_POST['removetrooper']))
	{
		if(isset($_POST['trooperSelectEdit']) && $_POST['trooperSelectEdit'] >= 0)
		{
			$array = array('success' => 'true', 'data' => 'Trooper removed!');
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has removed trooper ID [".cleanInput($_POST['trooperSelectEdit'])."] on event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']), 18, convertDataToJSON("SELECT * FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."' AND id = '".cleanInput($_POST['signid'])."'"));

			// Query the database
			$conn->query("DELETE FROM event_sign_up WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."' AND id = '".cleanInput($_POST['signid'])."'");
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
		
		$query2 .= " ORDER BY FIELD(costume, ".$mainCostumes.") DESC, costume";
		
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
								<th>Selection</th>	<th>Trooper TKID / Name</td>	<th>Trooper Costume</th>	<th>Trooper Backup Costume</th>	<th>Trooper Status</th>';
				}

				// List troopers
				echo '
				<tr name="roster_'.$db->trooperid.'" signid="'.$db->id.'">
					<td>
						<input type="hidden" name="tkid" signid="'.$db->id.'" value = "'.getTKNumber($db->trooperid).'" />
						<input type="hidden" name="troopername" signid="'.$db->id.'" value = "'.getName(cleanInput($db->trooperid)).'" />
						<input type="hidden" name="eventId" signid="'.$db->id.'" value = "'.cleanInput($_POST['eventId']).'" />
						<input type="radio" name="trooperSelectEdit" signid="'.$db->id.'" value="'.$db->trooperid.'" signid="'.$db->id.'" />
					</td>

					<td>
						<div name="tknumber1'.$db->trooperid.'" signid="'.$db->id.'"><a href="index.php?profile='.$db->trooperid.'" target="_blank">'.readTKNumber(getTKNumber($db->trooperid), getSquadID($db->trooperid)).' - '.getName($db->trooperid).'</a></div>
					</td>

					<td>
						<div name="costume1'.$db->trooperid.'" signid="'.$db->id.'">'.ifEmpty(getCostume($db->costume), "N/A").'</div>
						<div name="costume2'.$db->trooperid.'" signid="'.$db->id.'" style="display:none;">
							<select name="costumeValSelect'.$db->trooperid.'" signid="'.$db->id.'">';
							
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
						<div name="backup1'.$db->trooperid.'" signid="'.$db->id.'">'.ifEmpty(getCostume($db->costume_backup), "N/A").'</div>
						<div name="backup2'.$db->trooperid.'" signid="'.$db->id.'" style="display:none;">

						<select name="costumeVal'.$db->trooperid.'" signid="'.$db->id.'">';

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
						<div name="status1'.$db->trooperid.'" signid="'.$db->id.'">'.getStatus($db->status).'</div>
						<div name="status2'.$db->trooperid.'" signid="'.$db->id.'" style="display:none;">
							<select name="statusVal'.$db->trooperid.'" signid="'.$db->id.'">
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
						<th>Selection</th>	<th>Trooper TKID / Name</td>	<th>Trooper Costume</th>	<th>Trooper Backup Costume</th>	<th>Trooper Status</th>
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
		$query = "SELECT troopers.id AS troopida, troopers.name AS troopername, troopers.tkid, troopers.squad FROM troopers WHERE NOT EXISTS (SELECT event_sign_up.trooperid FROM event_sign_up WHERE event_sign_up.trooperid = troopers.id AND event_sign_up.troopid = '".cleanInput($_POST['eventId'])."' AND event_sign_up.trooperid != ".placeholder.") ORDER BY troopers.name";

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
				$array = array('id' => $db->id, 'name' => $db->name, 'venue' => $db->venue, 'dateStart' => $db->dateStart, 'dateEnd' => $db->dateEnd, 'website' => $db->website, 'numberOfAttend' => $db->numberOfAttend, 'requestedNumber' => $db->requestedNumber, 'requestedCharacter' => $db->requestedCharacter, 'secureChanging' => $db->secureChanging, 'blasters' => $db->blasters, 'lightsabers' => $db->lightsabers, 'parking' => $db->parking, 'mobility' => $db->mobility, 'amenities' => $db->amenities, 'referred' => $db->referred, 'comments' => $db->comments, 'location' => $db->location, 'squad' => $db->squad, 'label' => $db->label, 'postComment' => $db->postComment, 'notes' => $db->notes, 'limitedEvent' => $db->limitedEvent, 'limitTo' => $db->limitTo, 'limit501st' => $db->limit501st, 'limitTotalTroopers' => $db->limitTotalTroopers, 'closed' => $db->closed, 'charityDirectFunds' => $db->charityDirectFunds, 'charityIndirectFunds' => $db->charityIndirectFunds, 'charityName' => $db->charityName, 'charityAddHours' => $db->charityAddHours,'charityNote' => $db->charityNote, 'eventLink' => $db->link, 'thread_id' => $db->thread_id, 'post_id' => $db->post_id);

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// Add
					$array[$club_value['dbLimit']] = $db->{$club_value['dbLimit']};
				}

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
			
			// Set up add to query
			$addToQuery = "";
			
			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Add
				$addToQuery .= "" . $club_value['dbLimit'] . " = '".cleanInput($_POST[$club_value['dbLimit']])."', ";
			}
			
			// If event is linked
			if($_POST['eventLink'] > 0)
			{
				// Query the database - linked
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', ".$addToQuery." limit501st = '".cleanInput($_POST['limit501st'])."', limitTotalTroopers = '".cleanInput($_POST['limitTotalTroopers'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventLink'])."' OR id = '".cleanInput($_POST['eventLink'])."'") or die($conn->error);

				// Update if limited event
				resetTrooperStatus(cleanInput($_POST['eventIdE']), cleanInput($_POST['eventLink']));
				resetTrooperStatus(cleanInput($_POST['eventLink']));
				
				// Update date
				$conn->query("UPDATE events SET dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."' WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);

				// Query database for event info from above
				$query = "SELECT * FROM events WHERE thread_body != 0 AND id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventLink'])."' OR id = '".cleanInput($_POST['eventLink'])."'";
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						// Make thread body
						$thread_body = threadTemplate($_POST['eventName'], $_POST['eventVenue'], $_POST['location'], $date1, $date2, $_POST['website'], $_POST['numberOfAttend'], $_POST['requestedNumber'], $_POST['requestedCharacter'], $_POST['secure'], $_POST['blasters'], $_POST['lightsabers'], $_POST['parking'], $_POST['mobility'], $_POST['amenities'], $_POST['comments'], $_POST['referred'], $db->id);

						// Update thread
						editPost($db->post_id, $thread_body);
						moveThread($db->thread_id, $squadArray[intval(cleanInput($_POST['squadm']) - 1)]['eventForum']);
					}
				}
			}
			else if($getNumOfLinks->num_rows > 0)
			{
				// Query the database - linked
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', ".$addToQuery."limit501st = '".cleanInput($_POST['limit501st'])."', limitTotalTroopers = '".cleanInput($_POST['limitTotalTroopers'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);

				// Update if limited event
				resetTrooperStatus(cleanInput($_POST['eventIdE']), cleanInput($_POST['eventIdE']));
				
				// Update date
				$conn->query("UPDATE events SET dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."' WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);

				// Query database for event info from above
				$query = "SELECT * FROM events WHERE thread_id != 0 AND post_id != 0 AND (id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventIdE'])."')";
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						// Make thread body
						$thread_body = threadTemplate($_POST['eventName'], $_POST['eventVenue'], $_POST['location'], $date1, $date2, $_POST['website'], $_POST['numberOfAttend'], $_POST['requestedNumber'], $_POST['requestedCharacter'], $_POST['secure'], $_POST['blasters'], $_POST['lightsabers'], $_POST['parking'], $_POST['mobility'], $_POST['amenities'], $_POST['comments'], $_POST['referred'], $db->id);

						// Update thread
						editPost($db->post_id, $thread_body);
						moveThread($db->thread_id, $squadArray[intval(cleanInput($_POST['squadm']) - 1)]['eventForum']);
					}
				}
			}
			else
			{
				// Make thread body
				$thread_body = threadTemplate($_POST['eventName'], $_POST['eventVenue'], $_POST['location'], $date1, $date2, $_POST['website'], $_POST['numberOfAttend'], $_POST['requestedNumber'], $_POST['requestedCharacter'], $_POST['secure'], $_POST['blasters'], $_POST['lightsabers'], $_POST['parking'], $_POST['mobility'], $_POST['amenities'], $_POST['comments'], $_POST['referred'], $_POST['eventIdE']);

				// Update thread
				editPost(getEventPostID(cleanInput($_POST['eventIdE'])), $thread_body);
				moveThread(getEventThreadID(cleanInput($_POST['eventIdE'])), $squadArray[intval(cleanInput($_POST['squadm']) - 1)]['eventForum']);
				
				// Query the database - if not linked
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', ".$addToQuery."limit501st = '".cleanInput($_POST['limit501st'])."', limitTotalTroopers = '".cleanInput($_POST['limitTotalTroopers'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."'") or die($conn->error);

				// Update if limited event
				resetTrooperStatus(cleanInput($_POST['eventIdE']));
			}
			
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

						// Set up add to query
						$addToQuery1 = "";
						$addToQuery2 = "";

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							// Add
							$addToQuery1 .= "'".cleanInput($_POST[$club_value['dbLimit']])."', ";
							$addToQuery2 .= "" . $club_value['dbLimit'] . ", ";
						}
					
						// Query the database
						$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limit501st, limitTotalTroopers, ".$addToQuery2." squad, link) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".$date1."', '".$date2."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitTotalTroopers'])."', ".$addToQuery1."'".cleanInput($_POST['squadm'])."', '".$link."')") or die($conn->error);

						$last_id = $conn->insert_id;
						
						// Send notification to command staff
						sendNotification(getName($_SESSION['id']) . " has added a shift: [" . $link . "]", cleanInput($_SESSION['id']), 19, convertDataToJSON("SELECT * FROM events WHERE id = '".$last_id."'"));
						
						// We just sent a notification, don't send another below
						$sendNotificationCheck = false;
					}
				}
			}
			
			// Send notification that event was edited
			sendNotification(getName($_SESSION['id']) . " has edited event ID: [" . cleanInput($_POST['eventIdE']) . "]", cleanInput($_SESSION['id']), 14, convertDataToJSON("SELECT * FROM events WHERE id = '".cleanInput($_POST['eventIdE'])."'"));

			// For updating title, send date back
			$newDate = "[" . date("l", strtotime($date1)) . " : " . date("m/d - h:i A", strtotime($date1)) . " - " . date("h:i A", strtotime($date2)) . "] ";

			$array = array('success' => 'true', 'data' => 'Event has been updated!', 'newdate' => $newDate);
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
		// Set trooper ID
		$trooperID = 0;

		// Check if this is add friend
		if(isset($_POST['addfriend']))
		{
			// Prevent bug of getting signed up twice
			$eventCheck = inEvent(cleanInput($_POST['trooperSelect']), cleanInput($_POST['event']));

			// Set
			$trooperID = cleanInput($_POST['trooperSelect']);
		}
		else
		{
			// Prevent bug of getting signed up twice
			$eventCheck = inEvent(cleanInput($_SESSION['id']), cleanInput($_POST['event']));

			// Set
			$trooperID = cleanInput($_SESSION['id']);
		}

		// Check if already in troop and exclude placeholder account
		if($eventCheck['inTroop'] == 1 && $trooperID != placeholder)
		{
			die("ALREADY IN THIS TROOP!");
		}

		// End prevent bug of getting signed up twice
		
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

		// Get status post variable
		$status = cleanInput($_POST['status']);

		// Check to see if this event is full

		// Set up limits
		$limit501st = "";

		// Set up limit total
		$limit501stTotal = eventClubCount(cleanInput($_POST['event']), 0);

		// Set up club count
		$clubCount = 1;

		// Loop through clubs
		foreach($clubArray as $club => $club_value)
		{
			// Set up limits
			${$club_value['dbLimit']} = "";

			// Set up limit totals
			${$club_value['dbLimit'] . "Total"} = eventClubCount(cleanInput($_POST['event']), $clubCount);

			// Increment club count
			$clubCount++;
		}

		// Query to get limits
		$query = "SELECT * FROM events WHERE id = '".cleanInput($_POST['event'])."'";

		// Set limit total
		$limitTotal = 0;

		// Is this a total trooper event?
		$totalTrooperEvent = false;

		// Output
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Set 501
				$limit501st = $db->limit501st;

				// Add
				$limitTotal += $db->limit501st;


				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// Set
					${$club_value['dbLimit']} = $db->{$club_value['dbLimit']};

					// Add
					$limitTotal += $db->{$club_value['dbLimit']};
				}

				// Check for total limit set, if it is, replace limit with it
				if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
				{
					$limitTotal = $db->limitTotalTroopers;
					$totalTrooperEvent = true;
				}
			}
		}

		// Set troop full - not used at the moment, but will keep it here for now
		$troopFull = false;

		if($totalTrooperEvent)
		{
			/* TOTAL TROOPERS */
			
			if($limitTotal - eventClubCount(cleanInput($_POST['event']), "all") <= 0 && $status != 4)
			{
				// Troop is full, set to stand by
				$status = 1;

				// Set troop full
				$troopFull = true;
			}
		}
		else
		{
			/* CHECK IF SQUADS / CLUB ARE FULL */

			// 501
			if((getCostumeClub(cleanInput($_POST['costume'])) == 0 && ($limit501st - eventClubCount(cleanInput($_POST['event']), 0)) <= 0) && $status != 4)
			{
				// Troop is full, set to stand by
				$status = 1;

				// Set troop full
				$troopFull = true;
			}

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Loop through costumes
				foreach($club_value['costumes'] as $costume)
				{
					// Make sure not a dual costume
					if($costume != $dualCostume)
					{
						// Check club
						if((getCostumeClub(cleanInput($_POST['costume'])) == $costume && (${$club_value['dbLimit']} - eventClubCount(cleanInput($_POST['event']), $costume)) <= 0) && $status != 4)
						{
							// Troop is full, set to stand by
							$status = 1;

							// Set troop full
							$troopFull = true;
						}
					}
				}
			}
		}

		// End of check to see if this event is full
		
		// Check if this is add friend
		if(isset($_POST['addfriend']))
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup, addedby) VALUES ('".cleanInput($_POST['trooperSelect'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".$status."', '".cleanInput($_POST['backupcostume'])."', '".cleanInput($_SESSION['id'])."')") or die($conn->error);
			
			// Send to database to send out notifictions later
			$conn->query("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES ('".cleanInput($_POST['event'])."', '".cleanInput($_POST['trooperSelect'])."', '1')");
		}
		else
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup) VALUES ('".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".$status."', '".cleanInput($_POST['backupcostume'])."')") or die($conn->error);
			
			// Send to database to send out notifictions later
			$conn->query("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES ('".cleanInput($_POST['event'])."', '".cleanInput($_SESSION['id'])."', '1')");
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
				$query2 = "SELECT event_sign_up.id AS signId, event_sign_up.note, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, event_sign_up.status, event_sign_up.signuptime, troopers.id AS trooperId, troopers.name, troopers.tkid, troopers.squad FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".cleanInput($_POST['event'])."' ORDER BY event_sign_up.id ASC";
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
							<input type="hidden" name="troopidC" id="troopidC" value="'.cleanInput($_POST['event']).'" />
							<input type="hidden" name="myId" id="myId" value="'.cleanInput($_SESSION['id']).'" />

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
									'.drawSupportBadge($db2->trooperId).'
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>';

									// If a placeholder account, allow edit name
									if($db2->trooperId == placeholder)
									{
										$data .= '
										<input type="text" name="placeholdertext" signid="'.$db2->signId.'" value="'.$db2->note.'" />';
									}

									// Show who added the trooper
									if($db2->addedby != 0)
									{
										$data .= '
										<br /><small>Added by:<br />' . getName($db2->addedby) . '</small>';
									}

								$data .= '
								</td>
									
								<td>
									'.readTKNumber($db2->tkid, $db2->squad).'
								</td>
								
								<td name="trooperRosterCostume" id="trooperRosterCostume">
									<select name="modifysignupFormCostume" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

									// Display costumes
									$query3 = "SELECT * FROM costumes WHERE ";
									
									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " era = '".$db->limitTo."' OR era = '4' AND ";
									}
									
									$query3 .= costume_restrict_query(false, $db2->trooperId) . " ORDER BY FIELD(costume, ".$mainCostumes."".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume";
									
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
									<select name="modiftybackupcostumeForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

									// Display costumes
									$query3 = "SELECT * FROM costumes WHERE ";
									
									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " era = '".$db->limitTo."' OR era = '4' AND ";
									}
									
									$query3 .= costume_restrict_query(false, $db2->trooperId) . " ORDER BY FIELD(costume, ".$mainCostumes."".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume";
									
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
								
								<td id="'.$db2->trooperId.'Status" aria-label="'.date("F j, Y, g:i:s a", strtotime($db2->signuptime)).'" data-balloon-pos="up">
								<div name="trooperRosterStatus">';
								
									if($db->limitedEvent != 1)
									{
										// If on stand by
										if($db2->status == 1)
										{
											$data .= '
											<select name="modifysignupStatusForm" id="modifysignupStatusForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
												<option value="0" '.echoSelect(1, $db2->status).'>Stand By</option>
												<option value="4" '.echoSelect(4, $db2->status).'>Cancel</option>
											</select>';
										}
										// Regular
										else
										{
											$data .= '
											<select name="modifysignupStatusForm" id="modifysignupStatusForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
												<option value="0" '.echoSelect(0, $db2->status).'>I\'ll be there!</option>
												<option value="2" '.echoSelect(2, $db2->status).'>Tentative</option>
												<option value="4" '.echoSelect(4, $db2->status).'>Cancel</option>
											</select>';
										}
									}
									else
									{
										$data .= '
										<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
										(Pending Command Staff Approval)';

										// If is admin and limited event
										if(isAdmin() && $db->limitedEvent == 1)
										{
											// Set status
											$data .= '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
										}

										$data .= '
										</div>';								
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
									'.drawSupportBadge($db2->trooperId).'
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>';

									// If a placeholder account, allow edit name
									if($db2->trooperId == placeholder)
									{
										$data .= '
										<input type="text" name="placeholdertext" signid="'.$db2->signId.'" value="'.$db2->note.'" />';
									}

									// Show who added the trooper
									if($db2->addedby != 0)
									{
										$data .= '
										<br /><small>Added by:<br />' . getName($db2->addedby) . '</small>';
									}

								$data .= '
								</td>
									
								<td>
									'.readTKNumber($db2->tkid, $db2->squad).'
								</td>
								
								<td name="trooperRosterCostume">
									'.getCostume($db2->costume).'
								</td>
								
								<td name="trooperRosterBackup">
									'.ifEmpty(getCostume($db2->costume_backup), "N/A").'
								</td>
								
								<td id="'.$db2->trooperId.'Status" aria-label="'.date("F j, Y, g:i:s a", strtotime($db2->signuptime)).'" data-balloon-pos="up">
								<div name="trooperRosterStatus">
								<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
									'.getStatus($db2->status);

									// If is admin and limited event
									if(isAdmin() && $db->limitedEvent == 1)
									{
										// If set to going
										if($db2->status == 0)
										{
											// Set status
											$data .= '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
										}
										// If set to not picked
										else if($db2->status == 6)
										{
											// Set status
											$data .= '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>';
										}
									}

								$data .= '
								</div>
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
									'.drawSupportBadge($db2->trooperId).'
									<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>';

									// If a placeholder account, allow edit name
									if($db2->trooperId == placeholder)
									{
										$data .= '
										<b>'.$db2->note.'</b>';
									}

									// Show who added the trooper
									if($db2->addedby != 0)
									{
										$data .= '
										<br /><small>Added by:<br />' . getName($db2->addedby) . '</small>';
									}

								$data .= '
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
								
								<td id="'.$db2->trooperId.'Status" aria-label="'.date("F j, Y, g:i:s a", strtotime($db2->signuptime)).'" data-balloon-pos="up">
									<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
									'.getStatus($db2->status);

									// If is admin and limited event
									if(isAdmin() && $db->limitedEvent == 1)
									{
										// If set to going
										if($db2->status == 0)
										{
											// Set status
											$data .= '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
										}
										// If set to not picked
										else if($db2->status == 6)
										{
											// Set status
											$data .= '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>';
										}
									}

								$data .= '
								</div>
								</td>
							</tr>';
						}

						// Increment trooper count
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
					<hr />
					<div name="signeduparea" id="signeduparea">
						<p><b>You are signed up for this troop!</b></p>
					</div>';
				}

				// Update troopers remaining
				$data2 = '
				<ul>
					<li>This event is limited to '.$limitTotal.' troopers. ';

					// Check for total limit set, if it is, add remaining troopers
					if($totalTrooperEvent)
					{
						$data2 .= '
						' . troopersRemaining($limitTotal, eventClubCount($db->id, "all")) . '</li>';
					}
					else
					{
						$data2 .= '
						<li>This event is limited to '.$db->limit501st.' 501st troopers. '.troopersRemaining($db->limit501st, eventClubCount($db->id, 0)).' </li>';

						// Set up club count
						$clubCount = 1;

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							$data2 .= '
							<li>This event is limited to '.$db->{$club_value['dbLimit']}.' '. $club_value['name'] .' troopers. '.troopersRemaining($db->{$club_value['dbLimit']}, eventClubCount($db->id, $clubCount)).'</li>';

							// Increment club count
							$clubCount++;
						}
					}

				$data2 .= '
				</ul>';

				// Send back data
				$array = array('success' => 'success', 'data' => $data, 'id' => $_SESSION['id'], 'troopersRemaining' => $data2);
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
				$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costume'])."', status = '3' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'") or die($conn->error);
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
