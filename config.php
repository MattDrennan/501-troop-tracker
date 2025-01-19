<?php

/**
 * This file is used for configuration and loading functions.
 *
 * @author  Matthew Drennan
 *
 */

// Set time zone
date_default_timezone_set("America/New_York");

// Unlimited time to execute
ini_set('max_execution_time', '0');
set_time_limit(0);

// PHP Mail namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Calendar links - namespace
use Spatie\CalendarLinks\Link;

// Composer Autoload
require 'vendor/autoload.php';

// Calendar Namespace
use benhall14\phpCalendar\Calendar;

// Start Calendar
$calendar = new Calendar();

// Include credential file
require 'cred.php';

// Include smileys
require 'script/php/smiley.php';

// Start session
session_start();

// Connect to server
$conn = new mysqli(dbServer, dbUser, dbPassword, dbName);
 
// Check connection to server
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

/**
 * This variable is used to put these costumes first in a query
 * 
 * @var string
*/
$mainCostumes = "'N/A', 'Command Staff', 'Handler'";

/**
 * This is used to format the time to Eastern Standard Time
 * 
 * @param string $date This is the format the date should be displayed in
 * @param string $format This is the date to be formatted
 * @return string Returns date and Eastern Standard Time
*/
function formatTime($date, $format)
{
	$datetime = new DateTime($date, new DateTimeZone('UTC'));
	$datetime->setTimezone(new DateTimeZone('America/New_York'));
	return $datetime->format($format);
}

/**
 * A collection of random tip strings that will be returned to user at random
 * 
 * @return string Returns a random tip string
*/
function dailyTip()
{
	// Prevent on certain pages
	if(@$_GET['action'] == "login" || @$_GET['action'] == "logout") { return; }

	// Get a random number
	$randomNumber = rand(0, 15);

	// Set tip
	$tip = '';

	// Set link
	$link = '';

	// Give tip based on number
	switch($randomNumber)
	{
		case 0:
			$tip = 'Did you know you could upload photos and share them with other troopers?';
			$link = 'https://youtu.be/aODHyWMMVUQ';
		break;

		case 1:
			$tip = 'Did you know you could acheive milestone awards and show them off on your profile?';
			$link = 'https://youtu.be/W-wcceu6xzI';
		break;

		case 2:
			$tip = 'Did you know you could add a friend to a troop, without them logging in?';
			$link = 'https://youtu.be/C0WCxIRZafQ';
		break;

		case 3:
			$tip = 'Did you know you could change the theme of the troop tracker?';
			$link = 'https://youtu.be/IPykBoeDGcg';
		break;

		case 4:
			$tip = 'Did you know you could search past troops and search for troop counts between a time range?';
			$link = 'https://youtu.be/-pXqGZLiVpM';
		break;

		case 5:
			$tip = 'Did you know you could see all troops that have occured in the past?';
			$link = 'https://youtu.be/17UPK4AoKxg';
		break;

		case 6:
			$tip = 'Did you know you could sort troops by squad on the homepage?';
			$link = 'https://youtu.be/H-nnM5jndZA';
		break;

		case 7:
			$tip = 'Did you know you can view the troops you\'re signed up for?';
			$link = 'https://youtu.be/Rn3EnhudHyc';
		break;

		case 8:
			$tip = 'Did you know you there is a calendar view?';
			$link = 'https://youtu.be/02ERoFw7XlY';
		break;

		case 9:
			$tip = 'Did you know you can sort troops by the troop name?';
			$link = 'https://youtu.be/y_I8ssRjek8';
		break;

		case 10:
			$tip = 'Did you know you can subscribe for event updates, and it will send e-mails when troopers sign up, cancel, or post comments?';
			$link = 'https://youtu.be/5pp7_FKg7cI';
		break;

		case 11:
			$tip = 'Did you know you can add events to your Google, Apple, or other calendar?';
			$link = 'https://youtu.be/cefnojYUy-Y';
		break;

		case 12:
			$tip = 'Did you know you can post comments or have a discussion on the troop tracker?';
			$link = 'https://youtu.be/tS-bCXbCzs4';
		break;

		case 13:
			$tip = 'Did you know you can type when selecting a costume to find it easier?';
			$link = 'https://youtu.be/YLjiVGgqe-Y';
		
		case 14:
			$tip = 'Did you know you can add someone to a troop that is not a member and does not have tracker access?';
			$link = 'https://www.youtube.com/watch?v=mDeJaANqLIk';
		break;

		default:
			$tip = 'Did you know you can add someone to the tracker that does not have an account?';
			$link = 'https://www.youtube.com/watch?v=mDeJaANqLIk';
		break;
	}

	// If link set
	if($link != "")
	{
		return '<span style="text-align: center; display: block;"><a href="'.$link.'" target="_blank" style="color: #54f77d;"><b>TIP:</b> ' . $tip . '</a></span>';
	}
	else
	{
		return '<span style="text-align: center; color: #54f77d; display: block;"><b>TIP:</b> ' . $tip . '</span>';
	}
}

/**
 * Returns an HTML string of links to add an event to calendar
 * 
 * @param string $name Name of the event
 * @param string $location Location of the event
 * @param string $description Description of the event
 * @param string $date1 Start date of the event
 * @param string $date2 End date of the event
 * @return string Returns an HTML string to display to user
*/
function showCalendarLinks($name, $location, $description, $date1, $date2)
{
	// Convert dates
	$date1 = date('Y-m-d H:i', strtotime($date1));
	$date2 = date('Y-m-d H:i', strtotime($date2));
	
	// Calendar links - from and to dates
	$from = DateTime::createFromFormat('Y-m-d H:i', $date1);
	$to = DateTime::createFromFormat('Y-m-d H:i', $date2);

	// Create link
	$link = Link::create($name, $from, $to)->description($description)->address($location);
	
	// Show link
	return '
	<p class="calendar-links">
		<b>Add to calendar:</b>
		<br />
		<a href="'.$link->google().'" target="_blank"><img src="images/google.png" alt="Google Calendar" /></a> <a href="'.$link->yahoo().'" target="_blank"><img src="images/yahoo.png" alt="Yahoo Calendar" /></a> <a href="'.$link->webOutlook().'" target="_blank"><img src="images/outlook.png" alt="Outlook Calendar" /></a> <a href="'.$link->ics().'" target="_blank"><img src="images/ics.png" alt="ICS Calendar" /></a>
	</p>';
}

/**
 * Returns total troop counts for each club of the defined trooper, as well as favorite costume and money raised
 * 
 * @param int $id The trooper to get troop counts for
 * @return string Returns an HTML string to display the information to trooper
*/
function getTroopCounts($id)
{
	global $conn, $dualCostume, $clubArray, $squadArray;

	// Set up string
	$troopCountString = "";

	// Get troop counts - All - 1 Year
	$statement = $conn->prepare("SELECT event_sign_up.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND event_sign_up.status = '3' AND event_sign_up.trooperid = ? and events.dateStart > NOW() - INTERVAL 1 YEAR GROUP BY events.id, event_sign_up.id");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->store_result();
	$countAll = $statement->num_rows;

	// Get troop counts - 501st
	$statement = $conn->prepare("SELECT event_sign_up.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND event_sign_up.status = '3' AND event_sign_up.trooperid = ? AND ".getCostumeQueryValuesSquad(1)." GROUP BY events.id, event_sign_up.id");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->store_result();
	$count = $statement->num_rows;

	// Add to string
	$troopCountString .= '
	<p><b>501st Troops:</b> '.number_format($count).'</p>';

	// Set up Squad ID
	$clubID = count($squadArray) + 1;
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Count query
		$statement = $conn->prepare("SELECT event_sign_up.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND event_sign_up.status = '3' AND event_sign_up.trooperid = ? AND ".getCostumeQueryValues($clubID)." GROUP BY events.id, event_sign_up.id");
		$statement->bind_param("i", $id);
		$statement->execute();
		$statement->store_result();
		$count = $statement->num_rows;

		// Add to string
		$troopCountString .= '
		<p><b>'.$club_value['name'].' Troops:</b> '.number_format($count).'</p>';

		// Increment club ID
		$clubID++;
	}

	// Get total count
	$statement = $conn->prepare("SELECT id FROM event_sign_up WHERE trooperid = ? AND status = '3'");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->store_result();
	$count_total = $statement->num_rows;

	// Get favorite costume
	$statement = $conn->prepare("SELECT costume, COUNT(*) FROM event_sign_up WHERE trooperid = ? AND costume != 706 AND costume != 720 AND costume != 721 GROUP BY costume ORDER BY COUNT(costume) DESC LIMIT 1");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($favoriteCostume, $favoriteCostumeCount);
	$statement->fetch();
	$statement->close();

	// Get total money raised
	$statement = $conn->prepare("SELECT SUM(charityDirectFunds) FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($charityDirectFunds);
	$statement->fetch();
	$statement->close();

	$statement = $conn->prepare("SELECT SUM(charityIndirectFunds) FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($charityIndirectFunds);
	$statement->fetch();
	$statement->close();

	$statement = $conn->prepare("SELECT SUM(TIMESTAMPDIFF(HOUR, events.dateStart, events.dateEnd) + events.charityAddHours) FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($chairtyHours);
	$statement->fetch();
	$statement->close();

	// Prevent notice error
	if($favoriteCostume == "")
	{
		$favoriteCostume['costume'] = 0;
	}

	// Add to string
	$troopCountString .= '
	<p><b>Total Finished Troops:</b> ' . number_format($count_total) . '</p>
	<p><b>Total Troops Last 365 Days:</b> '.number_format($countAll).'</p>
	<p><b>Favorite Costume:</b> '.ifEmpty(getCostume($favoriteCostume), "N/A").'</p>
	<p><b>Volunteer Hours:</b> '.number_format($chairtyHours).'</p>
	<p><b>Direct Donations Raised:</b> $'.number_format($charityDirectFunds).'</p>
	<p><b>Indirect Donations Raised:</b> $'.number_format($charityIndirectFunds).'</p>';

	// Return
	return $troopCountString;
}

/**
 * Returns the category ID in the forum, based on the label
 * 
 * @param int $label The label category for the event
 * @param int $squad The chosen squad territory for the event
 * @return int Returns the forum category ID
*/
function labelToForumCategory($label, $squad) {
	global $virtualTroop, $conventionTroop, $disneyTroop, $squadArray;

	switch($label) {
		case 3:
			return $disneyTroop;
		break;

		case 4:
			return $conventionTroop;
		break;

		case 7:
			return $virtualTroop;
		break;

		case 11:
			return $lflTroop;
		break;

		default:
			return $squadArray[intval($squad - 1)]['eventForum'];
		break;
	}
}

/**
 * Returns the category ID in the forum, based on the label
 * 
 * @param int $label The label category for the event
 * @param int $squad The chosen squad territory for the event
 * @return int Returns the forum category ID
*/
function labelToForumCategoryArchive($label, $squad) {
	global $virtualTroop, $conventionTroop, $disneyTroop, $squadArray;

	switch($label) {
		case 3:
			return $disneyTroopArchive;
		break;

		case 4:
			return $conventionTroopArchive;
		break;

		case 7:
			return $virtualTroopArchive;
		break;

		case 11:
			return $lflTroopArchive;
		break;

		default:
			return $squadArray[intval($squad - 1)]['eventForumArchive'];
		break;
	}
}

/**
 * Returns garrison and squad images to display on the front page. A trooper can click images to see events for that squad.
 * 
 * @return string Returns an HTML string to display to trooper
*/
function showSquadButtons()
{
	global $squadArray;
	
	// Return var
	$returnVar = '';
	
	// Set count
	$squadID = 1;
	
	// Set up garrison link
	$returnVar .= '<a href="index.php"><img src="images/'.garrisonImage.'" alt="'.garrison.' Troops" '.isSquadActive(0).' /></a>';
	
	// Loop through squads
	foreach($squadArray as $squad => $squad_value)
	{
		// Add to return var
		$returnVar .= '
		<a href="index.php?squad='.$squadID.'"><img src="images/'.$squad_value['logo'].'" alt="'.$squad_value['name'].' Troops" '.isSquadActive($squadID).' /></a>';
		
		// Increment
		$squadID++;
	}
	
	return $returnVar;
}

/**
 * Returns squads and clubs, and converts them to options to display back to trooper.
 * 
 * @param boolean $clubs Optional. This is used to hide/show clubs in the select
 * @param string $insideElement Optional. Leave blank for a plain select, copy to for the copyEventSelect method, or select to set a selected option.
 * @param int $eid Optional. This is the event connected to the select.
 * @param int $squadP Optional. This is squad connected to the event, that will set a default option to select.
 * @param string $rebelOnly Optional. This will stop at the first club in the list, and not display the others.
 * @return string Returns an HTML string containing select and option elements
*/
function squadSelectList($clubs = true, $insideElement = "", $eid = 0, $squadP = 0, $rebelOnly = false)
{
	global $squadArray, $clubArray;
	
	// Set count
	$squadID = 1;
	
	// Return var
	$returnVar = '';
	
	// Loop through squads
	foreach($squadArray as $squad => $squad_value)
	{
		// If insideElement is nothing
		if($insideElement == "")
		{
			// Add to return var
			$returnVar .= '
			<option value="'.$squadID.'">'.$squad_value['name'].'</option>';
		}
		// If insideElement is copy
		else if($insideElement == "copy")
		{
			// Add to return var
			$returnVar .= '
			<option value="'.$squadID.'" '.copyEventSelect($eid, $squadP, $squadID).'>'.$squad_value['name'].'</option>';
		}
		// If insideElement is select
		else if($insideElement == "select")
		{
			// Add to return var
			$returnVar .= '
			<option value="'.$squadID.'" '.echoSelect($squadID, cleanInput($_POST['squad'])).'>'.$squad_value['name'].'</option>';
		}
		
		// Increment
		$squadID++;
	}
	
	// If clubs set to true, show clubs
	if($clubs)
	{
		// Loop through clubs
		foreach($clubArray as $squad => $squad_value)
		{
			// If insideElement is nothing
			if($insideElement == "")
			{
				// Add to return var
				$returnVar .= '
				<option value="'.$squadID.'">'.$squad_value['name'].'</option>';
			}
			// If insideElement is copy
			else if($insideElement == "copy")
			{
				// Add to return var
				$returnVar .= '
				<option value="'.$squadID.'" '.copyEventSelect($eid, $squadP, $squadID).'>'.$squad_value['name'].'</option>';
			}
			// If insideElement is select
			else if($insideElement == "select")
			{
				// Add to return var
				$returnVar .= '
				<option value="'.$squadID.'" '.echoSelect($squadID, cleanInput($_POST['squad'])).'>'.$squad_value['name'].'</option>';
			}

			// Stop at Rebels
			if($rebelOnly)
			{
				break;
			}
			
			// Increment
			$squadID++;
		}
	}
	
	return $returnVar;
}

/**
 * Returns an HTML results of pending troops
 * 
 * @param int $trooperid The trooper ID of the trooper to get pending troops
 * @return string Returns an HTML results of pending troops
*/
function pendingTroopsDisplay($trooperid)
{
	global $conn;

	// Set up return string
	$returnString = "";

	// Set up increment
	$i = 0;

	// Get data
	$statement = $conn->prepare("SELECT events.squad AS eventSquad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.dateStart, events.dateEnd, troopers.id, troopers.name FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopers.id = ? AND troopers.id != ".placeholder." AND events.closed = '0' AND event_sign_up.status = '0' ORDER BY events.dateEnd ASC");
	$statement->bind_param("i", $trooperid);
	$statement->execute();
	
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Format date
			$dateFormat = date('m-d-Y', strtotime($db->dateEnd));

			if($i == 0)
			{
				$returnString .= '
				<h2 class="tm-section-header" id="upcoming-troops-header">Upcoming Troops</h2>
				<table border="1" class="space-content">
				<tr>
					<th>Event Name</th>	<th>Date</th>	<th>Pending Costume</th>
				</tr>';
			}

			$returnString .= '
			<tr>
				<td>

				'.getSquadLogo($db->eventSquad).'

				<a href="index.php?event='.$db->eventId.'">'.$db->eventName.'</a></td>	<td>'.$dateFormat.'</td>	<td>'.ifEmpty('<a href="index.php?action=costume&costumeid='.$db->costume.'">' . getCostume($db->costume) . '</a>', "N/A").'

				</td>
			</tr>';

			// Increment
			$i++;
		}
	}

	// If results
	if($i > 0)
	{
		$returnString .= '
		</table>
		<br />';
	}

	return $returnString;
}

