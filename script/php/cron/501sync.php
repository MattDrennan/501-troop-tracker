<?php

/**
 * This file is used for scraping 501st data.
 * 
 * This should be run weekly by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Check date time for sync
$query = "SELECT syncdate FROM settings";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Compare dates
		if(strtotime($db->syncdate) >= strtotime("-7 day"))
		{
			// Prevent script from continuing
			die("Already updated recently.");
		}
	}
}

// Reset databases
$conn->query("DELETE FROM 501st_troopers");
$conn->query("DELETE FROM 501st_costumes");

// Get contents of file
$json = file_get_contents("https://www.501st.com/memberAPI/v3/garrisons/9/members");
$obj = json_decode($json);

// Loop through all members
foreach($obj->unit->members as $value)
{
	try {
		// Get specific data on member
		$json2 = file_get_contents("https://www.501st.com/memberAPI/v3/legionId/" . $value->legionId);
		$obj2 = json_decode($json2);

		$conn->query("INSERT INTO 501st_troopers (legionid, name, thumbnail, link, squad, approved, status, standing, joindate) VALUES ('".$value->legionId."', '".$value->fullName."', '".$value->thumbnail."', '".$value->link."', '".convertSquadId($value->squadId)."', '".convertMemberApproved($obj2->memberApproved)."', '".convertMemberStatus($obj2->memberStatus)."', '".convertMemberStanding($obj2->memberStanding)."', '".$obj2->joinDate."')");
	} catch (Exception $ex) {
		die("Failed!");
	}
}

// Wait
sleep(10);

// Loop through all members
foreach($obj->unit->members as $value)
{
	try {
		// Get specific data on member - costumes
		$json2 = file_get_contents("https://www.501st.com/memberAPI/v3/legionId/" . $value->legionId . "/costumes");
		$obj2 = json_decode($json2);
		
		// Loop through all costumes
		foreach($obj2->costumes as $costume)
		{
			// Insert into database
			$conn->query("INSERT INTO 501st_costumes (legionid, costumeid, prefix, costumename, photo, thumbnail, bucketoff) VALUES ('".$value->legionId."', '".$costume->costumeId."', '".$costume->prefix."', '".$costume->costumeName."', '".$costume->photoURL."', '".$costume->thumbnail."', '".$costume->bucketOffPhoto."')");
		}
		
		// Wait
		sleep(5);
	} catch (Exception $ex) {
		die("Failed!");
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
<br />
COMPLETE!';

// Update date time for last sync
$conn->query("UPDATE settings SET syncdate = NOW()");

/**
 * Converts the member approval string value to an interger
 *
 * @param string $value The string value to be formatted
 * @return int Returns 1 for yes and 0 for all else
 */
function convertMemberApproved($value)
{
	$returnValue = 0;
	
	// Check member status
	if($value == "YES")
	{
		$returnValue = 1;
	}

	return $returnValue;
}

/**
 * Returns an interger based on the member status
 *
 * @param string $value The string value to be formatted
 * @return int Returns 1 for active, 2 for reserve, and 0 for all else
 */
function convertMemberStatus($value)
{
	$returnValue = 0;
	
	// Check member status
	if($value == "Active")
	{
		$returnValue = 1;
	}
	else if($value == "Reserve")
	{
		$returnValue = 2;
	}

	return $returnValue;
}

/**
 * Returns an interger based on the member standing
 *
 * @param string $value The string value to be formatted
 * @return int Returns 1 for good, and 0 for all else
 */
function convertMemberStanding($value)
{
	$returnValue = 0;
	
	// Check member standing
	if($value == "Good")
	{
		$returnValue = 1;
	}

	return $returnValue;
}

/**
 * Returns the squad's ID for troop tracker
 *
 * @param int $value The string value to be formatted
 * @return int Returns squad ID based on value
 */
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