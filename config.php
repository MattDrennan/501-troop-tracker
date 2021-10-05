<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Unlimited time to execute
ini_set('max_execution_time', '0');
set_time_limit(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* Exception class. */
require 'script/lib/phpmail/src/Exception.php';

/* The main PHPMailer class. */
require 'script/lib/phpmail/src/PHPMailer.php';

/* SMTP class, needed if you want to use SMTP. */
require 'script/lib/phpmail/src/SMTP.php';

// Include credential file
require 'cred.php';

session_start();

// Connect to server
$conn = new mysqli(dbServer, dbUser, dbPassword, dbName);
 
// Check connection to server
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

// addSquadLink: Returns a href link for a squad based on selection
function addSquadLink($squad, $match, $name)
{
	// Set up link
	$link = "";
	
	// If squad's don't match show link
	if($squad != $match)
	{
		$link = '<a href="index.php?action=trooptracker&squad='.$squad.'">'.$name.'</a>';
	}
	else
	{
		$link = $name;
	}
	
	// Return
	return $link;
}

// email_check: Checks if e-mail is verified
function email_check()
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$_SESSION['id']."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->email_verify == 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}
}

// getMyCostumes: A function which returns a string of costumes assigned to user in synced database
function getMyCostumes($id)
{
	global $conn;
	
	// Setup array
	$costume = "";
	
	// Get data
	$query = "SELECT costumename FROM 501st_costumes WHERE legionid = '".$id."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			$costume .= ", '" . $db->costumename . "'";
		}
	}
	
	// Return
	return $costume;
}

// showCostumes: A function which displays all the users costumes in synced database
function showCostumes($id)
{
	global $conn;
	
	// Get data
	$query = "SELECT * FROM 501st_costumes WHERE legionid = '".$id."'";
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			echo '
			<div style="text-align: center;">
				<h3>'.$db->costumename.'<h3>
				<p>';
					// Set up image count
					$iC = 0;
					
					// Check if image is available
					if(@getimagesize($db->photo)[0])
					{
						echo '
						<img src="'.$db->photo.'" />';
						
						// Increment
						$iC++;
					}
					
					// Check if image is available
					if(@getimagesize($db->bucketoff)[0])
					{
						echo '
						<img src="'.$db->bucketoff.'" />';
						
						// Increment
						$iC++;
					}
					
					// If no image available
					if($iC == 0)
					{
						echo '
						No images available for costume.';
					}
				echo '
				</p>
			</div>';
			
			// Increment
			$i++;
		}
	}
	
	// If no results
	if($i == 0)
	{
		echo '
		<p style="text-align: center;">
			No costumes to display!
		</p>';
	}
}

// getSquad: Gets squad by location
function getSquad($address)
{
	// Squad code
	$squad = 0;

	// Request
	$geocode = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&key=".googleKey."");
    $output = json_decode($geocode);

    // Get Data
    if(isset($output->results[0]->address_components[4]->long_name))
    {
    	$county = $output->results[0]->address_components[4]->long_name;

	    // Parjai
	    if($county == "Escambia County" || $county == "Santa Rosa" || $county == "Okaloosa County" || $county == "Walton County" || $county == "Holmes County" || $county == "Washington County" || $county == "Jackson County" || $county == "Calhoun County" || $county == "Bay County" || $county == "Gulf County" || $county == "Gadsen County" || $county == "Liberty County" || $county == "Leon County" || $county == "Wakulla County" || $county == "Franklin County")
	    {
	    	$squad = 3;
	    }

	    // Squad 7
	    else if($county == "Jefferson County" || $county == "Madison County" || $county == "Taylor County" || $county == "Hamilton County" || $county == "Suwannee County" || $county == "Lafayette County" || $county == "Dixie County" || $county == "Columbia County" || $county == "Gilchrist County" || $county == "Baker County" || $county == "Union County" || $county == "Bradford County" || $county == "Alachua County" || $county == "Levy County" || $county == "Nassau County" || $county == "Duval County" || $county == "Clay County" || $county == "St. Johns County" || $county == "Putnam County" || $county == "Flagler County" || $county == "Marion County")
	    {
	    	$squad = 4;
	    }

	    // Makaze
	    else if($county == "Volusia County" || $county == "Citrus County" || $county == "Lake County" || $county == "Seminole County" || $county == "Orange County" || $county == "Brevard County" || $county == "Osceola County" || $county == "Highlands County" || $county == "Okeechobee County" || $county == "Indian River County")
	    {
	    	$squad = 2;
	    }

	    // Tampa Bay
	    else if($county == "Charlotte County" || $county == "Lee County" || $county == "Desolo County" || $county == "Hardee County" || $county == "Sarasota County" || $county == "Manatee County" || $county == "Hillsborough County" || $county == "Polk County" || $county == "Pasco County" || $county == "Pinellas County" || $county == "Sumter County" || $county == "Hernando County")
	    {
	    	$squad = 5;
	    }

	    // Everglades
	    else if($county == "Hendry County" || $county == "Palm Beach County" || $county == "Broward County" || $county == "Collier County" || $county == "Monroe County" || $county == "Dade County" || $county == "Glades County" || $county == "Martin County" || $county == "St. Lucie County")
	    {
	    	$squad = 1;
	    }
	    else
	    {
	    	$squad = 2;
	    }
	}

    return $squad;
}