/**
 * Restricts the trooper's costume based on there membership to certain clubs
 * 
 * @param boolean $addWhere Optional. This is used to add a "where" to the MySQL query.
 * @param int $friendID Optional. This is used to determine which costumes to display. If this interval does not match the session interval, then all costumes will display.
 * @param boolean $allowDualCostume Optional. This is used to disable/enable allowing dual costumes to show.
 * @return string Returns a query to restrict costumes of clubs a trooper is not a member of
*/
function costume_restrict_query($addWhere = false, $friendID = 0, $allowDualCostume = true)
{
	global $conn, $clubArray, $dualCostume;
	
	// Set up query
	$returnQuery = " ";
	
	// Should add where?
	if($addWhere)
	{
		$returnQuery .= "WHERE ";
	}
	
	$returnQuery .= "(";

	// Set up query to check add a friend
	$friendQuery = "";

	// Check if friend ID
	if($friendID != $_SESSION['id'] && $friendID != 0)
	{
		$friendQuery = " OR (costumes.club >= 0) AND (costumes.club NOT IN (".implode(",", $dualCostume)."))";
	}

	// 501 member, prepare to add or statement if a dual member
	$hit = false;
	
	$statement = $conn->prepare("SELECT * FROM troopers WHERE id = ?");
	$statement->bind_param("i", $_SESSION['id']);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// 501
			if($db->p501 == 1 || $db->p501 == 2 || $db->p501 == 4)
			{
				$returnQuery .= "costumes.club = 0";

				// 501 member
				$hit = true;
			}

			// Set up step count
			$i = 0;

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Check club member status
				if($db->{$club_value['db']} == 1 || $db->{$club_value['db']} == 2 || $db->{$club_value['db']} == 4)
				{
					// First step and a 501 member, add the OR to prevent issues
					if($i == 0 && $hit)
					{
						$returnQuery .= " OR ";
					}

					foreach($club_value['costumes'] as $costume)
					{
						if(!$allowDualCostume && in_array($costume, $dualCostume))
						{
							continue;
						}
						
						// Passed first step, keep adding OR
						if($i > 0)
						{
							$returnQuery .= " OR ";
						}

						$returnQuery .= "costumes.club = ".$costume."";

						// Increment step
						$i++;
					}
				}
			}
		}
	}
	
	$returnQuery .= ")";
	
	return $returnQuery . $friendQuery;
}

/**
 * This is used to display all the smiley's in HTML from smiley.php
 * 
 * @return string Returns a string of all the smiley's to be displayed to trooper
*/
function smileyEditor()
{
	global $replacements;

	// Set up return variable
	$returnVar = "";

	// Set up loop variable
	$i = 0;

	foreach($replacements as $smiley => $smiley_value)
	{
		$returnVar .= $smiley_value . ' ';
	}

	return $returnVar;
}

/**
 * Converts text to BB Code
 * 
 * @param string $text The text to convert to BB Code and smilies
 * @return string Returns a new string that can display BB Code and smilies
*/
function showBBcodes($text)
{
	global $replacements;

	$text = strip_tags($text);

	// BBcode array
	$find = array(
		'~\[b\](.*?)\[/b\]~s',
		'~\[i\](.*?)\[/i\]~s',
		'~\[u\](.*?)\[/u\]~s',
		'~\[QUOTE=(.*?)\](.*?)\[/QUOTE\]~s',
		'~\[quotec trooperid=(.*?) name=(.*?) tkid=(.*?) commentid=(.*?)\](.*?)\[/quotec\]~s',
		'~\[size=(.*?)\](.*?)\[/size\]~s',
		'~\[color=(.*?)\](.*?)\[/color\]~s',
		'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
		'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s'
	);

	// HTML tags to replace BBcode
	$replace = array(
		'<b>$1</b>',
		'<i>$1</i>',
		'<span style="text-decoration:underline;">$1</span>',
		'<pre>$2</pre><br />',
		'<a href="#comment_$4">$2 - $3</a><br /><span class="quotec">$5</'.'span><br />',
		'<span style="font-size:$1px;">$2</span>',
		'<span style="color:$1;">$2</span>',
		'<a href="$1">$1</a>',
		'<img src="$1" alt="" />'
	);

	// Replacing the BBcodes with corresponding HTML tags
	$text = preg_replace($find, $replace, $text);

	return str_replace(array_keys($replacements), $replacements, $text);
}

/**
 * Draws a support badge if the user is a supporter
 * 
 * @param int $id The ID of the trooper to determine if they are a supporter
 * @return string Returns an HTML image string, displaying the suppoer badge
*/
function drawSupportBadge($id) {
	global $conn;
	
	// Set up value
	$value = "";
	
	if(isSupporter($id)) {
		$value = '<img src="images/FLGHeart_small.png" width="32px" height="32px" /><br />';
	}
	
	// Return
	return $value;
}

/**
 * Draws a visual graph for troopers to see what we need to support the garrison
 * 
 * @return string Returns an HTML string to display the graph to troopers
*/
function drawSupportGraph()
{
	global $conn, $forumURL;

	// Prevent on certain pages
	if(@$_GET['action'] == "login" || @$_GET['action'] == "logout") { return; }
	
	// Set return value
	$return = "";
	
	// Check if user is logged in and don't show for command staff
	if(loggedIn())
	{
		// Count number of troopers supporting
		$statement = $conn->prepare("SELECT SUM(amount) FROM donations WHERE datetime > date_add(date_add(LAST_DAY(NOW()),interval 1 DAY),interval -1 MONTH)");
		$statement->execute();
		$statement->bind_result($getSupportNum);
		$statement->fetch();
		$statement->close();

		// Find the position of the last slash
		$lastSlashPos = strrpos($forumURL, '/');

		// If a slash was found, truncate the URL after it
		if ($lastSlashPos !== false) {
		    $cleanedURL = substr($forumURL, 0, $lastSlashPos + 1);
		}

		// Get JSON
		$json = file_get_contents($cleanedURL . 'user-upgrades.php');
		$obj = json_decode($json, true);

		// Check if the JSON was decoded properly
		if ($obj === null) {
		    die('Error decoding JSON data.');
		}

		// Get the current month and year
		$currentMonth = date('m');
		$currentYear = date('Y');

		// Get the current month and year
		$currentMonth = date('m');
		$currentYear = date('Y');

		// Check if the combinedResults array exists
		if (isset($obj['combinedResults']) && is_array($obj['combinedResults'])) {
		    // Loop through each result in combinedResults
		    foreach ($obj['combinedResults'] as $result) {
		        // Ensure the 'start_date' and 'end_date' exist and are valid timestamps
		        if (isset($result['start_date']) && isset($result['end_date']) &&
		            is_numeric($result['start_date']) && is_numeric($result['end_date'])) {

		            // Get the month and year of the result's start_date
		            $startMonth = date('m', $result['start_date']);
		            $startYear = date('Y', $result['start_date']);

		            // Get the month and year of the result's end_date
		            $endMonth = date('m', $result['end_date']);
		            $endYear = date('Y', $result['end_date']);

		            // Check if the current month is between the start_date and end_date
		            if (
		                ($startYear < $currentYear || ($startYear == $currentYear && $startMonth <= $currentMonth)) &&
		                ($endYear > $currentYear || ($endYear == $currentYear && $endMonth >= $currentMonth))
		            ) {
		                // Loop through each userUpgrade
		                foreach ($obj['userUpgrades'] as $result2) {
		                    // Check if this result has the specific user_upgrade_id
		                    if (isset($result2['user_upgrade_id']) && $result2['user_upgrade_id'] == $result['user_upgrade_id']) {
		                        $getSupportNum += $result2['cost_amount'];
		                    }
		                }
		            }
		        }
		    }
		}
		
		// Set goal from site settings
		$statement = $conn->prepare("SELECT supportgoal FROM settings");
		$statement->execute();
		$statement->bind_result($goal);
		$statement->fetch();
		$statement->close();
		
		// Hide for command staff
		if(isset($_GET['action']) && $_GET['action'] == "commandstaff")
		{
			// Set goal to 0 to hide
			$goal = 0;
		}
		
		// If goal is 0, there is no goal and do not show
		if($goal != 0)
		{			
			// Find percent
			$percent = floor(($getSupportNum/$goal) * 100);
			
			// Don't allow over 100
			if($percent > 100)
			{
				$percent = 100;
			}
			
			// Format to currency
			$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
			$goal = $formatter->formatCurrency($goal, 'USD');
			
			$return .= '
			<style>
				.bargraph
				{
					background-color: rgb(192, 192, 192);
					width: 80%;
					border-radius: 15px;
					margin: auto;
				}
			  
				.progress
				{
					background-color: rgb(116, 194, 92);
					color: white;
					padding: 1%;
					text-align: right;
					font-size: 20px;
					border-radius: 15px;
					width: '.$percent.'%;
				}
			</style>
			
			<h2 class="tm-section-header">'.date("F").' - Donation Goal - '.$goal.' </h2>
			
			<p style="text-align: center;">
				<div class="bargraph">
					<div class="progress">'.$percent.'%</div>
				</div>
			</p>';
			
			// Don't show link on donation page
			if(isset($_GET['action']) && $_GET['action'] == "donation")
			{
				// Blank
			}
			else
			{
				// Don't show link if they are a supporter
				if(isSupporter($_SESSION['id']))
				{
					$return .= '
					<p style="text-align: center;">
						<a href="'.$forumURL.'account/upgrades">The '.garrison.' needs your support! Click here to learn more.</a>
					</p>';
				}
				else
				{
					// Did support
					$return .= '
					<p style="text-align: center;">
						<a href="'.$forumURL.'account/upgrades">Thank you for your contribution! Manage your donations here.</a>
					</p>';
				}
			}
			
			$return .= '<hr />';
			
			// Don't show anything if hit goal
			if($percent >= 100) { $return = ''; }
		}
	}
	
	return $return;
}

/**
 * Returns whether the trooper is tagged in a photo
 * 
 * @param int $photoid The ID of the photo
 * @param int $trooperid The ID of the trooper
 * @return boolean
*/
function isInPhoto($photoid, $trooperid) {
	global $conn;

	// Query database for photos
	$statement = $conn->prepare("SELECT * FROM tagged WHERE trooperid = ? AND photoid = ?");
	$statement->bind_param("ii", $trooperid, $photoid);
	$statement->execute();

	$value = false;

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$value = true;
		}
	}

	return $value;
}

/**
 * Returns the troopers ranking
 * 
 * @param int $trooperID The ID of the trooper
 * @return int
*/

function getTrooperRanking($trooperID)
{
	global $conn;

	// Get rank of trooper
	$i = 0;

	$statement = $conn->prepare("SELECT trooperid, COUNT(trooperid) AS total FROM event_sign_up LEFT JOIN events ON event_sign_up.trooperid = events.id WHERE event_sign_up.trooperid != ".placeholder." AND events.closed = '1' AND event_sign_up.status = '3' GROUP BY trooperid ORDER BY total DESC");
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Increment
			$i++;

			// Found trooper
			if($db->trooperid == $trooperID)
			{
				break;
			}
		}
	}

	return $i;
}

/**
 * If a limited event, resets all troopers attendance status in an event, and recalculates status
 * 
 * @param int $eventID The event ID to check
 * @param int $link The main event ID for a shift event
 * @return void
*/
function resetTrooperStatus($eventID, $link = 0)
{
	global $conn, $clubArray, $trackerURL;

	// Store changes
	$before = [];

	// Fetch rows BEFORE update
	$select = $conn->prepare("SELECT id, trooperid, status FROM event_sign_up WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = ?");
	$select->bind_param("i", $eventID);
	$select->execute();
	$result = $select->get_result();

	// Store trooperid and status in the before array
	while ($row = $result->fetch_assoc()) {
	    $before[$row['id']] = ['status' => $row['status'], 'trooperid' => $row['trooperid']];
	}

	// Get data
	$statement = $conn->prepare("SELECT * FROM events WHERE closed = '0' AND id = ?");
	$statement->bind_param("i", $eventID);
	$statement->execute();
	
	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Check each club to see if it's limited
			if($db->limit501st > 500 || $db->limit501st < 500)
			{
				// Reset event sign up
				$statement = $conn->prepare("UPDATE event_sign_up SET status = '1' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' AND 0 = (SELECT club FROM costumes WHERE id = event_sign_up.costume) ".($db->limitHandlers > 500 || $db->limitHandlers < 500 ? 'AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) NOT LIKE \'%handler%\'' : '')."");
				$statement->execute();

				// Update statuses to going if room
				$statement = $conn->prepare("UPDATE event_sign_up SET status = '0' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' AND 0 = (SELECT club FROM costumes WHERE id = event_sign_up.costume) ".($db->limitHandlers > 500 || $db->limitHandlers < 500 ? 'AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) NOT LIKE \'%handler%\'' : '')." ORDER BY signuptime ASC LIMIT " . $db->limit501st);
				$statement->execute();

				// Send notifications for changed status
				if ($statement->affected_rows > 0) {
				    // Fetch rows AFTER update
				    $select->execute();
				    $result = $select->get_result();

					// After update
				    while ($row = $result->fetch_assoc()) {
				        // Compare before and after values
				        if ($before[$row['id']]['status'] !== $row['status']) {
				            createAlert(getUserID($before[$row['id']]['trooperid']), getEventTitle($eventID) . ': Your status on this event is now '.getStatus($row['status']).'.', $trackerURL . '/index.php?event=' . $eventID);
				        }
				    }
				}
			}

			// Loop through clubs to check limits
			foreach($clubArray as $club => $club_value)
			{
				if($db->{$club_value['dbLimit']} > 500 || $db->{$club_value['dbLimit']} < 500)
				{
					// Reset event sign up
					$statement = $conn->prepare("UPDATE event_sign_up SET status = '1' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' AND ".$club_value['costumes'][0]." = (SELECT club FROM costumes WHERE id = event_sign_up.costume) ".($db->limitHandlers > 500 || $db->limitHandlers < 500 ? 'AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) NOT LIKE \'%handler%\'' : '')."");
					$statement->execute();

					// Update statuses to going if room
					$statement = $conn->prepare("UPDATE event_sign_up SET status = '0' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' AND ".$club_value['costumes'][0]." = (SELECT club FROM costumes WHERE id = event_sign_up.costume) ".($db->limitHandlers > 500 || $db->limitHandlers < 500 ? 'AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) NOT LIKE \'%handler%\'' : '')." ORDER BY signuptime ASC LIMIT " . $db->{$club_value['dbLimit']});
					$statement->execute();

					// Send notifications for changed status
					if ($statement->affected_rows > 0) {
					    // Fetch rows AFTER update
					    $select->execute();
					    $result = $select->get_result();

						// After update
					    while ($row = $result->fetch_assoc()) {
					        // Compare before and after values
					        if ($before[$row['id']]['status'] !== $row['status']) {
				            	createAlert(getUserID($before[$row['id']]['trooperid']), getEventTitle($eventID) . ': Your status on this event is now '.getStatus($row['status']).'.', $trackerURL . '/index.php?event=' . $eventID);
					        }
					    }
					}
				}
			}

			// Check total limit
			if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
			{
				// Reset event sign up
				$statement = $conn->prepare("UPDATE event_sign_up SET status = '1' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' ".($db->limitHandlers > 500 || $db->limitHandlers < 500 ? 'AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) NOT LIKE \'%handler%\'' : '')."");
				$statement->execute();

				// Update statuses to going if room
				$statement = $conn->prepare("UPDATE event_sign_up SET status = '0' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' ".($db->limitHandlers > 500 || $db->limitHandlers < 500 ? 'AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) NOT LIKE \'%handler%\'' : '')." ORDER BY signuptime ASC LIMIT " . $db->limitTotalTroopers);
				$statement->execute();

				// Send notifications for changed status
				if ($statement->affected_rows > 0) {
				    // Fetch rows AFTER update
				    $select->execute();
				    $result = $select->get_result();

					// After update
				    while ($row = $result->fetch_assoc()) {
				        // Compare before and after values
				        if ($before[$row['id']]['status'] !== $row['status']) {
				            createAlert(getUserID($before[$row['id']]['trooperid']), getEventTitle($eventID) . ': Your status on this event is now '.getStatus($row['status']).'.', $trackerURL . '/index.php?event=' . $eventID);
				        }
				    }
				}
			}
			
			// Check handler limit
			if($db->limitHandlers > 500 || $db->limitHandlers < 500)
			{
				// Reset event sign up
				$statement = $conn->prepare("UPDATE event_sign_up SET status = '1' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) LIKE '%handler%'");
				$statement->execute();

				// Update statuses to going if room
				$statement = $conn->prepare("UPDATE event_sign_up SET status = '0' WHERE (status = 0 OR status = 1 OR status = 2) AND troopid = '".$db->id."' AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) LIKE '%handler%' ORDER BY signuptime ASC LIMIT " . $db->limitHandlers);
				$statement->execute();

				// Send notifications for changed status
				if ($statement->affected_rows > 0) {
				    // Fetch rows AFTER update
				    $select->execute();
				    $result = $select->get_result();

					// After update
				    while ($row = $result->fetch_assoc()) {
				        // Compare before and after values
				        if ($before[$row['id']]['status'] !== $row['status']) {
				            createAlert(getUserID($before[$row['id']]['trooperid']), getEventTitle($eventID) . ': Your status on this event is now '.getStatus($row['status']).'.', $trackerURL . '/index.php?event=' . $eventID);
				        }
				    }
				}
			}
		}
	}
}

/*********************** XENFORO ***********************/

/**
 * Return's the forum profile of the trooper, if available
 * 
 * @param int $id The Troop Tracker ID of the trooper
 * @return string Returns the HTML needed to display the trooper avatar
*/
function getForumAvatar($id)
{
	// Xenforo
	$xenforo = @getUserForumID(getUserID($id))['user']['avatar_urls']['m'];

	if($xenforo != "")
	{
		return $xenforo;
	}
	else
	{
		return '';
	}
}

