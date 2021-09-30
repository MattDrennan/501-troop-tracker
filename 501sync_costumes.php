<?php

// Include config file
include "config.php";

// Get contents of file
$json = file_get_contents("https://www.501st.com/memberAPI/v3/garrisons/9/members");
$obj = json_decode($json);

// Keep track of records
$j = 0;
$n = 0;
$u = 0;

// Loop through all members
foreach($obj->unit->members as $value)
{
	// Get specific data on member - costumes
	$json2 = file_get_contents("https://www.501st.com/memberAPI/v3/legionId/" . $value->legionId . "/costumes");
	$obj2 = json_decode($json2);
	
	// Loop through all costumes
	foreach($obj2->costumes as $costume)
	{
		// Check to see if this member exists in database, and if they do, check to see if we need to update anything
		$query = "SELECT * FROM 501st_costumes WHERE legionid = '".$value->legionId."' AND costumeid = '".$costume->costumeId."'";
		
		// Start from zero
		$i = 0;

		// Run through query
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Record exists, lets check it for updates
				
				// Photo
				if($costume->photoURL != $db->photo)
				{
					$conn->query("UPDATE 501st_costumes SET photo = '".$costume->photoURL."' WHERE legionid = '".$db->legionid."' AND costumeid = '".$db->costumeid."'");

					// Increment to keep track
					$u++;
				}
				
				// Thumbnail
				if($costume->thumbnail != $db->thumbnail)
				{
					$conn->query("UPDATE 501st_costumes SET thumbnail = '".$costume->thumbnail."' WHERE legionid = '".$db->legionid."' AND costumeid = '".$db->costumeid."'");

					// Increment to keep track
					$u++;
				}
				
				// Bucket Off
				if($costume->bucketOffPhoto != $db->bucketoff)
				{
					$conn->query("UPDATE 501st_costumes SET bucketoff = '".$costume->bucketOffPhoto."' WHERE legionid = '".$db->legionid."' AND costumeid = '".$db->costumeid."'");

					// Increment to keep track
					$u++;
				}
				
				$i++;
			}
		}
		
		// If no results, add costume to database
		if($i == 0)
		{
			// Insert into database
			$conn->query("INSERT INTO 501st_costumes (legionid, costumeid, prefix, costumename, photo, thumbnail, bucketoff) VALUES ('".$value->legionId."', '".$costume->costumeId."', '".$costume->prefix."', '".$costume->costumeName."', '".$costume->photoURL."', '".$costume->thumbnail."', '".$costume->bucketOffPhoto."')");
		
			// Increment to keep track
			$j++;
		}
	}
	
	// Check to see if this member exists in costume database, and if they do, check to see if we need to update anything
	$query = "SELECT * FROM 501st_costumes WHERE legionid = '".$value->legionId."'";

	// Run through query
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set up found var
			$found = false;
			
			// Loop through all costumes
			foreach($obj2->costumes as $costume)
			{
				// Check to see if costume matches database
				if($costume->costumeId == $db->costumeid)
				{
					// Set found variable
					$found = true;
				}
			}
			
			// If not found, this costume has been removed, update database to reflect changes
			if(!$found)
			{
				// Delete
				$conn->query("DELETE FROM 501st_costumes WHERE id = '".$db->id."'");
				
				// Increment to keep track
				$n++;
			}
		}
	}
}

echo '
Records Added: ' . $j . '
<br />
Records Updated ' . $u . '
<br />
Records Deleted:' . $n;

?>