<?php

/**
 * This file monitors changes to notification_check database, and sends notifications based on the results.
 * 
 * This file should be run every two minutes by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Loop through all events to send notifications
$query = "SELECT notification_check.troopid, notification_check.commentid, events.squad, events.name, events.id, events.comments FROM notification_check LEFT JOIN events ON events.id = notification_check.troopid WHERE notification_check.troopid != 0 AND notification_check.commentid = 0 AND notification_check.trooperid = 0 AND notification_check.trooperstatus = 0 AND notification_check.troopstatus = 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Build e-mail
		$emailBody = "";
		$emailBody .= readInput($db->name) . " has been added in " . getSquadName($db->squad) . "\n\nhttps://www.fl501st.com/troop-tracker/index.php?event=".$db->id."\n\n\n\n";
		$emailBody .= "You can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com";

		// Loop through all troopers
		$query2 = "SELECT * FROM troopers WHERE email != '' AND subscribe = '1' AND efast = '1' AND esquad".$db->squad." = '1'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Send e-mail
				sendEmail($db2->email, readInput($db2->name), "Troop Tracker: " . readInput($db->name) . " posted!", $emailBody);
			}
		}

		// Send notification to Discord
		sendEventNotify($db->id, readInput($db->name), readInput($db->comments), $db->squad);
	}
}

// Loop through all trooper updates
$query = "SELECT notification_check.troopid, notification_check.commentid, notification_check.trooperid, notification_check.trooperstatus, events.squad, events.name, events.id FROM notification_check LEFT JOIN events ON events.id = notification_check.troopid WHERE notification_check.troopid != 0 AND notification_check.commentid = 0 AND notification_check.trooperid != 0 AND notification_check.trooperstatus != 0 AND notification_check.troopstatus = 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Check status
		if($db->trooperstatus == 1)
		{
			// Send update
			sendEventUpdate($db->troopid, $db->trooperid, "Troop Tracker: Trooper Signed Up On " . readInput($db->name), getName($db->trooperid) . " signed up on " . readInput($db->name) . ".");
		}
		// Check status
		if($db->trooperstatus == 2)
		{
			// Send update
			sendEventUpdate($db->troopid, $db->trooperid, "Troop Tracker: Trooper Canceled Sign Up On " . readInput($db->name), getName($db->trooperid) . " canceled on " . readInput($db->name) . ".");
		}
	}
}

// Loop through all trooper updates
$query = "SELECT notification_check.troopid, notification_check.commentid, notification_check.trooperid, notification_check.trooperstatus, notification_check.troopstatus, events.squad, events.name, events.id FROM notification_check LEFT JOIN events ON events.id = notification_check.troopid WHERE notification_check.troopid != 0 AND notification_check.commentid = 0 AND notification_check.trooperid != 0 AND notification_check.trooperstatus = 0 AND notification_check.troopstatus != 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Check status
		if($db->troopstatus == 2)
		{
			// Send update
			sendEventUpdate($db->troopid, $db->trooperid, "Troop Tracker: " . getEventTitle($db->troopid) . " has been canceled.", getEventTitle($db->troopid) . " has been canceled by command staff. Please refer to the troop for more details.");
		}
	}
}

$conn->query("DELETE FROM notification_check");
	
?>