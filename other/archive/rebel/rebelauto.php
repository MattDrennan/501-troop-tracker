<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Unlimited time to execute
ini_set('max_execution_time', '0');
set_time_limit(0);

// Include credential file
require 'cred.php';

// Connect to server - 1
$conn = new mysqli(dbServer, dbUser, dbPassword, dbName);

// Connect to server - 2
$conn2 = new mysqli(dbServer2, dbUser2, dbPassword2, dbName2);

// Check for errors
// Check connection to server - 1
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

// Check connection to server - 2
if ($conn2->connect_error)
{
	trigger_error('Database connection failed: ' . $conn2->connect_error, E_USER_ERROR);
}

$conn->query("UPDATE troopers SET oldid = 0");
$conn->query("UPDATE events SET oldid = 0");

// Go through the users on old database
$query = "SELECT * FROM troopers WHERE forum_id NOT IN (SELECT rebelforum FROM ".dbName2.".troopers WHERE rebelforum != 'NULL')";
if ($result = mysqli_query($conn2, $query) or die($conn2->error))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Insert into database
		$conn->query("INSERT INTO troopers (tkid, forum_id, mandoid, name, oldid, squad, rebelforum, approved) VALUES (0, '', 0, '".strip_tags(addslashes($db->name))."', '".$db->id."', 6, '".strip_tags(addslashes($db->forum_id))."', 1)") or die(error_log($conn->error));
		
		echo $db->name . '<br />';
	}
}

// Go through the users on old database
$query = "SELECT * FROM troopers WHERE forum_id IN (SELECT rebelforum FROM ".dbName2.".troopers WHERE rebelforum != 'NULL')";
if ($result = mysqli_query($conn2, $query) or die($conn2->error))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Update database
		$conn->query("UPDATE ".dbName2.".troopers, rebeltracker.troopers SET ".dbName2.".troopers.oldid = '".$db->id."' WHERE rebelforum = '".addslashes($db->forum_id)."'");
		
		echo $db->id . ' UPDATED' . '<br />';
	}
}

// Add events that the 501st troop tracker does not have
$query = "SELECT * FROM events WHERE newid = '0'";
if ($result = mysqli_query($conn2, $query) or die($conn2->error))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Convert int to date
		$intToDate = date("Y-m-d H:i:s", $db->date);
		
		// Insert into database
		$conn->query("INSERT INTO events (name, oldid, dateStart, dateEnd, comments, squad, closed) VALUES ('".strip_tags(addslashes($db->title))."', '".$db->id."', '".$intToDate."', '".$intToDate."', '".strip_tags(addslashes($db->description))."', 0, 1)") or die(error_log($conn->error));
		
		// Update other event database
		$conn2->query("UPDATE events SET newid = '".$conn->insert_id."' WHERE id = '".$db->id."'");
		
		echo $db->title . '<br />';
	}
}

// Link troopers
$query = "SELECT * FROM event_linker";
if ($result = mysqli_query($conn2, $query) or die($conn2->error))
{
	while ($db = mysqli_fetch_object($result))
	{
		$trooperId = -1;
		$eventId = -1;
		
		// Get new data - events
		$query2 = "SELECT id FROM troopers WHERE oldid = '".$db->trooper_id."'";
		if ($result2 = mysqli_query($conn, $query2) or die($conn->error))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				$trooperId = $db2->id;
			}
		}
		
		// Get new data - troopers
		$query2 = "SELECT newid FROM events WHERE id = '".$db->event_id."'";
		if ($result2 = mysqli_query($conn2, $query2) or die($conn->error))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				$eventId = $db2->newid;
			}
		}
		
		// Get if already in troop
		$troopCount = $conn->query("SELECT trooperid FROM event_sign_up WHERE troopid = '".$eventId."' AND trooperid = '".$trooperId."'");
		
		// Check troop counts and handle costume based on it
		if($troopCount->num_rows == 0)
		{
			// Insert into database - Rebel Legion
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, status, costume, attended_costume) VALUES ('".$trooperId."', '".$eventId."', 3, 720, 720)") or die(error_log($conn->error));
		}
		else
		{
			$conn->query("UPDATE event_sign_up SET costume = '721', attended_costume = '721' WHERE troopid = '".$eventId."' AND trooperid = '".$trooperId."'");
		}
		
		echo $trooperId . ' - ' . $eventId . '<br />';
	}
}

?>