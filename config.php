<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set time zone
date_default_timezone_set("America/New_York");

// Unlimited time to execute
ini_set('max_execution_time', '0');
set_time_limit(0);

// PHP Mail namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Twitter namespace
use DG\Twitter\Twitter;

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

// Start session
session_start();

// Connect to server
$conn = new mysqli(dbServer, dbUser, dbPassword, dbName);
 
// Check connection to server
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

// Main costume string
$mainCostumes = "'501st: N/A', '501st: Command Staff', '501st: Handler'";

// formatTime: Changes the time to timezone
function formatTime($date, $format)
{
	$datetime = new DateTime($date, new DateTimeZone('UTC'));
	$datetime->setTimezone(new DateTimeZone('America/New_York'));
	return $datetime->format($format);
}

// randomTip: Returns random tip
function dailyTip()
{
	// Get a random number
	$randomNumber = rand(0, 14);

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

		default:
			$tip = 'Did you know you can add a shortcut on your phone? <a href="https://www.youtube.com/watch?v=_UhtyHbL8uY" target="_blank">IOS</a> / <a href="https://www.youtube.com/watch?v=S4Xu_N4ByBs" target="_blank">Android</a>';
			$link = '';
		break;
	}

	// If link set
	if($link != "")
	{
		return '<p style="text-align: center;"><a href="'.$link.'" target="_blank"><b>TIP:</b> ' . $tip . '</a></p>';
	}
	else
	{
		return '<p style="text-align: center;"><b>TIP:</b> ' . $tip . '</p>';
	}
}

// showCalendarLinks: Returns links to add event to calendar
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
	<p style="text-align: center;">
		<b>Add to calendar:</b>
		<br />
		<a href="'.$link->google().'" target="_blank"><img src="images/google.png" alt="Google Calendar" /></a> <a href="'.$link->yahoo().'" target="_blank"><img src="images/yahoo.png" alt="Yahoo Calendar" /></a> <a href="'.$link->webOutlook().'" target="_blank"><img src="images/outlook.png" alt="Outlook Calendar" /></a> <a href="'.$link->ics().'" target="_blank"><img src="images/ics.png" alt="ICS Calendar" /></a>
	</p>';
}

// displaySquadLinks: Returns links for each garrison for troop tracker
function displaySquadLinks($squadLink)
{
	global $squadArray;
	
	// Return var
	$returnVar = '';
	
	// Set count
	$squadID = 1;
	
	// Set up garrison link
	$returnVar .= addSquadLink(0, $squadLink, "All");
	
	// Loop through squads
	foreach($squadArray as $squad => $squad_value)
	{
		// Add to return var
		$returnVar .= 
		' | ' . addSquadLink($squadID, $squadLink, $squad_value['name']);
		
		// Increment
		$squadID++;
	}
	
	return $returnVar;
}

// getTroopCounts: Returns the users total troop counts for each club
function getTroopCounts($id)
{
	global $conn, $dualCostume, $clubArray, $squadArray;

	// Set up string
	$troopCountString = "";

	// Get troop counts - 501st
	$count = $conn->query("SELECT event_sign_up.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND event_sign_up.status = '3' AND event_sign_up.trooperid = '".$id."' AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '".$dualCostume."' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);

	// Add to string
	$troopCountString .= '
	<p><b>501st Troops:</b> '.number_format($count->num_rows).'</p>';

	// Set up Squad ID
	$clubID = count($squadArray) + 1;
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Count query
		$count = $conn->query("SELECT event_sign_up.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND event_sign_up.status = '3' AND event_sign_up.trooperid = '".$id."' AND ".getCostumeQueryValues($clubID)." GROUP BY events.id, event_sign_up.id") or die($conn->error);

		// Add to string
		$troopCountString .= '
		<p><b>'.$club_value['name'].' Troops:</b> '.number_format($count->num_rows).'</p>';

		// Increment club ID
		$clubID++;
	}

	// Get total count
	$count_total = $conn->query("SELECT id FROM event_sign_up WHERE trooperid = '".$id."' AND status = '3'");

	// Get favorite costume
	$favoriteCostume_get = $conn->query("SELECT costume, COUNT(*) FROM event_sign_up WHERE trooperid = '".$id."' AND costume != 706 AND costume != 720 AND costume != 721 GROUP BY costume ORDER BY COUNT(costume) DESC LIMIT 1") or die($conn->error);
	$favoriteCostume = mysqli_fetch_array($favoriteCostume_get);

	// Get total money raised
	$moneyRaised_get = $conn->query("SELECT SUM(moneyRaised) FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$id."'") or die($conn->error);
	$moneyRaised = mysqli_fetch_array($moneyRaised_get);

	// Prevent notice error
	if($favoriteCostume == "")
	{
		$favoriteCostume['costume'] = 0;
	}

	// Add to string
	$troopCountString .= '
	<p><b>Total Finished Troops:</b> ' . number_format($count_total->num_rows) . '</p>
	<p><b>Favorite Costume:</b> '.ifEmpty(getCostume($favoriteCostume['costume']), "N/A").'</p>
	<p><b>Money Raised:</b> $'.number_format($moneyRaised[0]).'</p>';

	// Return
	return $troopCountString;
}

// showSquadButtons: Returns garrison and squad images on front page
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

// squadSelectList: Returns options for select tag of squads
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

// costume_restrict_query: Restricts the trooper's costume based on there membership
function costume_restrict_query($addWhere = false)
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

	// Club detected
	$hit = false;

	// Count dual costume hit
	$dualHit = 0;
	
	$query = "SELECT * FROM troopers WHERE id = '".cleanInput($_SESSION['id'])."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// 501
			if($db->p501 == 1 || $db->p501 == 2)
			{
				$returnQuery .= "costumes.club = 0";
				
				// Set
				$hit = true;

				// Add to dual count
				$dualHit++;
			}

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Check club member status
				if($db->{$club_value['db']} == 1 || $db->{$club_value['db']} == 2)
				{
					// If club from previous hit, add OR
					if($hit)
					{
						$returnQuery .= " OR ";
						$hit = false;
					}

					// Set even check
					$i = 1;

					foreach($club_value['costumes'] as $costume)
					{
						// Don't add dual costume yet
						if($costume != $dualCostume)
						{
							// Add costome to query
							$returnQuery .= "costumes.club = ".$costume."";

							// Increment even count
							$i++;

							if($i % 2 == 0)
							{
								$returnQuery .= " OR ";
							}
						}
						else
						{
							$dualHit++;
						}
					}

					// Set hit
					$hit = true;
				}
			}
			
			// Check if dual hit has been hit at least twice
			if($dualHit >= 2)
			{	
				$returnQuery .= "costumes.club = ".$dualCostume."";
			}
			else
			{
				// Trim query
				$returnQueryCheck = substr($returnQuery, -3);

				// If ends with OR, trim off
				if($returnQueryCheck == "OR ")
				{
					$returnQuery = substr($returnQuery, 0, -3);
				}
			}
		}
	}
	
	$returnQuery .= ")";
	
	return $returnQuery;
}

