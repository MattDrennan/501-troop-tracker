<?php

/**
 * This file is used for providing an API to be used by other developers.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include "config.php";

// Main Data
$data = array();

if(isset($_GET['trooperid']) || isset($_GET['tkid']))
{
	// Query - Suppress
	@$query = "";

	$statement = $conn->prepare("SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.dateStart, events.dateEnd, troopers.id, troopers.name, troopers.forum_id, troopers.rebelforum, troopers.tkid, troopers.squad FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE (troopers.id = ? OR (troopers.tkid = ?) AND troopers.squad = ?) AND events.closed = '1' AND event_sign_up.status = '3' ORDER BY events.dateEnd DESC");
	$statement->bind_param("iii", $_GET['trooperid'], $_GET['tkid'], $_GET['squad']);
	$statement->execute();

	// Start count
	$i = 0;

	// Trooper variables
	$trooperName = "";
	$trooperTKID = "";
	$trooperForumID = "";
	$trooperRebelForum = "";
	$troopCount = 0;
	$eventArray = array();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set variables
			$trooperName = $db->name;
			$trooperTKID = $db->tkid;
			$trooperForumID = $db->forum_id;
			$trooperRebelForum = $db->rebelforum;
			
			// Start eventWithin
			$eventWithin = array("eventID" => $db->eventId, "eventName" => $db->eventName, "dateStart" => $db->dateStart, "dateEnd" => $db->dateEnd);
			
			// Push events
			array_push($eventArray, $eventWithin);
			
			// Increment
			$i++;
		}
	}

	// Set troop count
	$troopCount = $i;

	// Set temp array
	$tempArray = array("trooperName" => $trooperName, "tkid" => $trooperTKID, "501forum" => $trooperForumID, "rebelforum" => $trooperRebelForum, "events" => $eventArray, "troopCount" => $troopCount);

	// Set data
	array_push($data, $tempArray);
}
else if(isset($_GET['photos']) && isset($_GET['amount']))
{
	// Query
	$statement = $conn->prepare("SELECT * FROM uploads WHERE admin = '0' ORDER BY RAND() LIMIT ?");
	$statement->bind_param("i", $_GET['amount']);
	$statement->execute();
	
	// Set variables
	$uploadArray = array();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Start uploadWithin
			$uploadWithin = array("fileName" => $db->filename, "troopID" => $db->troopid, "trooperID" => $db->trooperid);
			
			// Push events
			array_push($uploadArray, $uploadWithin);
		}
	}
	
	// Set data
	array_push($data, $uploadArray);
}
else if(isset($_GET['events']))
{
	// Query
	$statement = $conn->prepare("SELECT * FROM events WHERE dateStart >= CURDATE() AND (closed = '0' OR closed = '3' OR closed = '4') ORDER BY dateStart");
	$statement->execute();

	// Load events that are today or in the future
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
	        $tempArray = array(
	        	'troopid' => $db->id,
	            'name' => $db->name,
	            'dateStart' => $db->dateStart,
	            'dateEnd' => $db->dateEnd,
	            'location' => $db->location
	        );

			array_push($data, $tempArray);
		}
	}
}

// If output not set
if(!isset($_GET['slideshow']))
{
	// Output JSON
	header('Content-type: application/json');
	echo json_encode($data);
}
else
{
	echo '
	<script src="https://www.w3schools.com/lib/w3.js"></script>';
	
	foreach($data[0] as $item)
	{
		// Get path info
		$info = pathinfo("images/uploads/".$item["fileName"]."");

		echo '
		<img class="slideshow" src="images/uploads/resize/'.$info['filename'].'.jpg" width="100%" height="100%">';
	}
	
	echo '
	<script>
	w3.slideshow(".slideshow", 3000);
	</script>';
}

?>