<?php

/**
 * This file is used for processing AJAX requests.
 *
 * @author  Matthew Drennan
 *
 */

include 'config.php';

/******************** ROSTER TROOPER CONFIRMATION *******************************/

if(isset($_GET['do']) && $_GET['do'] == "roster-trooper-confirmation" && loggedIn() && isAdmin())
{
	// Prevent changing status
	if(cleanInput($_POST['status']) != 3 && cleanInput($_POST['status']) != 4) { return false; }

	// Update trooper sign up
	$conn->query("UPDATE event_sign_up SET status = '".cleanInput($_POST['status'])."' WHERE id = '".cleanInput($_POST['signid'])."'");
}

/******************** ROSTER SAVE TROOPER TKID *******************************/

if(isset($_GET['do']) && $_GET['do'] == "roster-edit-tkid" && loggedIn() && isAdmin())
{
	// Check if TKID already exists
	if(!doesTKExist(cleanInput($_POST['new_tkid']), getSquadID(cleanInput($_POST['trooperid']))))
	{
		// Update trooper TKID
		$conn->query("UPDATE troopers SET tkid = '".cleanInput($_POST['new_tkid'])."' WHERE id = '".cleanInput($_POST['trooperid'])."'");

		// Send JSON
		$array = array('success' => true);
		echo json_encode($array);
	}
	else
	{
		// Send JSON
		$array = array('success' => false);
		echo json_encode($array);
	}
}

/******************** SAVE FAVORITE COSTUMES *******************************/

if(isset($_GET['do']) && $_GET['do'] == "savefavoritecostumes" && loggedIn())
{
	// Delete all from database
	$conn->query("DELETE FROM favorite_costumes WHERE trooperid = '".cleanInput($_SESSION['id'])."'");
	
	// Insert into database
	foreach(explode(",", $_POST['costumes']) as $value)
	{
		$conn->query("INSERT INTO favorite_costumes (trooperid, costumeid) VALUES ('".cleanInput($_SESSION['id'])."', '".cleanInput($value)."')");
	}
}

/******************** SAVE PLACEHOLDER *******************************/

