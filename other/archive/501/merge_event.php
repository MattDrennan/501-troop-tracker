<?php

/**
 * This file is used for merging two events.
 * 
 * Edit variables deleteID and newID to merge events. deleteID is the event ID to be deleted, and newID is the event ID to be retained.
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

// Check for errors
// Check connection to server - 1
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

// Enter the ID to be deleted, and enter the ID to merge
$deleteID = "";
$newID = "";

// Don't run twice check
$eventCount = $conn->query("SELECT id FROM events WHERE id = '".$deleteID."'") or die($conn->error);

// Duplicate check
if($eventCount->num_rows == 0)
{
	die("Already done.");
}

/**
 * Returns the club ID based on the costume ID
 * 
 * @param int $id The costume ID
 * @return int Returns the costume club ID
 */
function getCostumeClub($id)
{
	global $conn;
	
	$query = "SELECT * FROM costumes WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->club;
		}
	}
}

// event sign ups - Rebels
$query = "SELECT * FROM event_sign_up WHERE troopid = '".$deleteID."'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Set up data exists check
		$i = 0;
		
		// Go event sign ups - 501
		$query2 = "SELECT * FROM event_sign_up WHERE trooperid = '".$db->trooperid."' AND troopid = '".$newID."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				if((getCostumeClub($db->costume) == 1 && getCostumeClub($db2->costume) == 0) || (getCostumeClub($db2->costume) == 1 && getCostumeClub($db->costume) == 0))
				{
					// Print
					echo $db2->troopid . ' - DUAL<br />';
					
					// Update event sign up to dual costume
					$conn->query("UPDATE event_sign_up SET costume = '721' WHERE trooperid = '".$db->trooperid."' AND troopid = '".$newID."'") or die($conn->error);
					
					// Delete old event sign up
					$conn->query("DELETE FROM event_sign_up WHERE id = '".$db->id."'") or die($conn->error);
				}
				
				// Increment - data exists
				$i++;
			}
		}
		
		// If troop not found (Not a duplicate)
		if($i == 0)
		{
			// Print
			echo $db->troopid . ' - not exist<br />';
			
			// Update event sign up to dual costume
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, status, costume) VALUES ('".$db->trooperid."', '".$newID."', 3, '".$db->costume."')") or die($conn->error);
			
			// Delete old event sign up
			$conn->query("DELETE FROM event_sign_up WHERE id = '".$db->id."'") or die($conn->error);
		}
	}
}

$conn->query("DELETE FROM events WHERE id = '".$deleteID."'") or die($conn->error);

?>