/**
 * Get's auth data from Xenforo for logging in
 * 
 * @param int $user_id The Xenforo user ID
 * @return json Returns JSON data of login token
*/
function getAuthForum($user_id)
{
	global $forumURL;
	
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "/auth/login-token",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "user_id=" . $user_id,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Login the trooper with there Xenforo credentials. Used for single sign on.
 * 
 * @param string $username The username of the trooper
 * @param string $password The password of the trooper
 * @return json Return's the Xenforo user data if success
*/
function loginWithForum($username, $password)
{
	global $forumURL;
	
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/auth",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "login=" . urlencode($username) . "&password=" . urlencode($password) . "",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Create's an alert in Xenforo
 * 
 * @param int $to The forum ID the alert is to be sent to
 * @param string $message The message of the alert to be sent
 * @param string $link The link where you want the alert to direct to
 * @return json Return alert success
*/
function createAlert($to, $message, $link = null)
{
	global $forumURL;
	global $trackerURL;

	// Use $link if provided, otherwise default to $trackerURL
	$link = $link ?? $trackerURL;
	
	// Create Thread
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/alerts",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "from_user_id=0&to_user_id=" . urlencode($to) . "&alert=" . urlencode($message) . "&link_url=" . urlencode($link) . "&link_title=Troop%20Tracker%20Link",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Gets threads from forum
 * 
 * @param int $userID (optional) The user ID of the user you want to load announcements from
 * @return json Return's the thread data from forum if success
*/
function getThreadsFromForum($userID = xenforoAPI_userID)
{
	global $forumURL, $forumAnnounceID;
	
	// Get user forum info by forum name
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/forums/" . $forumAnnounceID . "/threads&order=post_date&direction=desc&unread=1",
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . $userID,
	  ],
	]);
	
	// This must be uncommented in a test environment
	//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Create's a thread in Xenforo
 * 
 * @param int $id The forum ID to be posted in
 * @param string $title The title of the thread
 * @param string $message The body of the thread
 * @param int $userID (optional) The user ID of the user you want to post
 * @return json Return's the thread data if success
*/
function createThread($id, $title, $message, $userID = xenforoAPI_userID)
{
	global $forumURL;
	
	// Create Thread
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/threads",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "node_id=" . $id . "&title=" . urlencode($title) . "&message=" . urlencode($message) . "&api_bypass_permissions=1",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . $userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Edits a thread in Xenforo
 * 
 * @param int $id The thread ID to be edited
 * @param string $title The title of the thread
 * @return json Return's the Xenforo thread data if success
*/
function editThread($id, $title)
{
	global $forumURL;
	
	// Edit Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/threads/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "title=" . urlencode($title) . "&api_bypass_permissions=1",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Locks a thread in Xenforo
 * 
 * @param int $id The post ID to be locked
 * @return json Return's the thread data if success
*/
function lockThread($id)
{
	global $forumURL;
	
	// Edit Thread
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/threads/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "discussion_open=" . false . "&api_bypass_permissions=1",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Create's a post in Xenforo
 * 
 * @param int $id The forum ID to be posted in
 * @param string $message The body of the post
 * @param int $userID The Xenforo user ID of the trooper posting. Default value = super admin
 * @return json Return's the Xenforo user data if success
*/
function createPost($id, $message, $userID = xenforoAPI_userID)
{
	global $forumURL;
	
	// Create Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/posts",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "thread_id=" . $id . "&message=" . urlencode($message) . "&api_bypass_permissions=1",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . $userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Edits a post in Xenforo
 * 
 * @param int $id The post ID to be edited
 * @param string $message The body of the post
 * @return json Return's the Xenforo post data if success
*/
function editPost($id, $message)
{
	global $forumURL;
	
	// Edit Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/posts/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "message=" . urlencode($message) . "&api_bypass_permissions=1",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Moves a thread to specified forum
 * 
 * @param int $id The post ID to be moved
 * @param int $forum The forum ID the post is to be moved
 * @return json Return's the Xenforo post data if success
*/
function moveThread($id, $forum)
{
	global $forumURL;
	
	// Edit Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/threads/" . $id . "/move",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "target_node_id=" . $forum . "&api_bypass_permissions=1",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Get's Xenforo forum user by username
 * 
 * @param string $username The username of the Xenforo user
 * @return json Return's the Xenforo user data if success
*/
function getUserForum($username)
{
	global $forumURL;
	
	// Get user forum info by forum name
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/users/find-name&username=" . urlencode($username),
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Reply to Xenforo thread
 * 
 * @param int $threadid The ID of the thread
 * @param int $userid The ID of the Xenforo user
 * @param int $message Message of the reply
 * @return json Success response
*/
function replyThread($threadid, $userid, $message)
{
	global $forumURL;
	
	// Update user by forum groups by ID
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/posts",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "thread_id=" . $threadid . "&message=" . $message,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . $userid,
	  ],
	]);

	$response = curl_exec($curl);
	
	echo curl_error($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Get's Xenforo forum posts from thread
 * 
 * @param int $threadid The ID of the thread
 * @param int $page Return posts on page
 * @return json Return's the Xenforo user data if success
*/
function getThreadPosts($threadid, $page)
{
	global $forumURL;
	
	// Get user forum info by forum name
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/threads/".$threadid."&with_posts=true&page=".$page."",
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Get user alerts
 * 
 * @param int $userid The ID of the user
 * @return json Success response
*/
function getAlerts($userid)
{
	global $forumURL;
	
	// Get user forum info by forum name
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/alerts?unread=1",
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . $userid,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Get user private messages (unread)
 * 
 * @param int $userid The ID of the user
 * @return json Success response
*/
function getConversations($userid)
{
	global $forumURL;
	
	// Get user forum info by forum name
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/conversations&unread=1",
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . $userid,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Get's Xenforo forum user by ID
 * 
 * @param int $id The user ID of the Xenforo user
 * @return json Return's the Xenforo user data if success
*/
function getUserForumID($id)
{
	global $forumURL;
	
	// Get user forum info by forum ID
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/users/" . $id,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Updates Xenforo user's custom variables
 * 
 * @param int $id The user ID of the Xenforo user
 * @param string $custom The custom variable to be changed
 * @param string $value The new value for custom variable
 * @return json Return's JSON data
*/
function updateUserCustom($id, $custom, $value)
{
	global $forumURL;
	
	// Update user by forum groups by ID
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/users/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "custom_fields[".$custom."]=" . $value,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Update's user forum groups by ID
 * 
 * @param int $id The user ID of the Xenforo user
 * @param int $groupid The new group ID to set
 * @param array $group_ids An array of ints to be set for Xenforo secondary groups
 * @return json Return's JSON data
*/
function updateUserForumGroup($id, $groupid, $group_ids)
{
	global $forumURL;
	
	// Update user by forum groups by ID
	$curl = curl_init();

	// Set up
	$groupString = "";

	// Create string for secondary group IDs
	foreach($group_ids as $value)
	{
		$groupString .= "secondary_group_ids[]=" . $value . "&";
	}

	$groupString = substr($groupString, 0, -1);

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/users/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "user_group_id=" . $groupid . "&" . $groupString,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Deletes a post in Xenforo
 * 
 * @param int $id The post ID of the Xenforo post to be deleted
 * @param boolean $hard_delete Optional. If set to true, will delete the post completely with no record
 * @return json Return's JSON data
*/
function deletePost($id, $hard_delete = false)
{
	global $forumURL;
	
	// Delete Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/posts/" . $id,
	  CURLOPT_CUSTOMREQUEST => "DELETE",
	  CURLOPT_POSTFIELDS => "hard_delete=" . $hard_delete . "&api_bypass_permissions=1",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/**
 * Deletes thread in Xenforo
 * 
 * @param int $id The thread ID of the Xenforo thread to be deleted
 * @param boolean $hard_delete Optional. If set to true, will delete the thread completely with no record
 * @return json Return's JSON data
*/
function deleteThread($id, $hard_delete = false)
{
	global $forumURL;
	
	// Delete Thread
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => $forumURL . "api/threads/" . $id,
	  CURLOPT_CUSTOMREQUEST => "DELETE",
	  CURLOPT_POSTFIELDS => "hard_delete=" . $hard_delete . "&api_bypass_permissions=1",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	    "XF-Api-User: " . xenforoAPI_userID,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

/*********************** END XENFORO ***********************/

/**
 * Determines if a trooper is a supporter
 * 
 * @param int $id The ID of the trooper
 * @return boolean Returns true or false if a trooper is a supporter
*/
function isSupporter($id)
{
	global $conn, $userGroupSupporter;
	
	// Set up value
	$value = 0;

	$statement = $conn->prepare("SELECT supporter FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	$xenforo = @getUserForumID(getUserID($id))['user']['secondary_group_ids'];
	
	if($xenforo != null) {
		// If in Xenforo supporter group, change value
		if(@in_array($userGroupSupporter, $xenforo)) {
			$value = 1;
		}
	}
	
	// Return
	return $value;
}

/**
 * Returns a troopers Rebel Legion forum username
 * 
 * @param int $id The ID of the trooper
 * @return boolean Returns the troopers Rebel Forum username from local database
*/
function getRebelLegionUser($id)
{
	global $conn;
	
	// Set up value
	$value = "";
	
	// Get data
	$statement = $conn->prepare("SELECT rebelforum FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();
	
	// Return
	return $value;
}

/**
 * Returns an array of Rebel Legion info about the trooper
 * 
 * @param string $forumid The Rebel Legion forum username of the trooper
 * @return array Returns an array of Rebel Legion information about the trooper
*/
function getRebelInfo($forumid)
{
	global $conn;
	
	// Setup array
	$array = [];
	$array['id'] = '';
	$array['name'] = '';
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM rebel_troopers WHERE rebelforum = ?");
	$statement->bind_param("i", $forumid);
	$statement->execute();
	
	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$array['id'] = $db->rebelid;
			$array['name'] = $db->name;
		}
	}
	
	// Return
	return $array;
}

/**
 * Returns a troopers Mando Mercs CAT #
 * 
 * @param int $id The ID of the trooper
 * @return int Returns the CAT number for the trooper
*/
function getMandoLegionUser($id)
{
	global $conn;
	
	// Set up value
	$value = 0;
	
	// Get data
	$statement = $conn->prepare("SELECT mandoid FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();
	
	// Return
	return $value;
}

/**
 * Returns an array of Mando Mercs info about the trooper
 * 
 * @param int $mandoid The CAT# of the trooper
 * @return int Returns an array of Mando Mercs information about the trooper
*/
function getMandoInfo($mandoid)
{
	global $conn;
	
	// Setup array
	$array = [];
	$array['id'] = '';
	$array['name'] = '';
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM mando_troopers WHERE mandoid = ?");
	$statement->bind_param("i", $mandoid);
	$statement->execute();
	
	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$array['id'] = $db->mandoid;
			$array['name'] = $db->name;
			$array['costume'] = $db->name;
		}
	}
	
	// Return
	return $array;
}

/**
 * Returns a trooper's Saber Guild ID number
 * 
 * @param int $id The ID of the trooper
 * @return int Returns the Saber Guild ID of the trooper
*/
function getSGUser($id)
{
	global $conn;
	
	// Set up value
	$value = 0;
	
	// Get data
	$statement = $conn->prepare("SELECT sgid FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();
	
	// Return
	return $value;
}

/**
 * Returns an array of Saber Guild info about trooper
 * 
 * @param int $sgid The Saber Guild ID of the trooper
 * @return array Returns an array of Saber Guild information about the trooper
*/
function getSGINfo($sgid)
{
	global $conn;
	
	// Setup array
	$array = [];
	$array['sgid'] = '';
	$array['name'] = '';
	$array['image'] = '';
	$array['link'] = '';
	$array['costumename'] = '';
	$array['rank'] = '';
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM sg_troopers WHERE sgid = CONCAT('SG-', ?, '')");
	$statement->bind_param("i", $sgid);
	$statement->execute();

	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$array['sgid'] = $db->sgid;
			$array['name'] = $db->name;
			$array['image'] = $db->image;
			$array['link'] = $db->link;
			$array['costumename'] = $db->link;
			$array['rank'] = $db->link;
		}
	}
	
	// Return
	return $array;
}

/**
 * Returns a boolean if found in the 501st squad roster
 * 
 * @param int $id The ID of the trooper
 * @param int $squad The ID of the squad to search
 * @return array Returns an array of 501st information about the trooper
*/
function ifIn501Roster($id, $squad)
{
	global $conn, $squadArray;

	$found = false;
	
	if($squad == "all") {
		// Get data
		$statement = $conn->prepare("SELECT * FROM 501st_troopers WHERE legionid = ?");
		$statement->bind_param("i", $id);
		$statement->execute();
		
		// Run query...
		if ($result = $statement->get_result()) {
			while ($db = mysqli_fetch_object($result)) {
				$found = true;
			}
		}
	} else {
		// Check if 501st member
		if($squad <= count($squadArray)) {
			// Get data
			$statement = $conn->prepare("SELECT * FROM 501st_troopers WHERE legionid = ? AND squad = ?");
			$statement->bind_param("ii", $id, $squad);
			$statement->execute();
			
			// Run query...
			if ($result = $statement->get_result()) {
				while ($db = mysqli_fetch_object($result)) {
					$found = true;
				}
			}
		}
	}
	
	// Return
	return $found;
}

/**
 * Returns an array of 501st info about the trooper
 * 
 * @param int $id The ID of the trooper
 * @param int $squad The ID of the squad of the trooper
 * @return array Returns an array of 501st information about the trooper
*/
function get501Info($id, $squad)
{
	global $conn, $squadArray;
	
	// Setup array
	$array = [];
	$array['link'] = '';
	
	// Check if 501st member
	if($squad <= count($squadArray))
	{
		// Get data
		$statement = $conn->prepare("SELECT * FROM 501st_troopers WHERE legionid = ?");
		$statement->bind_param("i", $id);
		$statement->execute();
		
		// Run query...
		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				$array['link'] = $db->link;
				$array['joindate'] = $db->joindate;
			}
		}
	}
	
	// Return
	return $array;
}

/**
 * Returns a string of costumes assigned to user in synced Rebel Legion database
 * 
 * @param string $id The Rebel Legion forum username of the trooper
 * @return string Returns an array of Rebel Legion costume information about the trooper
*/
function getMyRebelCostumes($id)
{
	global $conn;
	
	// Setup string
	$costume = "";
	
	// Get data
	$statement = $conn->prepare("SELECT costumename FROM rebel_costumes WHERE rebelid = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	
	// Run query...
	if($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$costume .= ", '" . $db->costumename . "'";
		}
	}
	
	// Return
	return $costume;
}

/**
 * Returns a string of 501st costumes assigned to user in synced database
 * 
 * @param string $id The Rebel Legion forum username of the trooper
 * @return string Returns an array of Rebel Legion costume information about the trooper
*/
function getMyCostumes($id, $squad)
{
	global $conn, $squadArray;
	
	// Setup string
	$costume = "";
	
	// Check if 501st member
	if($squad <= count($squadArray))
	{
		// Get data
		$statement = $conn->prepare("SELECT costumename FROM 501st_costumes WHERE legionid = ?");
		$statement->bind_param("i", $id);
		$statement->execute();
		
		// Run query...
		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				$costume .= ", '" . addslashes($db->costumename) . "'";
			}
		}
	}
	
	// Return
	return $costume;
}

/**
 * Displays all the troopers costumes in synced Rebel Legion database
 * 
 * @param string $id The Rebel Legion forum username of the trooper
 * @return string Returns an HTML string of images of the troopers costumes
*/
function showRebelCostumes($id)
{
	global $conn;
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM rebel_costumes WHERE rebelid = ?");
	$statement->bind_param("s", $id);
	$statement->execute();
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			echo '
			<div style="text-align: center;">
				<h3>'.$db->costumename.'<h3>
				<p>
					<img src="'.$db->costumeimage.'" />
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
			No Rebel Legion costumes to display!
		</p>';
	}
}

/**
 * Displays all the troopers costumes in synced Mando Mercs database
 * 
 * @param string $id The CAT # of the troooper
 * @return string Returns an HTML string of images of the troopers costumes
*/
function showMandoCostumes($id)
{
	global $conn;
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM mando_costumes WHERE mandoid = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			echo '
			<div style="text-align: center;">
				<p>
					<img src="'.$db->costumeurl.'" />
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
			No Mando Mercs costumes to display!
		</p>';
	}
}

/**
 * Displays all the troopers costumes in synced Saber Guild database
 * 
 * @param string $id The Saber Guild ID of the troooper
 * @return string Returns an HTML string of images of the troopers costumes
*/
function showSGCostumes($id)
{
	global $conn;
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM sg_troopers WHERE sgid = CONCAT('SG-', ?, '') AND sgid > 0");
	$statement->bind_param("i", $id);
	$statement->execute();
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			echo '
			<div style="text-align: center;">
					<h3>
						'.$db->costumename.'
					</h3>
					
					<img src="'.$db->image.'" style="width: 50%" height="500" />
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
			No Saber Guild costumes to display!
		</p>';
	}
}

/**
 * Displays all the troopers costumes in synced Droid Builders database
 * 
 * @param string $id The garrison forum username
 * @return string Returns an HTML string of images of the troopers droids
*/
function showDroids($forum)
{
	global $conn;
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM droid_troopers WHERE forum_id = ?");
	$statement->bind_param("s", $forum);
	$statement->execute();
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			echo '
			<div style="text-align: center;">
					<h3>
						'.$db->droidname.'
					</h3>
					
					<img src="'.$db->imageurl.'" style="width: 50%" height="500" />
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
			No Droid Builder droids to display!
		</p>';
	}
}

/**
 * Displays all the troopers costumes in synced 501st database
 * 
 * @param int $id The trooper ID
 * @param int $id The trooper's squad ID
 * @return string Returns an HTML string of images of the troopers droids
*/
function showCostumes($id, $squad)
{
	global $conn, $squadArray;
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM 501st_costumes WHERE legionid = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	
	// Set up count
	$i = 0;
	
	// Check if 501st member
	if($squad <= count($squadArray))
	{
		// Run query...
		if ($result = $statement->get_result())
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
	}
	
	// If no results
	if($i == 0)
	{
		echo '
		<p style="text-align: center;">
			No 501st Legion costumes to display!
		</p>';
	}
}

/**
 * Converts squad ID to Discord role ID
 * 
 * @param int $squad The ID of the squad
 * @return string Returns a string of the role ID
*/
function squadToDiscord($squad)
{
	if($squad == 0)
	{
		return '<@&948046239956627506>';
	}
	else if($squad == 1)
	{
		return '<@&914344158678900766>';
	}
	else if($squad == 2)
	{
		return '<@&914343663474200597>';
	}
	else if($squad == 3)
	{
		return '<@&914344264253718568>';
	}
	else if($squad == 4)
	{
		return '<@&914344334776737822>';
	}
	else if($squad == 5)
	{
		return '<@&914344438472527912>';
	}
	else
	{
		return garrison;
	}
}

/**
 * Send's a notification to the Discord event channel using a WebHook
 * 
 * @param int $id The ID of the event
 * @param string $name The name of the event
 * @param string $description The description of the event
 * @param int $squad The ID of the squad to be converted to a role ID
 * @return void
*/
function sendEventNotify($id, $name, $description, $squad)
{
	global $trackerURL;
	$webhookurl = discordWeb1;

	//=======================================================================================================
	// Compose message. You can use Markdown
	// Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
	//========================================================================================================

	$timestamp = date("c", strtotime("now"));

	$json_data = json_encode([
	    // Message
	    "content" => "".$name." has been added in ".squadToDiscord($squad).".",
	    
	    // Username
	    "username" => "Event Bot",

	    // Text-to-speech
	    "tts" => false,

	    // Embeds Array
	    "embeds" => [
	        [
	            // Embed Title
	            "title" => $name,

	            // Embed Type
	            "type" => "rich",

	            // Embed Description
	            "description" => $description,

	            // URL of title link
	            "url" => "".$trackerURL."/index.php?event=" . $id,

	            // Timestamp of embed must be formatted as ISO8601
	            "timestamp" => $timestamp,

	            // Embed left border color in HEX
	            "color" => hexdec("3366ff")
	        ]
	    ]

	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


	$ch = curl_init( $webhookurl );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

	$response = curl_exec( $ch );
	// If you need to debug, or find out why you can't send message uncomment line below, and execute script.
	// echo $response;
	curl_close( $ch );
}

/**
 * Gets the latitude and longitude of an address 
 * 
 * @param string $address The address of the event
 * @return json Returns the latitude and longitude of an address
*/
function getLatLong($address) {
	// Get geo data from Google Maps API by address 
	$geocodeFromAddr = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . googleKey . ""); 
	 
	// Decode JSON data returned by API 
	$apiResponse = json_decode($geocodeFromAddr); 
	 
	// Retrieve latitude and longitude from API data 
	$latitude  = $apiResponse->results[0]->geometry->location->lat;  
	$longitude = $apiResponse->results[0]->geometry->location->lng;

	// Create array of data
	$array = array("latitude" => $latitude, "longitude" => $longitude);
	return $array;
}

/**
 * Gets squad by location using the Google API
 * 
 * @param string $address The address of the event
 * @return int Returns the ID of the squad based on location
*/
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

/**
 * Returns the shortened name for the particular costume club value
 * 
 * @param int $club The costume club value to get the shortened name
 * @return string Returns the shortened name
*/
function getCostumeAbbreviation($clubid)
{
	global $clubArray, $squadArray;
	
	// Set return value
	$returnValue = "";

	if(in_array($clubid, $squadArray[0]['costumes']))
	{
		// Set
		$returnValue .= '(501st) ';
	}
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Check if squad ID matches value
		if(in_array($clubid, $club_value['costumes']))
		{
			// Set
			$returnValue .= '(' . $club_value['name'] . ') ';
		}
	}

	// Remove whitespace
	$returnValue = rtrim($returnValue);

	$returnValue .= ': ';

	$strCount = substr_count($returnValue, '(');

	// Add dual, triple, or multiple for easy searching
	if($strCount == 2) {
		$returnValue = '(DUAL) ' . $returnValue;
	} else if ($strCount == 3) {
		$returnValue = '(TRIPLE) ' . $returnValue;
	} else if ($strCount > 3) {
		$returnValue = '(MULTIPLE) ' . $returnValue;
	}

	return $returnValue;
}

/**
 * Returns the squad name / club name
 * 
 * @param int $value The ID of the squad or club to get the name
 * @return string Returns the name of the squad or club
*/
function getSquadName($value)
{
	global $squadArray, $clubArray;
	
	// Set return value
	$returnValue = garrison;
	
	// Set squad ID
	$squadID = 1;
	
	// Loop through squads
	foreach($squadArray as $squad => $squad_value)
	{
		// Check if squad ID matches value
		if($squadID == $value)
		{
			// Set
			$returnValue = $squad_value['name'];
		}
		
		// Increment
		$squadID++;
	}
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Check if squad ID matches value
		if($squadID == $value)
		{
			// Set
			$returnValue = $club_value['name'];
		}
		
		// Increment
		$squadID++;
	}

	return $returnValue;
}

/**
 * Returns the squad logo
 * 
 * @param int $value The ID of the squad or club to get the name
 * @return string Returns the logo of the squad
*/
function getSquadLogo($value)
{
	global $squadArray;
	
	// Set return value
	$returnValue = '<img src="images/'.garrisonImage.'" alt="'.garrison.'" style="width: 32px; height: 32px;" />';
	
	// Set squad ID
	$squadID = 1;
	
	// Loop through squads
	foreach($squadArray as $squad => $squad_value)
	{
		// Check if squad ID matches value
		if($squadID == $value)
		{
			// Set
			$returnValue = '<img src="images/'.$squad_value['logo'].'" alt="'.$squad_value['name'].'" style="width: 32px; height: 32px;" />';
		}
		
		// Increment
		$squadID++;
	}

	return $returnValue;
}

/**
 * Returns query for costume values for club. This will display costumes from the club specified.
 * 
 * @param int $clubid The ID of the club
 * @return string Returns query
*/
function getCostumeQueryValues($clubid)
{
	global $squadArray, $clubArray;
	
	// Set up count
	$clubCount = count($squadArray) + 1;
	
	// Query set up
	$query = "";
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Check if club matches
		if($clubid == $clubCount)
		{
			// Get costume count
			$costumeCount = count($club_value['costumes']);
			
			// Step count
			$i = 0;
			
			// Add to query
			$query .= "(";
			
			// Match
			foreach($club_value['costumes'] as $costume)
			{
				// Add to query
				$query .= "'".$costume."' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)";

				// Increment step
				$i++;
				
				// Check if need to add OR
				if($i < $costumeCount)
				{
					// Add OR
					$query .= " OR ";
				}
			}
			
			// Close query
			$query .= ")";
		}
		
		// Increment
		$clubCount++;
	}
	
	// Return
	return $query;
}

/**
 * Returns query for costume values for squad. This will display costumes from the squad specified.
 * 
 * @param int $squadid The ID of the squad
 * @return string Returns query
*/
function getCostumeQueryValuesSquad($squadid)
{
	global $squadArray, $clubArray;
	
	// Set up count
	$squadCount = 0;
	
	// Query set up
	$query = "";
	
	// Loop through clubs
	foreach($squadArray as $squad => $squad_value)
	{
		// Check if club matches
		if($squadid == $squadCount)
		{
			// Get costume count
			$costumeCount = count($squad_value['costumes']);
			
			// Step count
			$i = 0;
			
			// Add to query
			$query .= "(";
			
			// Match
			foreach($squad_value['costumes'] as $costume)
			{
				// Add to query
				$query .= "'".$costume."' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)";

				// Increment step
				$i++;
				
				// Check if need to add OR
				if($i < $costumeCount)
				{
					// Add OR
					$query .= " OR ";
				}
			}
			
			// Close query
			$query .= ")";
		}
		
		// Increment
		$squadCount++;
	}
	
	// Return
	return $query;
}

/**
 * Returns the comment in a red color if it is marked as important
 * 
 * @param int $value The comment ID
 * @param string $text The body of the comment
 * @return string Return HTML string of the important comment
*/
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

/**
 * Check if the user is logged into Troop Tracker
 * 
 * @return boolean
*/
function loggedIn()
{
	if(isset($_SESSION['id']))
	{
		return true;
	}
	return false;
}

/**
 * Checks if handler
 * 
 * @param int $trooperid The ID of the trooper
 * @return boolean Returns if trooper is a handler
*/
function isHandler($trooperid)
{
	global $conn, $squadArray, $clubArray;

	// Get data
	$statement = $conn->prepare("SELECT * FROM troopers WHERE id = ?");
	$statement->bind_param("i", $trooperid);
	$statement->execute();

	// Set up return variable
	$isHandler = false;
	
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$db_data1 = $squadArray[intval($db->squad - 1)]['db'];
			$db_data2 = $clubArray[intval($db->squad - 1)]['db'];

			if($db->squad <= count($squadArray)) {
				if($db->$db_data1 == 4) {
					$isHandler = true;
				}
			} else {
				if($db->$db_data2 == 4) {
					$isHandler = true;
				}
			}
		}
	}

	return $isHandler;
}

/**
 * Converts a query to JSON. This is used extensively for notifications.
 * 
 * @param string $query The query to run, to convert to JSON
 * @return json Returns the query string to JSON
*/
function convertDataToJSON($query)
{
	global $conn;

	// Set up array
	$array = array();

	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Loop through all columns, get data
			foreach($db as $c => $d)
			{
				// Don't save password
				if($c != "password")
				{
					$array[$c] = $d;
				}
			}
		}
	}

	return json_encode($array);
}

