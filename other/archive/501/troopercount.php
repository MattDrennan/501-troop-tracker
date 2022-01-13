<?php

include 'config.php';

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

?>