// getSquadName: Returns the squad name
function getSquadName($value)
{
	$returnValue = "";

	if($value == 0)
	{
		$returnValue = 'Florida Garrison';
	}
	else if($value == 1)
	{
		$returnValue = 'Everglades Squad';
	}
	else if($value == 2)
	{
		$returnValue = 'Makaze Squad';
	}
	else if($value == 3)
	{
		$returnValue = 'Parjai Squad';
	}
	else if($value == 4)
	{
		$returnValue = 'Squad 7';
	}
	else if($value == 5)
	{
		$returnValue = 'Tampa Bay Squad';
	}
	else if($value == 6)
	{
		$returnValue = 'Rebel Legion';
	}
	else if($value == 7)
	{
		$returnValue = 'Droid Builders';
	}
	else if($value == 8)
	{
		$returnValue = 'Mando Mercs';
	}
	else if($value == 9)
	{
		$returnValue = 'Other';
	}

	return $returnValue;
}

function isImportant($value, $text)
{
	if($value == 1)
	{
		return "<div style='color:red;'>".$text."</div>";
	}
	else
	{
		return $text;
	}
}

function loggedIn()
{
	if(isset($_SESSION['id']))
	{
		return true;
	}
	return false;
}

// sendNotification: Sends a notification to the log
function sendNotification($message, $trooperid)
{
	global $conn;
	
	$conn->query("INSERT INTO notifications (message, trooperid) VALUES ('".$message."', '".$trooperid."')");
}

// checkTroopCounts: Checks the troop counts, and puts the information into notifications
function checkTroopCounts($count, $message, $trooperid)
{
	global $conn;
	
	// Counts to check
	$counts = [1, 10, 25, 50, 75, 100, 150, 200, 250, 300, 400, 500, 501];
	
	// Search notifications for previous notifications, so we don't duplicate
	$query = "SELECT * FROM notifications WHERE trooperid='".$trooperid."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			foreach($counts as $value)
			{
				if(str_contains($db->message, $value))
				{
					// Find in array
					$pos = array_search($value, $counts);
					
					// Remove from array
					unset($counts[$pos]);
				}
			}
		}
	}
	
	// Loop through remaining counts to check
	foreach($counts as $value)
	{
		if($count >= $value)
		{
			// Replace [COUNT] with actual count
			$tempMessage = $message;
			$tempMessage = str_replace("[COUNT]", $value, $tempMessage);
			
			$conn->query("INSERT INTO notifications (message, trooperid) VALUES ('".cleanInput($tempMessage)."', '".cleanInput($trooperid)."')");
		}
	}
}

// myEmail: gets users email
function myEmail()
{
	global $conn;
	
	$query = "SELECT email FROM troopers WHERE id='".$_SESSION['id']."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->email;
		}
	}
}