/**
 * Returns if the trooper has an award
 * 
 * @param int $trooperid The ID of the trooper
 * @param int $awardid The ID of the award
 * @param boolean $echo Optional. Returns text to output
 * @param boolean $remove Optional. When set, will hide or show element
 * @return string Returns HTML string
*/
function hasAward($trooperid, $awardid, $echo = false, $remove = false)
{
	global $conn;
	
	// Get data
	$statement = $conn->prepare("SELECT * FROM award_troopers WHERE trooperid = ? AND awardid = ?");
	$statement->bind_param("ii", $trooperid, $awardid);
	$statement->execute();

	// Set up return variable
	$hasAward = false;
	
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set
			$hasAward = true;
		}
	}
	
	// Does not print
	if(!$echo)
	{
		return $hasAward;
	}
	else
	{
		// Does not have award
		if($hasAward && !$remove)
		{
			return 'style = "display: none;"';
		}
		else if(!$hasAward && $remove)
		{
			return 'style = "display: none;"';
		}
		else
		{
			return '';
		}
	}
}

/**
 * Sends a notification to the log
 * 
 * 0 = N/A
 * 0 - N/A
 * 1 - Add Costume
 * 2 - Delete Costume
 * 3 - Edit Costume
 * 4 - Delete Award
 * 5 - Add Award
 * 6 - Give Award Trooper
 * 7 - Edit Award
 * 8 - Deny Trooper
 * 9 - Approve Trooper
 * 10 - Delete Trooper
 * 11 - Update Trooper
 * 12 - Add Trooper
 * 13 - Add Event
 * 14 - Edit Event
 * 15 - Add Trooper To Event
 * 16 - Delete Event
 * 17 - Set Charity
 * 18 - Remove Trooper From Event
 * 19 - Add Shift From Edit
 * 20 - Add Title
 * 21 - Delete Title
 * 22 - Give Title
 * 23 - Edit Title
 * 24 - Remove Title
 * 25 - Remove Award
 * 26 - Update Advanced Options
 * 27 - Add event link
 * 28 - Delete event link
 * 29 - Edit event link
 * 
 * @param string $message Body of the message for the log
 * @param int $trooperid The ID of the trooper
 * @param int $type Optional. The ID of the action
 * @param string $json Optional. The JSON data to send along with the log
 * @return void
*/
function sendNotification($message, $trooperid, $type = 0, $json = "")
{
	global $conn;

	$message = cleanInput($message);
	$json = cleanInput($json);
	
	$statement = $conn->prepare("INSERT INTO notifications (message, trooperid, type, json) VALUES (?, ?, ?, ?)");
	$statement->bind_param("siss", $message, $trooperid, $type, $json);
	$statement->execute();
}

/**
 * Checks if a trooper can enter an event, if the event is linked
 * 
 * @param int $trooperid The ID of the trooper to check
 * @param int $troopid The ID of the event to check
 * @return bool
*/
function checkLinkedEvents($trooperid, $troopid) {
	global $conn;

	// Get link2
	$statement = $conn->prepare("SELECT link2 FROM events WHERE id = ?");
	$statement->bind_param("i", $troopid);
	$statement->execute();
	$statement->bind_result($link2);
	$statement->fetch();
	$statement->close();

	// Setup troop count
	$troopCount = 0;

	if($link2 > 0) {
		// Get link2 limit
		$statement = $conn->prepare("SELECT allowed_sign_ups FROM event_link WHERE id = ?");
		$statement->bind_param("i", $link2);
		$statement->execute();
		$statement->bind_result($allowed_sign_ups);
		$statement->fetch();
		$statement->close();

		// Loop through events with link2
		$statement = $conn->prepare("SELECT * FROM events WHERE link2 = ?");
		$statement->bind_param("i", $link2);
		$statement->execute();

		if ($result = $statement->get_result()) {
			while ($db = mysqli_fetch_object($result)) {
				$statement2 = $conn->prepare("SELECT id FROM event_sign_up WHERE trooperid = ? AND troopid = ?");
				$statement2->bind_param("ii", $trooperid, $db->id);
				$statement2->execute();
				$statement2->store_result();

				// Add to count
				$troopCount += $statement2->num_rows;
			}
		}

		// Check if we can enter troop
		if($troopCount < $allowed_sign_ups) {
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}

/**
 * Checks the troop counts of all clubs, to determine if a trooper has reached a milestone
 * 
 * @param int $id The ID of the trooper
 * @return void
*/
function troopCheck($id)
{
	global $conn, $clubArray, $squadArray;
	
	// Notify how many troops did a trooper attend - 501st
	$statement = $conn->prepare("SELECT COUNT(*) FROM event_sign_up WHERE trooperid = ? AND status = '3' AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume))");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($count);
	$statement->fetch();
	$statement->close();
	
	// 501st
	checkTroopCounts($count, "501ST: " . getName($id) . " now has [COUNT] troop(s)", $id, "501ST");
	
	// Set club ID
	$clubID = count($squadArray) + 1;
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Notify how many troops did a trooper attend of club
		$statement = $conn->prepare("SELECT COUNT(*) FROM event_sign_up WHERE trooperid = ? AND status = '3' AND ".getCostumeQueryValues($clubID)."");
		$statement->bind_param("i", $id);
		$statement->execute();
		$statement->bind_result($count);
		$statement->fetch();
		$statement->close();
		
		// Check troop count of club
		checkTroopCounts($count, strtoupper($club_value['name']) . ": " . getName($id) . " now has [COUNT] troop(s)", $id, strtoupper($club_value['name']));
		
		// Increment club count
		$clubID++;
	}
}

/**
 * Searches notification log to filter out milestones that have already been reached by the trooper
 * 
 * @param int $count The count to check based on the troopers count
 * @param string $message The message to search in the notification log
 * @param int $trooperid The ID of the trooper
 * @param int $club The ID of the club
 * @return void
*/
function checkTroopCounts($count, $message, $trooperid, $club)
{
	global $conn;
	
	// Counts to check
	$counts = [1, 10, 25, 50, 75, 100, 150, 200, 250, 300, 400, 500, 501];
	
	// Search notifications for previous notifications, so we don't duplicate - check message for club name
	$statement = $conn->prepare("SELECT * FROM notifications WHERE trooperid = ? AND message LIKE CONCAT('%', ?, '%')");
	$statement->bind_param("is", $trooperid, $club);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			foreach($counts as $value)
			{
				if(strpos($db->message, "now has " . $value) !== false)
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
			
			$statement = $conn->prepare("INSERT INTO notifications (message, trooperid) VALUES (?, ?)");
			$statement->bind_param("si", $tempMessage, $trooperid);
			$statement->execute();
		}
	}
}

/**
 * Returns the trooper's e-mail
 * 
 * @return string The trooper's e-mail
*/
function myEmail()
{
	global $conn;

	$statement = $conn->prepare("SELECT email FROM troopers WHERE id = ?");
	$statement->bind_param("i", $_SESSION['id']);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Returns the trooper's set theme
 * 
 * @return string The trooper's set theme
*/
function myTheme()
{
	global $conn;
	
	$theme = "floridadark";

	if(loggedIn())
	{
		$statement = $conn->prepare("SELECT theme FROM troopers WHERE id = ?");
		$statement->bind_param("i", $_SESSION['id']);
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				switch($db->theme)
				{
					case 0:
						$theme = "floridadark";
					break;
					
					case 1:
						$theme = "everglades";
					break;
					
					case 2:
						$theme = "makaze";
					break;
					
					case 3:
						$theme = "florida";
					break;
				}
			}
		}
	}
	
	return $theme;
}