if(isset($_GET['do']) && $_GET['do'] == "saveplaceholder" && loggedIn())
{
	// Query database for placeholder
	$query = "SELECT * FROM event_sign_up WHERE id = '".cleanInput($_POST['signid'])."' AND trooperid = '".placeholder."'";
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Check if added or is admin
			if($db->addedby == $_SESSION['id'] || isAdmin())
			{
				$conn->query("UPDATE event_sign_up SET note = '".cleanInput($_POST['note'])."' WHERE id = '".cleanInput($_POST['signid'])."'");
			}
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
				$data = nl2br(readInput(isImportant($db->important, showBBcodes(cleanInput($_POST['comment'])))));
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
		// If set to not a member, remove squad
		if($_POST['permission'] == 0)
		{
			$queryAdd .= "squad = 0, ";
		}
		
		$queryAdd .= "p501";
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
			// Set variables for trooper we are modifying
			if(cleanInput($_POST['trooperid']) == $db->trooperid)
			{
				// Sign ID
				$id = $db->id;
				
				// Get old status
				$oldStatus = $db->status;
			}
			
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
	$limitHandlers = "";

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
			
			// Set handler
			$limitHandlers = $db->limitHandlers;

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
	if(strpos(strtolower(getCostume(cleanInput($_POST['costume']))), "handler") === false)
	{
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
	}
	else
	{
		// Handler check
		if(($limitHandlers - handlerEventCount(cleanInput($_POST['troopid']))) <= 0 && $status != 4 && inEvent(cleanInput($_POST['trooperid']), cleanInput($_POST['troopid']))['inTroop'] != 1)
		{
			// Troop is full, set to stand by
			$status = 1;

			// Set troop full
			$troopFull = true;
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
	
	// Check if status is being changed from cancel to another
	if($oldStatus == 4 && $status < 4)
	{
		// Delete
		$conn->query("DELETE FROM event_sign_up WHERE id = '".$id."'");
		
		// Set added by based on who is updating trooper
		if(cleanInput($_POST['trooperid']) == $_SESSION['id'])
		{
			$addedby = 0;
		}
		else
		{
			$addedby = $_SESSION['id'];
		}
		
		// Insert
		$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, costume_backup, status, addedby) VALUES ('".cleanInput($_POST['trooperid'])."', '".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['costume_backup'])."', '".$status."', '".$addedby."')");
		$last_id = $conn->insert_id;
	}
	
	resetTrooperStatus(cleanInput($_POST['troopid']));
	
	$rosterUpdate = getRoster(cleanInput($_POST['troopid']), $limitTotal, $totalTrooperEvent, true);

	// Send JSON
	$array = array('success' => 'true', 'status' => $status, 'troopFull' => $troopFull, 'limit501st' => $limit501st, 'limit501stTotal' => $limit501stTotal, 'rosterData' => $rosterUpdate[0], 'troopersRemaining' => $rosterUpdate[1]);

	echo json_encode($array);
}

/************************* Comments **************************************/

// Enter comment into database
if(isset($_GET['do']) && $_GET['do'] == "postcomment" && isset($_POST['submitComment']) && loggedIn())
{
	if(strlen(trim($_POST['comment'])) > 0 && ($_POST['important'] == 0 || $_POST['important'] == 1))
	{
		// Query - check if a comment has been recently posted by this trooper
		$commentCheck = $conn->query("SELECT id FROM comments WHERE comment = '".cleanInput($_POST['comment'])."' AND posted > NOW() - INTERVAL 5 MINUTE");

		// Check comment check
		if($commentCheck->num_rows == 0)
		{
			// Query the database
			$conn->query("INSERT INTO comments (troopid, trooperid, comment, important) VALUES ('".cleanInput($_POST['eventId'])."', '".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['comment'])."', '".cleanInput($_POST['important'])."')");

			// Get last ID of comment
			$last_id = $conn->insert_id;

			// Get thread ID
			$thread_id = getEventThreadID(cleanInput($_POST['eventId']));

			// Check if there is a forum thread
			if($thread_id > 0)
			{
				// Post to forum
				$post = createPost($thread_id, $_POST['comment'], getUserID($_SESSION['id']));

				// Update comment
				$conn->query("UPDATE comments SET post_id = '".$post['post']['post_id']."' WHERE id = '".$last_id."'");
			}
		}

		// Set up query string
		$troops = "";
		
		// Check if link
		$link = isLink(cleanInput($_POST['eventId']));
		
		// Make sure this is a linked event
		if($link > 0)
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
					<td><span style="float: left;">'.$admin.'<a href="#/" id="quoteComment_'.$db->id.'" name="'.$db->id.'" troopername="'.getTrooperForum($db->trooperid).'" user_id="'.getUserID($db->trooperid).'" post_id="'.$db->post_id.'"><img src="images/quote.png" alt="Quote Comment"></a></span> <a href="index.php?profile='.$db->trooperid.'">'.getName($db->trooperid).' - '.readTKNumber(getTKNumber($db->trooperid), getTrooperSquad($db->trooperid)).'</a>'.getForumAvatar($db->trooperid).''.$newdate.'</td>
				</tr>
				
				<tr>
					<td name="insideComment">'.nl2br(isImportant($db->important, showBBcodes($comment))).'</td>
				</tr>

				</table>

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

				$returnMessage .= '<option value="'.$db->id.'" awardTitle="'.readInput($db->title).'" awardID="'.$db->id.'" awardImage="'.$db->icon.'">'.readInput($db->title).'</option>';

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

				$returnMessage .= '<option value="'.$db->id.'">'.readInput($db->title).'</option>';

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

					$returnMessage2 .= '<option value="'.$db->id.'">'.readInput($db->name).'</option>';

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
					
					$returnMessage2 .= '<option value="'.$db->id.'">'.readInput($db->title).'</option>';

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

		if ($result = mysqli_query($conn, $query))
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
				$array = array('name' => readInput($db->name), 'email' => $db->email, 'forum' => $db->forum_id, 'phone' => $db->phone, 'squad' => getSquadName($db->squad), 'tkid' => readTKNumber($db->tkid, $db->squad), 'link' => $link, 'user_id' => $db->user_id);

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

		if ($result = mysqli_query($conn, $query))
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

		if ($result = mysqli_query($conn, $query))
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
				$array = array('id' => $db->id, 'name' => readInput($db->name), 'phone' => readInput($db->phone), 'squad' => $db->squad, 'permissions' => $db->permissions, 'p501' => $db->p501, 'tkid' => $db->tkid, 'forumid' => readInput($db->forum_id), 'supporter' => $db->supporter, 'spTrooper' => $db->spTrooper, 'spCostume' => $db->spCostume, 'spAward' => $db->spAward);

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// Push to array
					$array[$club_value['db']] = $db->{$club_value['db']};

					// If DB3 defined
					if($club_value['db3Name'] != "")
					{
						$array[$club_value['db3']] = readInput($db->{$club_value['db3']}); 
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
					$addToQuery .= "".$club_value['db3']." = '".filter_var($_POST[$club_value['db3']], FILTER_SANITIZE_ADD_SLASHES)."', ";
				}
			}
			
			// Query the database
			$conn->query("UPDATE troopers SET name = '".cleanInput($_POST['user'])."', phone = '".cleanInput(cleanInput($_POST['phone']))."', squad = '".cleanInput($_POST['squad'])."', p501 = '".cleanInput($_POST['p501'])."', ".$addToQuery." tkid = '".$tkid."', forum_id = '".filter_var($_POST['forumid'], FILTER_SANITIZE_ADD_SLASHES)."', supporter = '".cleanInput($_POST['supporter'])."' WHERE id = '".cleanInput($_POST['userIDE'])."'");

			// If super user, update special permissions
			if(hasPermission(1))
			{
				// Give default value if null
				if(!isset($_POST['spTrooper'])) { $_POST['spTrooper'] = 0; } else { $_POST['spTrooper'] = 1; }
				if(!isset($_POST['spCostume'])) { $_POST['spCostume'] = 0; } else { $_POST['spCostume'] = 1; }
				if(!isset($_POST['spAward'])) { $_POST['spAward'] = 0; } else { $_POST['spAward'] = 1; }

				// Query the database
				$conn->query("UPDATE troopers SET spTrooper = '".cleanInput($_POST['spTrooper'])."', spCostume = '".cleanInput($_POST['spCostume'])."', spAward = '".cleanInput($_POST['spAward'])."', permissions = '".cleanInput($_POST['permissions'])."' WHERE id = '".cleanInput($_POST['userIDE'])."'");
			}

			$array = array('success' => 'true', 'newname' => readInput(cleanInput($_POST['user'])) . " - " . readTKNumber($tkid, getTrooperSquad(cleanInput($_POST['userIDE']))) . " - " . readInput(cleanInput($_POST['forumid'])), 'data' => $message);
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

		if ($result = mysqli_query($conn, $query))
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
		$conn->query("UPDATE troopers SET " . cleanInput($_POST['setting']) . " = CASE " . cleanInput($_POST['setting']) . " WHEN 1 THEN 0 WHEN 0 THEN 1 END WHERE id = '".$_SESSION['id']."'");
	}
}

