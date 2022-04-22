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
		// Insert into database
		$conn->query("INSERT INTO droid_troopers (forum_id, droidname, imageurl) VALUES ('".$value[0]."', '".$value[1]."', '".$value[2]."')");
	}

	// Increment
	$i++;
}

?>