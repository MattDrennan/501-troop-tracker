<?php

/**
 * This file is used for checking events and updating information in the database
 * 
 * This should be run every two minutes
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Loop through open events, with no lat and long coords
$query = "SELECT * FROM events WHERE dateStart >= CURDATE() AND (closed = '0' OR closed = '3' OR closed = '4') AND latitude IS NULL AND longitude IS NULL LIMIT 2";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Get data
		$event = getLatLong($db->location);
		
		// Update
		$statement = $conn->prepare("UPDATE events SET latitude = ?, longitude = ? WHERE id = ?");
		$statement->bind_param("ssi", $event['latitude'], $event['longitude'], $db->id);
		$statement->execute();
	}
}

?>