/**
 * Fetches a specific column from the events table.
 * 
 * @param string $column The column name to retrieve.
 * @param int|null $id Optional. The ID of the event to filter by.
 * @return array|string|null An array of values, a single value, or null if no data is found.
 */
function getEventColumn($column, $id = null)
{
    global $conn;

    // Validate column name to allow any column safely
    $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column); // Remove invalid characters

    // Prepare SQL query
    $sql = "SELECT `$column` FROM events";
    if ($id !== null) {
        $sql .= " WHERE id = ?";
    }

    $statement = $conn->prepare($sql);

    // Bind parameters if ID is provided
    if ($id !== null) {
        $statement->bind_param("i", $id);
    }

    $statement->execute();
    $result = $statement->get_result();

    // Fetch results
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row[$column];
    }

    // Cleanup
    $statement->close();
    $result->free();

    // Return data
    if ($id !== null) {
        // Return a single value if ID was specified
        return $data[0] ?? null;
    }

    return $data; // Return all matching values if no ID was specified
}

/**
 * Returns the event title
 * 
 * @param int $id The ID of the event
 * @param boolean $link If set, will return a link to the main event
 * @return string The trooper's e-mail
*/
function getEventTitle($id, $link = false)
{
	global $conn;

	$statement = $conn->prepare("SELECT * FROM events WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($link)
			{
				return '<a href=\'index.php?event='. $db->id .'\'>' . readInput($db->name) . '</a>';
			}
			else
			{
				return readInput($db->name);
			}
		}
	}
}