// Request access
if(isset($_GET['do']) && $_GET['do'] == "requestaccess")
{
	if($_POST['submitRequest'])
	{
		// Check we have all the data we need server side. JQuery should do this, but this is a backup
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

		// If TKID is not numeric
		if(!is_numeric($tkid))
		{
			$failed = true;
			echo '<li>TKID must be an integer.</li>';
		}
		
		// Check if TKID can be 0
		if($_POST['accountType'] == 1 && $tkid == 0)
		{
			$failed = true;
			echo '<li>TKID cannot be zero.</li>';
		}
		
		// Check if TKID cannot be 0
		if($_POST['accountType'] == 4 && $tkid > 0)
		{
			$failed = true;
			echo '<li>TKID must be zero for a handler account.</li>';
		}
		
		// Set squad variable
		$squad = cleanInput($_POST['squad']);
		
		// Check if 501st
		if($squad <= count($squadArray))
		{
			// Query ID database
			$idcheck = $conn->query("SELECT id FROM troopers WHERE tkid = '".$tkid."' AND squad <= ".count($squadArray)."");
		}
		else
		{
			// In a club - query by club
			$idcheck = $conn->query("SELECT id FROM troopers WHERE tkid = '".$tkid."' AND squad = ".$squad."");
		}
		
		// Query 501st forum
		$forumcheck = $conn->query("SELECT forum_id FROM troopers WHERE forum_id = '".cleanInput($_POST['forumid'])."'");
		
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
					$clubid = $conn->query("SELECT ".$club_value['db3']." FROM troopers WHERE ".$club_value['db3']." = '".cleanInput($_POST[$club_value['db3']])."'");
					
					// Check if club ID exists
					if($clubid->num_rows > 0)
					{
						$failed = true;
						echo '<li>'.$club_value['name'].' ID is already taken. Please contact the '.garrison.' Webmaster for further assistance.</li>';
					}
				}
			}
		}

		// Check if ID exists, if not set to 0
		if($_POST["accountType"] == 1 && $idcheck->num_rows > 0)
		{
			$failed = true;
			echo '<li>TKID is taken. If you have troops on the old troop tracker, <a href="index.php?action=setup">click here to request access</a>.</li>';
		}

		// Login with forum
		$forumLogin = loginWithForum($_POST['forumid'], $_POST['forumpassword']);

		// Verify forum and password
		if(!isset($forumLogin['success']))
		{
			$failed = true;
			echo '<li>Incorrect '.garrison.' Board username and password.</li>';
		} else {
			// Query user_id to prevent duplicates
			$forumcheck = $conn->query("SELECT user_id FROM troopers WHERE user_id = '".$forumLogin['user']['user_id']."'");
			
			// Check if user_id exists
			if($forumcheck->num_rows > 0)
			{
				$failed = true;
				echo '<li>You already have a Troop Tracker account.</li>';
			}
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
				$p501 = cleanInput($_POST['accountType']);
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
					${$club_value['db']} = cleanInput($_POST['accountType']);
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
			$conn->query("INSERT INTO troopers (user_id, name, tkid, email, forum_id, ".$addToQuery1."p501,".$addToQuery3."phone, squad, password) VALUES ('".$forumLogin['user']['user_id']."', '".cleanInput($_POST['name'])."', '".floatval($tkid)."', '".$forumLogin['user']['email']."', '".cleanInput($_POST['forumid'])."',".$addToQuery2."'".$p501."',".$addToQuery4."'".cleanInput($_POST['phone'])."', '".$squad."', '".password_hash($_POST['forumpassword'], PASSWORD_DEFAULT)."')");
			
			// Last ID
			$last_id = $conn->insert_id;
			
			echo '<li>Request submitted! You will receive an e-mail when your request is approved or denied.</li>';
		}

		echo '</ul>';
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
			$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limit501st, limitTotalTroopers, limitHandlers, friendLimit, allowTentative, ".$addToQuery1."squad) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".cleanInput($date1)."', '".cleanInput($date2)."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitTotalTroopers'])."', '".cleanInput($_POST['limitHandlers'])."', '".cleanInput($_POST['friendLimit'])."', '".cleanInput($_POST['allowTentative'])."', ".$addToQuery2."'".cleanInput($_POST['squadm'])."')");
			
			// Event ID - Last insert from database
			$eventId = $conn->insert_id;

			// Only notify if event is in the future
			if(strtotime($date1) > strtotime("now"))
			{
				// Send to database to send out notifictions later
				$conn->query("INSERT INTO notification_check (troopid) VALUES ($eventId)");
			}

			// Check if is shift event
			$isShift = false;
			
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
						// Set shift event
						$isShift = true;
					
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
						$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limit501st, limitTotalTroopers, limitHandlers, friendLimit, allowTentative, ".$addToQuery1."squad, link) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".$date1."', '".$date2."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitTotalTroopers'])."', '".cleanInput($_POST['limitHandlers'])."', '".cleanInput($_POST['friendLimit'])."', '".cleanInput($_POST['allowTentative'])."', ".$addToQuery2."'".cleanInput($_POST['squadm'])."', '".$eventId."')");

						// Last ID
						$last_id = $conn->insert_id;

						// Only create thread if we can and admin allows
						if($_POST['postToBoards'] == 1 && ($_POST['squadm'] != 0 || $_POST['label'] == 7 || $_POST['label'] == 4 || $_POST['label'] == 3))
						{
							// Make thread body
							$thread_body = threadTemplate($_POST['eventName'], $_POST['eventVenue'], $_POST['location'], $date1, $date2, $_POST['website'], $_POST['numberOfAttend'], $_POST['requestedNumber'], $_POST['requestedCharacter'], $_POST['secure'], $_POST['blasters'], $_POST['lightsabers'], $_POST['parking'], $_POST['mobility'], $_POST['amenities'], $_POST['comments'], $_POST['referred'], $last_id);

							// ID of forum category
							$forumCat = 0;
							
							// If a virtual troop, send to distance category
							if($_POST['label'] == 7)
							{
								$forumCat = $virtualTroop;
							}
							else if($_POST['label'] == 4)
							{
								$forumCat = $conventionTroop;
							}
							else if($_POST['label'] == 3)
							{
								$forumCat = $disneyTroop;
							}
							else
							{
								$forumCat = $squadArray[intval($_POST['squadm'] - 1)]['eventForum'];
							}

							// Create thread on forum
							$thread = createThread($forumCat, date("m/d/y h:i A", strtotime($date1)) . " - " . date("h:i A", strtotime($date2)) . " " . $_POST['eventName'], $thread_body, getUserID($_SESSION['id']));

							// Update event
							$conn->query("UPDATE events SET thread_id = '".$thread['thread']['thread_id']."', post_id = '".$thread['thread']['last_post_id']."' WHERE id = '".$last_id."'");
						}

						// Send notification to command staff
						sendNotification(getName($_SESSION['id']) . " has added an event: [" . $last_id . "][" . cleanInput($_POST['eventName']) . "]", cleanInput($_SESSION['id']), 13, convertDataToJSON("SELECT * FROM events WHERE id = '".$last_id."'"));
					}
				}
			}

			// Only create thread if we can and admin allows
			if($_POST['postToBoards'] == 1 && ($_POST['squadm'] != 0 || $_POST['label'] == 7 || $_POST['label'] == 4 || $_POST['label'] == 3))
			{
				// Make thread body
				$thread_body = threadTemplate($_POST['eventName'], $_POST['eventVenue'], $_POST['location'], $date1, $date2, $_POST['website'], $_POST['numberOfAttend'], $_POST['requestedNumber'], $_POST['requestedCharacter'], $_POST['secure'], $_POST['blasters'], $_POST['lightsabers'], $_POST['parking'], $_POST['mobility'], $_POST['amenities'], $_POST['comments'], $_POST['referred'], $eventId);

				// ID of forum category
				$forumCat = 0;
				
				// If a virtual troop, send to distance category
				if($_POST['label'] == 7)
				{
					$forumCat = $virtualTroop;
				}
				else if($_POST['label'] == 4)
				{
					$forumCat = $conventionTroop;
				}
				else if($_POST['label'] == 3)
				{
					$forumCat = $disneyTroop;
				}
				else
				{
					$forumCat = $squadArray[intval($_POST['squadm'] - 1)]['eventForum'];
				}

				// Set title
				$title = date("m/d/y", strtotime($date1)) . " " . $_POST['eventName'];

				// Change date based on event type
				if($isShift) {
					// Fix where wrong date would be posted on forum
					$date1 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['dateStart'])));
					$date2 = date('Y-m-d H:i:s', strtotime(cleanInput($_POST['dateEnd'])));
					
					// Set up title
					$title = date("m/d/y h:i A", strtotime($date1)) . " - " . date("h:i A", strtotime($date2)) . " " . $_POST['eventName'];
				}

				// Create thread on forum
				$thread = createThread($forumCat, $title, $thread_body, getUserID($_SESSION['id']));

				// Make sure value is set from createThread
				if(isset($thread['thread']['thread_id']))
				{
					// Update event with thread and post IDs
					$conn->query("UPDATE events SET thread_id = '".$thread['thread']['thread_id']."', post_id = '".$thread['thread']['last_post_id']."' WHERE id = '".$eventId."'");
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
		$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costumeValSelect' . $_POST['trooperSelectEdit'] . ''])."', costume_backup = '".cleanInput($_POST['costumeVal' . $_POST['trooperSelectEdit'] . ''])."', status = '".cleanInput($_POST['statusVal' . $_POST['trooperSelectEdit'] . ''])."' WHERE trooperid = '".cleanInput($_POST['trooperSelectEdit'])."' AND troopid = '".cleanInput($_POST['eventId'])."' AND id = '".cleanInput($_POST['signid'])."'");

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
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, costume_backup, status) VALUES ('".cleanInput($_POST['trooperSelect'])."', '".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['costume'])."', '".cleanInput($_POST['costumebackup'])."', '".cleanInput($_POST['status'])."')");
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

	// Event status set...
	if(isset($_POST['eventStatus']))
	{
		// Event submitted for lock...
		if($_POST['eventStatus'] == 3)
		{
			// Query the database
			$conn->query("UPDATE events SET closed = '3' WHERE id = '".cleanInput($_POST['eventId'])."'");
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has locked event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
		}
		// Event submitted for cancelation...
		else if($_POST['eventStatus'] == 2)
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
		else if($_POST['eventStatus'] == 1)
		{
			// Query the database
			$conn->query("UPDATE events SET closed = '1' WHERE id = '".cleanInput($_POST['eventId'])."'");
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has finished event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
		}
		// Event submitted for open...
		else if($_POST['eventStatus'] == 0)
		{
			// Query the database
			$conn->query("UPDATE events SET closed = '0' WHERE id = '".cleanInput($_POST['eventId'])."'");
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has reopened event ID: [" . cleanInput($_POST['eventId']) . "]", cleanInput($_SESSION['id']));
		}
		// Event submitted for set full...
		else if($_POST['eventStatus'] == 4)
		{
			// Query the database
			$conn->query("UPDATE events SET closed = '4' WHERE id = '".cleanInput($_POST['eventId'])."'");
			
			// Send notification to command staff
			sendNotification(getName($_SESSION['id']) . " has set event ID: [" . cleanInput($_POST['eventId']) . "] to full.", cleanInput($_SESSION['id']));
		}
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
		$limitToGet = $conn->query("SELECT limitTo FROM events WHERE id = '".cleanInput($_POST['eventId'])."'");
		$limitToGetVal = $limitToGet->fetch_row();
		
		// If limited to certain costumes, only show certain costumes...
		if($limitToGetVal[0] < 4)
		{
			$query2 .= " WHERE era = '".$limitToGetVal[0]."' OR era = '4'";
		}
		
		$query2 .= " ORDER BY FIELD(costume, ".$mainCostumes."".mainCostumesBuild($_SESSION['id']).") DESC, costume";
		
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
						<div name="tknumber1'.$db->trooperid.'" signid="'.$db->id.'"><a href="index.php?profile='.$db->trooperid.'" target="_blank">'.readTKNumber(getTKNumber($db->trooperid), getSquadID($db->trooperid)).' - '.getName($db->trooperid).'</a></div>';

						// If placeholder
						if($db->trooperid == placeholder)
						{
							echo '<input type="text" name="placeholdertext" signid="'.$db->id.'" value="'.$db->note.'" />';
						}

					echo '
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
								<option value="7" '.echoSelect(7, $db->status).'>No Show</option>
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
		if ($result = mysqli_query($conn, $query))
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
				$array = array('id' => $db->id, 'name' => readInput($db->name), 'venue' => readInput($db->venue), 'dateStart' => $db->dateStart, 'dateEnd' => $db->dateEnd, 'website' => readInput($db->website), 'numberOfAttend' => $db->numberOfAttend, 'requestedNumber' => $db->requestedNumber, 'requestedCharacter' => readInput($db->requestedCharacter), 'secureChanging' => $db->secureChanging, 'blasters' => $db->blasters, 'lightsabers' => $db->lightsabers, 'parking' => $db->parking, 'mobility' => $db->mobility, 'amenities' => readInput($db->amenities), 'referred' => readInput($db->referred), 'comments' => readInput($db->comments), 'location' => readInput($db->location), 'squad' => $db->squad, 'label' => $db->label, 'postComment' => $db->postComment, 'notes' => readInput($db->notes), 'limitedEvent' => $db->limitedEvent, 'limitTo' => $db->limitTo, 'limit501st' => $db->limit501st, 'limitTotalTroopers' => $db->limitTotalTroopers, 'limitHandlers' => $db->limitHandlers, 'allowTentative' => $db->allowTentative, 'friendLimit' => $db->friendLimit, 'closed' => $db->closed, 'charityDirectFunds' => $db->charityDirectFunds, 'charityIndirectFunds' => $db->charityIndirectFunds, 'charityName' => readInput($db->charityName), 'charityAddHours' => $db->charityAddHours,'charityNote' => readInput($db->charityNote), 'eventLink' => $db->link, 'thread_id' => $db->thread_id, 'post_id' => $db->post_id);

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
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', ".$addToQuery." limit501st = '".cleanInput($_POST['limit501st'])."', limitTotalTroopers = '".cleanInput($_POST['limitTotalTroopers'])."', limitHandlers = '".cleanInput($_POST['limitHandlers'])."', friendLimit = '".cleanInput($_POST['friendLimit'])."', allowTentative = '".cleanInput($_POST['allowTentative'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventLink'])."' OR id = '".cleanInput($_POST['eventLink'])."'");

				// Update if limited event
				resetTrooperStatus(cleanInput($_POST['eventIdE']), cleanInput($_POST['eventLink']));
				resetTrooperStatus(cleanInput($_POST['eventLink']));
				
				// Update date
				$conn->query("UPDATE events SET dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."' WHERE id = '".cleanInput($_POST['eventIdE'])."'");

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
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', ".$addToQuery."limit501st = '".cleanInput($_POST['limit501st'])."', limitTotalTroopers = '".cleanInput($_POST['limitTotalTroopers'])."', limitHandlers = '".cleanInput($_POST['limitHandlers'])."', friendLimit = '".cleanInput($_POST['friendLimit'])."', allowTentative = '".cleanInput($_POST['allowTentative'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."' OR link = '".cleanInput($_POST['eventIdE'])."'");

				// Update if limited event
				resetTrooperStatus(cleanInput($_POST['eventIdE']), cleanInput($_POST['eventIdE']));
				
				// Update date
				$conn->query("UPDATE events SET dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."' WHERE id = '".cleanInput($_POST['eventIdE'])."'");

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
				$conn->query("UPDATE events SET name = '".cleanInput($_POST['eventName'])."', venue =  '".cleanInput($_POST['eventVenue'])."', dateStart = '".cleanInput($date1)."', dateEnd = '".cleanInput($date2)."', website = '".cleanInput($_POST['website'])."', numberOfAttend = '".cleanInput($_POST['numberOfAttend'])."', requestedNumber = '".cleanInput($_POST['requestedNumber'])."', requestedCharacter = '".cleanInput($_POST['requestedCharacter'])."', secureChanging = '".cleanInput($_POST['secure'])."', blasters = '".cleanInput($_POST['blasters'])."', lightsabers = '".cleanInput($_POST['lightsabers'])."', parking = '".cleanInput($_POST['parking'])."', mobility = '".cleanInput($_POST['mobility'])."', amenities = '".cleanInput($_POST['amenities'])."', referred = '".cleanInput($_POST['referred'])."', comments = '".cleanInput($_POST['comments'])."', location = '".cleanInput($_POST['location'])."', squad = '".cleanInput($_POST['squadm'])."', label = '".cleanInput($_POST['label'])."', limitedEvent = '".cleanInput($_POST['limitedEvent'])."', limitTo = '".cleanInput($_POST['era'])."', ".$addToQuery."limit501st = '".cleanInput($_POST['limit501st'])."', limitTotalTroopers = '".cleanInput($_POST['limitTotalTroopers'])."', limitHandlers = '".cleanInput($_POST['limitHandlers'])."', friendLimit = '".cleanInput($_POST['friendLimit'])."', allowTentative = '".cleanInput($_POST['allowTentative'])."' WHERE id = '".cleanInput($_POST['eventIdE'])."'");

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
						$conn->query("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, comments, location, label, limitedEvent, limitTo, limit501st, limitTotalTroopers, limitHandlers, friendLimit, allowTentative, ".$addToQuery2." squad, link) VALUES ('".cleanInput($_POST['eventName'])."', '".cleanInput($_POST['eventVenue'])."', '".$date1."', '".$date2."', '".cleanInput($_POST['website'])."', '".cleanInput($_POST['numberOfAttend'])."', '".cleanInput($_POST['requestedNumber'])."', '".cleanInput($_POST['requestedCharacter'])."', '".cleanInput($_POST['secure'])."', '".cleanInput($_POST['blasters'])."', '".cleanInput($_POST['lightsabers'])."', '".cleanInput($_POST['parking'])."', '".cleanInput($_POST['mobility'])."', '".cleanInput($_POST['amenities'])."', '".cleanInput($_POST['referred'])."', '".cleanInput($_POST['comments'])."', '".cleanInput($_POST['location'])."', '".cleanInput($_POST['label'])."', '".cleanInput($_POST['limitedEvent'])."', '".cleanInput($_POST['era'])."', '".cleanInput($_POST['limit501st'])."', '".cleanInput($_POST['limitTotalTroopers'])."', '".cleanInput($_POST['limitHandlers'])."', '".cleanInput($_POST['friendLimit'])."', '".cleanInput($_POST['allowTentative'])."', ".$addToQuery1."'".cleanInput($_POST['squadm'])."', '".$link."')");

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
		
		// Set up success message
		$success = "success";
		$success_message = "Success!";
		
		// Get number of troopers that trooper signed up for event
		$numFriends = $conn->query("SELECT id FROM event_sign_up WHERE addedby = '".$_SESSION['id']."' AND troopid = '".cleanInput($_POST['event'])."'");

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
			$eventCheck = inEvent($_SESSION['id'], cleanInput($_POST['event']));

			// Set
			$trooperID = $_SESSION['id'];
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
		
		// Set handler total
		$limitHandlers = 0;
		
		// Set friend limit
		$friendLimit = 0;

		// Is this a total trooper event?
		$totalTrooperEvent = false;

		// Output
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Set 501
				$limit501st = $db->limit501st;
				
				// Set handlers
				$limitHandlers = $db->limitHandlers;
				
				// Set friend limit
				$friendLimit = $db->friendLimit;

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

		// Check for total limit set, if it is, check if troop is full based on total
		if(strpos(strtolower(getCostume(cleanInput($_POST['costume']))), "handler") === false)
		{
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
		}
		else
		{
			// Handler check
			if(($limitHandlers - handlerEventCount(cleanInput($_POST['event']))) <= 0 && $status != 4)
			{
				// Troop is full, set to stand by
				$status = 1;

				// Set troop full
				$troopFull = true;
			}
		}

		// End of check to see if this event is full
		
		// Check if this is add friend
		if(isset($_POST['addfriend']))
		{
			// Check if can add friend based on friend count
			if($numFriends->num_rows >= $friendLimit)
			{
				$success = "friend_fail";
				$success_message = "You cannot add anymore friends!";
			}
			else
			{
				// Query the database
				$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup, addedby) VALUES ('".cleanInput($_POST['trooperSelect'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".$status."', '".cleanInput($_POST['backupcostume'])."', '".cleanInput($_SESSION['id'])."')");
				
				// Send to database to send out notifictions later
				$conn->query("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES ('".cleanInput($_POST['event'])."', '".cleanInput($_POST['trooperSelect'])."', '1')");
			}
		}
		else
		{
			// Query the database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup) VALUES ('".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['event'])."', '".cleanInput($_POST['costume'])."', '".$status."', '".cleanInput($_POST['backupcostume'])."')");
			
			// Send to database to send out notifictions later
			$conn->query("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES ('".cleanInput($_POST['event'])."', '".cleanInput($_SESSION['id'])."', '1')");
		}

		$rosterUpdate = getRoster(cleanInput($_POST['event']), $limitTotal, $totalTrooperEvent, isset($_POST['addfriend']));
		
		// Send back data
		$array = array('success' => $success, 'success_message' => $success_message, 'numFriends' => $numFriends->num_rows, 'data' => $rosterUpdate[0], 'id' => $_SESSION['id'], 'troopersRemaining' => $rosterUpdate[1]);
		echo json_encode($array);
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
				$conn->query("UPDATE event_sign_up SET costume = '".cleanInput($_POST['costume'])."', status = '3' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'");
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
				$conn->query("UPDATE event_sign_up SET status = '4' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($list[$i])."'");
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
