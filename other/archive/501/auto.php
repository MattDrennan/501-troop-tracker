<?php

/**
 * This file is used for converting old troop tracker data to the new troop tracker.
 *
 * @author  Matthew Drennan
 *
 */

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

// Records added
$userRecords = 0;
$eventRecords = 0;
$linkRecords = 0;

// Start ID
$startTrooperId = 0;
$startEventId = 0;
$startLinkId = 0;

// Last ID
$lastTrooperId = 0;
$lastEventId = 0;
$lastLinkId = 0;

// Get settings
$query = "SELECT * FROM settings";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Trooper
		$startTrooperId = $db->lastidtrooper;
		$lastTrooperId = $db->lastidtrooper;
		
		// Event
		$startEventId = $db->lastidevent;
		$lastEventId = $db->lastidevent;
		
		// Link
		$startLinkId = $db->lastidlink;
		$lastLinkId = $db->lastidlink;
	}
}

// Go through the users on old database
$query = "SELECT * FROM troopers WHERE id > ".$startTrooperId."";
if ($result = mysqli_query($conn2, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Insert into database
		$conn->query("INSERT INTO troopers (name, oldid, squad, tkid, forum_id, approved) VALUES ('".$db->name."', '".$db->id."', '".getSquadID($db->squad_id)."', '".$db->tkid."', '".$db->forum_id."', 1)") or die(error_log($conn->error));
		
		// Update records count
		$userRecords++;
		
		// Update ID
		$lastTrooperId = $db->id;
	}
}

// Update last trooper id in database
$conn->query("UPDATE settings SET lastidtrooper = '".$lastTrooperId."'");

// Go through the old events
$query = "SELECT * FROM events WHERE id > ".$startEventId."";
if ($result = mysqli_query($conn2, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Convert int to date
		$intToDate = date("Y-m-d H:i:s", $db->date);
		
		// Insert into database
		$conn->query("INSERT INTO events (name, oldid, dateStart, dateEnd, comments, squad, closed) VALUES ('".$db->title."', '".$db->id."', '".$intToDate."', '".$intToDate."', '".$db->description."', '".getSquadID($db->event_squad)."', 1)") or die(error_log($conn->error));
		
		// Update records count
		$eventRecords++;
		
		// Update ID
		$lastEventId = $db->id;
	}
}

// Update last event id in database
$conn->query("UPDATE settings SET lastidevent = '".$lastEventId."'");

// Go through the event linker
$query = "SELECT * FROM event_linker WHERE id > ".$startLinkId."";
if ($result = mysqli_query($conn2, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		$trooperId = 0;
		$eventId = 0;
		
		// Get new data - events
		$query2 = "SELECT id FROM troopers WHERE oldid = '".$db->trooper_id."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				$trooperId = $db2->id;
			}
		}
		
		// Get new data - troopers
		$query2 = "SELECT id FROM events WHERE oldid = '".$db->event_id."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				$eventId = $db2->id;
			}
		}
		
		// Insert into database
		$conn->query("INSERT INTO event_sign_up (trooperid, troopid, status) VALUES ('".$trooperId."', '".$eventId."', 3)") or die(error_log($conn->error));
		
		// Update records count
		$linkRecords++;
		
		// Update ID
		$lastLinkId = $db->id;
	}
}

// Update last trooper id in database
$conn->query("UPDATE settings SET lastidlink = '".$lastLinkId."'");

echo '
Finished!
<br />
User Records Added: '.$userRecords.'
<br />
Event Records Added: '.$eventRecords.'
<br />
Link Records Added: '.$linkRecords.'
<br />';

/**
 * Converts the string value to squad ID
 *
 * @param string $value The string value to be formatted
 * @return int Returns the squad ID
 */
function getSquadID($value)
{
	// Get squad value, convert to number_format
	$squad_number = 0;
	
	if($value == "parjai")
	{
		$squad_number = 3;
	}
	else if($value == "tampabay")
	{
		$squad_number = 5;
	}
	else if($value == "squad7")
	{
		$squad_number = 4;
	}
	else if($value == "everglades")
	{
		$squad_number = 1;
	}
	else if($value == "makaze")
	{
		$squad_number = 2;
	}
	else if($value == "z-nongarrison")
	{
		// Set to -1, later the user can set their squad/club
		$squad_number = -1;
	}
	else if($value == "z-nosquad")
	{
		// Set to -1, later the user can set their squad/club
		$squad_number = -1;
	}
	
	return $squad_number;
}

?>