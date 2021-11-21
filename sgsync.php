<?php

// Include config
include 'config.php';

// Get Simple PHP DOM Tool - just a note, for this code to work, $stripRN must be false in tool
include('tool/dom/simple_html_dom.php');

// Purge SG troopers
$conn->query("DELETE FROM sg_troopers") or die($conn->error);

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

?>