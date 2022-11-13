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
$conn->query("DELETE FROM sg_troopers");

// Pull extra data from spreadsheet
$values = getSheet("1PcveycMujakkKeG2m4y8iFunrFbo2KVpQJ00GyPI3b8", "Sheet1");

// Reset Saber Guild Status
$conn->query("UPDATE troopers SET pSG = 0");

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

		// Insert into database
		$conn->query("INSERT INTO sg_troopers (sgid, name, image, ranktitle, costumename, link) VALUES ('".cleanInput($value[2])."', '".cleanInput($value[0])."', '".cleanInput($image)."', '".cleanInput($value[1])."', '".cleanInput($value[3])."', '')");
		
		// Update status to regular member
		$conn->query("UPDATE troopers SET pSG = 1 WHERE sgid = '".str_replace("SG-", "", $value[2])."' AND sgid > 0");
	}

	// Increment
	$i++;
}

?>