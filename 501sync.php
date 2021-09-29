<?php

// Include config file
include "config.php";

// Get contents of file
$json = file_get_contents("https://www.501st.com/memberAPI/v3/garrisons/9/members");
$obj = json_decode($json);

// Keep track of records
$j = 0;
$l = 0;
$n = 0;

// Loop through all members
foreach($obj->unit->members as $value)
{
	// Check to see if this member exists in database, and if they do, check to see if we need to update anything
	$query = "SELECT * FROM 501st_troopers WHERE legionid = '".$value->legionId."'";
	
	// Start from zero
	$i = 0;

	// Run through query
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Check to see if information has changed
			
			// Name
			if($value->fullName != $db->name)
			{
				$conn->query("UPDATE 501st_troopers SET name = '".$value->fullName."' WHERE legionid = '".$db->legionid."'");
				
				// Increment to keep track
				$n++;
			}
			
			// Thumbnail
			if($value->thumbnail != $db->thumbnail)
			{
				$conn->query("UPDATE 501st_troopers SET thumbnail = '".$value->thumbnail."' WHERE legionid = '".$db->legionid."'");

				// Increment to keep track
				$n++;
			}
			
			// Link
			if($value->link != $db->link)
			{
				$conn->query("UPDATE 501st_troopers SET link = '".$value->link."' WHERE legionid = '".$db->legionid."'");
				
				// Increment to keep track
				$n++;
			}
			
			// Squad
			if(convertSquadId($value->squadId) != $db->squad)
			{
				$conn->query("UPDATE 501st_troopers SET squad = '".convertSquadId($value->squadId)."' WHERE legionid = '".$db->legionid."'");
				
				// Increment to keep track
				$n++;
			}
			
			// Increment
			$i++;
		}
	}
	
	// If no results
	if($i == 0)
	{
		$conn->query("INSERT INTO 501st_troopers (legionid, name, thumbnail, link, squad) VALUES ('".$value->legionId."', '".$value->fullName."', '".$value->thumbnail."', '".$value->link."', '".convertSquadId($value->squadId)."')");
		
		// Increment to keep track
		$j++;
	}
}

// Delete old members - loop through all members
$query = "SELECT * FROM 501_troopers";

// Run through query
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Set found var
		$found = false;
		
		// Loop through all members from JSON
		foreach($obj->unit->members as $value)
		{
			if($value->legionId == $db->legionid)
			{
				$found = true;
			}
		}
		
		// If not found...
		if(!$found)
		{
			$conn->query("DELETE FROM 501st_troopers WHERE legionid = '".$db->legionid."'");
			
			// Increment to keep track
			$l++;
		}
	}
}

$getNumOfTroopers = $conn->query("SELECT legionid FROM 501st_troopers");
$getNumOfTroopersFL = $conn->query("SELECT legionid FROM 501st_troopers WHERE squad = '0'");
$getNumOfTroopersEverglades = $conn->query("SELECT legionid FROM 501st_troopers WHERE squad = '1'");
$getNumOfTroopersMakaze = $conn->query("SELECT legionid FROM 501st_troopers WHERE squad = '2'");
$getNumOfTroopersParjai = $conn->query("SELECT legionid FROM 501st_troopers WHERE squad = '3'");
$getNumOfTroopersSquad7 = $conn->query("SELECT legionid FROM 501st_troopers WHERE squad = '4'");
$getNumOfTroopersTampa = $conn->query("SELECT legionid FROM 501st_troopers WHERE squad = '5'");


echo '
Total Members: ' . $getNumOfTroopers->num_rows . '
<br />
Total Members (No Squad): ' . $getNumOfTroopersFL->num_rows . '
<br />
Total Members (Everglades): ' . $getNumOfTroopersEverglades->num_rows . '
<br />
Total Members (Makaze): ' . $getNumOfTroopersMakaze->num_rows . '
<br />
Total Members (Parjai): ' . $getNumOfTroopersParjai->num_rows . '
<br />
Total Members (Squad 7): ' . $getNumOfTroopersSquad7->num_rows . '
<br />
Total Members (Tampa): ' . $getNumOfTroopersTampa->num_rows . '
<br /><br />
Records Added: ' . $j . '
<br />
Records Updated: ' . $n . '
<br />
Records Deleted: ' . $l . '';

// convertSquadId: Returns the squad's ID for troop tracker
function convertSquadId($value)
{
	$returnValue = 0;

	// Tampa Bay Squad
	if($value == 110)
	{
		$returnValue = 5;
	}
	// Squad 7 Squad
	else if($value == 136)
	{
		$returnValue = 4;
	}
	// Parjai Squad
	else if($value == 126)
	{
		$returnValue = 3;
	}
	// Makaze Squad
	else if($value == 124)
	{
		$returnValue = 2;
	}
	// Everglades Squad
	else if($value == 113)
	{
		$returnValue = 1;
	}

	return $returnValue;
}

?>