// myTheme: gets users theme
function myTheme()
{
	global $conn;
	
	$query = "SELECT theme FROM troopers WHERE id='".$_SESSION['id']."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->theme;
		}
	}
}

// getEventTitle: gets event title
function getEventTitle($id)
{
	global $conn;
	
	$query = "SELECT * FROM events WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->name;
		}
	}
}

// Converts other club ID numbers to a readable format
function readTKNumber($tkid)
{
	// Format id for non members
	// Rebel
	if(substr($tkid, 0, 6) === '111111')
	{
		$tkid = substr($tkid, 6);
		$tkid = "R" . $tkid;
	}
	// Droid
	else if(substr($tkid, 0, 6) === '222222')
	{
		$tkid = substr($tkid, 6);
		$tkid = "D" . $tkid;
	}
	// Mandos
	else if(substr($tkid, 0, 6) === '333333')
	{
		$tkid = substr($tkid, 6);
		$tkid = "M" . $tkid;
	}
	// Other
	else if(substr($tkid, 0, 6) === '444444')
	{
		$tkid = substr($tkid, 6);
		$tkid = "O" . $tkid;
	}
	else
	{
		$tkid = "TK" . $tkid;
	}

	return $tkid;
}

// Returns if page is active
function isPageActive($page)
{
	if(isset($_GET['action']))
	{
		if($_GET['action'] == $page)
		{
			return 'class="active"';
		}
	}
	else
	{
		if($page == "home")
		{
			return 'class="active"';
		}
	}
}

// Returns if squad is active
function isSquadActive($squad)
{
	if(isset($_GET['squad']))
	{
		if($squad == $_GET['squad'] && $_GET['squad'] != "mytroops")
		{
			// Squad
			return 'class="squadlink"';
		}
	}
	else
	{
		// Whole state
		if($squad == 0)
		{
			return 'class="squadlink"';
		}
	}
}

// getTKNumber: gets TK number
function getTKNumber($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->tkid;
		}
	}
}

// getName: gets the user's name
function getName($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->name;
		}
	}
}

// getIdNumberFromTK: gets ID number from TK number
function getIDNumberFromTK($tk)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE tkid='".$tk."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->id;
		}
	}
}

// copyEvent: Helps with copying event values to create an event page
function copyEvent($eid, $value, $default = -1)
{
	// Check eid
	if($eid > 0)
	{
		// Return value if eid set
		return $value;
	}
	else
	{
		// Check if default value
		if($default > -1)
		{
			return $default;
		}
		else
		{
			// Return nothing if eid not set
			return '';
		}
	}
}

// copyEventSelect: Helps with copying event values to create an event page - this function selects from select list
function copyEventSelect($eid, $value, $value2, $default = -1)
{
	// Check eid
	if($eid > 0)
	{
		// Check if value is NULL
		if($value === NULL)
		{
			// If null compare values
			if($value == NULL && $value != 0 && $value2 == "null")
			{
				return 'SELECTED';
			}
			else if($default > -1)
			{			
				if($value2 == $default)
				{
					return 'SELECTED';
				}
			}
		}
		else
		{
			// Checks if this is the select option
			if($value == $value2)
			{
				// Return value if eid set
				return 'SELECTED';
			}
			// If both values null, no data
			else if($value == "" && $value2 == "null")
			{
				return 'SELECTED';
			}
		}
	}
	else
	{
		if($default > -1)
		{			
			if($value2 == $default)
			{
				return 'SELECTED';
			}
		}
		else
		{
			// Return nothing if eid not set and not a null value
			return '';
		}
	}
}

// If the user ID is assigned to an event
function inEvent($id, $event)
{
	global $conn;

	$array = ["inTroop" => "0", "status" => ""];
	$status = "";
	
	$query = "SELECT * FROM event_sign_up WHERE trooperid = '".$id."' AND troopid = '".$event."'";
	$i = 0;
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			$i++;
			$status = $db->status;
		}
	}

	// If in an event
	if($i > 0)
	{
		$array = ["inTroop" => "1", "status" => $status];
	}

	return $array;
}

