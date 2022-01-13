<?php

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