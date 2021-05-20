<?php

/*
* This file will merge the old trooper tracker data with the new tracker.
* This file should be run once, then deleted.
*
* Be sure to adjust the DB Info variables before proceeding. DB Info - 1 is the new troop tracker database info
* and the DB Info - 2 is the old troop tracker data.
*
* IMPORTANT!! Be sure to run this before inserting any user data into the new database
*/

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

if(!isset($_GET['do']))
{
	// Link to start
	echo '
	<a href="merge.php?do=install">Click here to begin.</a>
	<br /><br />
	<b>PLEASE BE AWARE:</b>
	<ul>
		<li>
			This file will merge the old trooper tracker data with the new tracker.
		</li>
		
		<li>
			This file should be run once, then deleted.
		</li>
		
		<li>
			Be sure to adjust the DB Info variables before proceeding. DB Info - 1 is the new troop tracker database info and the DB Info - 2 is the old troop tracker data.
		</li>
		
		<li>
			DO NOT KEEP THIS FILE ON A LIVE SERVER!
		</li>
	</ul>';
}
else
{
	if($_GET['do'] == "install")
	{
		// Arrays
		$trooperArray = [];
		$eventArray = [];
		
		// Go through the users
		$query = "SELECT * FROM troopers";
		if ($result = mysqli_query($conn2, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Insert into database
				$conn->query("INSERT INTO troopers (name, oldid, squad, tkid, forum_id, approved) VALUES ('".$db->name."', '".$db->id."', '".getSquadID($db->squad_id)."', '".$db->tkid."', '".$db->forum_id."', 1)") or die(error_log($conn->error));
				
				// Update Array
				$trooperArray[$db->id] = $conn->insert_id;
			}
		}

		// Go through the events
		$query = "SELECT * FROM events";
		if ($result = mysqli_query($conn2, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Convert int to date
				$intToDate = date("Y-m-d H:i:s", $db->date);
				
				// Insert into database
				$conn->query("INSERT INTO events (name, oldid, dateStart, dateEnd, comments, squad, closed) VALUES ('".$db->title."', '".$db->id."', '".$intToDate."', '".$intToDate."', '".$db->description."', '".getSquadID($db->event_squad)."', 1)") or die(error_log($conn->error));
				
				// Update Array
				$eventArray[$db->id] = $conn->insert_id;
			}
		}

		// Go through the event linker
		$query = "SELECT * FROM event_linker";
		if ($result = mysqli_query($conn2, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Insert into database
				$conn->query("INSERT INTO event_sign_up (trooperid, troopid, status, attend) VALUES ('".$trooperArray[$db->trooper_id]."', '".$eventArray[$db->event_id]."', 3, 1)") or die(error_log($conn->error));
			}
		}

		// Message
		echo 'Success!';
	}
}

// getSquadID: Gets the ID of the squad from the string value
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