/**
 * Returns the event's label
 * 
 * @param int $id The ID of the event
 * @return int Returns event label
*/
function getEventLabel($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT label FROM events WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Returns the event's label
 * 
 * @param int $id The ID of the event
 * @return int Returns event squad
*/
function getEventSquad($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT squad FROM events WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Returns the event Xenforo thread ID on the forum
 * 
 * @param int $id The ID of the event
 * @return int Returns thread ID
*/
function getEventThreadID($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT thread_id FROM events WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Returns the event Xenforo post ID on the forum
 * 
 * @param int $id The ID of the event
 * @return int Returns post ID
*/
function getEventPostID($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT post_id FROM events WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Removes letters from string
 * 
 * @param string $string The string to be processed
 * @return string Returns string with no letters
*/
function removeLetters($string)
{
	return preg_replace('/[^0-9,.]+/', '', $string);
}

/**
 * Converts TKID and other club ID numbers to a readable format
 * 
 * @param int $tkid The TKID of the trooper
 * @param int $squad The squad or club ID of the trooper
 * @param int $trooperid The ID of the trooper
 * @return int Returns the TKID of the trooper based on squad or club
*/
function readTKNumber($tkid, $squad, $trooperid)
{
	global $conn, $clubArray, $squadArray;

	if($tkid == 0 && $squad <= count($squadArray)) {
		$tkid = "Not Assigned";
	} else {
		// If in a club other than 501st
		if($squad > count($squadArray)) {
			if($clubArray[intval($squad) - (count($squadArray) + 1)]['db3'] != "") {
				// Get TK prefix from database
				$statement = $conn->prepare("SELECT " . $clubArray[intval($squad) - (count($squadArray) + 1)]['db3'] . " FROM troopers WHERE id = ?");
				$statement->bind_param("i", $trooperid);
				$statement->execute();
				$statement->bind_result($tkid);
				$statement->fetch();
				$statement->close();

				$tkid = $clubArray[intval($squad) - (count($squadArray) + 1)]['db3Short'] . ': ' . $tkid;
			} else {
				// In a club without a special ID
				$tkid = "Not Assigned";
			}
		} else {
			// If in 501st
			$prefix = "TK";
			
			// Get TK prefix from database
			$statement = $conn->prepare("SELECT prefix FROM 501st_costumes WHERE legionid = ? LIMIT 1");
			$statement->bind_param("i", $tkid);
			$statement->execute();
			$statement->bind_result($getPrefix_value);
			$statement->fetch();
			$statement->close();
			
			// Make sure TK prefix was found
			if(isset($getPrefix_value) && $getPrefix_value != "") {
				$prefix = $getPrefix_value;
			}
			
			$tkid = $prefix . $tkid;
		}
	}

	return $tkid;
}

/**
 * Returns if page is active
 * 
 * @param int $page ID of the active page
 * @return string Returns HTML string
*/
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

/**
 * Returns if squad is active, this is used on the homepage
 * 
 * @param int $squad ID of the squad
 * @return string Returns HTML string
*/
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

/**
 * Returns TK number for trooper
 * 
 * @param int $id ID of the trooper
 * @param boolean $read Optional. If set, will process the TKID through readTKNumber()
 * @return string Returns HTML string
*/
function getTKNumber($id, $read = false)
{
	global $conn;

	$statement = $conn->prepare("SELECT * FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Don't read, just output
			if(!$read)
			{
				return $db->tkid;
			}
			else
			{
				return readTKNumber($db->tkid, $db->squad, $db->id);
			}
		}
	}
}

/**
 * Returns trooper ID from TK number
 * 
 * @param int $tkid TKID of the trooper
 * @return int ID of the trooper
*/
function getIDFromTKNumber($tkid)
{
	global $conn, $squadArray;

	$value = 0;

	$statement = $conn->prepare("SELECT id FROM troopers WHERE tkid = ? AND squad <= ".count($squadArray)."");
	$statement->bind_param("i", $tkid);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Returns squad of the trooper
 * 
 * @param int $id ID of the trooper
 * @return int ID of the squad
*/
function getTrooperSquad($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT squad FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Returns forum username of trooper
 * 
 * @param int $id ID of the trooper
 * @return string Forum username of the trooper
*/
function getTrooperForum($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT forum_id FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Returns the ID of the club assigned to a costume
 * 
 * @param int $id ID of the costume
 * @return int ID of the club
*/
function getCostumeClub($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT club FROM costumes WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Replaces the costume ID with an N/A costume
 * 
 * @param int $id The costume ID
 * @return int Returns the N/A costume for the club
 */
function replaceCostumeID($id)
{
	global $clubArray, $dualCostume, $dualNA;

	$club = getCostumeClub($id);

	if($club == 0) { return 0; }
	//if(in_array($club, $dualCostume)) { return $dualNA; }

	return intval($clubArray[$club - 1]['naCostume']);
}

/**
 * Get's the file name of the file
 * 
 * @param string $file The path to file
 * @return string Returns the file name without the extension
 */
function getFileName($file)
{
	$info = pathinfo($file);
	return $info['filename'];
}

/**
 * Display's user information at top of profile page, used on profiles
 * 
 * @param int $id ID of the trooper
 * @param int $tkid TKID of the trooper
 * @param int $squad ID of the trooper's squad
 * @param string $forum Forum username of the trooper
 * @param string $phone Phone number of the trooper
 * @return void
*/
function profileTop($id, $tkid, $name, $squad, $forum, $phone)
{
	global $conn, $squadArray, $clubArray, $userGroupRankImages, $forumURL;
	
	// Command Staff Edit Link
	if(isAdmin())
	{
		echo '
		<h2 class="tm-section-header">Admin Controls</h2>
		<p style="text-align: center;"><a href="index.php?action=commandstaff&do=managetroopers&uid='.$id.'" class="button">Edit/View Member in Command Staff Area</a></p>';
	}

	echo '
	<p style="text-align: center;">
		<a href="#upcoming-troops-header" class="button">Upcoming Troops</a>

		<a href="#troop-history-header" class="button">Troop History</a>

		<a href="#awards-header" class="button">Awards</a>

		<a href="#photo_section" class="button">Tagged Photos</a>

		<a href="#donation-header" class="button">Donations</a>

		<a href="#costumes-header" class="button">Costumes</a>
	</p>';
	
	// Only show 501st thumbnail, if a 501st member
	if(getTrooperSquad($tkid) <= count($squadArray))
	{
		// Get 501st thumbnail Info
		$statement = $conn->prepare("SELECT thumbnail FROM 501st_troopers WHERE legionid = ?");
		$statement->bind_param("i", $tkid);
		$statement->execute();
		$statement->bind_result($thumbnail);
		$statement->fetch();
		$statement->close();
	}
	
	// Get Rebel Legion thumbnail info
	$rebelid = getRebelLegionUser($id);
	
	$statement = $conn->prepare("SELECT costumeimage FROM rebel_costumes WHERE rebelid = ? LIMIT 1");
	$statement->bind_param("s", $rebelid);
	$statement->execute();
	$statement->bind_result($thumbnail_rebel);
	$statement->fetch();
	$statement->close();
	
	// Get permission type
	$statement = $conn->prepare("SELECT permissions FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($permission);
	$statement->fetch();
	$statement->close();
	
	echo '
	<h2 class="tm-section-header">'.($permission == 3 ? 'In Memoriam...<br />' : '').''.$name.' - '.readTKNumber($tkid, $squad, $id).'</h2>';
	
	// RIP Member
	if($permission == 3) {
		echo '
		<p style="text-align: center;">
			<b>No one\'s ever really gone - Thank you for your service. The Force will be with you. Always.</b>
		</p>';
	}
	
	// Avatar
	
	// Does have a avatar?
	$haveAvatar = false;

	// Xenforo
	$xenforo = @getUserForumID(getUserID($id))['user']['avatar_urls']['m'];

	if($xenforo != "")
	{
		echo '
		<p style="text-align: center;">
			<img src="'.$xenforo.'" />
		</p>';

		// Set
		$haveAvatar = true;
	}
	
	// 501
	if(isset($thumbnail))
	{
		echo '
		<p style="text-align: center;">
			<img src="'.$thumbnail.'" />
		</p>';
		
		// Set
		$haveAvatar = true;
	}
	
	// Rebel
	if(isset($thumbnail_rebel))
	{
		echo '
		<p style="text-align: center;">
			<img src="'.str_replace("-A", "sm", $thumbnail_rebel).'" />
		</p>';
		
		// Set
		$haveAvatar = true;
	}
	
	// If does not have an avatar
	if(!$haveAvatar)
	{
		echo '
		<p style="text-align: center;">
			<img src="https://www.501st.com/memberdata/templates/tk_head.jpg" />
		</p>';
	}

	echo '
	<style>
	#rankBars {
		text-align: center;
	}

	#rankBars img {
		display: block;
		text-align: center;
		margin: auto;
		width: 150px;
		height: 30px;
	}
	</style>

	<div id="rankBars">';

	// Show rank images
	$xenforo = @getUserForumID(getUserID($id))['user']['secondary_group_ids'];

	if($xenforo != "")
	{
		// Setup URL
		// Find the position of the last slash
		$lastSlashPos = strrpos($forumURL, '/');

		// If a slash was found, truncate the URL after it
		if ($lastSlashPos !== false) {
		    $cleanedURL = substr($forumURL, 0, $lastSlashPos + 1);
		}

		// Get JSON
		$json = file_get_contents($cleanedURL . 'groups.php');
		$obj = json_decode($json, true);

		$ranks = array();

		// Loop through all groups associated with trooper
		foreach($xenforo as $value)
		{
			array_push($ranks,
				array(
				"bannerText" => $obj[$value]['bannerText'],
				"order" => $obj[$value]['order']
			));
		}

		// Sort the array by 'order' (order) in descending order
		uasort($ranks, function ($a, $b) {
		    return $b['order'] <=> $a['order']; // Descending order
		});

		// Display after being ordered
		foreach($ranks as $value)
		{
			echo $value['bannerText'];
		}
	}

	echo '
	</div>';
	
	// Check if the username was found
	if(isset(getUserForum($forum)['exact']['user_id'])) {
		echo '
		<p style="text-align: center;"><a href="'.$forumURL.'members/'.$forum.'.'.getUserForum($forum)['exact']['user_id'].'" target="_blank" class="button">View Boards Profile</a></p>';
	} else {
		echo '
		<p style="text-align: center;">Boards Name: '.$forum.'</p>';
	}

	echo '
	<p style="text-align: center;">
		<b>Troop Tracker Rank:</b><br />#'.getTrooperRanking($id).'
	</p>';

	if(isset(get501Info($tkid, $squad)['joindate']) && !is_null(get501Info($tkid, $squad)['joindate']))
	{
		echo '
		<p style="text-align: center;">
			<b>501st Member Since:</b>
			<br />
			'.date("F, d, Y", strtotime(get501Info($tkid, $squad)['joindate'])).'
		</p>';
	}
	
	if(isAdmin() && $phone != "")
	{
		echo '
		<p style="text-align: center;"><b>Phone Number:</b><br />'.formatPhoneNumber($phone).'</p>';
	}
}

/**
 * Return's a formatted phone number
 * 
 * @param string $phoneNumber Phone number to be formatted
 * @return string The formatted phone number
*/
function formatPhoneNumber($phoneNumber)
{
	$phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

	if(strlen($phoneNumber) > 10)
	{
		$countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
		$areaCode = substr($phoneNumber, -10, 3);
		$nextThree = substr($phoneNumber, -7, 3);
		$lastFour = substr($phoneNumber, -4, 4);

		$phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
	}
	else if(strlen($phoneNumber) == 10)
	{
		$areaCode = substr($phoneNumber, 0, 3);
		$nextThree = substr($phoneNumber, 3, 3);
		$lastFour = substr($phoneNumber, 6, 4);

		$phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
	}
	else if(strlen($phoneNumber) == 7)
	{
		$nextThree = substr($phoneNumber, 0, 3);
		$lastFour = substr($phoneNumber, 3, 4);

		$phoneNumber = $nextThree.'-'.$lastFour;
	}

	return $phoneNumber;
}

/**
 * Return's if a trooper exists
 * 
 * @param int $id ID of the trooper
 * @return boolean Returns if trooper exists
*/
function profileExist($id)
{
	global $conn;

	// Set up return var
	$doesExist = false;

	$statement = $conn->prepare("SELECT id FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->store_result();
	$doesExist = $statement->num_rows;

	if($doesExist > 0) { $doesExist = true; }
	
	// Return
	return $doesExist;
}

/**
 * Return's the provided data into BB code format to be displayed on the forum
 * 
 * @param string $eventName The name of the event
 * @param string $eventVenue The venue of the event
 * @param string $location The address of the event
 * @param string $date1 The start date for the event
 * @param string $date2 The end date for the event
 * @param string $website The website of the event
 * @param int $numberOfAttend The number of projected attendees
 * @param int $requestedNumber The requested number of characters
 * @param int $requestedCharacter The requested characters
 * @param int $secure Is there provided secure areas?
 * @param int $blasters Are blasters allowed?
 * @param int $lightsabers Are lightsabers allowed?
 * @param int $parking Is there parking?
 * @param int $mobility Is this venue accessible?
 * @param string $amenities Is there amenities?
 * @param string $comments Additional information on the troop - BB Code supported
 * @param string $referred Who referred the event
 * @param int $eventId ID for the event
 * @param int $eventType The ID of the event type for the event
 * @param string $roster A string of the troopers attending the troop
 * @return string
*/
function threadTemplate($eventName, $eventVenue, $location, $date1, $date2, $website, $numberOfAttend, $requestedNumber, $requestedCharacter, $secure, $blasters, $lightsabers, $parking, $mobility, $amenities, $comments, $referred, $eventId, $eventType = 0, $roster = "")
{
	global $conn;
	global $trackerURL;
	
	$returnString = '';

	$returnString .= '
	[b]Event Name:[/b] '.readInput($eventName).'
	[b]Venue:[/b] '.readInput($eventVenue).'
	[b]Venue address:[/b] '.readInput($location).'
	[b]Event Start:[/b] '.date("m/d/y h:i A", strtotime($date1)).'
	[b]Event End:[/b] '.date("m/d/y h:i A", strtotime($date2)).'';

	// Exclude unimportant information from armor party events
	if($eventType != 10 && $eventType != 7)
	{
		$returnString .= '
		[b]Event Website:[/b] '.readInput($website).'
		[b]Expected number of attendees:[/b] '.$numberOfAttend.'
		[b]Requested number of characters:[/b] '.$requestedNumber.'
		[b]Requested character types:[/b] '.readInput($requestedCharacter).'
		[b]Secure changing/staging area:[/b] '.yesNo($secure).'
		[b]Can troopers carry blasters:[/b] '.yesNo($blasters).'
		[b]Can troopers carry/bring props like lightsabers and staffs:[/b] '.yesNo($lightsabers).'
		[b]Is parking available:[/b] '.yesNo($parking).'
		[b]Is venue accessible to those with limited mobility:[/b] '.yesNo($mobility).'';
	}

	// Exclude unimportant information from virtual troops
	if($eventType != 7)
	{
		$returnString .= '
		[b]Amenities available at venue:[/b] '.ifEmpty(readInput($amenities), "No amenities for this event.").'';
	}


	$returnString .= '
	[b]Comments:[/b]
	'.ifEmpty(readInput($comments), "No comments for this event.").'
	[b]Referred by:[/b] '.ifEmpty(readInput($referred), "Not available").'

	'.$roster.'';
	
	// Loop through all admin photos
	$statement = $conn->prepare("SELECT * FROM uploads WHERE troopid = ? AND admin = '1' ORDER BY date ASC");
	$statement->bind_param("i", $eventId);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$returnString .= '
			[IMG]'.$trackerURL.'/images/uploads/'.$db->filename.'[/IMG]
			';
		}
	}

	$returnString .= '
	[b][u]Sign Up / Event Roster:[/u][/b]

	[url]'.$trackerURL.'/index.php?event=' . $eventId . '[/url]';

	$link = isLink($eventId);

	if($link > 0) {
		$returnString .= '

		[b][u]Other Shifts:[/u][/b]
		';

		$statement = $conn->prepare("SELECT * FROM events WHERE (id = ? OR link = ?) AND id != ? ORDER BY dateStart DESC");
		$statement->bind_param("iii", $link, $link, $eventId);
		$statement->execute();
		
		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				$returnString .= '
				-[url='.$trackerURL.'/index.php?event='.$db->id.'][b]' . date("l", strtotime($db->dateStart)) . '[/b] : [i]' . date("m/d - h:i A", strtotime($db->dateStart)) . ' - ' . date("h:i A", strtotime($db->dateEnd)) . '[/i] ' .''.$db->name.'[/url]
				';
			}
		}

		$returnString .= '
		[b]To view all shift event forum posts on one page, view the event page on the Troop Tracker. This forum page will only show this shifts forum posts.[/b]
		';
	}

	// Get link2
	$link2 = isLink2($eventId);

	// Show linked events
	if($link == 0 && $link2 > 0) {
		$returnString .= '
		[b][u]Related Troops:[/u][/b]';

		// Query database for linked events
		$statement = $conn->prepare("SELECT * FROM events WHERE link2 = ? AND id != ? ORDER BY dateStart DESC");
		$statement->bind_param("ii", $link2, $eventId);
		$statement->execute();
		
		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				$returnString .= '
				[url="'.$trackerURL.'/index.php?event=' . $db->id . '"]' . (isLink($db->id) > 0 ? '[b]'.date('l', strtotime($db->dateStart)).'[/b] - ' . date('M d, Y', strtotime($db->dateStart)) . ' ' . date('h:i A', strtotime($db->dateStart)) . ' - ' . date('h:i A', strtotime($db->dateEnd)) . ' '. $db->name : date('M d, Y', strtotime($db->dateStart)) . ': ' . $db->name) .'[/url]
				';
			}
		}

		$returnString .= '
		[b]This event is connected to other related events; therefore, sign-up limits may apply.[/b]

		[b]To view all related event forum posts on one page, view the event page on the Troop Tracker. This forum page will only show this events forum posts.[/b]
		';
	} else {
		$returnString .= '
		[b]This event is connected to other related events; therefore, sign-up limits may apply.[/b]';
	}

	return $returnString;
}

/**
 * Returns the hours between two date times
 * 
 * @param string $datetime1 The first date to check
 * @param string $datetime2 The second date to check
 */
function timeBetweenDates($datetime1, $datetime2)
{
	$date1 = new DateTime($datetime1);
	$date2 = new DateTime($datetime2);
	
	$diff = $date2->diff($date1);
	
	return $diff->h;
}

/**
 * Return's the user's ID from Xenforo Forum
 * 
 * @param int $id ID of the trooper
 * @return int Returns user ID from Xenforo Forum
*/
function getUserID($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT user_id FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Return's the tracker ID based on Xenforo Forum ID
 * 
 * @param int $userid ID of the trooper on forum
 * @return int Returns ID from tracker
*/
function getIDFromUserID($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT id FROM troopers WHERE user_id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Return's the troopers's name
 * 
 * @param int $id ID of the trooper
 * @return string Returns trooper's name
*/
function getName($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT name FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Return's the troopers's e-mail
 * 
 * @param int $id ID of the trooper
 * @return string Returns trooper's e-mail
*/
function getEmail($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT email FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Return's the troopers's phone number
 * 
 * @param int $id ID of the trooper
 * @return string Returns trooper's phone number
*/
function getPhone($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT phone FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Return's the troopers's squad ID
 * 
 * @param int $id ID of the trooper
 * @return string Returns trooper's squad ID
*/
function getSquadID($id)
{
	global $conn;

	$statement = $conn->prepare("SELECT squad FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	return $value;
}

/**
 * Helps with copying event values to create an event page. Set's default values when copying events.
 * 
 * @param int $eid ID of the event
 * @param string $value Value loaded from database
 * @param int $default Optional. Default value for field
 * @return string Default value of field
*/
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

/**
 * Helps with copying event values to create an event page. Set's default values when copying events for select elements.
 * 
 * @param int $eid ID of the event
 * @param string $value Value loaded from database
 * @param string $value2 Default value for field
 * @param int $default Optional. Default value for field if interval.
 * @return string Default value of field
*/
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

/**
 * Search for trooper in event, and return if in event
 * 
 * @param int $id ID of the trooper
 * @param int event ID of the event
 * @return array Returns an array, [inTroop] if trooper is in event, and [status] of the trooper
*/
function inEvent($id, $event)
{
	global $conn;

	$array = ["inTroop" => "0", "status" => ""];
	$status = "";

	$statement = $conn->prepare("SELECT * FROM event_sign_up WHERE trooperid = ? AND troopid = ?");
	$statement->bind_param("ii", $id, $event);
	$statement->execute();

	$i = 0;
	if ($result = $statement->get_result())
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

/**
* Returns an updated roster
*
* @param int $eventID The event ID to fetch
* @param int $limitTotal The limit total for the event
* @param boolean $totalTrooperEvent If the event is an event that checks the total limit
* @param boolean $signedUp If the trooper is signed up for this event. Default: false
* @array Returns an HTML string of the updated roster and HTML string of the current trooper counts remaining
*/
function getRoster($eventID, $limitTotal = 0, $totalTrooperEvent = 0, $signedUp = false)
{
	global $conn, $mainCostumes, $clubArray;
	
	// Define data variable for below code
	$data = "";
	$data2 = "";

	// Get data to send back - query the event data for the information

	// Query database for event info
	$statement = $conn->prepare("SELECT * FROM events WHERE id = ?");
	$statement->bind_param("i", $eventID);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{

			// Query database for roster info
			$statement2 = $conn->prepare("SELECT event_sign_up.id AS signId, event_sign_up.note, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, event_sign_up.status, event_sign_up.signuptime, troopers.id AS trooperId, troopers.name, troopers.tkid, troopers.squad FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = ? ORDER BY event_sign_up.id ASC");
			$statement2->bind_param("i", $eventID);
			$statement2->execute();

			$i = 0;

			if ($result2 = $statement2->get_result())
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// Use this for later to determine which select box to show...
					$status = $db2->status;

					// If no events to show...
					if($i == 0)
					{
						$data .= '
						<form action="process.php?do=modifysignup" method="POST" name="modifysignupForm" id="modifysignupForm">
						
						<!-- Hidden variables -->
						<input type="hidden" name="modifysignupTroopIdForm" id="modifysignupTroopIdForm" value="'.$db->id.'" />
						<input type="hidden" name="limitedEventCancel" id="limitedEventCancel" value="'.$db->limitedEvent.'" />
						<input type="hidden" name="troopid" id="troopid" value="'.$eventID.'" />
						<input type="hidden" name="myId" id="myId" value="'.(isset($_SESSION['id']) ? $_SESSION['id'] : '0').'" />

						<div style="overflow-x: auto;">
						<table border="1">
						<tr>
							<th>Trooper Name</th>	<th>TKID</th>	<th>Costume</th>	<th>Backup Costume</th>	<th>Status</th>
						</tr>';
					}
					
					// Create row, change based on status
					if($db2->status == 4 || $db2->status == 6 || $db2->status == 7) {
						$data .= '
						<tr class="canceled-troop">';
					}
					else if($db2->status == 2) {
						$data .= '
						<tr class="tentative-troop">';
					}
					else if($db2->status == 1) {
						$data .= '
						<tr class="standby-troop">';
					} else {
						$data .= '
						<tr>';
					}

					// Allow for users to edit their status from the event, and make sure the event is not closed, and the user did not cancel
					if(loggedIn() && ($db2->trooperId == $_SESSION['id'] || $_SESSION['id'] == $db2->addedby) && ($db->closed == 0 || $db->closed == 4))
					{
						$data .= '
						<td>
							'.drawSupportBadge($db2->trooperId).'
							<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>';

							// If a placeholder account, allow edit name
							if($db2->trooperId == placeholder)
							{
								$data .= '
								<input type="text" name="placeholdertext" signid="'.$db2->signId.'" value="'.$db2->note.'" />';
							}

							// Show who added the trooper
							if($db2->addedby != 0)
							{
								$data .= '
								<br /><small>Added by:<br />' . getName($db2->addedby) . '</small>';
							}

						$data .= '
						</td>
							
						<td>
							'.readTKNumber($db2->tkid, $db2->squad, $db2->trooperId).'
						</td>
						
						<td name="trooperRosterCostume" id="trooperRosterCostume">
							<select name="modifysignupFormCostume" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

							// Display costumes
							$statement3 = $conn->prepare("SELECT * FROM costumes WHERE " . costume_restrict_query(false, $db2->trooperId, false) . " ORDER BY FIELD(costume, ".$mainCostumes."".mainCostumesBuild($db2->trooperId)."".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume");
							$statement3->execute();
							
							if ($result3 = $statement3->get_result())
							{
								while ($db3 = mysqli_fetch_object($result3))
								{
									if($db2->costume == $db3->id)
									{
										// If this is the selected costume, make it selected
										$data .= '
										<option value="'. $db3->id .'" SELECTED>'.getCostumeAbbreviation($db3->club).' '.$db3->costume.'</option>';
									}
									else
									{
										// Default
										$data .= '
										<option value="'. $db3->id .'">'.getCostumeAbbreviation($db3->club).' '.$db3->costume.'</option>';
									}
								}
							}

							$data .= '
							</select>
						</td>
						
						<td name="trooperRosterBackup" id="trooperRosterBackup">
							<select name="modiftybackupcostumeForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';
							
							// Count results
							$c = 0;

							// Display costumes
							$statement3 = $conn->prepare("SELECT * FROM costumes WHERE " . costume_restrict_query(false, $db2->trooperId, false) . " ORDER BY FIELD(costume, ".$mainCostumes."".mainCostumesBuild($db2->trooperId)."".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume");
							$statement3->execute();
							
							// Amount of costumes
							if ($result3 = $statement3->get_result())
							{
								while ($db3 = mysqli_fetch_object($result3))
								{
									// If costume set to backup and first result
									if($db2->costume_backup == 0 && $c == 0)
									{
										$data .= '
										<option value="0" SELECTED>N/A</option>';
									}
									// Make sure this is a first result otherwise
									else if($c == 0)
									{
										$data .= '
										<option value="0">N/A</option>';
									}
									
									
									// If a costume matches
									if($db2->costume_backup == $db3->id)
									{
										$data .= '
										<option value="'.$db3->id.'" SELECTED>'.getCostumeAbbreviation($db3->club).' '.$db3->costume.'</option>';
									}
									// Start showing costumes
									else
									{
										$data .= '
										<option value="'.$db3->id.'">'.getCostumeAbbreviation($db3->club).' '.$db3->costume.'</option>';
									}
									
									// Increment
									$c++;
								}
							}

							$data .= '
							</select>
						</td>
						
						<td id="'.$db2->trooperId.'Status" aria-label="'.formatTime($db2->signuptime, 'F j, Y, g:i a').'" data-balloon-pos="up">
						<div name="trooperRosterStatus">';
						
							if($db->limitedEvent != 1)
							{
								// If on stand by
								if($db2->status == 1)
								{
									$data .= '
									<select name="modifysignupStatusForm" id="modifysignupStatusForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
										<option value="0" '.echoSelect(1, $db2->status).'>Stand By</option>
										<option value="4" '.echoSelect(4, $db2->status).'>Cancel</option>
									</select>';
								}
								// Regular
								else
								{
									$data .= '
									<select name="modifysignupStatusForm" id="modifysignupStatusForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
										<option value="0" '.echoSelect(0, $db2->status).'>I\'ll be there!</option>
										<option value="2" '.echoSelect(2, $db2->status).'>Tentative</option>
										<option value="4" '.echoSelect(4, $db2->status).'>Cancel</option>
									</select>';
								}
							}
							else
							{
								// Limited event - If pending approval
								if($db2->status == 5)
								{
									$data .= '
									<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
									(Pending Command Staff Approval)';

									// If is admin and limited event
									if(isAdmin() && $db->limitedEvent == 1 && $db->closed == 0)
									{
										// Set status
										$data .= '
										<br />
										<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>
										<br />
										<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
									}

									$data .= '</div>';
								}
								else
								{
									$data .= '
									<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

									$data .= getStatus($db2->status);

									// If is admin and limited event
									if(isAdmin() && $db->limitedEvent == 1 && $db->closed == 0)
									{
										// If set to going
										if($db2->status == 0)
										{
											// Set status
											$data .= '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
										}
										// If set to not picked
										else if($db2->status == 6)
										{
											// Set status
											$data .= '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>';
										}
									}

									$data .= '</div>';
								}							
							}

						$data .= '
						</div>
						</td>';
					}
					else
					{
						// If a user other than the current user
						$data .= '
						<td>
							'.drawSupportBadge($db2->trooperId).'
							<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>';

							// If a placeholder account, allow edit name
							if($db2->trooperId == placeholder)
							{
								$data .= '
								<b>'.$db2->note.'</b>';
							}

							// Show who added the trooper
							if($db2->addedby != 0)
							{
								$data .= '
								<br /><small>Added by:<br />' . getName($db2->addedby) . '</small>';
							}

						$data .= '
						</td>
							
						<td>
							'.readTKNumber($db2->tkid, $db2->squad, $db2->trooperId).'
						</td>
						
						<td>
							'.ifEmpty('<a href="index.php?action=costume&costumeid='.$db2->costume.'">' . getCostume($db2->costume) . '</a>', "N/A").'
						</td>
						
						<td>
							'.ifEmpty('<a href="index.php?action=costume&costumeid='.$db2->costume_backup.'">' . getCostume($db2->costume_backup) . '</a>', "N/A").'
						</td>
						
						<td id="'.$db2->trooperId.'Status" aria-label="'.formatTime($db2->signuptime, 'F j, Y, g:i a').'" data-balloon-pos="up">
							<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
							'.getStatus($db2->status);

							// If is admin and limited event
							if(isAdmin() && $db->limitedEvent == 1)
							{
								// If set to going
								if($db2->status == 0)
								{
									// Set status
									$data .= '
									<br />
									<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
								}
								else if($db2->status == 5) {
									$data .= '
									<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
									(Pending Command Staff Approval)';

									// If is admin and limited event
									if(isAdmin() && $db->limitedEvent == 1 && $db->closed == 0)
									{
										// Set status
										$data .= '
										<br />
										<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>
										<br />
										<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
									}
								}
								// If set to not picked
								else if($db2->status == 6)
								{
									// Set status
									$data .= '
									<br />
									<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>';
								}
							}

						$data .= '
						</div>
						</td>';
					}
					
					$data .= '
					</tr>';

					// Increment trooper count
					$i++;
				}
			}

			if($i == 0)
			{
				$data .= '
				<b>No troopers have signed up for this event!</b>
				<br />
				<br />';
			}
			else
			{
				$data .= '</table>
				</div>
				</form>';
			}

			// Update troopers remaining
			$data2 = '
			<ul>
				<li>This event is limited to '.$limitTotal.' troopers. ';

				// Check for total limit set, if it is, add remaining troopers
				if($totalTrooperEvent)
				{
					$data2 .= '
					' . troopersRemaining($limitTotal, eventClubCount($db->id, "all")) . '</li>';
				}
				else
				{
					$data2 .= '
					<li>This event is limited to '.$db->limit501st.' 501st troopers. '.troopersRemaining($db->limit501st, eventClubCount($db->id, 0)).' </li>';

					// Set up club count
					$clubCount = 1;

					// Loop through clubs
					foreach($clubArray as $club => $club_value)
					{
						$data2 .= '
						<li>This event is limited to '.$db->{$club_value['dbLimit']}.' '. $club_value['name'] .' troopers. '.troopersRemaining($db->{$club_value['dbLimit']}, eventClubCount($db->id, $clubCount)).'</li>';

						// Increment club count
						$clubCount++;
					}
				}
				
				// Check for total limit set, if it is, set event as limited
				if($db->limitHandlers > 500 || $db->limitHandlers < 500)
				{
					$data2 .= '
					<li>This event is limited to '.$db->limitHandlers.' handlers. <b>'.($db->limitHandlers - handlerEventCount($db->id)).' handlers remaining.</b></li>';
				}

			$data2 .= '
			</ul>';
		}
	}
	
	return [$data, $data2];
}

/**
 * Returns the status of the trooper
 * 
 * 0 = Going / 1 = Stand By / 2 = Tentative / 3 = Attended / 4 = Canceled / 5 = Pending / 6 = Not Picked / 7 = No Show
 * 
 * @param int $value The ID of the status
 * @return string Returns string of the status
*/
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
	else if($value == 7)
	{
		$returnValue = "No Show";
	}

	return $returnValue;
}

/**
 * Returns if a URL is valid
 * 
 * @param string $url URL to be validated
 * @return string Returns an HTML string of the validated URL
*/
function validate_url($url)
{
	$path = parse_url($url, PHP_URL_PATH);

	$encoded_path = array_map('urlencode', @explode('/', $path));
	$url = @str_replace($path, implode('/', $encoded_path), $url);

	if(filter_var(addHttp($url), FILTER_VALIDATE_URL) && strpos($url, "."))
	{
		return '<span style="word-wrap: break-word;"><a href="'.addHttp($url).'" target="_blank">'.$url.'</a></span>';
		
	}
	else
	{
		return 'No website available.';
	}
}

/**
 * Formats values if they are empty with a default value
 * 
 * If no value, show message. Default is EMPTY.
 * 
 * @param string $value Value to check if empty
 * @param string $message Message to display if empty
 * @return string Returns message string
*/
function ifEmpty($value, $message = "EMPTY")
{
	// Check for blank HTML
	$valueClean = @strip_tags($value);

	if($valueClean == "")
	{
		return $message;
	}
	else if(is_null($valueClean))
	{
		return $message;
	}
	else
	{
		return $value;
	}
}

/**
 * Returns costume string from costume ID
 * 
 * @param int $value ID of costume
 * @param bool $abbreviation Return the abbreviation
 * @return string Returns costume name
*/
function getCostume($id, $abbreviation = true)
{
	global $conn;

	$statement = $conn->prepare("SELECT costume FROM costumes WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($value);
	$statement->fetch();
	$statement->close();

	if(is_null($value)) {
		return '';
	} else if(!$abbreviation) {
		return $value;
	} else {
		return getCostumeAbbreviation(getCostumeClub($id)) . ' ' . $value;
	}
}

/**
 * Compares two values, if they match, will return SELECTED to HTML
 * 
 * @param int $value1 Value 1 to compare
 * @param int $value2 Value 2 to compare
 * @return string HTML SELECTED string
*/
function echoSelect($value1, $value2)
{
	$returnValue = "";

	if($value1 == $value2)
	{
		$returnValue = "SELECTED";
	}

	return $returnValue;
}

/**
 * Displays yes or no string based on interval value
 * 
 * @param int $value Checks interval value
 * @return string HTML yes or no string
*/
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

/**
 * Adds http to url string if it does not exist
 * 
 * @param string $url URL string to format
 * @return string HTML URL string
*/
function addHttp($url)
{
	if (!preg_match("~^(?:f|ht)tps?://~i", $url))
	{
		$url = "http://" . $url;
	}
	return $url;
}

/**
 * Returns a string of costumes that were favorited by the trooper
 *
 * @param int $trooperid The trooper ID of the trooper to query
 * @return string Returns a query string
*/
function mainCostumesBuild($trooperid)
{
	global $conn;
	
	$returnQuery = "";

	$statement = $conn->prepare("SELECT * FROM favorite_costumes WHERE trooperid = ?");
	$statement->bind_param("i", $trooperid);
	$statement->execute();
	
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$returnQuery .= ", '".@addslashes(getCostume($db->costumeid, false))."'";
		}
	}
	
	return $returnQuery;
}

/**
 * Returns if trooper is an admin or moderator
 * 
 * 1 = Super Admin / 2 = Moderator
 * 
 * @return boolean Returns if admin or moderator
*/
function isAdmin()
{
	global $conn;
	
	$isAdmin = false;
	
	if(isset($_SESSION['id']))
	{
		$statement = $conn->prepare("SELECT permissions FROM troopers WHERE id = ?");
		$statement->bind_param("i", $_SESSION['id']);
		$statement->execute();

		if ($result = $statement->get_result())
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

/**
 * Determines if trooper has permission to access
 * 
 * 0 = Regular Member, 1 = Super Admin, 2 = Moderator, 3 = RIP Member
 * 
 * @param int $permissionLevel1 First permission to check
 * @param int $permissionLevel2 Optional. Second permission to check
 * @param int $permissionLevel3 Optional. Third permission to check
 * @return boolean Returns if admin or moderator
*/
function hasPermission($permissionLevel1, $permissionLevel2 = -1, $permissionLevel3 = -1)
{
	global $conn;
	
	$isAllowed = false;
	
	if(isset($_SESSION['id']))
	{
		$statement = $conn->prepare("SELECT permissions FROM troopers WHERE id = ?");
		$statement->bind_param("i", $_SESSION['id']);
		$statement->execute();

		if ($result = $statement->get_result())
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
			}
		}
	}
	
	return $isAllowed;
}

/**
 * Sends alert to users in an event
 * 
 * Must be a moderator to utilize this method
 * 
 * @param int $troopid ID of the troop to message troopers
 * @param string $message The message to send to troopers
 * @param string $link The link where you want the alert to direct to
 * @return boolean Returns was a success
*/
function alertTroopersInEvent($troopid, $message, $link = null)
{
	global $conn;
	global $trackerURL;

	// Use $link if provided, otherwise default to $trackerURL
	$link = $link ?? $trackerURL;
	
	// Check if the trooper is a moderator
	$statement = $conn->prepare("SELECT * FROM event_sign_up WHERE troopid = ?");
	$statement->bind_param("i", $troopid);
	$statement->execute();

	if ($result = $statement->get_result()) {
		while ($db = mysqli_fetch_object($result)) {
			createAlert(getUserID($db->trooperid), $message, $link);
		}
	}
	
	return true;
}

/**
 * Determines if trooper has special permission access
 * 
 * Must be a moderator to utilize this method
 * 
 * @param string $permission Database value to check for special permission
 * @return boolean Returns if trooper has access
*/
function hasSpecialPermission($permission)
{
	global $conn;
	
	$hasPermission = false;
	
	// Check if the trooper is a moderator
	$statement = $conn->prepare("SELECT * FROM troopers WHERE id = ? AND permissions = 2");
	$statement->bind_param("i", $_SESSION['id']);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->{$permission} == 1)
			{
				$hasPermission = true;
			}
		}
	}
	
	return $hasPermission;
}

/**
 * Returns if trooper has club access
 * 
 * @param int $dbclub ID of club to check
 * @return boolean Returns if trooper has access to a club
*/
function isClubMember($dbclub)
{
	global $conn;
	
	$returnValue = 0;
	
	// Check if the trooper is a moderator
	$statement = $conn->prepare("SELECT * FROM troopers WHERE id = ?");
	$statement->bind_param("i", $_SESSION['id']);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$returnValue = $db->{$dbclub};
		}
	}
	
	return $returnValue;
}