// showBBcodes: Converts text to BB Code
function showBBcodes($text)
{
	$text = strip_tags($text);

	// BBcode array
	$find = array(
		'~\[b\](.*?)\[/b\]~s',
		'~\[i\](.*?)\[/i\]~s',
		'~\[u\](.*?)\[/u\]~s',
		'~\[quote\](.*?)\[/quote\]~s',
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
		'<pre>$1</'.'pre>',
		'<a href="#comment_$4">$2 - $3</a><br /><span class="quotec">$5</'.'span><br />',
		'<span style="font-size:$1px;">$2</span>',
		'<span style="color:$1;">$2</span>',
		'<a href="$1">$1</a>',
		'<img src="$1" alt="" />'
	);

	// Replacing the BBcodes with corresponding HTML tags
	return preg_replace($find,$replace,$text);
}

// countDonations: Count the number of donations between the two dates - can specify a user
function countDonations($trooperid = "*", $dateStart = "1900-12-1", $dateEnd = "9999-12-1")
{
	global $conn;
	
	// Query
	if($trooperid != "*")
	{
		// Trooper ID specified
		$getNumOfDonators = $conn->query("SELECT * FROM donations WHERE trooperid = ".$trooperid." AND datetime > '".$dateStart."' AND datetime < '".$dateEnd."'");
	}
	else
	{
		// Trooper ID not specified - wild card
		$getNumOfDonators = $conn->query("SELECT * FROM donations WHERE datetime > '".$dateStart."' AND datetime < '".$dateEnd."'");
	}
	
	// Return rows
	return $getNumOfDonators->num_rows;
}

// drawSupportBadge: A function that draws a support badge if the user is a supporter
function drawSupportBadge($id)
{
	global $conn;
	
	// Set up value
	$value = "";
	
	// Get data
	$query = "SELECT supporter FROM troopers WHERE id = '".$id."' AND supporter = '1'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set
			$value = '<img src="images/FLGHeart_small.png" width="32px" height="32px" /><br />';
		}
	}
	
	// Return
	return $value;
}

