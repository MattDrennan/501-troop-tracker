<?php

/**
 * This file is used for putting the trooper counts in the notification database, so redundant trooper count notifications are not sent.
 * 
 * This file should be run once, when all the old troop tracker data is merged.
 *
 * @author  Matthew Drennan
 *
 */

include 'config.php';

// Delete old notifications
$conn->query("DELETE FROM notifications WHERE message LIKE '%now has%'");

// Loop through all troopers
$query = "SELECT * FROM troopers";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Update counts
		troopCheck($db->id);
	}
}

// Update settings
$last_id = $conn->insert_id;
$conn->query("UPDATE settings SET lastnotification = '".$last_id."'");

?>