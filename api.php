<?php

// Include config
include "config.php";

// Query - Suppress
@$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, event_sign_up.attended_costume, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd, troopers.id, troopers.name, troopers.forum_id, troopers.rebelforum, troopers.tkid, troopers.squad FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE (troopers.id = '".cleanInput($_GET['trooperid'])."' OR (troopers.tkid = '".cleanInput($_GET['tkid'])."') AND troopers.squad = '".cleanInput($_GET['squad'])."') AND events.closed = '1' AND event_sign_up.status = '3' ORDER BY events.dateEnd DESC";

// Start count
$i = 0;

// Main Data
$data = array();

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
		$eventWithin = array("eventID" => $db->id, "eventName" => $db->eventName, "dateStart" => $db->dateStart, "dateEnd" => $db->dateEnd);
		
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

// Output JSON
header('Content-type: application/json');
echo json_encode($data);

?>