<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// DB Info
$dbServer = "localhost";
$dbUser = "root";
$dbPassword = "root";
$dbName = "501";

// Connect to server
$conn = new mysqli($dbServer, $dbUser, $dbPassword, $dbName);
 
// Check connection to server
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

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

function getSquad($address)
{
	// Squad code
	$squad = 0;

	// Request
	$geocode = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&key=AIzaSyBXGfnJb3XBBfP7we-S65uPOWP7AFcUDWA");
    $output = json_decode($geocode);

    // Get Data
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

    return $squad;
}

// getSquadName: Returns the squad name
function getSquadName($value)
{
	$returnValue = "";

	if($value == 1)
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

// myEmail: gets users email
function myEmail()
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$_SESSION['id']."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->email;
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
		if($squad == $_GET['squad'])
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

// getStatus: gets status of trooper
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
	if($value == 3)
	{
		$returnValue = "Attended";
	}
	if($value == 4)
	{
		$returnValue = "Canceled";
	}
	if($value == 5)
	{
		$returnValue = "Pending";
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

// ifEmpty: Show empty
function ifEmpty($value)
{
	if($value == "")
	{
		return "EMPTY";
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

function isAdmin()
{
	global $conn;
	
	$isAdmin = false;
	
	$query = "SELECT * FROM troopers WHERE id='".$_SESSION['id']."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->permissions == 1)
			{
				$isAdmin = true;
			}
		}
		return $isAdmin;
	}
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
	/* Exception class. */
	require 'script/lib/phpmail/src/Exception.php';

	/* The main PHPMailer class. */
	require 'script/lib/phpmail/src/PHPMailer.php';

	/* SMTP class, needed if you want to use SMTP. */
	require 'script/lib/phpmail/src/SMTP.php';

	$mail = new PHPMailer(TRUE);

	/* Set the mail sender. */
	$mail->setFrom('matt@drennansoftware.com', 'Matthew Drennan');

	/* Add a recipient. */
	$mail->addAddress($SendTo, $Name);

	/* Tells PHPMailer to use SMTP. */
	$mail->isSMTP();

	/* SMTP server address. */
	$mail->Host = 'sub5.mail.dreamhost.com';

	/* Use SMTP authentication. */
	$mail->SMTPAuth = TRUE;

	/* Set the encryption system. */
	$mail->SMTPSecure = 'tls';

	/* SMTP authentication username. */
	$mail->Username = 'matt@drennansoftware.com';

	/* SMTP authentication password. */
	$mail->Password = 'PASSWORD';

	/* Set the SMTP port. */
	$mail->Port = 587;

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

// If logged in, update active status
if(loggedIn())
{
	$conn->query("UPDATE troopers SET last_active = NOW() WHERE id='".$_SESSION['id']."'") or die($conn->error);
}

?>