// drawSupportGraph: A function that draws a visual graph for troopers to see what we need to support the garrison
function drawSupportGraph()
{
	global $conn;
	
	// Set return value
	$return = "";
	
	// Check if user is logged in and don't show for command staff
	if(loggedIn())
	{
		// Count number of troopers supporting
		$getNumOfSupport = $conn->query("SELECT SUM(amount) FROM donations WHERE datetime > date_add(date_add(LAST_DAY(NOW()),interval 1 DAY),interval -1 MONTH)");
		$getSupportNum = $getNumOfSupport->fetch_row();
		
		// Count times contributed
		$didSupportCount = $conn->query("SELECT trooperid FROM donations WHERE datetime > date_add(date_add(LAST_DAY(NOW()),interval 1 DAY),interval -1 MONTH) AND trooperid = '".$_SESSION['id']."'");
		
		// Get goal from site settings
		$getGoal = $conn->query("SELECT supportgoal FROM settings");
		$getGoal_value = $getGoal->fetch_row();
		
		// Set goal
		$goal = $getGoal_value[0];
		
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
			$percent = floor(($getSupportNum[0]/$goal) * 100);
			
			// Don't allow over 100
			if($percent > 100)
			{
				$percent = 100;
			}
			
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
			
			<h2 class="tm-section-header">'.date("F").' - Donation Goal</h2>
			
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
				if($didSupportCount->num_rows == 0)
				{
					// If not 100%, show learn more
					if($percent != 100)
					{
						$return .= '
						<p style="text-align: center;">
							<a href="index.php?action=donation">The '.garrison.' needs your support! Click here to learn more.</a>
						</p>';
					}
					else
					{
						// Reached 100%
						$return .= '
						<p style="text-align: center;">
							<a href="index.php?action=donation">Thank you for helping the garrison reach it\'s goal! Click here to help contribute.</a>
						</p>';
					}
				}
				else
				{
					// Did support
					$return .= '
					<p style="text-align: center;">
						<a href="index.php?action=donation">Thank you for your contribution! Click here to help contribute further.</a>
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

// getAuthForum: Get's auth data from Xenforo
function getAuthForum($user_id)
{
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/auth/login-token",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "user_id=" . $user_id,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// loginWithForum: Login the trooper with there xenforo credentials
function loginWithForum($username, $password)
{
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/auth",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "login=" . $username . "&password=" . $password . "",
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// createThread: Create's a thread in Xenforo
function createThread($id, $title, $message)
{
	// Create Thread
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/threads",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "node_id=" . $id . "&title=" . $title . "&message=" . $message,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// editThread: Locks a thread in Xenforo
function lockThread($id)
{
	// Edit Thread
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/threads/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "discussion_open=" . false,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// createPost: Create's a post in Xenforo
function createPost($id, $message)
{
	// Create Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/posts",
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "thread_id=" . $id . "&message=" . $message,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// editPost: Edits a post in Xenforo
function editPost($id, $message)
{
	// Edit Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/posts/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "message=" . $message,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// getUserForum: Get's Xenforo forum user by username
function getUserForum($username)
{
	// Get user forum info by forum name
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/users/find-name&username=" . $username,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// getUserForumID: Get's Xenforo forum user by ID
function getUserForumID($id)
{
	// Get user forum info by forum ID
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/users/" . $id,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// updateUserForumGroup: Update's user forum groups by ID
function updateUserForumGroup($id, $groupid, $group_ids)
{
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
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/users/" . $id,
	  CURLOPT_POST => 1,
	  CURLOPT_POSTFIELDS => "user_group_id=" . $groupid . "&" . $groupString,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// deletePost: Deletes a post in Xenforo
function deletePost($id, $hard_delete = false)
{
	// Delete Post
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/posts/" . $id,
	  CURLOPT_CUSTOMREQUEST => "DELETE",
	  CURLOPT_POSTFIELDS => "hard_delete=" . $hard_delete,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// deleteThread: Deletes thread in Xenforo
function deleteThread($id, $hard_delete = false)
{
	// Delete Thread
	$curl = curl_init();

	curl_setopt_array($curl, [
	  CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/threads/" . $id,
	  CURLOPT_CUSTOMREQUEST => "DELETE",
	  CURLOPT_POSTFIELDS => "hard_delete=" . $hard_delete,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_HTTPHEADER => [
	    "XF-Api-Key: " . xenforoAPI_superuser,
	  ],
	]);

	$response = curl_exec($curl);

	curl_close($curl);

	return json_decode($response, true);
}

// isSupporter: A function to determine if a trooper is a supporter
function isSupporter($id)
{
	global $conn;
	
	// Set up value
	$value = 0;
	
	// Get data
	$query = "SELECT supporter FROM troopers WHERE id = '".$id."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set
			$value = $db->supporter;
		}
	}
	
	// Return
	return $value;
}

// getRebelLegionUser: A function that returns a troopers Rebel Legion forum username
function getRebelLegionUser($id)
{
	global $conn;
	
	// Set up value
	$forumName = "";
	
	// Get data
	$query = "SELECT rebelforum FROM troopers WHERE id = '".$id."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set
			$forumName = $db->rebelforum;
		}
	}
	
	// Return
	return $forumName;
}

// getRebelInfo: A function which returns an array of info about trooper - Rebel Legion
function getRebelInfo($forumid)
{
	global $conn;
	
	// Setup array
	$array = [];
	$array['id'] = '';
	$array['name'] = '';
	
	// Get data
	$query = "SELECT * FROM rebel_troopers WHERE rebelforum = '".$forumid."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
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

// getMandoLegionUser: A function that returns a troopers Mando Mercs CAT #
function getMandoLegionUser($id)
{
	global $conn;
	
	// Set up value
	$mandoid = 0;
	
	// Get data
	$query = "SELECT mandoid FROM troopers WHERE id = '".$id."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set
			$mandoid = $db->mandoid;
		}
	}
	
	// Return
	return $mandoid;
}

// getMandoInfo: A function which returns an array of info about trooper - Mando Mercs
function getMandoInfo($mandoid)
{
	global $conn;
	
	// Setup array
	$array = [];
	$array['id'] = '';
	$array['name'] = '';
	
	// Get data
	$query = "SELECT * FROM mando_troopers WHERE mandoid = '".$mandoid."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
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

// getSGUser: A function that returns a troopers SG #
function getSGUser($id)
{
	global $conn;
	
	// Set up value
	$sgid = 0;
	
	// Get data
	$query = "SELECT sgid FROM troopers WHERE id = '".$id."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set
			$sgid = $db->sgid;
		}
	}
	
	// Return
	return $sgid;
}

// getSGINfo: A function which returns an array of info about trooper - Saber Guild
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
	$query = "SELECT * FROM sg_troopers WHERE sgid = 'SG-".$sgid."'";
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
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

// get501Info: A function which returns an array of info about trooper - 501st
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
		$query = "SELECT * FROM 501st_troopers WHERE legionid = '".$id."'";
		
		// Run query...
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$array['link'] = $db->link;
			}
		}
	}
	
	// Return
	return $array;
}

// getMyRebelCostumes: A function which returns a string of costumes assigned to user in synced database - Rebel Legion
function getMyRebelCostumes($id)
{
	global $conn;
	
	// Setup string
	$costume = "";
	
	// Get data
	$query = "SELECT costumename FROM rebel_costumes WHERE rebelid = '".$id."'";
	
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

// getMyCostumes: A function which returns a string of costumes assigned to user in synced database
function getMyCostumes($id, $squad)
{
	global $conn, $squadArray;
	
	// Setup string
	$costume = "";
	
	// Check if 501st member
	if($squad <= count($squadArray))
	{
		// Get data
		$query = "SELECT costumename FROM 501st_costumes WHERE legionid = '".$id."'";
		
		// Run query...
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$costume .= ", '501st: " . $db->costumename . "'";
			}
		}
	}
	
	// Return
	return $costume;
}

// showRebelCostumes: A function which displays all the users costumes in synced database - Rebel Legion
function showRebelCostumes($id)
{
	global $conn;
	
	// Get data
	$query = "SELECT * FROM rebel_costumes WHERE rebelid = '".$id."'";
	
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

// showMandoCostumes: A function which displays all the users costumes in synced database - Mando Mercs
function showMandoCostumes($id)
{
	global $conn;
	
	// Get data
	$query = "SELECT * FROM mando_costumes WHERE mandoid = '".$id."'";
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
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

// showSGCostumes: A function which displays all the users costumes in synced database - Saber Guild
function showSGCostumes($id)
{
	global $conn;
	
	// Get data
	$query = "SELECT * FROM sg_troopers WHERE sgid = 'SG-".$id."'";
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
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

// showDroids: A function which displays all the users droids in synced database - Droid Builders
function showDroids($forum)
{
	global $conn;
	
	// Get data
	$query = "SELECT * FROM droid_troopers WHERE forum_id = '".$forum."'";
	
	// Set up count
	$i = 0;
	
	// Run query...
	if ($result = mysqli_query($conn, $query))
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

// showCostumes: A function which displays all the users costumes in synced database
function showCostumes($id, $squad)
{
	global $conn, $squadArray;
	
	// Get data
	$query = "SELECT * FROM 501st_costumes WHERE legionid = '".$id."'";
	
	// Set up count
	$i = 0;
	
	// Check if 501st member
	if($squad <= count($squadArray))
	{
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

// postTweet: Posts a tweet to Twitter (FLGUPDATES)
function postTweet($message)
{
	// Credentials
	$twitter = new Twitter(consumerKey, consumerSecret, accessToken, accessTokenSecret);

	try
	{
		// Send tweet
		$tweet = $twitter->send($message);
	}
	catch (DG\Twitter\TwitterException $e)
	{
		// Do nothing
	}
}


// squadToDiscord: Converts squad ID to Discord
function squadToDiscord($squad)
{
	if($squad == 1)
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
		return 'Florida Garrison';
	}
}

// sendEventNotifty: Send's a notification to the event channel
function sendEventNotify($id, $name, $description, $squad)
{
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
	            "url" => "https://www.fl501st.com/troop-tracker/index.php?event=" . $id,

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

// getSquadName: Returns the squad name / club name
function getSquadName($value)
{
	global $squadArray, $clubArray;
	
	// Set return value
	$returnValue = "";
	
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

// getCostumeQueryValues: Returns query for costume values for club
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

// isImportant: Is the comment important? If so, we highlight it
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

// convertDataToJSON: Converts a query to JSON
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

// hasAward: Does the trooper have this award
function hasAward($trooperid, $awardid, $echo = false, $remove = false)
{
	global $conn;
	
	// Get data
	$query = "SELECT * FROM award_troopers WHERE trooperid = '".$trooperid."' AND awardid = '".$awardid."'";

	// Set up return variable
	$hasAward = false;
	
	if ($result = mysqli_query($conn, $query))
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

// hasTitle: Does the trooper have this title
function hasTitle($trooperid, $awardid, $echo = false, $remove = false)
{
	global $conn;
	
	// Get data
	$query = "SELECT * FROM title_troopers WHERE trooperid = '".$trooperid."' AND titleid = '".$awardid."'";

	// Set up return variable
	$hasTitle = false;
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Set
			$hasTitle = true;
		}
	}
	
	// Does not print
	if(!$echo)
	{
		return $hasTitle;
	}
	else
	{
		// Does not have title
		if($hasTitle && !$remove)
		{
			return 'style = "display: none;"';
		}
		else if(!$hasTitle && $remove)
		{
			return 'style = "display: none;"';
		}
		else
		{
			return '';
		}
	}
}

// sendNotification: Sends a notification to the log
function sendNotification($message, $trooperid, $type = 0, $json = "")
{
	global $conn;

	// 0 - N/A
	// 1 - Add Costume
	// 2 - Delete Costume
	// 3 - Edit Costume
	// 4 - Delete Award
	// 5 - Add Award
	// 6 - Give Award Trooper
	// 7 - Edit Award
	// 8 - Deny Trooper
	// 9 - Approve Trooper
	// 10 - Delete Trooper
	// 11 - Update Trooper
	// 12 - Add Trooper
	// 13 - Add Event
	// 14 - Edit Event
	// 15 - Add Trooper To Event
	// 16 - Delete Event
	// 17 - Set Charity
	// 18 - Remove Trooper From Event
	// 19 - Add Shift From Edit
	// 20 - Add Title
	// 21 - Delete Title
	// 22 - Give Title
	// 23 - Edit Title
	// 24 - Remove Title
	// 25 - Remove Award
	
	$conn->query("INSERT INTO notifications (message, trooperid, type, json) VALUES ('".$message."', '".$trooperid."', '".$type."', '".$json."')");
}

// troopCheck: Checks the troop counts of all clubs
function troopCheck($id)
{
	global $conn, $clubArray, $squadArray;
	
	// Notify how many troops did a trooper attend - 501st
	$trooperCount_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE trooperid = '".$id."' AND status = '3' AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR EXISTS(SELECT events.id, events.oldid FROM events WHERE events.oldid != 0 AND events.id = event_sign_up.troopid))") or die($conn->error);
	$count = $trooperCount_get->fetch_row();
	
	// 501st
	checkTroopCounts($count[0], "501ST: " . getName($id) . " now has [COUNT] troop(s)", $id, "501ST");
	
	// Set club ID
	$clubID = count($squadArray) + 1;
	
	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Notify how many troops did a trooper attend of club
		$trooperCount_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE trooperid = '".$id."' AND status = '3' AND ".getCostumeQueryValues($clubID)."") or die($conn->error);
		$count = $trooperCount_get->fetch_row();
		
		// Check troop count of club
		checkTroopCounts($count[0], strtoupper($club_value['name']) . ": " . getName($id) . " now has [COUNT] troop(s)", $id, strtoupper($club_value['name']));
		
		// Increment club count
		$clubID++;
	}
}

// checkTroopCounts: Checks the troop counts, and puts the information into notifications
function checkTroopCounts($count, $message, $trooperid, $club)
{
	global $conn;
	
	// Counts to check
	$counts = [1, 10, 25, 50, 75, 100, 150, 200, 250, 300, 400, 500, 501];
	
	// Search notifications for previous notifications, so we don't duplicate - check message for club name
	$query = "SELECT * FROM notifications WHERE trooperid = '".$trooperid."' AND message LIKE '%".$club."%'";
	if ($result = mysqli_query($conn, $query))
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
	
	$theme = "florida";

	if(loggedIn())
	{
		$query = "SELECT theme FROM troopers WHERE id = '".$_SESSION['id']."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				switch($db->theme)
				{
					case 0:
						$theme = "florida";
					break;
					
					case 1:
						$theme = "everglades";
					break;
					
					case 2:
						$theme = "makaze";
					break;
					
					case 3:
						$theme = "parjai";
					break;
				}
			}
		}
	}
	
	return $theme;
}

// getEventTitle: gets event title
function getEventTitle($id, $link = false)
{
	global $conn;
	
	$query = "SELECT * FROM events WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($link)
			{
				return '<a href=\'index.php?event='. $db->id .'\'>' . $db->name . '</a>';
			}
			else
			{
				return $db->name;
			}
		}
	}
}

// getEventThreadID: Gets event thread ID on forum
function getEventThreadID($id)
{
	global $conn;
	
	$query = "SELECT * FROM events WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->thread_id;
		}
	}
}