// getStatus: gets status of trooper - 0 = Going, 1 = Stand by, 2 = Tentative, 3 = Attended, 4 = Canceled, 5 = Pending, 6 = Not Picked
function getStatus($value)
{
	$returnValue = "";

	if($value == 0)
	{
		$returnValue = "Going";
	}
	else if($value == 1)
	{
		$returnValue = "Stand By";
	}
	else if($value == 2)
	{
		$returnValue = "Tentative";
	}
	else if($value == 3)
	{
		$returnValue = "Attended";
	}
	else if($value == 4)
	{
		$returnValue = "Canceled";
	}
	else if($value == 5)
	{
		$returnValue = "Pending";
	}
	else if($value == 6)
	{
		$returnValue = "Not Picked";
	}

	return $returnValue;
}

// getDatesFromRange: Get date ranges
function getDatesFromRange($start, $end, $format = 'M-d-Y')
{
    $array = array();
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach($period as $date) { 
        $array[] = $date->format($format); 
    }

    return $array;
}

// validate_url: Check if URL is valid
function validate_url($url)
{
	$path = parse_url($url, PHP_URL_PATH);
	$encoded_path = array_map('urlencode', explode('/', $path));
	$url = str_replace($path, implode('/', $encoded_path), $url);

	if(filter_var(addHttp($url), FILTER_VALIDATE_URL) && strpos($url, "."))
	{
		return '<a href="'.addHttp($url).'" target="_blank">'.$url.'</a>';
		
	}
	else
	{
		return 'No website available.';
	}
}

// ifEmpty: Show empty - if no value, show message. Default is EMPTY
function ifEmpty($value, $message = "EMPTY")
{
	if($value == "")
	{
		return $message;
	}
	else
	{
		return $value;
	}
}

// didAttend: Did the trooper attend?
function didAttend($value)
{
	$returnValue = "";

	if($value == 0)
	{
		$returnValue = "Did not attend";
	}
	else if($value == 1)
	{
		$returnValue = "Attended";
	}

	return $returnValue;
}

// getCostume: What was the costume?
function getCostume($value)
{
	global $conn;
	
	$query = "SELECT * FROM costumes WHERE id='".$value."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->costume;
		}
	}
}

// echoSelect: Selects the users set value
function echoSelect($value1, $value2)
{
	$returnValue = "";

	if($value1 == $value2)
	{
		$returnValue = "SELECTED";
	}

	return $returnValue;
}

// yesNo: Display yes or no
function yesNo($value)
{
	$returnValue = "";

	if($value == 0)
	{
		$returnValue = "No";
	}
	else
	{
		$returnValue = "Yes";
	}

	return $returnValue;
}

// addHttp: Adds http if does not exist
function addHttp($url)
{
	if (!preg_match("~^(?:f|ht)tps?://~i", $url))
	{
		$url = "http://" . $url;
	}
	return $url;
}

// isAdmin: Is the user an admin or squad leader?
function isAdmin()
{
	global $conn;
	
	$isAdmin = false;
	
	if(isset($_SESSION['id']))
	{
		$query = "SELECT * FROM troopers WHERE id='".$_SESSION['id']."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($db->permissions == 1 || $db->permissions == 2)
				{
					$isAdmin = true;
				}
			}
		}
	}
	
	return $isAdmin;
}

// hasPermission: Does the user have permission to access the data?
// 0 = 501st Member, 1 = Super Admin, 2 = Squad Leader, 3 = Reserve Member, 4 = Retired Member
function hasPermission($permissionLevel1, $permissionLevel2 = -1, $permissionLevel3 = -1, $permissionLevel4 = -1)
{
	global $conn;
	
	$isAllowed = false;
	
	if(isset($_SESSION['id']))
	{
		$query = "SELECT * FROM troopers WHERE id='".$_SESSION['id']."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($db->permissions == $permissionLevel1)
				{
					$isAllowed = true;
				}
				
				if($db->permissions == $permissionLevel2)
				{
					$isAllowed = true;
				}
				
				if($db->permissions == $permissionLevel3)
				{
					$isAllowed = true;
				}
				
				if($db->permissions == $permissionLevel4)
				{
					$isAllowed = true;
				}
			}
		}
	}
	
	return $isAllowed;
}

