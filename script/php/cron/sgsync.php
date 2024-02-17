<?php

/**
 * This file is used for scraping Saber Guild data.
 * 
 * This should be run weekly by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Get Simple PHP DOM Tool - just a note, for this code to work, $stripRN must be false in tool
include(dirname(__DIR__) . '/../../tool/dom/simple_html_dom.php');

// Purge SG troopers
$statement = $conn->prepare("DELETE FROM sg_troopers");
$statement->execute();

// Pull extra data from spreadsheet
$values = getSheet("1PcveycMujakkKeG2m4y8iFunrFbo2KVpQJ00GyPI3b8", "Sheet1");

// Reset Saber Guild Status
$statement = $conn->prepare("UPDATE troopers SET pSG = 0");
$statement->execute();

// Set up count
$i = 0;

foreach($values as $value)
{
	// If not first
	if($i != 0)
	{
		// Set up image
		$image = $value[4];
		
		// Convert Google Drive link
		if (strpos($image, "view?usp=drivesdk") !== false)
		{
			$image = explode("/", $image);
			$image = "https://drive.google.com/uc?id=" . $image[5] . "";
		}

		$value[2] = cleanInput($value[2]);
		$value[0] = cleanInput($value[0]);
		$value[1] = cleanInput($value[1]);
		$value[3] = cleanInput($value[3]);
		$image = cleanInput($image);

		// Insert into database
		$statement = $conn->prepare("INSERT INTO sg_troopers (sgid, name, image, ranktitle, costumename, link) VALUES (?, ?, ?, ?, ?, '')");
		$statement->bind_param("isssss", $value[2], $value[0], $image, $value[1], $value[3]);
		$statement->execute();
		
		// Update status to regular member
		$sgid = str_replace("SG-", "", $value[2]);
		$statement = $conn->prepare("UPDATE troopers SET pSG = 1 WHERE sgid = ? AND sgid > 0");
		$statement->bind_param("i", $sgid);
		$statement->execute();
	}

	// Increment
	$i++;
}

?>