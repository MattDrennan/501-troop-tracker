<?php

// Include config
include "config.php";

// Main Data
$data = array();

if(isset($_GET['trooperid']) || isset($_GET['tkid']))
{
	// Query - Suppress
	@$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd, troopers.id, troopers.name, troopers.forum_id, troopers.rebelforum, troopers.tkid, troopers.squad FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE (troopers.id = '".cleanInput($_GET['trooperid'])."' OR (troopers.tkid = '".cleanInput($_GET['tkid'])."') AND troopers.squad = '".cleanInput($_GET['squad'])."') AND events.closed = '1' AND event_sign_up.status = '3' ORDER BY events.dateEnd DESC";

	// Start count
	$i = 0;

	// Trooper variables
	$trooperName = "";
	$trooperTKID = "";
	$trooperForumID = "";
	$trooperRebelForum = "";
	$troopCount = 0;
	$eventArray = array();

	if ($result = mysqli_query($conn, $query) or die($conn->error))
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
	$query = "SELECT * FROM uploads WHERE admin = '0' ORDER BY RAND() LIMIT ".cleanInput($_GET['amount'])."";
	
	// Set variables
	$uploadArray = array();

	if ($result = mysqli_query($conn, $query) or die($conn->error))
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
		//echo $key;
		//print_r($item);
		echo '
		<img class="slideshow" src="https://www.fl501st.com/troop-tracker/images/uploads/'.$item["fileName"].'" width="100%" height="100%">';
	}
	
	echo '
	<script>
	w3.slideshow(".slideshow", 3000);
	</script>';
}

?>