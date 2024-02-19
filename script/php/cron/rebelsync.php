<?php

/**
 * This file is used for scraping Rebel Legion data.
 * 
 * This should be run weekly by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Get Simple PHP DOM Tool - just a note, for this code to work, $stripRN must be false in tool
include(dirname(__DIR__) . '/simple_html_dom.php');

// Check date time for sync
$statement = $conn->prepare("SELECT syncdaterebels FROM settings");
$statement->execute();
$statement->bind_result($syncdaterebels);
$statement->fetch();
$statement->close();

// Compare dates
if(strtotime($syncdaterebels) >= strtotime("-7 day"))
{
	// Prevent script from continuing
	die("Already updated recently.");
}

// Purge rebel troopers
$statement = $conn->prepare("DELETE FROM rebel_troopers");
$statement->execute();

// Purge rebel costumes
$statement = $conn->prepare("DELETE FROM rebel_costumes");
$statement->execute();

// Costume image array (duplicate check)
$costumeImagesG = array();

// Loop through all Rebels
$statement = $conn->prepare("SELECT * FROM troopers WHERE pRebel > 0");
$statement->execute();

if ($result = $statement->get_result())
{
	while ($db = mysqli_fetch_object($result))
	{
		// Search ID for profile
		$html = file_get_html('https://www.forum.rebellegion.com/forum/profile.php?mode=viewprofile&u=' . $db->rebelforum);

		// Set Rebel ID
		$rebelID = $db->rebelforum;
		$rebelForum = $db->rebelforum;
		
		// Costume name array
		$costumeNames = array();
		
		// Costume image array
		$costumeImages = array();
		
		// Loop through costume images on profile
		foreach($html->find('img[height=125]') as $r)
		{
			// Set should add to prevent duplicates
			$addTo = true;

			// Check to see if exists in duplicate array
			if(in_array(str_replace("sm", "-A", $r->src), $costumeImagesG))
			{
				$addTo = false;
			}
			
			// Check to see if we can add (duplicates)
			if($addTo)
			{
				// Push to array
				array_push($costumeImages, str_replace("sm", "-A", $r->src));
				
				// Push to array (to check for duplicates)
				array_push($costumeImagesG, str_replace("sm", "-A", $r->src));

				// Get costume names
				foreach($r->parent->parent->parent->find('span[class=gen] a') as $s)
				{
					array_push($costumeNames, $s->innertext);
				}
			}
		}
		
		// Start i count
		$cc = 0;

		// Loop through created arrays
		foreach($costumeNames as $c)
		{
			// Query
			$statement = $conn->prepare("INSERT INTO rebel_costumes (rebelid, costumename, costumeimage) VALUES (?, ?, ?)");
			$statement->bind_param("sss", $rebelID, $costumeNames[$cc], $costumeImages[$cc]);
			$statement->execute();
			
			// Increment
			$cc++;
		}

		// Get Rebel name
		foreach($html->find('td[class=catRight] span') as $r)
		{
			$rebelName = preg_match( '!\(([^\)]+)\)!', $r->innertext, $match);
			$rebelName = $match[1];
		}

		// Query
		$statement = $conn->prepare("INSERT INTO rebel_troopers (rebelid, name, rebelforum) VALUES (?, ?, ?)");
		$statement->bind_param("sss", $rebelID, $rebelName, $rebelForum);
		$statement->execute();
	}
}

// Pull extra data from spreadsheet
$values = getSheet("1I3FuS_uPg2nuC80PEA6tKYaVBd1Qh1allTOdVz3M6x0", "Troopers");

// Set up count
$i = 0;

foreach($values as $value)
{
	// If not first
	if($i != 0)
	{
		// Query
		$statement = $conn->prepare("INSERT INTO rebel_troopers (rebelid, name, rebelforum) VALUES (?, ?, ?)");
		$statement->bind_param("sss", $value[0], $value[1], $value[2]);
		$statement->execute();
	}

	// Increment
	$i++;
}

// Pull extra data from spreadsheet
$values = getSheet("1I3FuS_uPg2nuC80PEA6tKYaVBd1Qh1allTOdVz3M6x0", "Costumes");

// Set up count
$i = 0;

foreach($values as $value)
{
	// If not first
	if($i != 0)
	{
		// Insert into database
		$statement = $conn->prepare("INSERT INTO rebel_costumes (rebelid, costumename, costumeimage) VALUES (?, ?, ?)");
		$statement->bind_param("sss", $value[0], $value[1], $value[2]);
		$statement->execute();
	}

	// Increment
	$i++;
}

echo '
COMPLETE!';

// Update date time for last sync
$statement = $conn->prepare("UPDATE settings SET syncdaterebels = NOW()");
$statement->execute();

?>