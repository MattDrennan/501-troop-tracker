<?php

// Include config file
include "config.php";

// Loop through all troopers
$query = "SELECT events.id AS eventId, event_sign_up.id AS signupId, troopers.id AS trooperId, troopers.email AS email, troopers.name AS name FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE events.closed = '1' AND event_sign_up.attend = '0' AND troopers.subscribe = '1' AND troopers.email != '' GROUP BY troopers.id";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Get total troops that need attention
		$troops_get = $conn->query("SELECT COUNT(*) FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE events.closed = '1' AND event_sign_up.attend = '0' AND troopers.subscribe = '1' AND troopers.id = '".$db->trooperId."'") or die($conn->error);
		$count = $troops_get->fetch_row();
		
		// Set up message
		$message = 'Hello!\n\nYou have '.$count[0].' troops that need to be confirmed in order to give you troop credit. Please login to the troop tracker and confirm these troops.\n\nIf you need assistance, please contact your squad leader.\n\nYou can opt out of e-mails under: "Manage Account"\n\nhttps://trooptracking.com';
		
		// Send E-mail
		//sendEmail($db->email, $db->name, "Troop Tracker: Troops need your attention!", $message);
	}
}

// Set up message
$message = "";

// Loop through all notifications
$query = "SELECT notifications.id, notifications.message, notifications.trooperid, settings.lastnotification FROM notifications JOIN settings WHERE notifications.id > settings.lastnotification AND notifications.message LIKE '%now has%'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Update message
		$message .= $db->message . '\n\n';
		
		// Update last notification
		$conn->query("UPDATE settings SET lastnotification = '".$db->id."'");
	}
}

$message .= "You can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com";

// Loop through all members with admin
$query = "SELECT troopers.email, troopers.name, troopers.permissions FROM troopers WHERE (troopers.permissions = '1' OR troopers.permissions = '2') AND troopers.email != '' AND troopers.subscribe = '1'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Send E-mail
		sendEmail($db->email, $db->name, "Troop Tracker: Trooper(s) has hit a milestone!", $message);
	}
}
	
?>