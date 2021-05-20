<?php

// Unlimited time to execute
ini_set('max_execution_time', '0');
set_time_limit(0);

// DB Info - 1
$dbServer = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "troop";

// DB Info - 2
$dbServer2 = "localhost";
$dbUser2 = "root";
$dbPassword2 = "";
$dbName2 = "troopmerge";

// Connect to server - 1
$conn = new mysqli($dbServer, $dbUser, $dbPassword, $dbName);

// Connect to server - 2
$conn2 = new mysqli($dbServer2, $dbUser2, $dbPassword2, $dbName2);

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

// Arrays
$trooperArray = [];
$eventArray = [];

// First Event ID
$firstEvent = 0;

// Records added
$userRecords = 0;
$eventRecords = 0;
$linkRecords = 0;

// Go through the users on old database
$query = "SELECT * FROM troopers";
if ($result = mysqli_query($conn2, $query))
{
	// Set
	$found = false;
	
	while ($db = mysqli_fetch_object($result))
	{
		// Go through the users on new database
		$query2 = "SELECT * FROM troopers";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Found a match
				if($db->forum_id == $db2->forum_id)
				{
					$found = true;
					break;
				}
			}
		}
		
		// If not found
		if(!$found)
		{
			// Insert into database
			$conn->query("INSERT INTO troopers (name, squad, tkid, forum_id, approved) VALUES ('".$db->name."', '".getSquadID($db->squad_id)."', '".$db->tkid."', '".$db->forum_id."', 1)") or die(error_log($conn->error));
			
			// Update Array
			$trooperArray[$db->id] = $conn->insert_id;
			
			// Update records count
			$userRecords++;
		}
		
		// Reset
		$found = false;
	}
}

// Go through the old events
$query = "SELECT * FROM events";
if ($result = mysqli_query($conn2, $query))
{
	// Set
	$found = false;
	
	while ($db = mysqli_fetch_object($result))
	{
		// Go through the users on new database
		$query2 = "SELECT * FROM events";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Found a match
				if($db->title == $db2->name)
				{
					$found = true;
					break;
				}
			}
		}
		
		// If not found
		if(!$found)
		{
			// Convert int to date
			$intToDate = date("Y-m-d H:i:s", $db->date);
			
			// Insert into database
			$conn->query("INSERT INTO events (name, dateStart, dateEnd, comments, squad, closed) VALUES ('".$db->title."', '".$intToDate."', '".$intToDate."', '".$db->description."', '".getSquadID($db->event_squad)."', 1)") or die(error_log($conn->error));
			
			// Update Array
			$eventArray[$db->id] = $conn->insert_id;
			
			// Update ID
			if($firstEvent == 0)
			{
				$firstEvent = $db->id;
			}
			
			// Update records count
			$eventRecords++;
		}
		
		// Reset
		$found = false;
	}
}

if($firstEvent != 0)
{
	// Go through the event linker
	$query = "SELECT * FROM event_linker WHERE event_id > ".$firstEvent."";
	if ($result = mysqli_query($conn2, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Insert into database
			$conn->query("INSERT INTO event_sign_up (trooperid, troopid, status, attend) VALUES ('".$trooperArray[$db->trooper_id]."', '".$eventArray[$db->event_id]."', 3, 1)") or die(error_log($conn->error));
			
			// Update records count
			$linkRecords++;
		}
	}
}

echo '
Finished!
<br />
User Records Added: '.$userRecords.'
<br />
Event Records Added: '.$eventRecords.'
<br />
Link Records Added: '.$linkRecords.'
<br />';

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