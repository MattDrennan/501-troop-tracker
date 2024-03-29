<?php

/**
 * This file is used for merging two Rebel Legion accounts.
 * 
 * goodRebel is the Rebel Legion forum username to keep, and badRebel is the ID of the trooper to delete after merging.
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

// Enter trooper forum username - Rebel
$goodRebel = 0;

// Enter Bad Rebel ID
$badRebel = 0;

// Don't run twice check
$trooperCount = $conn->query("SELECT id FROM troopers WHERE id = '".$badRebel."'") or die($conn->error);

// Duplicate check
if($trooperCount->num_rows <= 0)
{
	die("Already done.");
}

// event sign ups - Bad Rebel Account
$query = "SELECT * FROM event_sign_up WHERE trooperid = '".$badRebel."'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Print
		echo $db->troopid . ' - not exist<br />';
		
		// Update event sign up to dual costume
		$conn->query("INSERT INTO event_sign_up (trooperid, troopid, status, costume) VALUES ('".$goodRebel."', '".$db->troopid."', 3, 720)") or die(error_log($conn->error));
		
		// Delete old event sign up
		$conn->query("DELETE FROM event_sign_up WHERE id = '".$db->id."'");
	}
}

// Delete old account - Rebel
$conn->query("DELETE FROM troopers WHERE id = '".$badRebel."'");

?>