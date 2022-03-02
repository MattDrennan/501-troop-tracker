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
$conn->query("DELETE FROM sg_troopers") or die($conn->error);

/*
// get DOM from URL or file
$html = file_get_html('https://saberguild.org/member-gallery/');

// Loop through comments
foreach($html->find('div.single-team-area') as $a)
{
	foreach($a->find('a') as $b)
	{
		// Get title which contains ID and name
		$title = $b->title;
		$title = explode("&#8211;", $title);

		// Translate to different variables
		$id = trim($title[0]);
		$id = preg_replace('/[^0-9]/', '', $id);

		$name = trim($title[1]);

		$link = $b->href;

		$image = "";

		foreach($a->find('img') as $b)
		{
			// Set image
			$image = $b->src;

			// Print
			print_r($b->src);
			echo '<br />';
			break;
		}

		// Insert into database
		$conn->query("INSERT INTO sg_troopers (sgid, name, image, link) VALUES ('".$id."', '".$name."', '".$image."', '".$link."')") or die($conn->error);

		// Print
		print_r($id);
		echo '<br />';
		print_r($name);
		echo '<br />';
		print_r($link);
		echo '<br />';
		break;
	}

	echo '<hr />';
}
*/

// Pull extra data from spreadsheet
$values = getSheet("1PcveycMujakkKeG2m4y8iFunrFbo2KVpQJ00GyPI3b8", "Sheet1");

// Reset Saber Guild Status
$conn->query("UPDATE troopers SET pOther = 0 WHERE sgid > 0") or die($conn->error);

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
		$conn->query("INSERT INTO sg_troopers (sgid, name, image, ranktitle, costumename, link) VALUES ('".cleanInput($value[2])."', '".cleanInput($value[0])."', '".cleanInput($image)."', '".cleanInput($value[1])."', '".cleanInput($value[3])."', '')") or die($conn->error);
		
		// Update status to regular member
		$conn->query("UPDATE troopers SET pOther = 1 WHERE sgid = '".str_replace("SG-", "", $value[2])."'") or die($conn->error);
	}

	// Increment
	$i++;
}

?>