// isWebsiteClosed: Is the website closed?
function isWebsiteClosed()
{
	global $conn;
	
	$isWebsiteClosed = false;
	
	$query = "SELECT * FROM settings LIMIT 1";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->siteclosed)
			{
				$isWebsiteClosed = true;
			}
		}
	}
	
	return $isWebsiteClosed;
}

// isSignUpClosed: Are the website sign ups closed?
function isSignUpClosed()
{
	global $conn;
	
	$isWebsiteClosed = false;
	
	$query = "SELECT * FROM settings LIMIT 1";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->signupclosed)
			{
				$isWebsiteClosed = true;
			}
		}
	}
	
	return $isWebsiteClosed;
}

// Does the TK ID exist?
function doesTKExist($tk)
{
	global $conn;
	
	$exist = false;
	
	$query = "SELECT * FROM troopers WHERE tkid='".$tk."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			$exist = true;
		}
	}

	return $exist;
}

// Is the TK ID registered?
function isTKRegistered($tk)
{
	global $conn;
	
	$registered = false;
	
	$query = "SELECT * FROM troopers WHERE tkid='".$tk."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->password != '')
			{
				$registered = true;
			}
		}
	}

	return $registered;
}

// Prevent hacks
function cleanInput($value)
{
	$value = strip_tags(addslashes($value));
	return $value;
}

function sendEmail($SendTo, $Name, $Subject, $Message)
{
	// MAIL
	$mail = new PHPMailer(TRUE);

	/* Set the mail sender. */
	$mail->setFrom(emailUser, 'Troop Tracker');

	/* Add a recipient. */
	$mail->addAddress($SendTo, $Name);

	/* Tells PHPMailer to use SMTP. */
	$mail->isSMTP();

	/* SMTP server address. */
	$mail->Host = emailServer;

	/* Use SMTP authentication. */
	$mail->SMTPAuth = TRUE;

	/* Set the encryption system. */
	$mail->SMTPSecure = 'tls';

	/* SMTP authentication username. */
	$mail->Username = emailUser;

	/* SMTP authentication password. */
	$mail->Password = emailPassword;

	/* Set the SMTP port. */
	$mail->Port = emailPort;

	/* Set the subject. */
	$mail->Subject = $Subject;

	/* Set the mail message body. */
	$mail->Body = $Message;

	/* Finally send the mail. */
	if (!$mail->send())
	{
	   /* PHPMailer error. */
	   echo $mail->ErrorInfo;
	}
	// END MAIL
}

// getEra: what is the era?
function getEra($number)
{
	// Return value
	$text = "";
	
	if($number == 0)
	{
		$text = "Prequel";
	}
	else if($number == 1)
	{
		$text = "Original";
	}
	else if($number == 2)
	{
		$text = "Sequel";
	}
	else if($number == 3)
	{
		$text = "Expanded";
	}
	else if($number == 4)
	{
		$text = "All";
	}
	
	// Return
	return $text;
}

// convertNumber: convert number to unlimited if 500
function convertNumber($number, $total)
{
	// Number is high enough return unlimited and if total is less than unlimited
	if($number == 500 && $total == 500)
	{
		$number = "unlimited";
	}
	
	// If total troopers allowed is set less than other trooper counts
	if($total < $number)
	{
		$number = $total;
	}
	
	// Return
	return $number;
}

// eraCheck: Check to see if the event is limited to certain costumes
function eraCheck($eventID, $costumeID)
{
	global $conn;

	// Variables
	$eventFail = false;	// Is this costume allowed?

	// Query database for event info
	$query = "SELECT * FROM events WHERE id = '".$eventID."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Query costume database to get information on the users costume
			$query4 = "SELECT * FROM costumes WHERE id = '".$costumeID."'";
			if ($result4 = mysqli_query($conn, $query4))
			{
				while ($db4 = mysqli_fetch_object($result4))
				{
					// Make sure event and costume era isn't set to "All" and check if the era and limited to match
					if($db->limitTo != 4 && $db4->era != 4 && $db->limitTo != $db4->era)
					{
						// Did not fail
						$eventFail = true;
					}
				}
			}
		}
	}

	// Return
	return $eventFail;
}