/**
 * Returns site message when closed
 *
 * @return string Returns the site closed message
*/
function getSiteMessage()
{
	global $conn;
	
	$siteMessage = "";

	$statement = $conn->prepare("SELECT sitemessage FROM settings LIMIT 1");
	$statement->execute();
	$statement->bind_result($siteMessage);
	$statement->fetch();
	$statement->close();

	// Check if site message is blank
	if($siteMessage != "") {
		$siteMessage = '<p style="text-align: center; font-size: 20px; color: red;"><b>**** Message From Command Staff ****</b></p><p style="text-align: center; color: red; font-size: 18px;">' . $siteMessage . '</p>';
	}
	
	return $siteMessage;
}

/**
 * Returns if the website is closed
 *
 * @return boolean Returns if the website is open or closed
*/
function isWebsiteClosed()
{
	global $conn;
	
	$isWebsiteClosed = false;

	$statement = $conn->prepare("SELECT * FROM settings LIMIT 1");
	$statement->execute();
	
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($db->siteclosed)
			{
				$isWebsiteClosed = true;
				
				if(loggedIn() && !isAdmin())
				{
					session_destroy();
				}
			}
		}
	}
	
	return $isWebsiteClosed;
}

/**
 * Returns if the website sign ups are open or closed
 *
 * @return boolean Returns if the website sign ups are open or closed
*/
function isSignUpClosed()
{
	global $conn;
	
	$isWebsiteClosed = false;
	
	$statement = $conn->prepare("SELECT * FROM settings LIMIT 1");
	$statement->execute();

	if ($result = $statement->get_result())
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

/**
 * Returns whether a phone number is valid (https://gist.github.com/gh-o-st/8126326)
 * 
 * @ return boolean Returns whether a phone number is valid
*/
function validPhone($phone) {
  $format_pattern = '/^(?:(?:\((?=\d{3}\)))?(\d{3})(?:(?<=\(\d{3})\))?[\s.\/-]?)?(\d{3})[\s\.\/-]?(\d{4})\s?(?:(?:(?:(?:e|x|ex|ext)\.?\:?|extension\:?)\s?)(?=\d+)(\d+))?$/';
  $nanpa_pattern = '/^(?:1)?(?(?!(37|96))[2-9][0-8][0-9](?<!(11)))?[2-9][0-9]{2}(?<!(11))[0-9]{4}(?<!(555(01([0-9][0-9])|1212)))$/';
 
  //Set array of variables to false initially
  $valid = array(
    'format' => false,
    'nanpa' => false,
    'ext' => false,
    'all' => false
  );
 
  //Check data against the format analyzer
  if(preg_match($format_pattern, $phone, $matchset)) {
    $valid['format'] = true;    
  }
 
  //If formatted properly, continue
  if($valid['format']) {
 
    //Set array of new components
    $components = array(
      'ac' => $matchset[1], //area code
      'xc' => $matchset[2], //exchange code
      'sn' => $matchset[3], //subscriber number
      'xn' => $matchset[4], //extension number
    );
 
    //Set array of number variants
    $numbers = array(
      'original' => $matchset[0],
      'stripped' => substr(preg_replace('[\D]', '', $matchset[0]), 0, 10)
    );
 
    //Now let's check the first ten digits against NANPA standards
    if(preg_match($nanpa_pattern, $numbers['stripped'])) {
      $valid['nanpa'] = true;
    }
 
    //If the NANPA guidelines have been met, continue
    if($valid['nanpa']) {
      if(!empty($components['xn'])) {
        if(preg_match('/^[\d]{1,6}$/', $components['xn'])) {
          $valid['ext'] = true;
        }
      }
      else {
        $valid['ext'] = true;
      }
    }
   
    //If the extension number is valid or non-existent, continue
    if($valid['ext']) {
      if(strlen($components['ac'].$components['xc'].$components['sn']) == 10) {
        $valid['all'] = true;
      }
    }
  }
  return $valid['all'];
}

/**
 * Returns if the TKID exists (NOTE: This is compatiable with 501st and Rebel Legion due to both clubs combining Troop Tracker data)
 *
 * @param int $tk The TKID of the trooper
 * @param int $squad The squad or club ID of the trooper
 * @return boolean Returns if the TKID exists
*/
function doesTKExist($tk, $squad = 0)
{
	global $conn, $squadArray;
	
	// Set up variables
	$exist = false;
	
	// If a 501st squad
	if($squad <= count($squadArray))
	{
		$statement = $conn->prepare("SELECT * FROM troopers WHERE tkid = ? AND squad <= ".count($squadArray)."");
		$statement->bind_param("i", $tk);
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				$exist = true;
			}
		}
	}
	else
	{
		// If a club
		$statement = $conn->prepare("SELECT * FROM troopers WHERE rebelforum = ? AND squad = ?");
		$statement->bind_param("ii", $tk, $squad);
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				$exist = true;
			}
		}
	}

	return $exist;
}

/**
 * Returns if the TKID is registered (NOTE: This is compatiable with 501st and Rebel Legion due to both clubs combining Troop Tracker data)
 *
 * @param int $tk The TKID of the trooper
 * @param int $squad The squad or club ID of the trooper
 * @return boolean Returns if the TKID is registered
*/
function isTKRegistered($tk, $squad = 0)
{
	global $conn, $squadArray;
	
	// Set up variables
	$registered = false;

	// If a 501st squad
	if($squad <= count($squadArray))
	{
		$statement = $conn->prepare("SELECT * FROM troopers WHERE tkid = ? AND squad <= ".count($squadArray)."");
		$statement->bind_param("i", $tk);
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($db->password != '')
				{
					$registered = true;
				}
			}
		}
	}
	else
	{
		// If a club
		$statement = $conn->prepare("SELECT * FROM troopers WHERE rebelforum = ? AND squad = ?");
		$statement->bind_param("si", $tk, $squad);
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($db->password != '')
				{
					$registered = true;
				}
			}
		}
	}

	return $registered;
}

/**
 * Prevents hacks by sanitizing input
 *
 * @param string $value The input to be sanitized
 * @return string Returns sanitized input
*/
function cleanInput($value)
{
	$value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	return $value;
}

/**
 * Converts cleanInput into readable text
 * 
 * @param string $value The input to be read
 * @return string Returns the readable data
*/
function readInput($value)
{
	$value = strip_tags(html_entity_decode(htmlspecialchars_decode($value ?? ''), ENT_QUOTES));

	return $value;
}

/**
 * Inserts a new event into the database
 *
 * @param string $name Name of the event
 * @param string $venue Name of the venue
 * @param string $dateStart Date and time the event starts
 * @param string $dateEnd Date and time the event ends
 * @param string $website URL of the website for event
 * @param int $numberOfAttend Projected number of attending public
 * @param int $requestedNumber Requested number of troopers
 * @param string $requestedCharacter The types of characters requested
 * @param int $secureChanging If there is secure changing
 * @param int $blasters If blasters are allowed
 * @param int $lightsabers If lightsabers are allowed
 * @param int $parking If parking is available
 * @param int $mobility If event has handicap access
 * @param string $amenities Types of amenities offered
 * @param string $referred Who referred the event
 * @param string $poc Point of contact for event
 * @param string $comments Describe the event
 * @param string $location Address of the event
 * @param string $label Type of event
 * @param int $limitedEvent If event is limited and needs command staff to pick each trooper
 * @param int $limit501st Limit number of 501st troopers (500 = do not limit)
 * @param int $limitTotalTroopers Limit number of total troopers (500 = do not limit)
 * @param int $limitHandlers Limit number of total handlers (500 = no not limit)
 * @param int $friendLimit Max number of friends a trooper can add to an event
 * @param int $allowTentative Set whether a trooper can set their status as tentative
 * @param int $squad Value for squad location
 * @param int $link (OPTIONAL // DEFAULT 0) ID of linked event
 * @return int return Last inserted ID
*/
function createEvent($name, $venue, $dateStart, $dateEnd, $website, $numberOfAttend, $requestedNumber, $requestedCharacter, $secureChanging, $blasters, $lightsabers, $parking, $mobility, $amenities, $referred, $poc, $comments, $location, $label, $limitedEvent, $limit501st, $limitTotalTroopers, $limitHandlers, $friendLimit, $allowTentative, $squad, $link = 0)
{
	global $conn;

	$name = cleanInput($name);
	$venue = cleanInput($venue);
	$website = cleanInput($website);
	$requestedCharacter = cleanInput($requestedCharacter);
	$amenities = cleanInput($amenities);
	$referred = cleanInput($referred);
	$poc = cleanInput($poc);
	$comments = cleanInput($comments);
	$location = cleanInput($location);

	$statement = $conn->prepare("INSERT INTO events (name, venue, dateStart, dateEnd, website, numberOfAttend, requestedNumber, requestedCharacter, secureChanging, blasters, lightsabers, parking, mobility, amenities, referred, poc, comments, location, label, limitedEvent, limit501st, limitTotalTroopers, limitHandlers, friendLimit, allowTentative, squad, link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$statement->bind_param("sssssiisiiiiissssssiiiiiiii", $name, $venue, $dateStart, $dateEnd, $website, $numberOfAttend, $requestedNumber, $requestedCharacter, $secureChanging, $blasters, $lightsabers, $parking, $mobility, $amenities, $referred, $poc, $comments, $location, $label, $limitedEvent, $limit501st, $limitTotalTroopers, $limitHandlers, $friendLimit, $allowTentative, $squad, $link);
	$statement->execute();

	return $conn->insert_id;
}

/**
 * Send's an e-mail if trooper is subscribed to an event
 *
 * @param int $troopid Event ID of the troop
 * @param int $trooperid ID of the trooper
 * @param string $subject Subject of the e-mail
 * @param string $message Body of the e-mail message
 * @return void
*/
function sendEventUpdate($troopid, $trooperid, $subject, $message)
{
	global $conn;
	global $trackerURL;

	// Alert message
	$messageAlert = $message;

	// Add footer to message
	$message = $message . "".$trackerURL."/index.php?event=".$troopid."\n\nYou can opt out of e-mails under: \"Manage Account\"\n\n".$trackerURL."/\n\nTo turn off this notification, go to the event page, and press the \"Unsubscribe\" button.";

	// Query database for trooper information and make sure they are subscribed to e-mail
	$statement = $conn->prepare("SELECT troopers.user_id, troopers.email, troopers.name, troopers.subscribe FROM troopers LEFT JOIN event_notifications ON troopers.id = event_notifications.trooperid WHERE event_notifications.troopid = ? AND troopers.subscribe = '1' AND troopers.email != ''");
	$statement->bind_param("i", $troopid);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Send e-mail
			@sendEmail($db->email, $db->name, $subject, $message);

			// Create alert
			createAlert($db->user_id, $messageAlert, ''.$trackerURL.'/index.php?event=' . $troopid);
		}
	}
}