// getEventPostID: Gets event post ID on forum
function getEventPostID($id)
{
	global $conn;
	
	$query = "SELECT * FROM events WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->post_id;
		}
	}
}

// getCommentPostID: Gets comment post ID on forum
function getCommentPostID($id)
{
	global $conn;
	
	$query = "SELECT * FROM comments WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->post_id;
		}
	}
}

// loginWithTKID: Converts TK number into squad
function loginWithTKID($tkid)
{
	global $clubArray, $squadArray, $conn;
	
	// Set club count
	$clubCount = 0;
	
	// Check if in club
	$inClub = false;
	
	// Set squad return
	$squad = 0;
	
	// Loop through squads
	foreach($clubArray as $club => $club_value)
	{
		// Get first letter of club
		$firstLetter = strtoupper(substr($club, 0, 1));
		
		// Check if ID starts with a club
		if(substr($tkid, 0, 1) === $firstLetter)
		{
			// Set club
			$squad = count($squadArray) + ($clubCount) + 1;
			
			// Set club check
			$inClub = true;
		}
		
		// Increment
		$clubCount++;
	}
	
	// If not in club, set default
	if(!$inClub)
	{
		if(substr($tkid, 0, 2) === 'TK')
		{
			// Remove TK
			$tkid = substr($tkid, 2);
		}
		
		// Get squad from database
		$getSquad = $conn->query("SELECT squad FROM troopers WHERE tkid = '".$tkid."'");
		$getSquad_value = $getSquad->fetch_row();
		
		// To prevent warnings, make sure value is set
		if(isset($getSquad_value[0]))
		{
			// Set squad
			$squad = $getSquad_value[0];
		}
	}
	
	// Return
	return $squad;
}

