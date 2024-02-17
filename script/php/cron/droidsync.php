<?php

/**
 * This file is used for scraping Droid Builder data.
 * 
 * This should be run weekly by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Purge Droids
$conn->query("DELETE FROM droid_troopers");

// Pull extra data from spreadsheet
$values = getSheet("195NT1crFYL_ECVyzoaD2F1QXGW5WxlnBDfDaLVtM87Y", "Sheet1");

// Set up count
$i = 0;

foreach($values as $value)
{
	// If not first
	if($i != 0)
	{
		$value[0] = cleanInput($value[0]);
		$value[1] = cleanInput($value[1]);
		$value[2] = cleanInput($value[2]);

		// Insert into database
		$statement = $conn->prepare("INSERT INTO droid_troopers (forum_id, droidname, imageurl) VALUES (?, ?, ?)");
		$statement->bind_param("sss", $value[0], $value[1], $value[2]);
		$statement->execute();
	}

	// Increment
	$i++;
}

?>