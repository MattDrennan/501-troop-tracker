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

// Check for errors
// Check connection to server - 1
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

// Enter trooper forum username - 501
$trooperForum = "";
$trooperRebel = "";

// Get 501 ID
$ID501_get = $conn->query("SELECT id FROM troopers WHERE forum_id = '".$trooperForum."'") or die($conn->error);
$ID501 = $ID501_get->fetch_row();

// Get Rebel ID
$IDRebel_get = $conn->query("SELECT id FROM troopers WHERE rebelforum = '".$trooperRebel."'") or die($conn->error);
$IDRebel = $IDRebel_get->fetch_row();

// Don't run twice check
$trooperCount = $conn->query("SELECT rebelforum FROM troopers WHERE rebelforum = '".$trooperRebel."' AND forum_id = '".$trooperForum."'") or die($conn->error);

// Duplicate check
if($trooperCount->num_rows > 0)
{
	die("Already done.");
}

// event sign ups - Rebels
$query = "SELECT * FROM event_sign_up WHERE trooperid = '".$IDRebel[0]."'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Set up data exists check
		$i = 0;
		
		// Go event sign ups - 501
		$query2 = "SELECT * FROM event_sign_up WHERE trooperid = '".$ID501[0]."' AND troopid = '".$db->troopid."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Print
				echo $db2->troopid . '<br />';
				
				// Update event sign up to dual costume
				$conn->query("UPDATE event_sign_up SET costume = '721', attended_costume = '721' WHERE trooperid = '".$ID501[0]."' AND troopid = '".$db->troopid."'");
				
				// Delete old event sign up
				$conn->query("DELETE FROM event_sign_up WHERE id = '".$db->id."'");
				
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
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, status, costume, attended_costume) VALUES ('".$ID501[0]."', '".$db->troopid."', 3, 720, 720)") or die(error_log($conn->error));
			
			// Delete old event sign up
			$conn->query("DELETE FROM event_sign_up WHERE id = '".$db->id."'");
		}
	}
}

// Delete old account - Rebel
$conn->query("DELETE FROM troopers WHERE id = '".$IDRebel[0]."'");

// Update new account
$conn->query("UPDATE troopers SET rebelforum = '".$trooperRebel."' WHERE id = '".$ID501[0]."'");

?>