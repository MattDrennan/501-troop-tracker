<?php

// Include config file
include "../../../config.php";

// Loop through all troopers
$query = "SELECT events.id AS eventId, event_sign_up.id AS signupId, troopers.id AS trooperId, troopers.email AS email, troopers.name AS name FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE events.closed = '1' AND event_sign_up.status = '0' AND troopers.subscribe = '1' AND troopers.email != '' AND troopers.econfirm = '1' GROUP BY troopers.id";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Get total troops that need attention
		$troops_get = $conn->query("SELECT COUNT(*) FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE events.closed = '1' AND event_sign_up.status = '0' AND troopers.subscribe = '1' AND troopers.id = '".$db->trooperId."'") or die($conn->error);
		$count = $troops_get->fetch_row();
		
		// Set up message
		$message = "Hello!\n\nYou have ".$count[0]." troops that need to be confirmed in order to give you troop credit. Please login to the troop tracker and confirm these troops.\n\nIf you need assistance, please contact your squad leader.\n\nYou can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com";
		
		// Send E-mail
		sendEmail($db->email, $db->name, "Troop Tracker: Troops need your attention!", $message);
	}
}

// Set up message
$message = "";

// Set up count
$i = 0;

// Loop through all notifications
$query = "SELECT notifications.id, notifications.message, notifications.trooperid, settings.lastnotification FROM notifications JOIN settings WHERE notifications.id > settings.lastnotification AND notifications.message LIKE '%now has%'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Update message
		$message .= $db->message . "\n\n";
		
		// Update last notification
		$conn->query("UPDATE settings SET lastnotification = '".$db->id."'");
		
		// Increment
		$i++;
	}
}

$message .= "You can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com";


// If notifications
if($i > 0)
{
	// Loop through all members with admin
	$query = "SELECT troopers.email, troopers.name, troopers.permissions FROM troopers WHERE (troopers.permissions = '1' OR troopers.permissions = '2') AND troopers.email != '' AND troopers.subscribe = '1' AND troopers.ecommandnotify = '1'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Send E-mail
			//sendEmail($db->email, $db->name, "Troop Tracker: Trooper(s) has hit a milestone!", $message);
		}
	}
}

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
		${"s" . $db->squad} .= $db->name . "\n\n" . $d1 . " - " . $d2 . "\n\n" . getSquadName($db->squad) . "\n\nhttps://www.fl501st.com/troop-tracker/index.php?event=".$db->id."\n\n\n\n";
		
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
			// Set up squad count
			$l = 1;

			// Check something to send
			$k = 0;

			// Set up e-mail
			$emailBody = "New events posted:\n\n";

			// Loop through squads
			foreach($squadArray as $squad => $squad_value)
			{
				// Check allow e-mails for squad
				if($db->{"esquad" . $l} == 1)
				{
					// Add squad information to e-mail
					$emailBody .= ${"s" . $l};

					// Increment something to send
					$k++;
				}

				// Increment squad count
				$l++;
			}

			// Add footer of e-mail
			$emailBody .= "You can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com";

			// Check if to send e-mail
			if($k > 0)
			{
				// Send E-mail
				sendEmail($db->email, $db->name, "Troop Tracker: New events posted!", $emailBody);
			}
		}
	}
}
	
?>