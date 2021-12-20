<?php

// Include config file
include "../../../config.php";

// Loop through all events to send notifications
$query = "SELECT notification_check.troopid, notification_check.commentid, events.squad, events.name, events.id FROM notification_check LEFT JOIN events ON events.id = notification_check.troopid WHERE notification_check.troopid != 0 AND notification_check.commentid = 0 AND notification_check.trooperid = 0 AND notification_check.trooperstatus = 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Build e-mail
		$emailBody = "";
		$emailBody .= $db->name . " has been added in " . getSquadName($db->squad) . "\n\nhttps://www.fl501st.com/troop-tracker/index.php?event=".$db->id."\n\n\n\n";
		$emailBody .= "You can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com";

		// Loop through all troopers
		$query2 = "SELECT * FROM troopers WHERE email != '' AND subscribe = '1' AND efast = '1' AND esquad".$db->squad." = '1'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Send e-mail
				sendEmail($db2->email, $db2->name, "Troop Tracker: " . $db->name . " posted!", $emailBody);
			}
		}

		// Send notification to Discord
		sendEventNotify($db->id, $db->name, $db->comments, $db->squad);
		
		// Post to Twitter
		postTweet("".$db->name." has been added in ".getSquadName($db->squad).".");
	}
}

// Loop through all comments to send notifications
$query = "SELECT notification_check.troopid, notification_check.commentid, events.squad, events.name, events.id FROM notification_check LEFT JOIN events ON events.id = notification_check.troopid WHERE notification_check.troopid != 0 AND notification_check.commentid != 0 AND notification_check.trooperid = 0 AND notification_check.trooperstatus = 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Set up comment values
		$commentName = "";
		$commentMessage = "";

		// Loop through comment to get values
		$query2 = "SELECT * FROM comments WHERE id = '".$db->commentid."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				$commentName = getName($db2->trooperid);
				$commentMessage = $db2->comment;
			}
		}

		// Loop through all troopers
		$query2 = "SELECT event_sign_up.id AS signupId, troopers.email, troopers.name, troopers.id FROM event_sign_up LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopers.subscribe = '1' AND troopers.email != '' AND troopers.ecomments = '1' AND event_sign_up.troopid = '".$db->id."' GROUP BY troopers.id";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Send E-mail
				@sendEmail($db2->email, $db2->name, "Troop Tracker: A comment has posted on ".$db->name."!", $commentName . ": " . $commentMessage . "\n\nhttps://www.fl501st.com/troop-tracker/index.php?event=".$db->id."\n\n\n\nYou can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com");
			}
		}
	}
}

// Loop through all trooper updates
$query = "SELECT notification_check.troopid, notification_check.commentid, notification_check.trooperid, notification_check.trooperstatus, events.squad, events.name, events.id FROM notification_check LEFT JOIN events ON events.id = notification_check.troopid WHERE notification_check.troopid != 0 AND notification_check.commentid = 0 AND notification_check.trooperid != 0 AND notification_check.trooperstatus != 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Check status
		if($db->trooperstatus == 1)
		{
			// Send update
			sendEventUpdate($db->troopid, $db->trooperid, "Troop Tracker: Trooper Signed Up On " . $db->name, getName($db->trooperid) . " signed up on " . $db->name . ".");
		}
		// Check status
		if($db->trooperstatus == 2)
		{
			// Send update
			sendEventUpdate($db->troopid, $db->trooperid, "Troop Tracker: Trooper Canceled Sign Up On " . $db->name, getName($db->trooperid) . " canceled on " . $db->name . ".");
		}
	}
}

$conn->query("DELETE FROM notification_check");
	
?>