// removeLetters: Removes letters from string
function removeLetters($string)
{
	return preg_replace('/[^0-9,.]+/', '', $string);
}

// readTKNumber: Converts other club ID numbers to a readable format
function readTKNumber($tkid, $squad)
{
	global $conn, $clubArray, $squadArray;
	
	// Is the trooper in a club?
	$inClub = false;

	// Based on squad ID, is the trooper in a club
	if($squad > count($squadArray))
	{
		// Get first letter of club
		$firstLetter = strtoupper(substr(getSquadName($squad), 0, 1));
		
		// Set TKID return
		$tkid = $firstLetter . $tkid;
		
		// Set inClub
		$inClub = true;
	}
	
	// If not in club, set default
	if(!$inClub)
	{
		$prefix = "TK";
		
		// Get TK prefix from database
		$getPrefix = $conn->query("SELECT prefix FROM 501st_costumes WHERE legionid = '".$tkid."' LIMIT 1");
		$getPrefix_value = $getPrefix->fetch_row();
		
		// Make sure TK prefix was found
		if(isset($getPrefix_value[0]) && $getPrefix_value[0] != "")
		{
			$prefix = $getPrefix_value[0];
		}
		
		$tkid = $prefix . $tkid;
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
function getTKNumber($id, $read = false)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
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
				return readTKNumber($db->tkid, $db->squad);
			}
		}
	}
}