// isEventFull: Check to see if the event is full ($eventID = ID of the event, $costumeID = costume they are going to wear)
function isEventFull($eventID, $costumeID)
{
	global $conn;

	// Variables
	$i = 0;	// 501st
	$rl = 0;	// Rebel Legion
	$droidb = 0;	// Droid builders
	$mandos = 0;	// Mandos
	$other = 0;	// Others
	$total = 0; // Total count
	$eventFull = false;	// Is the event full?

	// Query database for event info
	$query = "SELECT * FROM events WHERE id = '".$eventID."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Query database for roster info
			$query2 = "SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.reason, event_sign_up.attend, event_sign_up.attended_costume, event_sign_up.status, event_sign_up.troopid, troopers.id AS trooperId, troopers.name, troopers.tkid FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".$eventID."' AND status != '4'";

			if ($result2 = mysqli_query($conn, $query2))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// Query costume database to add to club counts
					$query4 = "SELECT * FROM costumes WHERE id = '".$db2->costume."'";
					if ($result4 = mysqli_query($conn, $query4))
					{
						while ($db4 = mysqli_fetch_object($result4))
						{
							// 501st
							if($db4->club == 0)
							{
								$i++;
								$total++;
							}
							// Rebel Legion
							else if($db4->club == 1)
							{
								$rl++;
								$total++;
							}
							// Mandos
							else if($db4->club == 2)
							{
								$mandos++;
								$total++;
							}
							// Droid Builders
							else if($db4->club == 3)
							{
								$droidb++;
								$total++;
							}
							// Rebel + 501st
							else if($db4->club == 4)
							{
								$i++;
								$rl++;
								$total++;
							}
							// Other
							else if($db4->club == 5)
							{
								$other++;
								$total++;
							}
							// All
							else if($db4->club == 6)
							{
								$i++;
								$rl++;
								$mandos++;
								$droidb++;
								$other++;
								$total++;
							}							
						}
					}
				}
			}

			// Final checks
			// Query costume database to get information on the users costume
			$query4 = "SELECT * FROM costumes WHERE id = '".$costumeID."'";
			if ($result4 = mysqli_query($conn, $query4))
			{
				while ($db4 = mysqli_fetch_object($result4))
				{
					// 501st
					if($db4->club == 0)
					{
						if(($i + 1) > $db->limit501st)
						{
							$eventFull = true;
						}
					}
					// Rebel Legion
					else if($db4->club == 1)
					{
						if(($rl + 1) > $db->limitRebels)
						{
							$eventFull = true;
						}
					}
					// Droid Builders
					else if($db4->club == 2)
					{
						if(($droidb + 1) > $db->limitDroid)
						{
							$eventFull = true;
						}
					}
					// Mandos
					else if($db4->club == 3)
					{
						if(($mandos + 1) > $db->limitMando)
						{
							$eventFull = true;
						}
					}
					// Other
					else if(($other + 1) > ($db->limit501st + $db->limitRebels + $db->limitDroid + $db->limitMando))
					{
						$eventFull = true;
					}							
				}
			}
			
			// Total trooper count check
			if($total >= $db->limitTotal)
			{
				$eventFull = true;
			}
		}
	}

	// Return
	return $eventFull;
}

// If logged in, update active status
if(loggedIn())
{
	$conn->query("UPDATE troopers SET last_active = NOW() WHERE id='".$_SESSION['id']."'") or die($conn->error);
}

// Check for events that need to be closed
$query = "SELECT * FROM events WHERE dateEnd < NOW() - INTERVAL 1 DAY and closed = '0'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Close them
		$conn->query("UPDATE events SET closed = '1' WHERE id = '".$db->id."'");
	}
}

?>
