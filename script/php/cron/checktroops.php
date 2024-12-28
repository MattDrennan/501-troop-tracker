<?php

/**
 * This file is used to send daily notifications of new troops and other information.
 * 
 * This should be run daily by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Loop through all troopers
$query = "SELECT events.id AS eventId, event_sign_up.id AS signupId, troopers.id AS trooperId, troopers.email AS email, troopers.name AS name FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE events.closed = '1' AND event_sign_up.status = '0' AND troopers.subscribe = '1' AND troopers.email != '' AND troopers.econfirm = '1' GROUP BY troopers.id";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// If retired or not allowed to access site, skip to next trooper
		if(!canAccess($db->trooperId)) { continue; }

		// Get total troops that need attention
		$troops_get = $conn->query("SELECT COUNT(*) FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE events.closed = '1' AND event_sign_up.status = '0' AND troopers.subscribe = '1' AND troopers.id = '".$db->trooperId."'");
		$count = $troops_get->fetch_row();
		
		// Set up message
		$message = "Hello!\n\nYou have ".$count[0]." troops that need to be confirmed in order to give you troop credit. Please login to the troop tracker and confirm these troops.\n\nConfirm troops here: ".$trackerURL."/index.php#confirmtroops\n\nIf you need assistance, please contact your squad leader.\n\nYou can opt out of e-mails under: \"Manage Account\"\n\n".$trackerURL."/";
		
		// Send E-mail
		sendEmail($db->email, readInput($db->name), "Troop Tracker: Troops need your attention!", readInput($message));
	}
}

// Garrison
$sM0 = "";

// Set up squad count - milestones
$y = 1;

// Loop through squads - milestones
foreach($squadArray as $squad => $squad_value)
{
	// Set up troops for e-mail
	${"sM" . $y} = "";

	// Increment
	$y++;
}

// Loop through clubs - milestones
foreach($clubArray as $club => $club_value)
{
	// Set up troops for e-mail
	${"sM" . $y} = "";

	// Increment
	$y++;
}

// Garrison
$sC0 = "";

// Set up squad count - comments
$l = 1;

// Loop through squads - comments
foreach($squadArray as $squad => $squad_value)
{
	// Set up troops for e-mail
	${"sC" . $l} = "";

	// Increment
	$l++;
}

// Loop through all notifications
$query = "SELECT notifications.id, notifications.message, notifications.trooperid, settings.lastnotification FROM notifications JOIN settings WHERE notifications.id > settings.lastnotification AND notifications.message LIKE '%now has%'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Loop through all troopers and get trooper
		$query2 = "SELECT * FROM troopers WHERE id = '".$db->trooperid."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Update message
				${"sM" . $db2->squad} .= '[' . getSquadName($db2->squad) . '] ' . $db->message . "\n\n";
				
				// Update last notification
				$conn->query("UPDATE settings SET lastnotification = '".$db->id."'");
			}
		}
	}
}

// Loop through all comments that are important
$query = "SELECT * FROM comments";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Loop through all comments that are important
		$query2 = "SELECT * FROM events WHERE id = '".$db->id."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Update message
				${"sC" . $db2->squad} .= getName($db->trooperid) . ': ' . $db->comment . "\n".$trackerURL."/index.php?event=".$db->troopid."\n\n";
			}
		}
	}
}

// Reset comments
$conn->query("DELETE FROM comments");

// Set up squad count
$i = 1;

// Set up add to query
$addToQuery = ", troopers.esquad0";

// Loop through squads
foreach($squadArray as $squad => $squad_value)
{
	// Build query
	$addToQuery .= ", troopers.esquad" . $i;

	// Increment squad count
	$i++;
}

// Loop through squads
foreach($clubArray as $club => $club_value)
{
	// Build query
	$addToQuery .= ", troopers.esquad" . $i;

	// Increment squad count
	$i++;
}

// Loop through all members with admin
$query = "SELECT troopers.email, troopers.id AS trooperId, troopers.name, troopers.permissions".$addToQuery." FROM troopers WHERE (troopers.permissions = '1' OR troopers.permissions = '2') AND troopers.email != '' AND troopers.subscribe = '1' AND troopers.ecommandnotify = '1'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// If retired or not allowed to access site, skip to next trooper
		if(!canAccess($db->trooperId)) { continue; }

		// Set up message
		$message = "";
		
		// Set up milestone count
		$mC = 0;

		// Set up comment count
		$cC = 0;
		
		// Trooper Milestones
		$message .= "Trooper Milestones:\n\n";

		// Garrison
		if($db->esquad0 == 1)
		{
			// Add squad information to e-mail
			$message .= $sM0;

			// Check if message has contents
			if($sM0 != "")
			{
				// Increment milestone count
				$mC++;
			}
		}
		
		// Set up squad count
		$i = 1;
		
		// Loop through squads
		foreach($squadArray as $squad => $squad_value)
		{
			// Check allow e-mails for squad
			if($db->{"esquad" . $i} == 1)
			{
				// Add squad information to e-mail
				$message .= ${"sM" . $i};

				// Check if message has contents
				if(${"sM" . $i} != "")
				{
					// Increment milestone count
					$mC++;
				}
			}

			// Increment squad count
			$i++;
		}
		
		// Loop through clubs
		foreach($clubArray as $club => $club_value)
		{
			// Check allow e-mails for club
			if($db->{"esquad" . $i} == 1)
			{
				// Add club information to e-mail
				$message .= ${"sM" . $i};

				// Check if message has contents
				if(${"sM" . $i} != "")
				{
					// Increment milestone count
					$mC++;
				}
			}

			// Increment club count
			$i++;
		}
		
		// If no milestones
		if($mC == 0)
		{
			$message .= "-None\n\n";
		}
		
		// Trooper Comments
		$message .= "Important Comments:\n\n";

		// Garrison
		if($db->esquad0 == 1)
		{
			// Add squad information to e-mail
			$message .= $sC0;

			// Check if message has contents
			if($sC0 != "")
			{
				// Increment milestone count
				$cC++;
			}
		}
		
		// Set up squad count
		$i = 1;
		
		// Loop through squads
		foreach($squadArray as $squad => $squad_value)
		{
			// Check allow e-mails for squad
			if($db->{"esquad" . $i} == 1)
			{
				// Add squad information to e-mail
				$message .= ${"sC" . $i};

				// Check if message has contents
				if(${"sC" . $i} != "")
				{
					// Increment milestone count
					$cC++;
				}
			}

			// Increment squad count
			$i++;
		}
		
		// If no comments
		if($cC == 0)
		{
			$message .= "-None\n\n";
		}
		
		// Add footer to e-mail
		$message .= "\n\nYou can opt out of e-mails under: \"Manage Account\"\n\n".$trackerURL."/";
		
		// Send e-mail if something to send
		if($mC > 0 || $cC > 0)
		{
			sendEmail($db->email, readInput($db->name), "Troop Tracker: Command Staff Notifications", readInput($message));
		}
	}
}

// Garrison
$s0 = "";

// Set up squad count
$i = 1;

// Loop through squads
foreach($squadArray as $squad => $squad_value)
{
	// Set up troops for e-mail
	${"s" . $i} = "";

	// Increment
	$i++;
}

// Set up last event
$lastEventID = 0;

// Set up count
$i = 0;

// Loop through all all events that are larger than the last event posted
$query = "SELECT * FROM events, settings WHERE events.id > settings.notifyevent AND events.dateStart > NOW()";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Dates
		$d1 = date('m-d-Y h:i A', strtotime($db->dateStart));
		$d2 = date('h:i A', strtotime($db->dateEnd));
		
		// Add to e-mail
		${"s" . $db->squad} .= $db->name . "\n\n" . $d1 . " - " . $d2 . "\n\n" . getSquadName($db->squad) . "\n\n".$trackerURL."/index.php?event=".$db->id."\n\n\n\n";
		
		// Set
		$lastEventID = $db->id;
		
		// Increment
		$i++;
	}
}

// If events
if($i > 0)
{
	// Update last notification
	$conn->query("UPDATE settings SET notifyevent = '".$lastEventID."'");

	// Loop through all troopers who are subscribed to e-mails
	$query = "SELECT * FROM troopers WHERE troopers.email != '' AND troopers.subscribe = '1'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// If retired or not allowed to access site, skip to next trooper
			if(!canAccess($db->id)) { continue; }

			// Set up squad count
			$l = 1;

			// Check something to send
			$k = 0;

			// Set up e-mail
			$emailBody = "New events posted:\n\n";

			// Garrison - Check allow e-mails for squad
			if($db->esquad0 == 1)
			{
				// Add squad information to e-mail
				$emailBody .= $s0;

				// Make sure not empty
				if($s0 != "")
				{
					// Increment something to send
					$k++;
				}
			}

			// Loop through squads
			foreach($squadArray as $squad => $squad_value)
			{
				// Check allow e-mails for squad
				if($db->{"esquad" . $l} == 1)
				{
					// Add squad information to e-mail
					$emailBody .= ${"s" . $l};

					// Make sure not empty
					if(${"s" . $l} != "")
					{
						// Increment something to send
						$k++;
					}
				}

				// Increment squad count
				$l++;
			}

			// Add footer of e-mail
			$emailBody .= "You can opt out of e-mails under: \"Manage Account\"\n\n".$trackerURL."/";

			// Check if to send e-mail
			if($k > 0)
			{
				// Send E-mail
				sendEmail($db->email, readInput($db->name), "Troop Tracker: New events posted!", readInput($emailBody));
			}
		}
	}
}
	
?>