// getIDFromTKNumber: gets ID from TK numbers (501st only)
function getIDFromTKNumber($tkid)
{
	global $conn, $squadArray;
	
	$query = "SELECT * FROM troopers WHERE tkid='".$tkid."' AND squad <= ".count($squadArray)."";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->id;
		}
	}
}

// getTrooperSquad: gets squad of trooper
function getTrooperSquad($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->squad;
		}
	}
}

// getTrooperForum: gets forum of trooper
function getTrooperForum($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->forum_id;
		}
	}
}

// getCostumeClub: gets the costumes club
function getCostumeClub($id)
{
	global $conn;
	
	$query = "SELECT * FROM costumes WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->club;
		}
	}
}

// profileTop: Display's user information at top of profile page
function profileTop($id, $tkid, $name, $squad, $forum, $phone)
{
	global $conn, $squadArray, $clubArray;
	
	// Command Staff Edit Link
	if(isAdmin())
	{
		echo '
		<h2 class="tm-section-header">Admin Controls</h2>
		<p style="text-align: center;"><a href="index.php?action=commandstaff&do=managetroopers&uid='.$id.'">Edit/View Member in Command Staff Area</a></p>';
	}
	
	// Only show 501st thumbnail, if a 501st member
	if(getTrooperSquad($tkid) <= count($squadArray))
	{
		// Get 501st thumbnail Info
		$thumbnail_get = $conn->query("SELECT thumbnail FROM 501st_troopers WHERE legionid = '".$tkid."'");
		$thumbnail = $thumbnail_get->fetch_row();
	}
	
	// Get Rebel Legion thumbnail info
	$thumbnail_get_rebel = $conn->query("SELECT costumeimage FROM rebel_costumes WHERE rebelid = '".getRebelInfo(getRebelLegionUser(cleanInput($id)))['id']."' LIMIT 1");
	$thumbnail_rebel = $thumbnail_get_rebel->fetch_row();
	
	echo '
	<h2 class="tm-section-header">'.$name.' - '.readTKNumber($tkid, $squad).'</h2>';
	
	// Avatar
	
	// Does have a avatar?
	$haveAvatar = false;
	
	// 501
	if(isset($thumbnail[0]))
	{
		echo '
		<p style="text-align: center;">
			<img src="'.$thumbnail[0].'" />
		</p>';
		
		// Set
		$haveAvatar = true;
	}
	
	// Rebel
	if(isset($thumbnail_rebel[0]))
	{
		echo '
		<p style="text-align: center;">
			<img src="'.str_replace("-A", "sm", $thumbnail_rebel[0]).'" />
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

	// Title ranks
	$query2 = "SELECT title_troopers.titleid, title_troopers.trooperid, titles.id, titles.title, titles.icon FROM title_troopers LEFT JOIN titles ON titles.id = title_troopers.titleid WHERE title_troopers.trooperid = '".cleanInput($_GET['profile'])."'";
	if ($result2 = mysqli_query($conn, $query2))
	{
		while ($db2 = mysqli_fetch_object($result2))
		{
			echo '
			<p style="text-align: center;">
				<img src="images/ranks/'.$db2->icon.'" />
			</p>';
		}
	}
	
	// Ranks for members
	$query2 = "SELECT * FROM troopers WHERE id = '".cleanInput($_GET['profile'])."'";
	if ($result2 = mysqli_query($conn, $query2))
	{
		while ($db2 = mysqli_fetch_object($result2))
		{
			echo '
			<div style="text-align: center;">';
			
			// 501
			// Active
			if($db2->p501 == 1)
			{
				echo '
				<p>
					<img src="images/ranks/legion_member.png" />
				</p>';
			}
			// Reserve
			else if($db2->p501 == 2)
			{
				echo '
				<p>
					<img src="images/ranks/legion_reserve.png" />
				</p>';
			}
			// Retired
			else if($db2->p501 == 3)
			{
				echo '
				<p>
					<img src="images/ranks/legion_retired.png" />
				</p>';
			}

			// Set up squad count
			$squadCount = 1;

			// Loop through clubs
			foreach($squadArray as $squad => $squad_value)
			{
				// Check
				if($db2->squad == $squadCount)
				{
					echo '
					<p>
						<img src="images/ranks/'.$squad_value['rankRegular'].'" />
					</p>';
				}

				// Increment
				$squadCount++;
			}

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Check rank
				if($db2->{$club_value['db']} == 1)
				{
					echo '
					<p>
						<img src="images/ranks/'.$club_value['rankRegular'].'" />
					</p>';
				}
				else if($db2->{$club_value['db']} == 2)
				{
					echo '
					<p>
						<img src="images/ranks/'.$club_value['rankReserve'].'" />
					</p>';
				}
				else if($db2->{$club_value['db']} == 3)
				{
					echo '
					<p>
						<img src="images/ranks/'.$club_value['rankRetired'].'" />
					</p>';
				}
			}
			
			echo '
			</div>';
		}
	}
	
	echo '
	<p style="text-align: center;"><a href="https://www.fl501st.com/boards/memberlist.php?mode=viewprofile&un='.urlencode($forum).'" target="_blank">View Boards Profile</a></p>';
	
	if(isAdmin() && $phone != "")
	{
		echo '
		<p style="text-align: center;"><b>Phone Number:</b><br />'.formatPhoneNumber($phone).'</p>';
	}
}

