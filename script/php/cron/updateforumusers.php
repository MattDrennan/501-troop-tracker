<?php

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Loop through all troopers with Xenforo set up
$query = "SELECT * FROM troopers WHERE user_id != 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Update TKID on forum
		updateUserCustom($db->user_id, "tkid", $db->tkid);
	}
}

?>