/**
 * Send's an e-mail to a specified trooper
 *
 * @param string $sendTo E-mail address to send to
 * @param string $Name Name of the trooper
 * @param string $Subject Subject of the e-mail
 * @param string $Mesage Body of the e-mail message
 * @return void
*/
function sendEmail($SendTo, $Name, $Subject, $Message)
{
	// MAIL
	$mail = new PHPMailer(TRUE);

	/* Set the mail sender. */
	$mail->setFrom(emailFrom, 'Troop Tracker');

	/* Add a recipient. */
	$mail->addAddress($SendTo, $Name);

	/* Tells PHPMailer to use SMTP. */
	$mail->isSMTP();
	
	/* Debug */
	//$mail->SMTPDebug = true;
	//$mail->Debugoutput = 'echo';

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
	   //echo $mail->ErrorInfo;
	}
	// END MAIL
}

/**
 * Returns the number of troopers remaining
 *
 * @param int $value1 First number to compare
 * @param int $value2 Second number to compare
 * @return string Returns string of remaining spots
*/
function troopersRemaining($value1, $value2)
{
	// Subtract values
	$remaining = $value1 - $value2;
	
	// Return remaining
	return '<b>' . $remaining . ' spots remaining.</b>';
}

/**
 * Returns number of troopers signed up for this event based on costume
 *
 * @param int $eventID ID of the event
 * @param int $clubID ID of the club
 * @return string Returns count of club in the event
*/
function eventClubCount($eventID, $clubID)
{
	global $conn, $clubArray, $dualCostume, $squadArray;
	
	// Variables
	$c501 = 0;
	
	// Loop through clubs to make variables
	foreach($clubArray as $club => $club_value)
	{
		// Set up variables
		${"c" . $club_value['dbLimit']} = 0;
	}
	
	// Total count
	$total = 0;

	// Total all together
	$totalAll = 0;
	
	// Set up return number
	$returnVal = 0;

	// Query database for roster info
	$statement = $conn->prepare("SELECT events.limitHandlers, event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, troopers.id AS trooperId, troopers.name, troopers.tkid FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid LEFT JOIN events ON events.id = event_sign_up.troopid WHERE troopid = ? AND (event_sign_up.status = 0 OR event_sign_up.status = 2)");
	$statement->bind_param("i", $eventID);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Prevent bug where if event does not limit handlers, handlers are not counted towards the total
			if($db->limitHandlers > 500 || $db->limitHandlers < 500)
			{
				$statement2 = $conn->prepare("SELECT * FROM costumes WHERE id = ? AND costume NOT LIKE '%handler%'");
				$statement2->bind_param("i", $db->costume);
				$statement2->execute();
			} else {
				$statement2 = $conn->prepare("SELECT * FROM costumes WHERE id = ?");
				$statement2->bind_param("i", $db->costume);
				$statement2->execute();
			}


			if ($result2 = $statement2->get_result())
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// 501st
					if($db2->club == 0)
					{
						$c501++;

						// Increment total count
						$totalAll++;
					}
					
					// Loop through clubs
					foreach($clubArray as $club => $club_value)
					{
						// Club
						if(in_array($db2->club, $club_value['costumes']))
						{
							// Increment to club
							${"c" . $club_value['dbLimit']}++;

							// Increment total count
							$totalAll++;
						}
					}						
				}
			}
		}
	}
	
	// If 501
	if($clubID == 0)
	{
		$returnVal = $c501;
	}
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Set return val if Club ID set to club
		if(($clubID == (count($squadArray) + 1) + $club) && $clubID >= count($squadArray) + 1)
		{
			$returnVal = ${"c" . $club_value['dbLimit']};
		}
	}

	// If want total
	if($clubID === "all")
	{
		$returnVal = $totalAll;
	}

	// Return
	return $returnVal;
}

/**
 * Returns number of handlers signed up for this event
 *
 * @param int $eventID ID of the event
 * @param int $clubID ID of the club
 * @return string Returns count of club in the event
*/
function handlerEventCount($eventID)
{
	global $conn;
	
	// Set total number to return
	$total = 0;

	// Query database for roster info
	$statement = $conn->prepare("SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, troopers.id AS trooperId, troopers.name, troopers.tkid FROM event_sign_up LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid LEFT JOIN costumes ON costumes.id = event_sign_up.costume WHERE troopid = ? AND (event_sign_up.status = 0 OR event_sign_up.status = 2) AND costumes.costume LIKE '%handler%'");
	$statement->bind_param("i", $eventID);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Query costume database to add to club counts
			$statement2 = $conn->prepare("SELECT * FROM costumes WHERE id = ?");
			$statement2->bind_param("i", $db->costume);
			$statement2->execute();

			if ($result2 = $statement2->get_result())
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					$total++;
				}
			}
		}
	}

	// Return
	return $total;
}

/**
 * Checks to see if the event is full
 *
 * @param int $eventID ID of the event
 * @param int $costumeID ID of the costume
 * @return boolean Returns if event is full based on costume choice
*/
function isEventFull($eventID, $costumeID)
{
	global $conn, $dualCostume, $clubArray;

	// Set up variables
	$eventFull = false;

	// Set up limits
	$limit501st = "";

	// Set up limit totals
	$limit501stTotal = eventClubCount($eventID, 0);

	// Set up club count
	$clubCount = 1;

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Set up variables
		${$club_value['dbLimit']} = "";

		// Set up limit totals
		${$club_value['dbLimit'] . "Total"} = eventClubCount($eventID, $clubCount);

		// Increment club count
		$clubCount++;
	}

	// Query - get limiuts // output
	$statement = $conn->prepare("SELECT * FROM events WHERE id = ?");
	$statement->bind_param("i", $eventID);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			$limit501st = $db->limit501st;

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Set up
				${$club_value['dbLimit']} = $db->{$club_value['dbLimit']};
			}
		}
	}

	// 501
	if(getCostumeClub($costumeID) == 0 && ($limit501st - eventClubCount($eventID, 0)) <= 0)
	{
		// Set event full
		$eventFull = true;
	}

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Loop through costumes
		foreach($club_value['costumes'] as $costume)
		{
			// Check club
			if(getCostumeClub($costumeID) == $costume && (${$club_value['dbLimit']} - eventClubCount($eventID, $costume)) <= 0)
			{
				// Set event full
				$eventFull = true;
			}
		}
	}

	// Return
	return $eventFull;
}

/**
 * Converts value to title string of permission
 *
 * @param int $value ID of the permission
 * @return string Returns string value based off permission ID
*/
function getPermissionName($value)
{
	if($value == 0)
	{
		return 'Regular Member';
	}
	else if($value == 1)
	{
		return 'Super Admin';
	}
	else if($value == 2)
	{
		return 'Moderator';
	}
	else
	{
		return 'Unknown';
	}
}

/**
 * Converts value to title string of permission
 *
 * @param int $value ID of the permission
 * @return string Returns string value based off permission ID
*/
function getClubPermissionName($value)
{
	if($value == 0)
	{
		return 'Not A Member';
	}
	else if($value == 1)
	{
		return 'Regular Member';
	}
	else if($value == 2)
	{
		return 'Reserve Member';
	}
	else if($value == 3)
	{
		return 'Retired Member';
	}
	else if($value == 4)
	{
		return 'Handler';
	}
	else
	{
		return 'Unknown';
	}
}

/**
 * Determines if a trooper can access the troop tracker to sign up for events
 * 
 * This is determined by if the trooper is in a reserved status.
 *
 * @param int $id ID of the trooper
 * @return string Returns if a trooper can access the troop tracker
*/
function canAccess($id)
{
	global $conn, $clubArray;
	
	// Set up var
	$canAccess = false;

	$statement = $conn->prepare("SELECT * FROM troopers WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// 501
			if($db->p501 != 3 && $db->p501 != 0)
			{
				$canAccess = true;
			}

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Check member status per club
				if($db->{$club_value['db']} != 3 && $db->{$club_value['db']} != 0)
				{
					$canAccess = true;
				}
			}
		}
	}
	
	return $canAccess;
}

/**
 * Returns if a e-mail setting is on or off
 *
 * @param string $column Database field name
 * @param boolean print Optional. If set to true, will return CHECKED
 * @return string Returns if a trooper is subscribed to an e-mail setting
*/
function emailSettingStatus($column, $print = false)
{
	global $conn;
	
	// Set status
	$status = 0;

	// Set up
	$continue = false;

	// Get a list of all columns to make sure data is validated
	$statement = $conn->prepare("SELECT COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'troopers'");
	$statement->execute();

	if($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Loop through results
			foreach($db as $row)
			{
				if($row == $column)
				{
					$continue = true;
				}
			}
		}
	}
	
	// Get email setting
	$statement = $conn->prepare("SELECT ".$column." FROM troopers WHERE id = ?");
	$statement->bind_param("i", $_SESSION['id']);
	$statement->execute();
	$statement->bind_result($status);
	$statement->fetch();
	$statement->close();
	
	// If print not set, return status
	if(!$print)
	{
		return $status;
	}
	else
	{
		// If print set, print checked
		if($status == 1)
		{
			return 'CHECKED';
		}
	}
}

/**
 * Returns if an event is linked
 *
 * @param int $id ID of the event
 * @return int ID of the linked event
*/
function isLink($id)
{
	global $conn;
	
	// Set link
	$link = 0;
	
	// Get number of events with link
	$statement = $conn->prepare("SELECT id FROM events WHERE link = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->store_result();
	$getNumOfLinks = $statement->num_rows;
	
	// Get link ID
	$statement = $conn->prepare("SELECT link FROM events WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($getLinkID_get);
	$statement->fetch();
	$statement->close();
	
	// If has links to event, or is linked, show shift data
	if($getNumOfLinks > 0 || $id != 0)
	{
		// If this event is the link
		if($getNumOfLinks > 0)
		{
			$link = $id;
		}
		else if($getLinkID_get != 0)
		{
			$link = $getLinkID_get;
		}
	}
	
	return $link;
}

/**
 * Returns value of link2 from event ID
 *
 * @param int $id ID of the event
 * @return int ID of the link2
*/
function isLink2($id)
{
	global $conn;
	
	// Get link ID
	$statement = $conn->prepare("SELECT link2 FROM events WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->bind_result($link2);
	$statement->fetch();
	$statement->close();
	
	return $link2;
}

/**
 * Sorts post_date for shift and regular events
 *
 * @param int $a Date value 1
 * @param int $b Date value 2
 * @return array Returns sorted array
*/
function custom_sort($a, $b) {
	return $a['post_date'] < $b['post_date'] ? 1 : -1;
}

/**
 * Get's Google Sheet
 *
 * @param int $spreadsheetId ID of the spreadsheet
 * @param int $get_range Sheet Name OR Sheet Name!A1:G3
 * @return string Returns values from spreadsheet
*/
function getSheet($spreadsheetId, $get_range)
{
	// Google API set up
	$client = new \Google_Client();
	$client->setApplicationName('Troop Tracker Google Sheets API');
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
	$client->setAccessType('offline');
	$client->setAuthConfig(__DIR__ . '/sheets_api_secret.json');
	$service = new Google_Service_Sheets($client);
	$response = $service->spreadsheets_values->get($spreadsheetId, $get_range);
	$values = $response->getValues();
	return $values;
}

/**
 * Edit's the Google Sheet
 *
 * @param int $spreadsheetId ID of the spreadsheet (in URL)
 * @param int $sheetName Name of the sheet we want to edit
 * @param int $columnFrom The letter of column we want to start editing from
 * @param int The letter of column we want to stop editing from
 * @param int $newValues The new values (array) we want to change the values to
 * @return void
*/
function editSheet($spreadsheetId, $sheetName, $columnFrom, $columnTo, $newValues)
{
	// Google API set up
	$client = new \Google_Client();
	$client->setApplicationName('Troop Tracker Google Sheets API');
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
	$client->setAccessType('offline');
	$client->setAuthConfig(__DIR__ . '/sheets_api_secret.json');
	$service = new Google_Service_Sheets($client);
	
	// Update range of the sheet
	$update_range = $sheetName . "!" . $columnFrom . ":" . $columnTo;
	
	// Change to value
	$values = [$newValues];
	
	// Google Sheet API to update
	$body = new Google_Service_Sheets_ValueRange(['values' => $values]);
	$params = ['valueInputOption' => 'RAW'];
	$update_sheet = $service->spreadsheets_values->update($spreadsheetId, $update_range, $body, $params);
}

/**
 * Adds to bottom of Google Sheet
 *
 * @param int $spreadsheetId ID of the spreadsheet (in URL)
 * @param int $sheetName Name of the sheet we want to edit
 * @param int $newValues Array of new values to add
 * @return void
*/
function addToSheet($spreadsheetId, $sheetName, $newValues)
{
	// Google API set up
	$client = new \Google_Client();
	$client->setApplicationName('Troop Tracker Google Sheets API');
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
	$client->setAccessType('offline');
	$client->setAuthConfig(__DIR__ . '/sheets_api_secret.json');
	$service = new Google_Service_Sheets($client);
	
	// Add to sheet
	$range = $sheetName;
	$valueRange = new Google_Service_Sheets_ValueRange();
	$valueRange->setValues(["values" => $newValues]); 
	$conf = ["valueInputOption" => "RAW"];
	$service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $conf);
}

/**
 * Deletes rows from sheet
 *
 * @param int $spreadsheetId ID of the spreadsheet (in URL)
 * @param int $sheetID The ID of the spreadsheet GID in URL
 * @param int $start Index to start delete
 * @param int $end Index to end delete
 * @return void
*/
function deleteSheetRows($spreadsheetId, $sheetID, $start, $end)
{
	// Google API set up
	$client = new \Google_Client();
	$client->setApplicationName('Troop Tracker Google Sheets API');
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
	$client->setAccessType('offline');
	$client->setAuthConfig(__DIR__ . '/sheets_api_secret.json');
	$service = new Google_Service_Sheets($client);

	// Delete
	$deleteOperation = array(
		'range' => array(
		'sheetId'   => $sheetID,
		'dimension' => 'ROWS',
		'startIndex'=> $start,
		'endIndex'  => ($end)
		)
	);

	$deletable_row[] = new Google_Service_Sheets_Request(array('deleteDimension' =>  $deleteOperation));

	$delete_body = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array('requests' => $deletable_row));
	$result = $service->spreadsheets->batchUpdate($spreadsheetId, $delete_body);
}

/**
 * Returns numbers from string
 *
 * @param string $str String to format into numbers)
 * @return str String with just numbers
*/
function get_numerics($str)
{
    preg_match_all('/\d+/', $str, $matches);
    return $matches[0][0];
}

// If trooper is not logged in and not set to login from login page
if(!loggedIn() && !isset($_POST['loginWithTK']))
{
	// If cookies are set
	if(isset($_COOKIE['TroopTrackerUsername']) && isset($_COOKIE['TroopTrackerPassword']) && $_COOKIE['TroopTrackerUsername'] != "" && $_COOKIE['TroopTrackerPassword'] != "")
	{
		// Set up fail check
		$failCheck = true;
		
		// Login with forum
		$forumLogin = loginWithForum($_COOKIE['TroopTrackerUsername'], $_COOKIE['TroopTrackerPassword']);
		
		// Check credentials
		if(isset($forumLogin['success']) && $forumLogin['success'] == 1)
		{
			// Update username if changed
			$statement = $conn->prepare("UPDATE troopers SET forum_id = ? WHERE user_id = ?");
			$statement->bind_param("si", $forumLogin['user']['username'], $forumLogin['user']['user_id']);
			$statement->execute();
		}
		
		$statement = $conn->prepare("SELECT * FROM troopers WHERE forum_id = ?");
		$statement->bind_param("s", $_COOKIE['TroopTrackerUsername']);
		$statement->execute();

		if($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				// If logged in with forum details, and password does not match
				if(isset($forumLogin['success']) && $forumLogin['success'] == 1)
				{
					// Update password, e-mail, and user ID
					$password = password_hash($_COOKIE['TroopTrackerPassword'], PASSWORD_DEFAULT);
					$statement2 = $conn->prepare("UPDATE troopers SET password = ?, email = ?, user_id = ? WHERE id = ?");
					$statement2->bind_param("ssii", $password, $forumLogin['user']['email'], $forumLogin['user']['user_id'], $db->id);
					$statement2->execute();
				}

				// Check if banned
				if(isset($forumLogin['success']) && $forumLogin['user']['is_banned'] == 1) { break; }

				// Check credentials and make sure trooper still has access
				if((isset($forumLogin['success']) && $forumLogin['success'] == 1 || (password_verify($_POST['password'], $db->password) && $db->permissions == 1)) && canAccess($db->id))
				{
					// Set session
					$_SESSION['id'] = $db->id;
					$_SESSION['tkid'] = $db->tkid;
					
					// Set success
					$failCheck = false;
				}
			}
		}
		
		// Something wrong happened, delete cookie
		if($failCheck)
		{
			// Destroy cookies
			setcookie("TroopTrackerUsername", "", time() - 3600);
			setcookie("TroopTrackerPassword", "", time() - 3600);
		}
	}
}

// Enable output buffering
ob_start();

// If logged in, update active status
if(loggedIn())
{
	$statement = $conn->prepare("UPDATE troopers SET last_active = NOW() WHERE id = ?");
	$statement->bind_param("i", $_SESSION['id']);
	$statement->execute();
}

?>