// formatPhoneNumber: Show the phone number properly
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

// profileExist: get's if user exists
function profileExist($id)
{
	global $conn;
	
	// Set up return var
	$doesExist = false;
	
	$query = "SELECT * FROM troopers WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Found
			$doesExist = true;
		}
	}
	
	// Return
	return $doesExist;
}

// getUserID: gets the user's ID Xenforo Forum
function getUserID($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id='".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->user_id;
		}
	}
}

// getName: gets the troopers's name
function getName($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->name;
		}
	}
}

// getEmail: gets the troopers's e-mail
function getEmail($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->email;
		}
	}
}

// getPhone: gets the user's phone
function getPhone($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->phone;
		}
	}
}

// getSquadID: gets the user's squad
function getSquadID($id)
{
	global $conn;
	
	$query = "SELECT * FROM troopers WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			return $db->squad;
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
		return '<span style="word-wrap: break-word;"><a href="'.addHttp($url).'" target="_blank">'.$url.'</a></span>';
		
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
	else if(is_null($value))
	{
		return $message;
	}
	else
	{
		return $value;
	}
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
				
				if(loggedIn() && !hasPermission(1))
				{
					session_destroy();
				}
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
function doesTKExist($tk, $squad = 0)
{
	global $conn, $squadArray;
	
	// Set up variables
	$exist = false;
	
	// If a 501st squad
	if($squad <= count($squadArray))
	{
		$query = "SELECT * FROM troopers WHERE tkid = '".$tk."' AND squad <= ".count($squadArray)."";
	}
	else
	{
		// If a club
		$query = "SELECT * FROM troopers WHERE rebelforum = '".$tk."' AND squad = ".$squad."";
	}

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
function isTKRegistered($tk, $squad = 0)
{
	global $conn, $squadArray;
	
	// Set up variables
	$registered = false;
	
	// If a 501st squad
	if($squad <= count($squadArray))
	{
		$query = "SELECT * FROM troopers WHERE tkid = '".$tk."' AND squad <= ".count($squadArray)."";
	}
	else
	{
		// If a club
		$query = "SELECT * FROM troopers WHERE rebelforum = '".$tk."' AND squad = ".$squad."";
	}

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

// cleanInput: Prevents hack by cleaning input
function cleanInput($value)
{
	$value = strip_tags(addslashes($value));
	return $value;
}

// sendEventUpdate: Send's an e-mail (if subscribed) to user
function sendEventUpdate($troopid, $trooperid, $subject, $message)
{
	global $conn;

	// Add footer to message
	$message = $message . "https://www.fl501st.com/troop-tracker/index.php?event=".$troopid."\n\nYou can opt out of e-mails under: \"Manage Account\"\n\nhttps://trooptracking.com\n\nTo turn off this notification, go to the event page, and press the \"Unsubscribe\" button.";

	// Query database for trooper information and make sure they are subscribed to e-mail
	$query = "SELECT troopers.email, troopers.name, troopers.subscribe FROM troopers LEFT JOIN event_notifications ON troopers.id = event_notifications.trooperid WHERE event_notifications.troopid = '".$troopid."' AND troopers.subscribe = '1' AND troopers.email != ''";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			@sendEmail($db->email, $db->name, $subject, $message);
		}
	}
}

// sendEmail: Send's an e-mail to specified user
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

// troopersRemaining: Returns the number of troopers remaining
function troopersRemaining($value1, $value2)
{
	// Subtract values
	$remaining = $value1 - $value2;
	
	// Return remaining
	return '<b>' . $remaining . ' spots remaining.</b>';
}

// eventClubCount: Returns number of troopers signed up for this event based on costume
function eventClubCount($eventID, $clubID)
{
	global $conn, $clubArray, $dualCostume;
	
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
	
	// Set up return number
	$returnVal = 0;

	// Query database for roster info
	$query = "SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, troopers.id AS trooperId, troopers.name, troopers.tkid FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".$eventID."' AND status != '1' AND status != '4' AND status != '6'";

	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Query costume database to add to club counts
			$query2 = "SELECT * FROM costumes WHERE id = '".$db->costume."'";
			if ($result2 = mysqli_query($conn, $query2))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// 501st
					if($db2->club == 0)
					{
						$c501++;
					}
					
					// Loop through clubs
					foreach($clubArray as $club => $club_value)
					{
						// Loop through costumes
						foreach($club_value['costumes'] as $costume)
						{
							// Club
							if($db2->club == $costume)
							{
								// Increment to club
								${"c" . $club_value['dbLimit']}++;
							}
						}
					}
					
					// Dual costume
					if($db2->club == $dualCostume)
					{
						// Just 501 because it will be added in the loop above as well
						$c501++;
						
						// Add to total
						$total++;
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
		// Loop through costumes
		foreach($club_value['costumes'] as $costume)
		{
			// Make sure not a dual costume
			if($clubID != $dualCostume)
			{
				// If club
				if($clubID == $costume)
				{
					$returnVal = ${"c" . $club_value['dbLimit']};
				}
			}
		}
	}
	
	// Dual costume
	if($clubID == $dualCostume)
	{
		$returnVal = $total;
	}

	// Return
	return $returnVal;
}

// isEventFull: Check to see if the event is full ($eventID = ID of the event, $costumeID = costume they are going to wear)
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

	// Query to get limits
	$query = "SELECT * FROM events WHERE id = '".$eventID."'";

	// Output
	if ($result = mysqli_query($conn, $query))
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

// getPermissionName: Converts value to title string of permission
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

// getClubPermissionName: Converts value to title string of permission
function getClubPermissionName($value, $type = "")
{
	if($value == 0)
	{
		return 'Not A Member';
	}
	else if($value == 1)
	{
		if($type == "sheets")
		{
			return 'Active';
		}
		else
		{
			return 'Regular Member';
		}
	}
	else if($value == 2)
	{
		if($type == "sheets")
		{
			return 'Reserve';
		}
		else
		{
			return 'Reserve Member';
		}
	}
	else if($value == 3)
	{
		if($type == "sheets")
		{
			return 'Retired';
		}
		else
		{
			return 'Retired Member';
		}
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

// canAccess: Determines if a trooper can access the troop tracker to sign up for events
function canAccess($id)
{
	global $conn, $clubArray;
	
	// Set up var
	$canAccess = false;
	
	$query = "SELECT * FROM troopers WHERE id = '".$id."'";
	if ($result = mysqli_query($conn, $query))
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

// emailSettingStatus: Is the setting on or off
function emailSettingStatus($column, $print = false)
{
	global $conn;
	
	// Set status
	$status = 0;
	
	// Get email setting
	$getStatus = $conn->query("SELECT ".$column." FROM troopers WHERE id = '".$_SESSION['id']."'");
	$getStatus_get = $getStatus->fetch_row();
	
	// Set status to query
	$status = $getStatus_get[0];
	
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

// isLink: Is this a linked event?
function isLink($id)
{
	global $conn;
	
	// Set link
	$link = 0;
	
	// Get number of events with link
	$getNumOfLinks = $conn->query("SELECT id FROM events WHERE link = '".$id."'");
	
	// Get link ID
	$getLinkID = $conn->query("SELECT link FROM events WHERE id = '".$id."'");
	$getLinkID_get = $getLinkID->fetch_row();
	
	// If has links to event, or is linked, show shift data
	if($getNumOfLinks->num_rows > 0 || $id != 0)
	{
		// If this event is the link
		if($getNumOfLinks->num_rows > 0)
		{
			$link = $id;
		}
		else if($getLinkID_get[0] != 0)
		{
			$link = $getLinkID_get[0];
		}
	}
	
	return $link;
}

// getSheet: Gets the Google Sheet values
// spreadsheetId: ID of spreadsheet (in URL)
// get_range: Sheet Name OR Sheet Name!A1:G3
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

// editSheet: Edit's the Google Sheet
// spreadsheetId: ID of spreadsheet (in URL)
// sheetName: Name of the sheet we want to edit
// columnFrom: The letter of column we want to start editing from
// columnTo: The letter of column we want to stop editing from
// newValues: The new values (array) we want to change the values to
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

// addToSheet: Adds to bottom of Google Sheet
// spreadsheetId: The ID of the spreadsheet
// sheetName: The name of the spreadsheet we want to add to
// newValues: Array of new values to add
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

// deleteSheetRows: Deletes rows from sheet
// spreadsheetId: The ID of the spreadsheet
// sheetID: The ID of the spreadsheet GID in URL
// start: Index to start delete
// end: Index to end delete
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

// get_numerics: Gets the numbers
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
		
		$query = "SELECT * FROM troopers WHERE forum_id = '".$_COOKIE['TroopTrackerUsername']."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Login with forum
				$forumLogin = loginWithForum($_COOKIE['TroopTrackerUsername'], $_COOKIE['TroopTrackerPassword']);

				// If logged in with forum details, and password does not match
				if(isset($forumLogin['success']) && $forumLogin['success'] == 1)
				{
					// Update password, e-mail, and user ID
					$conn->query("UPDATE troopers SET password = '".password_hash(cleanInput($_COOKIE['TroopTrackerPassword']), PASSWORD_DEFAULT)."', email = '".$forumLogin['user']['email']."', user_id = '".$forumLogin['user']['user_id']."', forum_id = '".$forumLogin['user']['username']."' WHERE id = '".$db->id."'");
				}

				// Check credentials and make sure trooper still has access
				if((isset($forumLogin['success']) && $forumLogin['success'] == 1 || password_verify(cleanInput($_POST['password']), $db->password)) && canAccess($db->id))
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
	$conn->query("UPDATE troopers SET last_active = NOW() WHERE id='".$_SESSION['id']."'") or die($conn->error);
}

// Check for events that need to be closed
$query = "SELECT * FROM events WHERE dateEnd < ".date('Y-m-d H:i:s')." - INTERVAL 1 HOUR AND closed != '2' AND closed != '1'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Close them
		$conn->query("UPDATE events SET closed = '1' WHERE id = '".$db->id."'");
	}
}

?>
