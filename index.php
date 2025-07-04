<?php

/**
 * This file is used for displaying the website.
 *
 * @author  Matthew Drennan
 *
 */

// Include config file
include 'config.php';

// Include Scripts
echo '
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Meta Data -->
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />';

	if(isset($_GET['event']) && !loggedIn())
	{
		// Query database for event info
		$statement = $conn->prepare("SELECT * FROM events WHERE id = ?");
		$statement->bind_param("i", $_GET['event']);
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Set up string to add to title if a linked event
				$add = "";
				
				// If this a linked event?
				if(isLink($db->id) > 0)
				{
					$add .= "[" . date("h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "] ";
				}

				echo '
				<meta property="og:title" content="'.$add.''.$db->name.'" />
				<meta property="og:description" content="'.ifEmpty(cleanInput($db->comments)).'" />
				<meta property="og:image" content="'.$trackerURL.'/images/logo.png" />';
			}
		}
	}

	echo '
	<!-- Title -->
	<title>501st '.garrison.' - Troop Tracker</title>
	
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap" rel="stylesheet" />
	
	<!-- Main Style Sheets -->
	<link href="fontawesome/css/all.min.css" rel="stylesheet" />';
	
	echo '
	<!-- Style Sheets -->
	<link href="css/main.css?v=2" rel="stylesheet" />
	<link rel="stylesheet" href="script/lib/jquery-ui.min.css">
	<link rel="stylesheet" href="script/lib/jquery-ui-timepicker-addon.css">
	<link href="css/dropzone.min.css" type="text/css" rel="stylesheet" />
	<link href="css/lightbox.min.css" rel="stylesheet" />
	<link href="css/calendar.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://unpkg.com/balloon-css/balloon.min.css">
	
	<!-- Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	
	<!-- Setup Variable -->
	<script>
	var forumURL = "'.$forumURL.'";
	var placeholder = '.placeholder.';
	var squadIDList = '.json_encode(array_merge([0], array_column($squadArray, 'squadID'))).';
	var clubArray = [';

	/* CHECK IF MEMBER CLUB DB VALUE */

	// Club count
	$clubCount = count($clubArray);

	// Club step
	$i = 0;

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		echo '"'.$club_value['db'].'"';

		// Add comma
		if($i < ($clubCount - 1))
		{
			echo ',';
		}

		// Increment
		$i++;
	}

	echo'];';

	/* DB LIMIT CLUBS */

	// Club step
	$i = 0;

	echo '
	var clubDBLimitArray = [';

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		echo '"'.$club_value['dbLimit'].'"';

		// Add comma
		if($i < ($clubCount - 1))
		{
			echo ',';
		}

		// Increment
		$i++;
	}

	echo'];';

	/* CLUB SPECIAL FORUM VALUE */

	// Club count with value
	$clubCount = array_filter($clubArray, function($x) { return !empty($x['db3Name']); });

	// Club step
	$i = 0;

	echo '
	var clubDB3Array = [';

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		// Don't allow empty result
		if($club_value['db3Name'] != "")
		{
			echo '"'.$club_value['db3'].'"';

			// Add comma
			if($i < count($clubCount) - 1)
			{
				echo ',';
			}

			// Increment
			$i++;
		}
	}

	echo'];';

	echo '
	// Clear limits
	function clearLimit()
	{';

	// Loop through clubs
	foreach($clubArray as $club => $club_value)
	{
		echo '
		$("#'.$club_value['dbLimit'].'").val(500);';
	}

	echo '
	}
	</script>

	<!-- JQUERY -->
	<script src="script/lib/jquery-3.4.1.min.js"></script>

	<!-- JQUERY UI -->
	<script src="script/lib/jquery-ui.min.js"></script>

	<!-- JQUERY SELECT -->
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<!-- Addons -->
	<script src="script/lib/jquery-ui-timepicker-addon.js"></script>
	<script src="script/js/validate/jquery.validate.min.js"></script>
	<script src="script/js/validate/additional-methods.min.js"></script>
	<script src="script/js/validate/validate.js?v=4"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
	
	<!-- Drop Zone -->
	<script src="script/lib/dropzone.min.js"></script>
	
	<!-- LightBox -->
	<script src="script/lib/lightbox.min.js"></script>

	<script>
 	$(function() {
		$("#datepicker").datetimepicker();
		$("#datepicker2").datetimepicker();
		$("#datepicker3").datetimepicker();
		$("#datepicker4").datetimepicker();
	});
	</script>
</head>

<body class="'.myTheme().'">

<div class="tm-container">
<div class="tm-text-white tm-page-header-container">
	<img src="images/logo.png" />
</div>
<div class="tm-main-content">
<section class="tm-section">

<div class="topnav" id="myTopnav">
<a href="index.php" '.isPageActive("home").'>Home</a>
<a href="'.$forumURL.'">Forums</a>';

// If not logged in
if(!loggedIn())
{
	// If website is not closed
	if(!isWebsiteClosed())
	{
		// If sign ups are not closed
		if(!isSignUpClosed())
		{
			echo '
			<a href="index.php?action=requestaccess" '.isPageActive("requestaccess").'>Request Access</a>
			<a href="index.php?action=setup" '.isPageActive("setup").'>Account Setup</a>
			<a href="index.php?action=faq" '.isPageActive("faq").'>FAQ</a>';
		}
	}
	
	echo '
	<a href="index.php?action=login" '.isPageActive("login").'>Login</a>';
}
else
{
	// Logged in
	echo '
	<a href="index.php?action=trooptracker" '.isPageActive("trooptracker").'>Stats & Search</a>
	<a href="index.php?action=account" '.isPageActive("account").'>Manage Account</a>';

	// If is admin
	if(isAdmin())
	{
		echo '
		<a href="index.php?action=commandstaff" '.isPageActive("commandstaff").'>Command Staff Portal</a>';
	}

	echo '
	<a href="index.php?action=logout" '.isPageActive("logout").'>Logout</a>';
}

// Icon for mobile phones
echo '
<a href="javascript:void(0);" class="icon" onclick="myFunction()"><i class="fa fa-bars"></i></a>
</div>

<div class="dashboard-row">';

// Show support graph
echo drawSupportGraph();

if(loggedIn())
{
	$userID = getUserID($_SESSION['id']);
	
	$alerts = @getAlerts($userID)['alerts'];
	$conversations = @getConversations($userID)['conversations'];

	// Don't show on logout page
	if(@$_GET['action'] != "logout") {
		// Forum notifications
		echo '
		<div class="user-welcome-box">
			<a href="index.php?profile='.$_SESSION['id'].'" style="color: yellow;">Welcome '.getName($_SESSION['id']).'!</a>
			<br />
			<a href="index.php?profile='.$_SESSION['id'].'">'.(getForumAvatar($_SESSION['id']) != "" ? '<img src="' . getForumAvatar($_SESSION['id']) . '" />' : '').'</a>
			<br />
			<a href="'.$forumURL.'account/alerts" '. ((@count($alerts) > 0 || @count($conversations) > 0) ? 'class="fading-text"' : '') .'>You have '.@count($alerts).' notifications and '.@count($conversations).' unread messages on the boards.</a>

			'.dailyTip().'
		</div>';
	}
	
	$threads = getThreadsFromForum($userID);

	// Check if threads has data
	if(!isset($threads['errors'])) {
		echo '<div class="container-announce">';
		foreach($threads['sticky'] as $thread => $thread_value) {
			echo '
			<div class="box">
				<div class="user">
					<img src="'.$thread_value['User']['avatar_urls']['s'].'" />
					<br />
					<a href="'. $thread_value['User']['view_url'] .'">'.$thread_value['User']['username'].'</a>
				</div>
				
				<div class="title">
					<a href="' . $thread_value['view_url'] . '">' . $thread_value['title'] . '</a>
				</div>
			</div>';
		}
		
		foreach($threads['threads'] as $thread => $thread_value) {
			echo '
			<div class="box">
				<div class="user">
					<img src="'.$thread_value['User']['avatar_urls']['s'].'" />
					<br />
					<a href="'. $thread_value['User']['view_url'] .'">'.$thread_value['User']['username'].'</a>
				</div>
				
				<div class="title">
					<a href="' . $thread_value['view_url'] . '">' . $thread_value['title'] . '</a>
				</div>
			</div>';
		}
		echo '</div>';
	}
}

echo '</div>';

// Show the account page
if(isset($_GET['action']) && $_GET['action'] == "account" && loggedIn())
{
	// Theme Button Submit
	if(isset($_POST['themeButton']))
	{
		$statement = $conn->prepare("UPDATE troopers SET theme = ? WHERE id = ?");
		$statement->bind_param("ii", $_POST['themeselect'], $_SESSION['id']);
		$statement->execute();
		
		echo '<div class="alert-box">Your theme has been changed. Please <a href="index.php?action=account">refresh</a> the page to see the changes.</div>';
	}
	
	// Account Page
	echo '
	<h2 class="tm-section-header">Manage Account</h2>

	<a href="#/" id="emailSettingLink" class="button">E-mail Settings</a> 
	<a href="#/" id="changephoneLink" class="button">Change Phone</a> 
	<a href="#/" id="changenameLink" class="button">Change Name</a> 
	<a href="#/" id="changethemeLink" class="button">Change Theme</a> 
	<a href="#/" id="favoriteCostumes" class="button">Favorite Costumes</a> 
	<a href="'.$forumURL.'account/upgrades" class="button">Donate</a> 
	<a href="index.php?profile='.$_SESSION['id'].'" class="button">View Your Profile</a>
	<br /><br />
	
	<div id="favoriteCostumesArea" style="display:none;">
		<p>Select your favorite costumes, and they will display at the top of the costume drop down boxes.</p>
		
		<select multiple="multiple" id="favoriteCostumeSelect" name="favoriteCostumeSelect">';
		
		$statement = $conn->prepare("SELECT costumes.id AS id, costumes.club, costumes.costume, favorite_costumes.costumeid, favorite_costumes.trooperid FROM costumes LEFT JOIN favorite_costumes ON favorite_costumes.costumeid = costumes.id ORDER BY costume");
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				$isFavorite = ($db->trooperid == $_SESSION['id']) ? echoSelect($db->id, $db->costumeid) : "";

				echo '
				<option value="'.$db->id.'" '. $isFavorite .'>'.getCostumeAbbreviation($db->club).' '.$db->costume .'</option>';
			}
		}
		
	echo '
		</select>
	</div>

	<div id="unsubscribe" style="display:none;">
		<h2 class="tm-section-header">E-mail Subscription</h2>
		<form action="process.php?do=unsubscribe" method="POST" name="unsubscribeForm" id="unsubscribeForm">';

		$statement = $conn->prepare("SELECT subscribe FROM troopers WHERE id = ?");
		$statement->bind_param("i", $_SESSION['id']);
		$statement->execute();
		
		// Is the trooper subscribed to e-mail?
		$subscribe = "";

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($db->subscribe == 1)
				{
					echo '
					<input type="submit" name="unsubscribeButton" id="unsubscribeButton" value="Unsubscribe All" />';
				}
				else
				{
					echo '
					<input type="submit" name="unsubscribeButton" id="unsubscribeButton" value="Subscribe" />';
					
					// Set is subscribed
					$subscribe = "style = \"display: none;\"";
				}
			}
		}
		echo '
		</form>
		
		<div id="emailSettingsOptions" '.$subscribe.'>
			<h3>Squads / Clubs</h3>
			<form action="process.php?do=emailsettings" method="POST" id="emailsettingsForm" name="emailsettingsForm">';

			// Garrison name
			echo '
			<input type="checkbox" name="esquad0" id="esquad0" ' . emailSettingStatus("esquad0", true) . ' />501st / '.garrison.'<br />';
			
			// Loop through squads
			foreach($squadArray as $squad => $squad_value)
			{
				echo '
				<input type="checkbox" name="esquad'.$squad_value['squadID'].'" id="esquad'.$squad_value['squadID'].'" ' . emailSettingStatus("esquad" . $squad_value['squadID'], true) . ' />' . $squad_value['name'] . '<br />';
			}
			
			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				echo '
				<input type="checkbox" name="esquad'.$club_value['squadID'].'" id="esquad'.$club_value['squadID'].'" ' . emailSettingStatus("esquad" . $club_value['squadID'], true) . ' />' . $club_value['name'] . '<br />';
			}
			
			echo '
				<p style="font-size: 12px;">
					<i>Note: Events are categorized by 501st squad territory. To receive event notifications for a particular area, ensure you subscribed to the appropriate squad(s). Club notifications are used in command staff e-mails, to send command staff information on trooper milestones based on squad or club.</i>
				</p>

				<h3>Website</h3>
				<input type="checkbox" name="efast" id="efast" ' . emailSettingStatus("efast", true) . ' />Instant Event Notification<br />
				<input type="checkbox" name="econfirm" id="econfirm" ' . emailSettingStatus("econfirm", true) . ' />Confirm Attendance Notification<br />
				<input type="checkbox" name="ecommandnotify" id="ecommandnotify" ' . emailSettingStatus("ecommandnotify", true) . ' />Command Staff Notifications<br />
			</form>
		</div>
	</div>
	
	<div id="changetheme" style="display:none;">
		<h2 class="tm-section-header">Change Theme</h2>
		<form action="index.php?action=account" method="POST" name="changethemeForm" id="changethemeForm">
			<select name="themeselect" id="themeselect">';

			$statement = $conn->prepare("SELECT theme FROM troopers WHERE id = ?");
			$statement->bind_param("i", $_SESSION['id']);
			$statement->execute();

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<option value="0" '.echoSelect(0, $db->theme).'>'.garrison.' Theme (Dark Theme)</option>
					<option value="1" '.echoSelect(1, $db->theme).'>Everglades Theme</option>
					<option value="2" '.echoSelect(2, $db->theme).'>Makaze Theme</option>
					<option value="3" '.echoSelect(3, $db->theme).'>'.garrison.' Theme</option>';
				}
			}
		echo '
				<input type="submit" name="themeButton" id="themeButton" value="Change Theme" />
			</select>
		</form>
	</div>

	<div id="changename" style="display:none;">
		<h2 class="tm-section-header">Change Your Name</h2>
		<form action="process.php?do=changename" method="POST" name="changeNameForm" id="changeNameForm">
				<input type="text" name="name" id="name" value="'.getName($_SESSION['id']).'" />
				<input type="submit" name="nameButton" id="nameButton" value="Update" />
		</form>
	</div>

	<div id="changephone" style="display:none;">
		<h2 class="tm-section-header">Change Phone Number</h2>
		<form action="process.php?do=changephone" method="POST" name="changePhoneForm" id="changePhoneForm">
			<input type="text" name="phone" id="phone" value="'.getPhone($_SESSION['id']).'" />
			<input type="submit" name="phoneButton" id="phoneButton" value="Update" />
		</form>
	</div>';
}

// Show the request access page
if(isset($_GET['action']) && $_GET['action'] == "requestaccess" && !isSignUpClosed() && !loggedIn())
{
	echo '
	<h2 class="tm-section-header">Request Access</h2>
	
	<div name="requestAccessFormArea" id="requestAccessFormArea">
		<p style="text-align: center; border: dashed white;">New to the 501st and/or '.garrison.'? Or are you solely a member of another club? Use this form below to start signing up for troops. Command Staff will need to approve your account prior to use.</p>
		
		<form action="process.php?do=requestaccess" name="requestAccessForm" id="requestAccessForm" method="POST">
			First & Last Name (use a nickname if you wish to remain anonymous): <input type="text" name="name" id="name" />
			<br /><br />
			Account Type: <input type="radio" name="accountType" value="1" CHECKED> Regular <input type="radio" name="accountType" value="4"> Handler 
			<br /><br />
			Phone (Optional): <input type="text" name="phone" id="phone" />
			<br /><br />
			'.garrison.' Forum Username: <input type="text" name="forumid" id="forumid" />
			<br /><br />
			'.garrison.' Forum Password: <input type="password" name="forumpassword" id="forumpassword" />
			<br /><br />';

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// If DB3 defined
				if($club_value['db3Name'] != "")
				{
					echo '
					'.$club_value['db3Name'].' (if applicable): <input type="text" name="'.$club_value['db3'].'" id="'.$club_value['db3'].'" />
					<br /><br />';
				}
			}

			echo '
			Squad/Club:
			<select name="squad_request" id="squad">
				'.squadSelectList().'
				<option value="0">'.garrison.' / 501st Visitor</option>
			</select>
			<span id="tkid_box">
				<br /><br />
				TKID (numbers only): <input type="number" min="0" name="tkid" id="tkid" />
			</span>
			<br /><br />
			<input type="submit" name="submitRequest" value="Request" />
			<br />
			<b>If you are a dual member, you will only need one account.</b>
		</form>
	</div>

	<div name="requestAccessFormArea2" id="requestAccessFormArea2"></div>';
}
else
{
	if(isset($_GET['action']) && $_GET['action'] == "requestaccess")
	{
		echo '<p style="text-align: center;"><b>You are already logged in.</b></p>';
	}
}

// Show the profile page
if(isset($_GET['profile']) && loggedIn())
{
	// Hold value
	$profile = cleanInput($_GET['profile']);
	
	// Convert TKID to profile
	if(isset($_GET['tkid']))
	{
		$profile = getIDFromTKNumber($_GET['tkid']);
	}
	
	// Get data
	$statement = $conn->prepare("SELECT event_sign_up.trooperid, events.squad AS eventSquad, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd, troopers.id, troopers.name, troopers.forum_id, troopers.tkid, troopers.squad, troopers.phone FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopers.id = ? AND troopers.id != ".placeholder." AND events.closed = '1' AND event_sign_up.status = '3' ORDER BY events.dateEnd DESC");
	$statement->bind_param("i", $profile);
	$statement->execute();
	
	// Count
	$i = 0;

	// Check if trooped all squads
	$troopedSquads = array();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				// Show profile information
				echo 
				profileTop($db->id, $db->tkid, $db->name, $db->squad, $db->forum_id, $db->phone);
				
				echo  '
				<span style="text-align: center;">' . getTroopCounts($profile) . '</span>
				<div style="overflow-x: auto;">
				'.pendingTroopsDisplay($profile).'

				<h2 class="tm-section-header" id="troop-history-header">Troop History</h2>
				<table border="1">
				<tr>
					<th>Event Name</th>	<th>Date</th>	<th>Attended Costume</th>
				</tr>';
			}
			
			// Set add to title if linked event
			$add = "";
			
			// If linked event
			if(isLink($db->eventId) > 0)
			{
				$add = "[<b>" . date("l", strtotime($db->dateStart)) . "</b> : ".date("m/d - h:i A", strtotime($db->dateStart))." - ".date("h:i A", strtotime($db->dateEnd))."] ";
			}

			echo '
			<tr>
				<td>

				'.getSquadLogo($db->eventSquad).'

				<a href="index.php?event='.$db->troopid.'">'.$add.''.$db->eventName.'</a></td>';
				
			$dateFormat = date('m-d-Y', strtotime($db->dateEnd));

			echo '
				<td>'.$dateFormat.'</td>	<td>'.ifEmpty('<a href="index.php?action=costume&costumeid='.$db->costume.'">' . getCostume($db->costume) . '</a>', "N/A").'</td>
			</tr>';

			// Increment trooped squads
			array_push($troopedSquads, $db->eventSquad);

			// Increment i
			$i++;
		}
	}

	// If profile does not exist
	if(!profileExist($profile))
	{
		echo '
		<p style="text-align: center;">
			<b>This trooper does not exist.</b>
		</p>';
	}
	// Check if placeholder
	else if($profile == placeholder)
	{
		echo '
		<p style="text-align: center;">
			<b>This is a placeholder account. A placeholder is used to fill space when a trooper does not have Troop Tracker access.</b>
		</p>';
	}
	else
	{
		// Profile exists - if nothing to show
		if($i == 0)
		{
			echo profileTop($profile, getTKNumber($profile), getName($profile), getTrooperSquad($profile), getTrooperForum($profile), getPhone($profile));
		}
		else
		{
			// Get count for awards
			$statement = $conn->prepare("SELECT id FROM event_sign_up WHERE status = '3' AND trooperid = ?");
			$statement->bind_param("i", $profile);
			$statement->execute();
			$statement->store_result();
			$count = $statement->num_rows;

			// Set up award count
			$j = 0;

			echo '
			</table>
			</div>
			
			<div class="profile-awards">
			
			<h2 class="tm-section-header" id="awards-header">Awards</h2>';
			
			// Check if supporter
			if(isSupporter($profile))
			{
				echo '<img src="images/flgdonate.png" />';
			}
			
			echo'
			<ul>';

			// Reduce array to unique values
			$troopedSquads = array_unique($troopedSquads);

			// Remove garrison from array
			$troopedSquads = array_diff($troopedSquads, array(0, -1));

			// If trooped in every squad, show an award
			if(count($troopedSquads) == count($squadArray)) {
				echo '<li><b>Trooped Every Squad!<b></li>';
			}

			if($count >= 1)
			{
				echo '<li>First Troop Completed!</li>';
				$j++;
			}

			if($count >= 10)
			{
				echo '<li>10 Troops</li>';
			}

			if($count >= 25)
			{
				echo '<li>25 Troops</li>';
			}

			if($count >= 50)
			{
				echo '<li>50 Troops</li>';
			}

			if($count >= 75)
			{
				echo '<li>75 Troops</li>';
			}

			if($count >= 100)
			{
				echo '<li>100 Troops</li>';
			}

			if($count >= 150)
			{
				echo '<li>150 Troops</li>';
			}

			if($count >= 200)
			{
				echo '<li>200 Troops</li>';
			}

			if($count >= 250)
			{
				echo '<li>250 Troops</li>';
			}

			if($count >= 300)
			{
				echo '<li>300 Troops</li>';
			}

			if($count >= 400)
			{
				echo '<li>400 Troops</li>';
			}

			if($count >= 500)
			{
				echo '<li>500 Troops</li>';
			}

			if($count >= 501)
			{
				echo '<li>501 Troops Award</li>';
			}

			// Get data from custom awards - load award user data
			$statement = $conn->prepare("SELECT award_troopers.awardid, award_troopers.trooperid, awards.id, awards.title, awards.icon FROM award_troopers LEFT JOIN awards ON awards.id = award_troopers.awardid WHERE award_troopers.trooperid = ?");
			$statement->bind_param("i", $profile);
			$statement->execute();

			if ($result2 = $statement->get_result())
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// If has icon...
					if($db2->icon == "")
					{
						echo '<li>'.$db2->title.'</li>';
					}
					else
					{
						echo '<li style="list-style-image: url(\'images/icons/'.$db2->icon.'\');">'.$db2->title.'</li>';
					}

					$j++;
				}
			}

			if($j == 0)
			{
				echo '<li>No awards yet!</li>';
			}
			
			echo '
			</ul>
			</div>

			<h2 class="tm-section-header" id="photo_section">Tagged Photos</h2>

			<div class="profile-donations">';

			// Set results per page
			$results = 5;
			
			// Get total results - query
			$statement = $conn->prepare("SELECT COUNT(uploads.id) AS total FROM uploads LEFT JOIN tagged ON uploads.id = tagged.photoid WHERE tagged.trooperid = ? AND admin = 0");
			$statement->bind_param("i", $profile);
			$statement->execute();
			$statement->bind_result($rowPage);
			$statement->fetch();
			$statement->close();
			
			// Set total pages
			$total_pages = ceil($rowPage / $results);
			
			// If page set
			if(isset($_GET['page']))
			{
				// Get page
				$page = intval($_GET['page']);
				
				// Start from
				$startFrom = ($page - 1) * $results;
			}
			else
			{
				// Default
				$page = 1;
				
				// Start from - default
				$startFrom = 0;
			}
			
			// Query database for photos
			$statement = $conn->prepare("SELECT uploads.filename, uploads.trooperid, uploads.id FROM uploads LEFT JOIN tagged ON uploads.id = tagged.photoid WHERE tagged.trooperid = ? AND admin = 0 ORDER BY uploads.date DESC LIMIT ?, ?");
			$statement->bind_param("iii", $profile, $startFrom, $results);
			$statement->execute();
			
			// Count photos
			$i = 0;
			
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{	
					echo '
					<div class="container-image">
						<a href="images/uploads/'.$db->filename.'" data-lightbox="photosadmin" data-title="Uploaded by '.getName($db->trooperid).'" id="photo'.$db->id.'"><img src="images/uploads/resize/'.getFileName($db->filename).'.jpg" width="200px" height="200px" class="image-c" /></a>
						
						<p class="container-text">
							<a href="index.php?action=editphoto&id='.$db->id.'">Edit</a>
							<br />
							<a href="#" photoid="'.$db->id.'" name="tagged">' . (isInPhoto($db->id, $_SESSION['id']) ? 'Untag Me' : 'Tag Me') . '</a>
						</p>
					</div>';
					
					$i++;
				}

				// If photos
				if($i > 0) {
					echo '
					<p class="center-content">
						<i>Press photos for full resolution version.</i>
					</p>';
				} else {
					echo '
					<p class="center-content">
						No tagged photos to display.
					</p>';
				}
			}
			
			// If photos
			if($total_pages > 1)
			{
				echo '<p>Pages: ';
				
				// Loop through pages
				for ($j = 1; $j <= $total_pages; $j++)
				{
					// If we are on this page...
					if($page == $j)
					{
						echo '
						'.$j.'';
					}
					else
					{
						echo '
						<a href="index.php?profile='.cleanInput($_GET['profile']).'&page='.$j.'#photo_section">'.$j.'</a>';
					}
					
					// If not that last page, add a comma
					if($j != $total_pages)
					{
						echo ', ';
					}
				}
				
				echo '</p>';
			}

			echo '
			</div>
			
			<div class="profile-donations">
			
			<h2 class="tm-section-header" id="donation-header">Donations</h2>
			
			<ul>';
	
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

			// Initialize a variable to store the total cost
			$getSupportNum = 0;

			// Get user_id
			$user_id = getUserID($profile);

			// Reset for donations
			$j = 0;

			// Check if the combinedResults array exists
			if (isset($obj['combinedResults']) && is_array($obj['combinedResults'])) {
			    // Loop through each result in combinedResults
			    foreach ($obj['combinedResults'] as $result) {
				    foreach ($obj['userUpgrades'] as $result2) {
				        // Check if this result has the specific user_upgrade_id
				        if (isset($result2['user_upgrade_id']) && $result2['user_upgrade_id'] == $result['user_upgrade_id'] && $result['user_id'] == $user_id) {
						    foreach ($obj['paymentLog'] as $result3) {
						        // Check if this result has the specific purchase_request_key
						        if($result['purchase_request_key'] == $result3['purchase_request_key']) {
						        	$obj2 = json_decode($result3['log_details'], true);
						        	$timestamp = strtotime($obj2['payment_date']);
									$date = new DateTime();
									$date->setTimestamp($timestamp);

									echo '<li>$' . intval($obj2['payment_gross']) . ' on ' . $date->format('m/d/Y') . '</li>';
									$j++;
						        }
						    }
				        }
				    }
			    }
			}
		
			// Get data from custom awards - load award user data
			$statement = $conn->prepare("SELECT * FROM donations WHERE trooperid = ? ORDER BY datetime DESC");
			$statement->bind_param("i", $profile);
			$statement->execute();

			if ($result2 = $statement->get_result())
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					$formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
					
					echo '<li>' . $formatter->formatCurrency($db2->amount, 'USD') . ' on '.date("m/d/Y", strtotime($db2->datetime)).'</li>';
					$j++;
				}
			}

			if($j == 0)
			{
				echo '<li>No donations yet!</li>';
			}
			echo '
			</ul>
			
			</div>';
		}
		
		echo '
		<h2 class="tm-section-header" id="costumes-header">Costumes</h2>';
		
		// Show 501st costumes
		showCostumes(getTKNumber($profile), getTrooperSquad($profile));
		
		// Show Rebel Legion costumes
		showRebelCostumes(getRebelLegionUser($profile));
		
		// Show Mando Mercs costumes
		showMandoCostumes(getMandoLegionUser($profile));

		// Show Saber Guild costumes
		showSGCostumes(getSGUser($profile));
		
		// Show Droid Builder costumes
		showDroids(getTrooperForum($profile));
	}
}
else
{
	if(isset($_GET['profile']))
	{
		echo '<p style="text-align: center;"><b>Please login to view this profile.</b></p>';
	}
}

// Photo Page
if(isset($_GET['action']) && $_GET['action'] == "photos")
{
	// Print
	echo '
	<h3>Recent Events With Photos</h3>';

	// Start photo count
	$i = 0;

	// Build query
	$statement = $conn->prepare("SELECT uploads.troopid, events.dateStart, events.dateEnd FROM uploads LEFT JOIN events ON uploads.troopid = events.id WHERE admin = '0' GROUP BY uploads.troopid ORDER BY events.dateEnd DESC LIMIT 100");
	$statement->execute();

	// Loop through query
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// First loop
			if($i == 0)
			{
				echo '
				<ul>';
			}

			$statement2 = $conn->prepare("SELECT id FROM uploads WHERE troopid = ? AND admin = '0'");
			$statement2->bind_param("i", $db->troopid);
			$statement2->execute();
			$statement2->store_result();
			$troopCount = $statement2->num_rows;

			echo '
			<li>
				<a href="index.php?event='.$db->troopid.'"><b>('.$troopCount.')</b> ['.date("m-d-Y h:i A", strtotime($db->dateStart)).' - '.date("h:i A", strtotime($db->dateEnd)).'] - '.getEventTitle($db->troopid).'</a>
			</li>';

			// Increment photo count
			$i++;
		}
	}

	// If photos exist
	if($i > 0)
	{
		echo '
		</ul>';
	}
	else
	{
		echo '
		<p style="text-align: center;">No photos to display.</p>';
	}
}

// Show the costume count page
if(isset($_GET['action']) && $_GET['action'] == "costume" && isset($_GET['costumeid']) && $_GET['costumeid'] >= 0)
{
	// Get data
	$statement = $conn->prepare("SELECT (SELECT COUNT(event_sign_up.trooperid)) AS troopCount, troopers.name AS trooperName, troopers.tkid, troopers.squad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd, (TIMESTAMPDIFF(HOUR, events.dateStart, events.dateEnd) + events.charityAddHours) AS charityHours FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE event_sign_up.costume = ? AND status = 3 AND events.closed = '1' AND event_sign_up.trooperid != " . placeholder . " GROUP BY event_sign_up.trooperid ORDER BY troopCount DESC");
	$statement->bind_param("i", $_GET['costumeid']);
	$statement->execute();

	// Troop count
	$i = 0;

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				echo '
				<h2 class="tm-section-header">'.getCostume($db->costume).'</h2>
				<div style="overflow-x: auto;">
				<table border="1">
				<tr>
					<th>Trooper Name</th>	<th>TKID</th>	<th>Costume Troop Count</th>
				</tr>';
			}

			echo '
			<tr>
				<td><a href="index.php?profile='.$db->trooperid.'">'.$db->trooperName.'</a></td>	<td>'.readTKNumber($db->tkid, $db->squad, $db->trooperid).'</td>	<td>'.$db->troopCount.'</td>
			</tr>';

			$i++;
		}
	}

	if($i > 0)
	{
		echo '
		</table>
		</div>';
	}
}

// Show the search page
if(isset($_GET['action']) && $_GET['action'] == "search")
{
	echo '
	<h2 class="tm-section-header">Search</h2>
	<div name="searchForm" id="searchForm">
		<form action="index.php?action=search" method="POST">';
			// Get our search type, and show certain fields
			if(!isset($_POST['searchType']) || $_POST['searchType'] == "regular")
			{
				echo '
				Search Troop Name: <input type="text" name="searchName" id="searchName" value="'. (!isset($_POST['searchName']) ? '' : cleanInput($_POST['searchName'])) .'" />
				<br /><br />
				Search Trooper Name: <input type="text" name="searchTrooperName" id="searchTrooperName" value="'. (!isset($_POST['searchTrooperName']) ? '' : cleanInput($_POST['searchTrooperName'])) .'" />
				<br /><br />';
			}
			
			echo '
			Date Start: <input type="text" name="dateStart" id="datepicker3" value="'. (!isset($_POST['dateStart']) ? '' : cleanInput($_POST['dateStart'])) .'" />
			<br /><br />
			Date End: <input type="text" name="dateEnd" id="datepicker4" value="'. (!isset($_POST['dateEnd']) ? '' : cleanInput($_POST['dateEnd'])) .'" />
			<br /><br />';
			
			// Get our search type, and show certain fields
			if(!isset($_POST['searchType']) || $_POST['searchType'] == "regular")
			{
				echo '
				Search TKID: <input type="text" name="tkID" id="tkID" value="'. (!isset($_POST['tkID']) ? '' : cleanInput($_POST['tkID'])) .'" />
				<br /><br />';
			}
			
			// Set search type
			echo '
			<input type="hidden" name="searchType" value="'. (!isset($_POST['searchType']) ? 'regular' : cleanInput($_POST['searchType'])) .'" />';
			
			// If trooper search, include searchType for another search
			if(isset($_POST['searchType']) && ($_POST['searchType'] == "trooper" || $_POST['searchType'] == "donations"))
			{
				echo '
				<select name="squad" id="squad">
					<option value="0" '.echoSelect(0, cleanInput($_POST['squad'])).'>All</option>
					'.squadSelectList(true, "select").'
				</select>
				<br /><br />';
				
				if($_POST['searchType'] == "trooper")
				{
					// If active only set
					if(isset($_POST['activeonly']) && $_POST['activeonly'] == 1)
					{
						echo '
						<input type="checkbox" name="activeonly" value="1" CHECKED /> Active Members Only?';	
					}
					else
					{
						echo '
						<input type="checkbox" name="activeonly" value="1" /> Active Members Only?';	
					}

					echo '
					<br /><br />';
				}

				if($_POST['searchType'] == "donations")
				{
					// If active only set
					if(isset($_POST['donationCheckBox']) && $_POST['donationCheckBox'] == 1) {
						echo '
						<input type="checkbox" name="donationCheckBox" value="1" CHECKED /> Charity Events Only?';	
					} else {
						echo '
						<input type="checkbox" name="donationCheckBox" value="1" /> Charity Events Only?';	
					}

					echo '
					<br /><br />';

					// If with value only set
					if(isset($_POST['donationCheckBox2']) && $_POST['donationCheckBox2'] == 1) {
						echo '
						<input type="checkbox" name="donationCheckBox2" value="1" CHECKED /> Events With Data?';	
					} else {
						echo '
						<input type="checkbox" name="donationCheckBox2" value="1" /> Events With Data?';	
					}

					echo '
					<br /><br />';
				}
			}

			// If costume search, include searchType for another search
			if(isset($_POST['searchType']) && $_POST['searchType'] == "costumecount")
			{
				echo '
				<select multiple style="height: 500px;" id="costumes_choice_search_box" name="costumes_choice_search_box[]">';
				
				$statement = $conn->prepare("SELECT costumes.id AS id, costumes.club, costumes.costume FROM costumes ORDER BY costume");
				$statement->execute();

				if ($result = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						$check = "";

						// Should we check the select
						if(isset($_POST['costumes_choice_search_box']))
						{
							if(in_array($db->id, $_POST['costumes_choice_search_box']))
							{
								$check = "SELECTED";
							}
						}

						echo '
						<option value="'.$db->id.'" '.$check.'>'.getCostumeAbbreviation($db->club).' '.$db->costume .'</option>';
					}
				}
				
				echo '
				</select>';
			}
			
			echo '
			<input type="submit" name="submitSearch" id="submitSearch" value="Search!" />
		</form>
	
	<br /><br />
	<hr />
	<br /><br />';

	// Format dates
	if(isset($_POST['submitSearch'])) {
		$dateStart = strtotime(($_POST['dateStart'] != "" ? $_POST['dateStart'] : '1990-01-01'));
		$dateEnd = strtotime(($_POST['dateStart'] != "" ? $_POST['dateEnd'] : '3000-01-01'));
		$dateStartQuery = date('Y-m-d H:i:s', $dateStart);
		$dateEndQuery = date('Y-m-d H:i:s', $dateEnd);
	}

	// Get our search type, and show certain fields
	// Regular search
	if(isset($_POST['searchType']) && $_POST['searchType'] == "regular")
	{
		// Get data
		$i = 0;
		$statement = $conn->prepare("SELECT events.squad AS eventSquad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopers.id != ".placeholder." AND troopers.tkid LIKE CONCAT('%', ?, '%') AND events.name LIKE CONCAT('%', ?, '%') AND events.dateStart >= ? AND events.dateEnd <= ? AND troopers.name LIKE CONCAT('%', ?, '%')");
		$statement->bind_param("sssss", $_POST['tkID'], $_POST['searchName'], $dateStartQuery, $dateEndQuery, $_POST['searchTrooperName']);
		$statement->execute();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($i == 0)
				{
					echo '
					<div style="overflow-x: auto;">
					<table border="1">
					<tr>
						<th>Event Name</th>	<th>Date</th>	<th>Name</th>	<th>TKID</th>	<th>Attended Costume</th>	<th>Status</th>
					</tr>';
				}
				
				$dateFormat = date('m-d-Y', strtotime($db->dateEnd));

				echo '<tr><td>' . getSquadLogo($db->eventSquad) .
				    '<a href="index.php?event=' . $db->eventId . '">' . $db->eventName . '</a></td>' .
				    '<td>' . $dateFormat . '</td>' .
				    '<td>' . (isset($db->trooperid) ? getName($db->trooperid) : 'N/A') . '</td>' .
				    '<td>' . (isset($db->trooperid) 
				        ? '<a href="index.php?profile=' . $db->trooperid . '">' .
				          readTKNumber(getTKNumber($db->trooperid), getTrooperSquad($db->trooperid), $db->trooperid) . '</a>'
				        : 'N/A') . '</td>' .
				    '<td>' . ifEmpty('<a href="index.php?action=costume&costumeid=' . $db->costume . '">' . getCostume($db->costume) . '</a>', "N/A") . '</td>' .
				    '<td>' . getStatus($db->status) . '</td></tr>';

				$i++;
			}
		}
	}
	else if(isset($_POST['searchType']) && $_POST['searchType'] == "costumecount")
	{	
		// Don't allow empty result
		if(!isset($_POST['costumes_choice_search_box'])) { echo 'No data available.'; exit; }

		// Loop through clubs
		foreach($_POST['costumes_choice_search_box'] as $costume)
		{
			echo '
			<h2 class="tm-section-header">'.getCostume($costume).'</h2>';

			// Get data
			$statement = $conn->prepare("SELECT (SELECT COUNT(event_sign_up.trooperid)) AS troopCount, troopers.name AS trooperName, troopers.tkid, troopers.squad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd, (TIMESTAMPDIFF(HOUR, events.dateStart, events.dateEnd) + events.charityAddHours) AS charityHours FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE event_sign_up.costume = ? AND status = 3 AND events.closed = '1' AND events.dateStart >= '".$dateStartQuery."' AND events.dateEnd <= '".$dateEndQuery."' GROUP BY event_sign_up.trooperid ORDER BY troopCount DESC");
			$statement->bind_param("i", $costume);
			$statement->execute();

			// Troop count
			$i = 0;

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					if($i == 0)
					{
						echo '
						<div style="overflow-x: auto;">
						<table border="1">
						<tr>
							<th>Trooper Name</th>	<th>TKID</th>	<th>Costume Troop Count</th>
						</tr>';
					}

					echo '
					<tr>
						<td><a href="index.php?profile='.$db->trooperid.'">'.$db->trooperName.'</a></td>	<td>'.readTKNumber($db->tkid, $db->squad, $db->trooperid).'</td>	<td>'.$db->troopCount.'</td>
					</tr>';

					$i++;
				}
			}

			if($i == 0)
			{
				echo '
				<p style="text-align: center;">
					No troops found.
				</p>';
			}

			if($i > 0)
			{
				echo '
				</table>
				</div>';
			}
		}
	}
	// Trooper search
	else if(isset($_POST['searchType']) && $_POST['searchType'] == "trooper")
	{
		// Set up array for CSV
		$list = array();

		// Get the squad search type
		if($_POST['squad'] == 0)
		{
			// If All

			// Loop through clubs // This is to get active member column values
			$checkClubsQuery = "(p501 = 1 OR p501 = 2) ";
			foreach($clubArray as $club => $club_value)
			{
				$checkClubsQuery .= "OR (" . $club_value['db'] . " = 1 OR " . $club_value['db'] . " = 2)";
			}

			// Add to query
			$statement = $conn->prepare("SELECT * FROM troopers WHERE troopers.id != ".placeholder." " . (isset($_POST['activeonly']) && $_POST['activeonly'] == 1 ? 'AND ' . '(' . $checkClubsQuery . ')' : '') . "");

			// Get troop counts
			$statement1 = $conn->prepare("SELECT id FROM events WHERE dateStart >= ? AND dateEnd <= ?");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->store_result();
			$troop_count = $statement1->num_rows;
			
			// Get charity counts
			$statement1 = $conn->prepare("SELECT SUM(charityDirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ?");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count);
			$statement1->fetch();
			$statement1->close();

			$statement1 = $conn->prepare("SELECT SUM(charityIndirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ?");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count2);
			$statement1->fetch();
			$statement1->close();
		}
		else if(($_POST['squad'] >= 1 && in_array($_POST['squad'], $validSquadIDs)))
		{
			// If 501st
			// Add to query
			$statement = $conn->prepare("SELECT * FROM troopers WHERE squad = ? AND troopers.id != ".placeholder." " . (isset($_POST['activeonly']) && $_POST['activeonly'] == 1 ? 'AND (p501 = \'1\' OR p501 = \'2\')' : '') . "");
			$statement->bind_param("i", $_POST['squad']);
			
			// Get troop counts
			$statement1 = $conn->prepare("SELECT id FROM events WHERE dateStart >= ? AND dateEnd <= ? AND squad = ?");
			$statement1->bind_param("ssi", $dateStartQuery, $dateEndQuery, $_POST['squad']);
			$statement1->execute();
			$statement1->store_result();
			$troop_count = $statement1->num_rows;
			
			// Get charity counts
			$statement1 = $conn->prepare("SELECT SUM(charityDirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ? AND squad = ?");
			$statement1->bind_param("ssi", $dateStartQuery, $dateEndQuery, $_POST['squad']);
			$statement1->execute();
			$statement1->bind_result($charity_count);
			$statement1->fetch();
			$statement1->close();

			$statement1 = $conn->prepare("SELECT SUM(charityIndirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ? AND squad = ?");
			$statement1->bind_param("ssi", $dateStartQuery, $dateEndQuery, $_POST['squad']);
			$statement1->execute();
			$statement1->bind_result($charity_count2);
			$statement1->fetch();
			$statement1->close();
		} else {
			// Any other club
			$dbValue = getClubBySquadID($_POST['squad'])['db'];

			$statement = $conn->prepare("SELECT * FROM troopers WHERE troopers.id != ".placeholder." " . (isset($_POST['activeonly']) && $_POST['activeonly'] == 1 ? 'AND ('. $dbValue .' = \'1\' OR ' . $dbValue . ' = \'2\')' : 'AND (' . $dbValue . ' > 0)') . "");

			// Get troop counts
			$statement1 = $conn->prepare("SELECT COUNT(total) FROM (SELECT event_sign_up.troopid AS total FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])." GROUP BY event_sign_up.troopid) AS ABC");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($troop_count);
			$statement1->fetch();
			$statement1->close();
			
			// Get charity counts
			$statement1 = $conn->prepare("SELECT SUM(total) FROM (SELECT events.charityDirectFunds AS total FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])." GROUP BY event_sign_up.troopid) AS ABC");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count);
			$statement1->fetch();
			$statement1->close();

			$statement1 = $conn->prepare("SELECT SUM(total) FROM (SELECT events.charityIndirectFunds AS total FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])." GROUP BY event_sign_up.troopid) AS ABC");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count2);
			$statement1->fetch();
			$statement1->close();
		}

		// Get data
		$i = 0;
		
		// Trooper array
		$troopArray = array();
		
		// Format numbers to prevent errors - charity
		if(!isset($charity_count))
		{
			$charity_count = 0;
		}

		if(!isset($charity_count2[0]))
		{
			$charity_count2 = 0;
		}
		
		// Format numbers to prevent errors - troop
		if(!isset($troop_count))
		{
			$troop_count = 0;
		}

		$statement->execute();
		
		// Start going through troopers
		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Show table
				if($i == 0)
				{
					echo '
					<p>
						Total Troops: '.$troop_count.'
					</p>
					
					<p>
						Direct Charity: $'.number_format($charity_count).'
					</p>

					<p>
						Indirect Charity: $'.number_format($charity_count2).'
					</p>
					
					<div style="overflow-x: auto;">
					<table border="1">
					<tr>
						<th>Name</th>	<th>TKID</th>	<th>Troop Count</th>	<th>Last Troop</th>
					</tr>';

					array_push($list, ["Total Troops: " . $troop_count]);
					array_push($list, ["Direct Charity: $" . number_format($charity_count)]);
					array_push($list, ["Indirect Charity: $" . number_format($charity_count2)]);
					array_push($list, [""]);
					array_push($list, ["Name", "TKID", "Troop Count", "Last Troop"]);
				}

				// Increment $i
				$i++;
				
				// If All
				if($_POST['squad'] == 0)
				{
					// Get troop counts - All
					$statement1 = $conn->prepare("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = ? AND events.dateStart >= ? AND events.dateEnd <= ? AND event_sign_up.status = '3' AND events.closed = '1'");
					$statement1->bind_param("iss", $db->id, $dateStartQuery, $dateEndQuery);
					$statement1->execute();
					$statement1->bind_result($count, $eventid);
					$statement1->fetch();
					$statement1->close();
				}
				else if(($_POST['squad'] >= 1 && in_array($_POST['squad'], $validSquadIDs)))
				{
					// If 501st
					// Get troop counts - 501st
					$statement1 = $conn->prepare("SELECT COUNT(event_sign_up.id), max(events.dateStart), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.status = '3' AND events.closed = '1' AND event_sign_up.trooperid = ? AND events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValuesSquad($_POST['squad'])."");
					$statement1->bind_param("iss", $db->id, $dateStartQuery, $dateEndQuery);
					$statement1->execute();
					$statement1->bind_result($count, $date, $eventid);
					$statement1->fetch();
					$statement1->close();
				} else {
					// If club
					$statement1 = $conn->prepare("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.status = '3' AND events.closed = '1' AND event_sign_up.trooperid = ? AND events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])."");
					$statement1->bind_param("iss", $db->id, $dateStartQuery, $dateEndQuery);
					$statement1->execute();
					$statement1->bind_result($count, $eventid);
					$statement1->fetch();
					$statement1->close();
				}
				
				// Create an array of our count
				$tempArray = array($db->tkid, $count, $db->name, $db->id, $db->squad, $date);
				
				// Push to main array
				array_push($troopArray, $tempArray);
			}
		}
		
		// Sort array for display
		$keys = array_column($troopArray, 1);
		array_multisort($keys, SORT_DESC, $troopArray);

		// Loop through array
		foreach($troopArray as $value)
		{
			$value[5] = (!empty($value[5]) ? date("m/d/Y", strtotime($value[5])) : 'N/A');

			// Display
			echo '
			<tr>
				<td>'.$value[2].'</td>	<td><a href="index.php?profile='.$value[3].'">'.readTKNumber($value[0], $value[4], $value[3]).'</a></td>	<td>'.$value[1].'</td>	<td>'.$value[5].'</td>
			</tr>';

			array_push($list, [$value[2], readTKNumber($value[0], $value[4], $value[3]), $value[1], $value[5]]);
		}
	}
	// Donation search
	else if(isset($_POST['searchType']) && $_POST['searchType'] == "donations")
	{
		// If All
		if($_POST['squad'] == 0)
		{
			$statement = $conn->prepare("SELECT (SELECT COUNT(event_sign_up.id) FROM event_sign_up WHERE event_sign_up.troopid = events.id AND event_sign_up.status = 3) AS troopercount, events.id AS id, events.dateStart, events.dateEnd, events.name, events.charityDirectFunds, events.charityAddHours, events.charityDirectFunds, events.charityIndirectFunds, events.charityName, events.charityNote FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE dateStart >= ? AND dateEnd <= ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')." GROUP BY events.id");
			$statement->bind_param("ss", $dateStartQuery, $dateEndQuery);

			// Get troop counts
			$statement1 = $conn->prepare("SELECT COUNT(id) FROM events WHERE dateStart >= ? AND dateEnd <= ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')."");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($troop_count);
			$statement1->fetch();
			$statement1->close();
			
			// Get charity counts
			$statement1 = $conn->prepare("SELECT SUM(charityDirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')."");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count);
			$statement1->fetch();
			$statement1->close();

			$statement1 = $conn->prepare("SELECT SUM(charityIndirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')."");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count2);
			$statement1->fetch();
			$statement1->close();
		}
		else if(($_POST['squad'] >= 1 && in_array($_POST['squad'], $validSquadIDs)))
		{
			// If 501st
			$statement = $conn->prepare("SELECT (SELECT COUNT(event_sign_up.id) FROM event_sign_up WHERE event_sign_up.troopid = events.id AND event_sign_up.status = 3) AS troopercount, events.id AS id, events.dateStart, events.dateEnd, events.name, events.charityDirectFunds, events.charityAddHours, events.charityDirectFunds, events.charityIndirectFunds, events.charityName, events.charityNote FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE dateStart >= ? AND dateEnd <= ? AND squad = ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')." GROUP BY events.id");
			$statement->bind_param("ssi", $dateStartQuery, $dateEndQuery, $_POST['squad']);
			
			// Get troop counts
			$statement1 = $conn->prepare("SELECT COUNT(id) FROM events WHERE dateStart >= ? AND dateEnd <= ? AND squad = ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')."");
			$statement1->bind_param("ssi", $dateStartQuery, $dateEndQuery, $_POST['squad']);
			$statement1->execute();
			$statement1->bind_result($troop_count);
			$statement1->fetch();
			$statement1->close();
			
			// Get charity counts
			$statement1 = $conn->prepare("SELECT SUM(charityDirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ? AND squad = ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')."");
			$statement1->bind_param("ssi", $dateStartQuery, $dateEndQuery, $_POST['squad']);
			$statement1->execute();
			$statement1->bind_result($charity_count);
			$statement1->fetch();
			$statement1->close();

			$statement1 = $conn->prepare("SELECT SUM(charityIndirectFunds) FROM events WHERE dateStart >= ? AND dateEnd <= ? AND squad = ? ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')."");
			$statement1->bind_param("ssi", $dateStartQuery, $dateEndQuery, $_POST['squad']);
			$statement1->execute();
			$statement1->bind_result($charity_count2);
			$statement1->fetch();
			$statement1->close();
		} else {
			// Clubs
			$statement = $conn->prepare("SELECT (SELECT COUNT(event_sign_up.id) FROM event_sign_up WHERE event_sign_up.troopid = events.id AND event_sign_up.status = 3 AND ".getCostumeQueryValues($_POST['squad']).") AS troopercount, events.id AS id, events.dateStart, events.dateEnd, events.name, events.charityDirectFunds, events.charityAddHours, events.charityDirectFunds, events.charityIndirectFunds, events.charityName, events.charityNote FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE dateStart >= ? AND dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])." ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')." GROUP BY events.id");
			$statement->bind_param("ss", $dateStartQuery, $dateEndQuery);

			// Get troop counts
			$statement1 = $conn->prepare("SELECT COUNT(total) FROM (SELECT event_sign_up.troopid AS total FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])." ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')." GROUP BY event_sign_up.troopid) AS ABC");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($troop_count);
			$statement1->fetch();
			$statement1->close();
			
			// Get charity counts
			$statement1 = $conn->prepare("SELECT SUM(total) FROM (SELECT events.charityDirectFunds AS total FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])." ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')." GROUP BY event_sign_up.troopid) AS ABC");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count);
			$statement1->fetch();
			$statement1->close();

			$statement1 = $conn->prepare("SELECT SUM(total) FROM (SELECT events.charityIndirectFunds AS total FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])." ".(isset($_POST['donationCheckBox']) ? 'AND events.label = 1' : '')." ".(isset($_POST['donationCheckBox2']) ? 'AND (events.charityDirectFunds > 0 OR events.charityIndirectFunds > 0 OR events.charityNote != "")' : '')." GROUP BY event_sign_up.troopid) AS ABC");
			$statement1->bind_param("ss", $dateStartQuery, $dateEndQuery);
			$statement1->execute();
			$statement1->bind_result($charity_count2);
			$statement1->fetch();
			$statement1->close();
		}

		// Get data
		$i = 0;
		
		// Troop array
		$troopArray = array();

		// Format numbers to prevent errors - charity
		if(!isset($charity_count))
		{
			$charity_count = 0;
		}

		if(!isset($charity_count2))
		{
			$charity_count2 = 0;
		}
		
		// Format numbers to prevent errors - troop
		if(!isset($troop_count))
		{
			$troop_count = 0;
		}
		
		// Start going through troopers
		$statement->execute();

		$list = array();

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Show table
				if($i == 0)
				{
					array_push($list, ['', '', $dateStartQuery . ' - ' . $dateEndQuery . ' Charity', '']);
					array_push($list, ['Total Troops: ', $troop_count]);
					array_push($list, ['Direct Charity: ', '$' . number_format($charity_count)]);
					array_push($list, ['Indirect Charity: ', '$' . number_format($charity_count2)]);
					array_push($list, ['']);
					array_push($list, ['Date', 'Event', 'Direct', 'Indirect', 'Charity Name', 'Hours', 'Troopers Attended', 'Notes']);

					echo '
					<p>
						Total Troops: '.$troop_count.'
					</p>
					
					<p>
						Direct Charity: $'.number_format($charity_count).'
					</p>

					<p>
						Indirect Charity: $'.number_format($charity_count2).'
					</p>
					
					<div style="overflow-x: auto;">
					<table border="1">
					<tr>
						<th>Date</th>	<th>Event</th>	<th>Direct</th>	<th>Indirect</th>	<th>Charity Name</th>	<th>Hours</th>	<th>Troopers Attended</th>	<th>Notes</th>
					</tr>';
				}

				// Increment $i
				$i++;
				
				// If All
				if($_POST['squad'] == 0)
				{
					// Get troop counts - All
					$statement1 = $conn->prepare("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = ? AND events.dateStart >= ? AND events.dateEnd <= ?");
					$statement1->bind_param("iss", $db->id, $dateStartQuery, $dateEndQuery);
					$statement1->execute();
					$statement1->bind_result($count, $eventid);
					$statement1->fetch();
					$statement1->close();
				}
				else if(($_POST['squad'] >= 1 && in_array($_POST['squad'], $validSquadIDs)))
				{
					// Get troop counts - 501st
					$statement1 = $conn->prepare("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = ? AND events.dateStart >= ? AND events.dateEnd <= ? AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR EXISTS(SELECT events.id FROM events WHERE events.id = event_sign_up.troopid))");
					$statement1->bind_param("iss", $db->id, $dateStartQuery, $dateEndQuery);
					$statement1->execute();
					$statement1->bind_result($count, $eventid);
					$statement1->fetch();
					$statement1->close();
				} else {
					// Get troop counts - 501st
					$statement1 = $conn->prepare("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = ? AND events.dateStart >= ? AND events.dateEnd <= ? AND ".getCostumeQueryValues($_POST['squad'])."");
					$statement1->bind_param("iss", $db->id, $dateStartQuery, $dateEndQuery);
					$statement1->execute();
					$statement1->bind_result($count, $eventid);
					$statement1->fetch();
					$statement1->close();
				}

				$hours = timeBetweenDates($db->dateStart, $db->dateEnd);
				$hours += intval($db->charityAddHours);

				$date = date("m/d/Y", strtotime($db->dateEnd));
				
				// Create an array of our count
				$tempArray = array($db->id, $db->name, $db->charityDirectFunds, $db->charityIndirectFunds, $db->charityName, $hours, $db->charityNote, $db->dateStart, $db->dateEnd, $db->troopercount);
				array_push($list, [$date, $db->name, $db->charityDirectFunds, $db->charityIndirectFunds, $db->charityName, $hours, $db->troopercount, $db->charityNote]);
				
				// Push to main array
				array_push($troopArray, $tempArray);
			}
		}
		
		// Sort array for display
		$keys = array_column($troopArray, 8);
		array_multisort($keys, SORT_DESC, $troopArray);

		// Loop through array
		foreach($troopArray as $value)
		{
			$date = date("m/d/Y", strtotime($value[8]));

			// Display
			echo '
			<tr>
				<td>'.$date.'</td>
				<td><a href="index.php?event='.$value[0].'">'.$value[1].'</a></td>
				<td>$'.number_format($value[2]).'</td>
				<td>$'.number_format($value[3]).'</td>
				<td>'.ifEmpty($value[4], "N/A").'</td>
				<td>'.number_format($value[5]).'</td>
				<td>'.$value[9].'</td>
				<td>'.ifEmpty(nl2br($value[6] ?? ''), "N/A").'</td>
			</tr>';
		}
	}

	// Don't use for this search type
	if(isset($_POST['searchType']) && $_POST['searchType'] != "costumecount")
	{
		// What to do if we have more than one field
		if($i > 0)
		{
			echo '
			</table>
			</div>';

			if($_POST['searchType'] == 'donations' || $_POST['searchType'] == 'trooper') {
				echo '
				<form action="script/php/gencsv.php" method="POST" target="_blank">
					<input type="hidden" name="data" value="'.htmlspecialchars(serialize($list)).'" />
					<input type="submit" value="Download CSV" />
				</form>';
			}
		}
		else
		{
			// Nothing to show
			echo '
			<p style="text-align: center;">
				<b>No results</b>
			</p>';
		}
	}
}

// Show the troop tracker page
if(isset($_GET['action']) && $_GET['action'] == "trooptracker" && loggedIn())
{
	echo '
	<h2 class="tm-section-header">My Stats</h2>

	<p style="text-align: center;">
		<b>Troop Tracker Rank:</b><br />#'.getTrooperRanking($_SESSION['id']).'
	</p>

	<p style="text-align: center;">
		<a href="#/" class="button" id="showstats" name="showstats">Show My Stats</a> 
		<a href="index.php?profile='.$_SESSION['id'].'" class="button">View My Profile</a>
	</p>

	<p style="text-align: center;">
		<a href="#/" class="button" id="show-top-troops">Top Troopers</a> 
	</p>

	<div class="top-troops" id="top-troops">
		<h2 class="tm-section-header">Top Troopers</h2>

		<h4><u>All Time</u></h4>

		<ol>';

		$statement = $conn->prepare("SELECT trooperid, COUNT(trooperid) AS total FROM event_sign_up LEFT JOIN events ON event_sign_up.trooperid = events.id WHERE event_sign_up.trooperid != ".placeholder." AND events.closed = '1' AND event_sign_up.status = '3' GROUP BY trooperid ORDER BY total DESC LIMIT 25");
		$statement->execute();

		$i = 0;

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				echo '<li><a href="index.php?profile='.$db->trooperid.'">' . getName($db->trooperid) . ' - '.$db->total.'</a></li>';

				$i++;
			}
		}

		// If none to display
		if($i == 0)
		{
			echo '<li>Nothing to display.</li>';
		}
		
		echo '
		</ol>

		<h4><u>Last 365 Days</u></h4>

		<ol>';

		$statement = $conn->prepare("SELECT trooperid, COUNT(trooperid) AS total FROM event_sign_up LEFT JOIN events ON event_sign_up.troopid = events.id WHERE events.dateEnd > NOW() - INTERVAL 365 DAY AND event_sign_up.trooperid != ".placeholder." AND events.closed = '1' AND event_sign_up.status = '3' GROUP BY trooperid ORDER BY total DESC LIMIT 25");
		$statement->execute();

		$i = 0;

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				echo '<li><a href="index.php?profile='.$db->trooperid.'">' . getName($db->trooperid) . ' - '.$db->total.'</a></li>';

				$i++;
			}
		}

		// If none to display
		if($i == 0)
		{
			echo '<li>Nothing to display.</li>';
		}
		
		echo '
		</ol>

		<h4><u>Last 30 Days</u></h4>

		<ol>';

		$statement = $conn->prepare("SELECT trooperid, COUNT(trooperid) AS total FROM event_sign_up LEFT JOIN events ON event_sign_up.troopid = events.id WHERE events.dateEnd > NOW() - INTERVAL 30 DAY AND event_sign_up.trooperid != ".placeholder." AND events.closed = '1' AND event_sign_up.status = '3' GROUP BY trooperid ORDER BY total DESC LIMIT 25");
		$statement->execute();

		$i = 0;

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				echo '<li><a href="index.php?profile='.$db->trooperid.'">' . getName($db->trooperid) . ' - '.$db->total.'</a></li>';

				$i++;
			}
		}

		// If none to display
		if($i == 0)
		{
			echo '<li>Nothing to display.</li>';
		}
		
		echo '
		</ol>
	</div>

	<div id="mystats" name="mystats" style="display: none;">';

	// Get data
	$statement = $conn->prepare("SELECT events.squad AS eventSquad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd, (TIMESTAMPDIFF(HOUR, events.dateStart, events.dateEnd) + events.charityAddHours) AS charityHours FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND status = 3 AND events.closed = '1' ORDER BY events.dateEnd DESC");
	$statement->execute();

	// Troop count
	$i = 0;

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				echo getTroopCounts($_SESSION['id']) . '
				<div style="overflow-x: auto;">
				<table border="1">
				<tr>
					<th>Troop</th>	<th>Costume</th>	<th>Charity</th>
				</tr>';
			}
			
			// Set add to title if linked event
			$add = "";
			
			// If linked event
			if(isLink($db->eventId) > 0)
			{
				$add = "[<b>" . date("l", strtotime($db->dateStart)) . "</b> : ".date("m/d - h:i A", strtotime($db->dateStart))." - ".date("h:i A", strtotime($db->dateEnd))."] ";
			}

			echo '
			<tr>
				<td>

				'.getSquadLogo($db->eventSquad).'

				<a href="index.php?event='.$db->eventId.'">'.$add.''.$db->eventName.'</a></td>	<td>'.ifEmpty(getCostume($db->costume), "N/A").'</td>	<td>Direct: $'.number_format($db->charityDirectFunds).'<br />Indirect: $'.number_format($db->charityIndirectFunds).'<br />Hours: '.$db->charityHours.'

				</td>
			</tr>';

			// Increment troop count
			$i++;
		}
	}

	if($i > 0)
	{
		echo '
		</table>
		</div>';
	}
	else
	{
		// No troops attended
		echo '
		<p><b>You have not attended any troops. Get out there and troop!</b></p>';
	}
	
	echo '
	</div>';

	// Show troop tracker for everyone
	echo '
	<h2 class="tm-section-header">Troop Tracker</h2>';

	// If squad is not set
	if(!isset($_GET['squad']))
	{
		// Get data
		$statement = $conn->prepare("SELECT events.squad AS eventSquad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd, (TIMESTAMPDIFF(HOUR, events.dateStart, events.dateEnd) + events.charityAddHours) AS charityHours FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE events.closed = '1' GROUP BY events.id ORDER BY events.dateEnd DESC LIMIT 20");
	}
	else
	{		
		// Set results per page
		$results = 20;

		// Check if squad is not all
		if($_GET['squad'] != 0)
		{
			// Count event total
			$statement2 = $conn->prepare("SELECT COUNT(id) AS total FROM events WHERE squad = ? AND closed = 1");
			$statement2->bind_param("i", $_GET['squad']);
			$statement2->execute();
			$statement2->bind_result($row);
			$statement2->fetch();
			$statement2->close();
		}
		else
		{
			// Count event total
			$statement2 = $conn->prepare("SELECT COUNT(id) AS total FROM events");
			$statement2->execute();
			$statement2->bind_result($row);
			$statement2->fetch();
			$statement2->close();
		}
		
		// Set total pages
		$total_pages = ceil($row / $results);
		
		// If page set
		if(isset($_GET['page']))
		{
			// Get page
			$page = $_GET['page'];
			
			// Start from
			$startFrom = ($page - 1) * $results;
		}
		else
		{
			// Default
			$page = 1;
			
			// Start from - default
			$startFrom = 0;
		}

		// Check if squad is not all
		if($_GET['squad'] != 0)
		{
			// Squad is set, show only that data
			$statement = $conn->prepare("SELECT events.squad AS eventSquad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd, (TIMESTAMPDIFF(HOUR, events.dateStart, events.dateEnd) + events.charityAddHours) AS charityHours FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND events.squad = ? GROUP BY events.id ORDER BY events.dateEnd DESC LIMIT ".$startFrom.", ".$results."");
			$statement->bind_param("i", $_GET['squad']);
		}
		else
		{
			// Squad is set, show only that data
			$statement = $conn->prepare("SELECT events.squad AS eventSquad, event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.charityDirectFunds, events.charityIndirectFunds, events.dateStart, events.dateEnd, (TIMESTAMPDIFF(HOUR, events.dateStart, events.dateEnd) + events.charityAddHours) AS charityHours FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE events.closed = '1' GROUP BY events.id ORDER BY events.dateEnd DESC LIMIT ".$startFrom.", ".$results."");
		}
	}

	// Query count
	$i = 0;
	
	// Total time spent
	$timeSpent = 0;

	$statement->execute();
	
	// Query
	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				// Get squad for squadLink function
				if(isset($_GET['squad']))
				{
					$squadLink = $_GET['squad'];
				}
				else
				{
					$squadLink = -1;
				}

				echo '
				<p style="text-align: center;">';
				
				echo '<a href="index.php?action=trooptracker&squad=0" '.(!isset($_GET['squad']) || @$_GET['squad'] == 0 ? 'style="font-weight: bold; color: yellow;"' : '').'>All</a>';
				
				// Loop through squads
				foreach($squadArray as $squad => $squad_value)
				{
					// Add to return var
					echo
					' | ' . '<a href="index.php?action=trooptracker&squad='.$squad_value['squadID'].'" '.(@$_GET['squad'] == $squad_value['squadID'] ? 'style="font-weight: bold; color: yellow;"' : '').'>'.$squad_value['name'].'</a>';
				}

				echo '
				</p>

				<div style="overflow-x: auto;">
				<table border="1">
				<tr>
					<th>Troop</th>	<th>Troopers Attended</th>	<th>Charity</th>
				</tr>';
			}

			// How many troopers attended
			$statement = $conn->prepare("SELECT COUNT(*) FROM event_sign_up WHERE troopid = ? AND status = '3'");
			$statement->bind_param("i", $db->troopid);
			$statement->execute();
			$statement->bind_result($count);
			$statement->fetch();
			$statement->close();

			echo '
			<tr>
				<td>

				' . ((@$_GET['squad'] == 0) ? getSquadLogo($db->eventSquad) : '') . '

				<a href="index.php?event='.$db->eventId.'">'. (isLink($db->eventId) > 0 ? '[<b>' . date("l", strtotime($db->dateStart)) . '</b> : '.date("m/d - h:i A", strtotime($db->dateStart)).' - '.date("h:i A", strtotime($db->dateEnd)).'] ' : '') .''.$db->eventName.'</a></td>	<td>'.$count.'</td>	<td>Direct: $'.@number_format($db->charityDirectFunds).'<br />Indirect: $'.@number_format($db->charityIndirectFunds).'<br />Hours: '.@number_format($db->charityHours).'

				</td>
			</tr>';

			$i++;
		}
	}

	if($i > 0)
	{
		// How many troops did the user attend
		$statement = $conn->prepare("SELECT costume, COUNT(*) FROM event_sign_up WHERE costume != 706 AND costume != 720 AND costume != 721 GROUP BY costume ORDER BY COUNT(costume) DESC LIMIT 1");
		$statement->execute();
		$statement->bind_result($favoriteCostume, $favoriteCostumeCount);
		$statement->fetch();
		$statement->close();
		// Prevent notice error
		if($favoriteCostume == "") { $favoriteCostume['costume'] = 0; }

		// How many troops did the user attend
		$statement = $conn->prepare("SELECT COUNT(*) FROM event_sign_up WHERE status = '3'");
		$statement->execute();
		$statement->bind_result($count1);
		$statement->fetch();
		$statement->close();

		// How many regular troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '0'");
		$statement->execute();
		$statement->bind_result($count2);
		$statement->fetch();
		$statement->close();

 		// How many armor party troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '10'");
		$statement->execute();
		$statement->bind_result($count13);
		$statement->fetch();
		$statement->close();

 		// How many PR troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '1'");
		$statement->execute();
		$statement->bind_result($count3);
		$statement->fetch();
		$statement->close();

		// How many Disney troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '2'");
		$statement->execute();
		$statement->bind_result($count4);
		$statement->fetch();
		$statement->close();

		// How many convention troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '3'");
		$statement->execute();
		$statement->bind_result($count5);
		$statement->fetch();
		$statement->close();

		// How many hospital troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '9'");
		$statement->execute();
		$statement->bind_result($count12);
		$statement->fetch();
		$statement->close();

		// How many wedding troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '4'");
		$statement->execute();
		$statement->bind_result($count6);
		$statement->fetch();
		$statement->close();

		// How many birthday party troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '5'");
		$statement->execute();
		$statement->bind_result($count7);
		$statement->fetch();
		$statement->close();

		// How many wedding troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '6'");
		$statement->execute();
		$statement->bind_result($count8);
		$statement->fetch();
		$statement->close();

		// How many virtual troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '7'");
		$statement->execute();
		$statement->bind_result($count9);
		$statement->fetch();
		$statement->close();

		// How many other troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '8'");
		$statement->execute();
		$statement->bind_result($count10);
		$statement->fetch();
		$statement->close();

		// How many total troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE closed = '1'");
		$statement->execute();
		$statement->bind_result($count11);
		$statement->fetch();
		$statement->close();

		// How many LFL troops
		$statement = $conn->prepare("SELECT COUNT(*) FROM events WHERE label = '11'");
		$statement->execute();
		$statement->bind_result($count14);
		$statement->fetch();
		$statement->close();

		// How much total money was raised
		$statement = $conn->prepare("SELECT SUM(charityDirectFunds) FROM events WHERE closed = '1'");
		$statement->execute();
		$statement->bind_result($countMoney);
		$statement->fetch();
		$statement->close();

		$statement = $conn->prepare("SELECT SUM(charityIndirectFunds) FROM events WHERE closed = '1'");
		$statement->execute();
		$statement->bind_result($countMoney2);
		$statement->fetch();
		$statement->close();
 
		echo '
		</table>
		</div>';
		
		// If squad is set
		if(isset($_GET['squad']))
		{
			echo '
			<p style="margin-left: 100px; text-align: right;">';
			
			// Loop through pages
			for ($i = 1; $i <= $total_pages; $i++)
			{
				// If we are on this page...
				if($page == $i)
				{
					echo '
					'.$i.'';
				}
				else
				{
					echo '
					<a href="index.php?action=trooptracker&squad='.cleanInput($_GET['squad']).'&page='.$i.'">'.$i.'</a>';
				}
				
				// If not that last page, add a comma
				if($i != $total_pages)
				{
					echo ', ';
				}
			}
			
			echo '
			</p>';
		}
		
		// If squad is not set
		if(!isset($_GET['squad']))
		{
			echo '
			<p><b>Favorite Costume:</b> '.ifEmpty(getCostume($favoriteCostume), "N/A").'</p>
			<p><b>Volunteers at Troops:</b> '.number_format($count1).'</p>
			<p><b>Direct Donations Raised:</b> $'.number_format($countMoney).'</p>
			<p><b>Indirect Donations Raised:</b> $'.number_format($countMoney2).'</p>
			<p><b>Regular Troops:</b> '.number_format($count2).'</p>
			<p><b>Armor Parties:</b> '.number_format($count13).'</p>
			<p><b>Charity Troops:</b> '.number_format($count3).'</p>
			<p><b>PR Troops:</b> '.number_format($count4).'</p>
			<p><b>Disney Troops:</b> '.number_format($count5).'</p>
			<p><b>LFL Troops:</b> '.number_format($count14).'</p>
			<p><b>Convention Troops:</b> '.number_format($count6).'</p>
			<p><b>Hospital Troops:</b> '.number_format($count12).'</p>
			<p><b>Wedding Troops:</b> '.number_format($count7).'</p>
			<p><b>Birthday Troops:</b> '.number_format($count8).'</p>
			<p><b>Virtual Troops:</b> '.number_format($count9).'</p>
			<p><b>Other Troops:</b> '.number_format($count10).'</p>
			<p><b>Total Finished Troops:</b> '.number_format($count11).'</p>';
		}
	}
	else
	{
		// No troops attended
		echo '
		<p style="text-align: center;"><b>No troops found!</b></p>';
	}

	echo '
	<hr />
	<h2 class="tm-section-header">Search</h2>
	<div name="searchForm" id="searchForm">
		<form action="index.php?action=search" method="POST">
			<div id="searchNameDiv">
			Search Troop Name: <input type="text" name="searchName" id="searchName" />
			<br /><br />
			Search Trooper Name: <input type="text" name="searchTrooperName" id="searchTrooperName" />
			<br /><br />
			</div>
			Date Start: <input type="text" name="dateStart" id="datepicker3" />
			<br /><br />
			Date End: <input type="text" name="dateEnd" id="datepicker4" />
			<br /><br />
			<div id="tkIDDiv">
			Search TKID: <input type="text" name="tkID" id="tkID" />
			<br /><br />
			</div>
			Search Type:
			<br />
			<input type="radio" name="searchType" value="regular" CHECKED />Default
			<br />
			<input type="radio" name="searchType" value="trooper" />Troop Count Per Trooper
			<br />
			<input type="radio" name="searchType" value="donations" />Donation Count Per Event
			<br />
			<input type="radio" name="searchType" value="costumecount" />Costume Count Per Trooper
			<br /><br />
			<div id="trooper_count_radio" style="display: none;">
				<select name="squad" id="squad">
					<option value="0" SELECTED>All</option>
					'.squadSelectList().'
				</select>
				<br /><br />
				<span name="activeRadios">
				<input type="checkbox" name="activeonly" value="1" /> Active Members Only?
				<br /><br />
				</span>
			</div>

			<div style="display: none;" name="donationCheckArea">
				<input type="checkbox" name="donationCheckBox" value="1" /> Charity Events Only?
				<br /><br />
				<input type="checkbox" name="donationCheckBox2" value="1" /> Events With Data?
				<br /><br />
			</div>
			<div id="costumes_choice_search" style="display: none;">
				<select multiple style="height: 500px;" id="costumes_choice_search_box" name="costumes_choice_search_box[]">';
				
				$statement = $conn->prepare("SELECT costumes.id AS id, costumes.club, costumes.costume FROM costumes ORDER BY costume");
				$statement->execute();

				if ($result = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						echo '
						<option value="'.$db->id.'">'.getCostumeAbbreviation($db->club).' '.$db->costume .'</option>';
					}
				}
				
				echo '
				</select>
			</div>
			<input type="submit" name="submitSearch" id="submitSearch" value="Search!" />
		</form>
	</div>';
}
else
{
	if(isset($_GET['action']) && $_GET['action'] == "trooptracker")
	{
		echo '<p style="text-align: center;"><b>Please login to view this page.</b></p>';
	}
}

// Show the command staff page
if(isset($_GET['action']) && $_GET['action'] == "commandstaff")
{
	// If the user is logged in and is an admin
	if(loggedIn() && isAdmin())
	{
		$statement = $conn->prepare("SELECT id FROM troopers WHERE approved = '0'");
		$statement->execute();
		$statement->store_result();
		$getTrooperNotifications = $statement->num_rows;

		echo '
		<h2 class="tm-section-header">Command Staff Welcome Area</h2>

		<p>
			<a href="index.php?action=commandstaff&do=createevent" class="button">Create an Event</a> 
			<a href="index.php?action=commandstaff&do=editevent" class="button">Edit an Event</a> 
			<a href="index.php?action=commandstaff&do=eventlinkmanager" class="button">Event Link Manager</a> 
			<a href="index.php?action=commandstaff&do=roster" class="button">Roster</a> 
			<a href="index.php?action=commandstaff&do=notifications" class="button">Notifications</a> 
			<a href="index.php?action=commandstaff&do=approvetroopers" class="button" id="trooperRequestButton" name="trooperRequestButton">Approve Trooper Requests - ('.$getTrooperNotifications.')</a> ';
			
			if(hasPermission(1))
			{
				echo ' 
				<a href="index.php?action=commandstaff&do=managecostumes" class="button">Costume Management</a> 
				<a href="index.php?action=commandstaff&do=managetroopers" class="button">Trooper Management</a> 
				<a href="index.php?action=commandstaff&do=assignawards" class="button">Award Management</a>
				<a href="index.php?action=commandstaff&do=stats" class="button">Statistics</a>';
			}

			// If have special permission for trooper management
			if(hasSpecialPermission("spTrooper"))
			{
				echo '<a href="index.php?action=commandstaff&do=managetroopers" class="button">Trooper Management</a> ';
			}

			// If have special permission for costume management
			if(hasSpecialPermission("spCostume"))
			{
				echo '<a href="index.php?action=commandstaff&do=managecostumes" class="button">Costume Management</a> ';
			}

			// If have special permission for award management
			if(hasSpecialPermission("spAward"))
			{
				echo '<a href="index.php?action=commandstaff&do=assignawards" class="button">Award Management</a> ';
			}

			echo '
			<a href="index.php?action=commandstaff&do=sitesettings" class="button">Site Settings</a>';
			
		echo '
		</p>';
		
		/**************************** Site Settings *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "sitesettings")
		{
			echo '
			<h3>Site Settings</h3>';
			
			// Get data
			$statement = $conn->prepare("SELECT * FROM settings LIMIT 1");
			$statement->execute();
			$i = 0;

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<form action="process.php?do=changesettings" method="POST" name="changeSettingsForm" id="changeSettingsForm">';
					
					// If site closed, show button
					if($db->siteclosed == 0)
					{
						// Close website button
						echo '
						<input type="submit" name="submitCloseSite" id="submitCloseSite" value="Close Website" />';
					}
					else
					{
						// Open website button
						echo '
						<input type="submit" name="submitCloseSite" id="submitCloseSite" value="Open Website" />';
					}
					
					// If sign up closed, show button
					// If super admin only
					if(hasPermission(1)) {
						if($db->signupclosed == 0)
						{
							// Close sign up button
							echo '
							<input type="submit" name="submitCloseSignUps" id="submitCloseSignUps" value="Close Sign Ups" />';
						}
						else
						{
							// Open sign up button
							echo '
							<input type="submit" name="submitCloseSignUps" id="submitCloseSignUps" value="Open Sign Ups" />';
						}
					}
					
					// Change donation support
					// If super admin only
					if(hasPermission(1)) {
						echo '
						<input type="submit" name="submitSupportGoal" id="submitSupportGoal" value="Change Support Goal" />
						
						<div id="settingsEditArea" name="settingsEditArea"></div>';
					}
						
					echo '
					</form>';
					
					$i++;
				}
			}
			
			if($i == 0)
			{
				echo '<p>ERROR: Settings not correctly set. Check database.</p>';
			}
		}

		/**************************** Trooper Check *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "stats")
		{	
			// Loop through squads
			foreach($squadArray as $squad => $squad_value)
			{
				// Set up name
				$squadName = str_replace(" ", "", $squad_value['name']);
				
				// Set variable
				$statement = $conn->prepare("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = ?");
				$statement->bind_param("i", $squad_value['squadID']);
				$statement->execute();
				$statement->store_result();
				${"totalAccountsSetUp" . $squadName} = $statement->num_rows;
			}
			
			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Set up name
				$clubName = str_replace(" ", "", $club_value['name']);
				
				// Set variable
				$statement = $conn->prepare("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = ?");
				$statement->bind_param("i", $club_value['squadID']);
				$statement->execute();
				$statement->store_result();
				${"totalAccountsSetUp" . $clubName} = $statement->num_rows;
				
				// Count active members
				$statement = $conn->prepare("SELECT id FROM troopers WHERE ".$club_value['db']." = '1'");
				$statement->execute();
				$statement->store_result();
				${"totalActive" . $clubName} = $statement->num_rows;
				
				// Count reserve members
				$statement = $conn->prepare("SELECT id FROM troopers WHERE ".$club_value['db']." = '2'");
				$statement->execute();
				$statement->store_result();
				${"totalReserve" . $clubName} = $statement->num_rows;
				
				// Count retired members
				$statement = $conn->prepare("SELECT id FROM troopers WHERE ".$club_value['db']." = '3'");
				$statement->execute();
				$statement->store_result();
				${"totalRetired" . $clubName} = $statement->num_rows;
			}

			// Extract squadIDs from only $squadArray
			$squadIDs = array_column($squadArray, 'squadID');

			// Build the SQL placeholders and types
			$placeholders = implode(',', array_fill(0, count($squadIDs), '?'));
			$types = str_repeat('i', count($squadIDs));

			$sql = "SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad IN ($placeholders)";
			$statement = $conn->prepare($sql);
			$statement->bind_param($types, ...$squadIDs);
			
			// Count number of users with set up accounts (TOTAL)
			$statement = $conn->prepare("SELECT id FROM troopers WHERE password != '' AND approved = '1'");
			$statement->execute();
			$statement->store_result();
			$totalAccountsSetUp = $statement->num_rows;

			// Total number of accounts
			$statement = $conn->prepare("SELECT id FROM troopers");
			$statement->execute();
			$statement->store_result();
			$totalAccounts = $statement->num_rows;

			// Total accounts not set up
			$totalNotSet = $totalAccounts - $totalAccountsSetUp;
			
			// Count active members - 501
			$statement = $conn->prepare("SELECT id FROM troopers WHERE p501 = '1'");
			$statement->execute();
			$statement->store_result();
			$totalActive501 = $statement->num_rows;
			
			// Count reserve members - 501
			$statement = $conn->prepare("SELECT id FROM troopers WHERE p501 = '2'");
			$statement->execute();
			$statement->store_result();
			$totalReserve501 = $statement->num_rows;
			
			// Count retired members - 501
			$statement = $conn->prepare("SELECT id FROM troopers WHERE p501 = '3'");
			$statement->execute();
			$statement->store_result();
			$totalRetired501 = $statement->num_rows;

			echo '
			<h2>Important People</h2>
			<h3>Super Admin</h3>
			<div class="stat-container">';

			// Show all super admins
			$statement = $conn->prepare("SELECT troopers.id, troopers.name, troopers.tkid, troopers.note, troopers.squad FROM troopers WHERE (permissions = '1') ORDER BY name");
			$statement->execute();

			// Trooper count set up
			$i = 0;

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<div class="title">
						<a href="index.php?profile='.$db->id.'" target="_blank">'.$db->name.'<br /><br />'.readTKNumber($db->tkid, $db->squad, $db->id).'</a>
						'.($db->note != "" ? '<br /><br />' . $db->note : '').'
					</div>';
					
					// Increment
					$i++;
				}
			}
			
			// No troopers
			if($i == 0)
			{
				echo '<p>No troopers to display.</p>';
			}

			echo '
			</div>

			<h3>Moderator</h3>
			<div class="stat-container">';

			// Show all super admins
			$statement = $conn->prepare("SELECT troopers.id, troopers.name, troopers.tkid, troopers.note, troopers.squad FROM troopers WHERE (permissions = '2') ORDER BY name");
			$statement->execute();

			// Trooper count set up
			$i = 0;

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<div class="title">
						<a href="index.php?profile='.$db->id.'" target="_blank">'.$db->name.'<br /><br />'.readTKNumber($db->tkid, $db->squad, $db->id).'</a>
						'.($db->note != "" ? '<br /><br />' . $db->note : '').'
					</div>';
					
					// Increment
					$i++;
				}
			}
			
			// No troopers
			if($i == 0)
			{
				echo '<p>No troopers to display.</p>';
			}

			echo '
			</div>

			<div style="text-align: center;">

			<br />
			<hr />
			<br />

			<h2>Statistics</h2>';

			// Get settings
			$statement = $conn->prepare("SELECT * FROM settings");
			$statement->execute();

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<p>
						<b>501st Sync Date:</b> '.$db->syncdate.'
					</p>
					<p>
						<b>Rebel Legion Sync Date:</b> '.$db->syncdaterebels.'
					</p>';
				}
			}

			echo '
			<br />
			<hr />
			<br />
			
			<h3>Troop Tracker Usage</h3>';

			$totalAccountsSetUp501 = 0;
			
			// Loop through squads
			foreach($squadArray as $squad => $squad_value)
			{
				// Set up name
				$squadName = str_replace(" ", "", $squad_value['name']);
				
				echo '
				<p><b>'.$squad_value['name'].' Total Accounts (Set Up):</b> '.number_format(${"totalAccountsSetUp" . $squadName}).'</p>';
				
				// Set variable
				$statement = $conn->prepare("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = ?");
				$statement->bind_param("i", $squad_value['squadID']);
				$statement->execute();
				$statement->store_result();
				${"totalAccountsSetUp" . $squadName} = $statement->num_rows;
				$totalAccountsSetUp501 += $statement->num_rows;
			}
			
			echo '
			<p><b>501st Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUp501).'</p>

			<br />
			<hr />
			<br />';
			
			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Set up name
				$clubName = str_replace(" ", "", $club_value['name']);
				
				echo '
				<p><b>'.$club_value['name'].' Total Accounts (Set Up):</b> ' . number_format(${"totalAccountsSetUp" . $clubName}) . '</p>';
			}
			
			echo '
			<br />
			<hr />
			<br />
			
			<p><b>Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUp).'</p>
			<p><b>Total Accounts (Not Set Up):</b> '.number_format($totalNotSet).'</p>
			<p><b>Total Accounts:</b> '.number_format($totalAccounts).'</p>
			
			<br />
			<hr />
			<br />
			
			<p><b>501st Total Accounts (Active):</b> '.number_format($totalActive501).'</p>
			<p><b>501st Total Accounts (Reserve):</b> '.number_format($totalReserve501).'</p>
			<p><b>501st Total Accounts (Retired):</b> '.number_format($totalRetired501).'</p>
			
			<br />
			<hr />
			<br />';
			
			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Set up name
				$clubName = str_replace(" ", "", $club_value['name']);
				
				echo '
				<p><b>'.$club_value['name'].' Total Accounts (Active):</b> ' . number_format(${"totalActive" . $clubName}) . '</p>
				<p><b>'.$club_value['name'].' Total Accounts (Reserve):</b> ' . number_format(${"totalReserve" . $clubName}) . '</p>
				<p><b>'.$club_value['name'].' Total Accounts (Retired):</b> ' . number_format(${"totalRetired" . $clubName}) . '</p>
				
				<br />
				<hr />
				<br />';
			}

			echo '
			</div>';
		}

		/**************************** Roster - Trooper Confirmation *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "trooperconfirmation" && isAdmin())
		{
			echo '
			<div class="section-card">
				<h2 class="tm-section-header">Trooper Confirmation - Squads/Clubs</h2>
			
				<p>
					<i>The following troopers have not confirmed a troop.</i>
				</p>

				<a href="index.php?action=commandstaff&do=trooperconfirmation" class="button">All</a>';

				foreach($squadArray as $squad => $squad_value)
				{
					if(getSquadID($_SESSION['id']) == $squad_value['squadID'] || hasPermission(1))
					{
						echo '<a href="index.php?action=commandstaff&do=trooperconfirmation&squad='.$squad_value['squadID'].'" class="button">' . $squad_value['name'] . '</a> ';
					}
				}
				
				foreach($clubArray as $club => $club_value)
				{
					if(isClubMember($club_value['db']) > 0 || hasPermission(1))
					{
						echo '<a href="index.php?action=commandstaff&do=trooperconfirmation&squad='.$club_value['squadID'].'" class="button">' . $club_value['name'] . '</a> ';
					}
				}
			
			echo '
			</div>

			<div class="section-card">
				<h2 class="tm-section-header">Admin Tools</h2>
				<a href="index.php?action=commandstaff&do=roster" class="button">Roster</a>
				<a href="index.php?action=commandstaff&do=troopercheck" class="button">Trooper Check</a>
			</div>';
			
			// Query count
			$i = 0;

			// Set trooper name
			$trooperName = "";

			// Query

			// Check if squad is requested
			if(isset($_GET['squad']))
			{
				// Which club to get
				if(in_array($_GET['squad'], $validSquadIDs))
				{
					$statement = $conn->prepare("SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status, event_sign_up.costume, event_sign_up.note, troopers.name AS trooperName FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE troopers.squad = ? AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1 AND troopers.id != 0 ORDER BY troopers.name");
					$statement->bind_param("i", $_GET['squad']);

					// Check if a member of club
					if(getSquadID($_SESSION['id']) != $_GET['squad'] && hasPermission(2))
					{
						die("<p>Not a member of this squad / club.</p>");
					}
				} else {
					$dbValue = getClubBySquadID($_GET['squad'])['db'];				

					$statement = $conn->prepare("SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status, event_sign_up.costume, event_sign_up.note, troopers.name AS trooperName FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE (troopers.".$dbValue." > 0) AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1 AND troopers.id != 0 ORDER BY troopers.name");

					// Check if a member of club
					if(isClubMember($dbValue) == 0 && hasPermission(2))
					{
						die("<p>Not a member of this squad / club.</p>");
					}
				}
			} else {
				$statement = $conn->prepare("SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status, event_sign_up.costume, event_sign_up.note, troopers.name AS trooperName FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id LEFT JOIN troopers ON event_sign_up.trooperid = troopers.id WHERE events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1 AND troopers.id != 0 ORDER BY troopers.name");
			}

			$statement->execute();
			
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Check if this is a different trooper
					if($trooperName != $db->trooperName)
					{
						// If not first
						if($i != 0)
						{
							echo '<hr />';
						}

						echo '
						<h2 class="tm-section-header">'.$db->trooperName.'</h2>';

						// Reset
						$trooperName = $db->trooperName;
					}

					echo '
					<p class="trooper-confirmation-box" signid="'.$db->signupId.'">
						<a href="index.php?event='.$db->eventId.'" target="_blank">'. (isLink($db->eventId) > 0 ? '[<b>' . date("l", strtotime($db->dateStart)) . '</b> : <i>' . date("m/d - h:i A", strtotime($db->dateStart)) . ' - ' . date("h:i A", strtotime($db->dateEnd)) . '</i>] ' : '') .''.$db->name.' '.$db->trooperName.' '.($db->note != '' ? 'noted as ' . $db->note : '').'</a>
						<br />
						<b>Attended As:</b> '.getCostume($db->costume).'
						<br /><br />
						<a href="#/" class="button" name="attend-button" status="3" signid="'.$db->signupId.'">Y</a>	<a href="#/" class="button" name="attend-button" status="4" signid="'.$db->signupId.'">N</a>
					</p>';

					// Increment
					$i++;
				}
			}
			
			// If data exists
			if($i > 0)
			{
				echo '
				</table>';
			}
			else
			{
				echo '
				<p style="text-align: center;">Nothing to display for this squad / club.</p>';
			}
		}

		/**************************** Roster *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "roster" && isAdmin())
		{
			echo '
			<div class="section-card">
				<h2 class="tm-section-header">Roster - Squads/Clubs</h2>

				<a href="index.php?action=commandstaff&do=roster" class="button">All</a> 
				<a href="index.php?action=commandstaff&do=roster&squad=all" class="button">All (501st)</a> ';
				
				foreach($squadArray as $squad => $squad_value)
				{
					if(getSquadID($_SESSION['id']) == $squad_value['squadID'] || hasPermission(1))
					{
						echo '<a href="index.php?action=commandstaff&do=roster&squad='.$squad_value['squadID'].'" class="button">' . $squad_value['name'] . '</a> ';
					}
				}
				
				foreach($clubArray as $club => $club_value)
				{
					if(isClubMember($club_value['db']) > 0 || hasPermission(1))
					{
						echo '<a href="index.php?action=commandstaff&do=roster&squad='.$club_value['squadID'].'" class="button">' . $club_value['name'] . '</a> ';
					}
				}
			
			echo '
			</div>

			<div class="section-card">
				<h2 class="tm-section-header">Admin Tools</h2>
				<a href="index.php?action=commandstaff&do=troopercheck" class="button">Trooper Check</a>
				<a href="index.php?action=commandstaff&do=trooperconfirmation" class="button">Trooper Confirmation</a>
			</div>';
			
			// Check if on a squad
			if(isset($_GET['squad']) && $_GET['squad'] != "all")
			{
				echo '
				<a href="#addtrooper" class="button">Add Trooper</a>';
			}
			
			// Check if squad is requested
			if(isset($_GET['squad']))
			{	
				// Which club to get
				if($_GET['squad'] == "all")
				{
					$statement = $conn->prepare("SELECT * FROM troopers WHERE p501 > 0 AND id != ".placeholder." AND approved = '1' ORDER BY name");
				}
				else if(in_array($_GET['squad'], $validSquadIDs))
				{
					$statement = $conn->prepare("SELECT * FROM troopers WHERE p501 > 0 AND squad = ? AND id != ".placeholder." AND approved = '1' ORDER BY name");
					$statement->bind_param("i", $_GET['squad']);

					// Check if a member of club
					if(getSquadID($_SESSION['id']) != $_GET['squad'] && hasPermission(2))
					{
						die("<p>Not a member of this squad / club.</p>");
					}
				} else {
					$dbValue = getClubBySquadID($_GET['squad'])['db'];			

					$statement = $conn->prepare("SELECT * FROM troopers WHERE ".$dbValue." > 0 AND id != ".placeholder." AND approved = '1' ORDER BY name");

					// Check if a member of club
					if(isClubMember($dbValue) == 0 && hasPermission(2))
					{
						die("<p>Not a member of this squad / club.</p>");
					}
				}
			} else {
				$statement = $conn->prepare("SELECT * FROM troopers WHERE id != ".placeholder." AND approved = '1' ORDER BY name");
			}
			
			// Query count
			$i = 0;

			// If squad set, have hidden field
			if(isset($_GET['squad']))
			{
				echo '
				<input type="hidden" name="club" id="club" value="'.cleanInput($_GET['squad']).'" />';
			}
			
			echo '
			<div style="overflow-x: auto;">
			<table id="masterRosterTable">
				<tr>
					<th>Name</th>
					<th>Board Name</th>';

					// Only show for clubs with DB3
					if (isset($_GET['squad']) && $_GET['squad'] !== "all") {
						$squadID = (int) $_GET['squad'];
						$clubName = '';
					
						foreach ($clubArray as $club) {
							if ((int) $club['squadID'] === $squadID && !empty($club['db3'])) {
								$clubName = $club['db3Name'];
								break;
							}
						}
					
						if (!empty($clubName)) {
							echo '<th>' . htmlspecialchars($clubName) . '</th>';
						}
					}								

					// Only show TKID for 501st
					if(isset($_GET['squad']) && in_array($_GET['squad'], $validSquadIDs))
					{
						echo '
						<th>TKID</th>';
					}
					
					// Only show if squad set
					if(isset($_GET['squad']))
					{
						echo '
						<th><a href="#/" id="sort-roster">Status</a></th>';
					}
					
				echo '
				</tr>';

			$statement->execute();
			
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{					
					echo '
					<tr name="row_'.$db->id.'">
						<td>
							'. (isset($_GET['squad']) && (in_array($_GET['squad'], $validSquadIDs) || $_GET['squad'] == "all") ? (ifIn501Roster($db->tkid, $_GET['squad']) ? '' : '<a href="https://www.501st.com/memberAPI/v3/legionId/' . $db->tkid . '" target="_blank">(?)</a> ') : '') . '<a href="index.php?profile='.$db->id.'" target="_blank">' . $db->name.'</a>
						</td>

						<td>
							'. (($db->user_id > 0) ? '<a href="'.$forumURL.'members/'.$db->forum_id.'.'.$db->user_id.'" target="_blank">'.$db->forum_id.'</a>' : $db->forum_id) .'
						</td>';

						if (isset($_GET['squad']) && $_GET['squad'] !== "all") {
							$squadID = (int) $_GET['squad'];
							foreach ($clubArray as $club) {
								if ((int) $club['squadID'] === $squadID && !empty($club['db3'])) {
									$field = $club['db3'];
									$value = $db->{$field};
									echo '
									<td>
										<input type="text" name="changedb3" db3="' . htmlspecialchars($field) . '" trooperid="' . $db->id . '" db3value="' . htmlspecialchars($value) . '" value="' . htmlspecialchars($value) . '" />
									</td>';
									break;
								}
							}
						}						

						if(isset($_GET['squad']) && in_array($_GET['squad'], $validSquadIDs))
						{
							echo '
							<td>
								<input type="number" name="changetkid" tkid="'.$db->tkid.'" trooperid="'.$db->id.'" value="'.$db->tkid.'" />
							</td>';
						}
						
						// Only show if squad set
						if(isset($_GET['squad']))
						{
							// Set up permission variable
							$permission = "";
							
							// Which club to get
							if(in_array($_GET['squad'], $validSquadIDs) || $_GET['squad'] == "all")
							{
								$permission = $db->p501;
							}
							
							// Loop through clubs
							foreach($clubArray as $club => $club_value)
							{
								// Match
								if($_GET['squad'] == $club_value['squadID'])
								{
									// Set permission
									$permission = $db->{$club_value['db']};
								}
							}
							
							// If a moderator
							if($db->permissions == 1 || $db->permissions == 2)
							{
								// Don't allow to edit
								echo '
								<td name="permission-box">
									'.getPermissionName($db->permissions).'
								</td>';
							}
							else
							{
								// Not moderator - allow edit
								echo '
								<td name="permission-box">
									<select name="changepermission" trooperid="'.$db->id.'">
										<option value="0" '.echoSelect(0, $permission).'>Not A Member</option>
										<option value="1" '.echoSelect(1, $permission).'>Regular Member</option>
										<option value="2" '.echoSelect(2, $permission).'>Reserve Member</option>
										<option value="3" '.echoSelect(3, $permission).'>Retired Member</option>
										<option value="4" '.echoSelect(4, $permission).'>Handler</option>
									</select>
								</td>';
							}
						}
		
					echo '
					</tr>';
					
					// Increment
					$i++;
				}
			}

			echo '
			</table>
			</div>';
			
			// If no data exists
			if($i <= 0)
			{
				echo '
				<p style="text-align: center;" id="nothing_text">Nothing to display for this squad / club.</p>';
			}
			
			if(isset($_GET['squad']) && $_GET['squad'] != "all")
			{
				// Amount of users
				$i = 0;

				// Which club to get
				if(in_array($_GET['squad'], $validSquadIDs))
				{
					// 501
					$statement = $conn->prepare("SELECT * FROM troopers WHERE p501 = 0 AND id != ".placeholder." ORDER BY name");
				} else {
					// Club
					$dbValue = getClubBySquadID($_GET['squad'])['db'];					

					$statement = $conn->prepare("SELECT * FROM troopers WHERE " . $dbValue . " = 0 AND id != ".placeholder." ORDER BY name");
				}

				$statement->execute();

				if($result = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						// Formatting
						if($i == 0)
						{
							echo '
							<h3 id="addtrooper">Add Trooper</h3>
							<form action="process.php?do=addmasterroster" method="POST" id="addMasterRosterForm">
							<input type="hidden" name="squad" value="'.cleanInput($_GET['squad']).'" />
							<select name="userID" id="userID">';
						}

						echo '
						<option
						value="'.$db->id.'"
						tkid="'.readTKNumber($db->tkid, $db->squad, $db->id).'"
						tkidBasic="'.$db->tkid.'"
						troopername="'.$db->name.'"
						forum_id="'.$db->forum_id.'"';

						if (isset($_GET['squad'])) {
							$squadID = (int) $_GET['squad'];
						
							foreach ($clubArray as $club) {
								if ((int) $club['squadID'] === $squadID && !empty($club['db3'])) {
									$field = $club['db3'];
									$value = $db->{$field};
									echo '
									db3="' . htmlspecialchars($field) . '"
									idvalue="' . htmlspecialchars($value) . '"';
									break;
								}
							}
						}						

						echo '
						>'.$db->name.' - '.$db->forum_id.' - '.readTKNumber($db->tkid, $db->squad, $db->id).'</option>';

						// Increment
						$i++;
					}
				}
				
				// If troopers exist
				if($i > 0)
				{
					echo '
					</select>
					
					<input type="submit" value="Add Trooper" id="addTrooperMaster" />
					</form>';
				}
				else
				{
					echo '
					<p id="addtrooper"><i>No troopers to add. Make sure they have a Rebel Legion Forum username assigned to their account.</i></p>';
				}
			}
		}
		
		/**************************** Trooper Check *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "troopercheck" && isAdmin())
		{
			echo '
			<div class="section-card">
				<h2 class="tm-section-header">Trooper Check - Squads/Clubs</h2>
			
				<p>
					<i>The following troopers do not have a documented troop from the past year. Retired members do not show on this list.</i>
				</p>

				<a href="index.php?action=commandstaff&do=troopercheck" class="button">All</a>';

				foreach($squadArray as $squad => $squad_value)
				{
					if(getSquadID($_SESSION['id']) == $squad_value['squadID'] || hasPermission(1))
					{
						echo '<a href="index.php?action=commandstaff&do=troopercheck&squad='.$squad_value['squadID'].'" class="button">' . $squad_value['name'] . '</a> ';
					}
				}
				
				foreach($clubArray as $club => $club_value)
				{
					if(isClubMember($club_value['db']) > 0 || hasPermission(1))
					{
						echo '<a href="index.php?action=commandstaff&do=troopercheck&squad='.$club_value['squadID'].'" class="button">' . $club_value['name'] . '</a> ';
					}
				}
			
			echo '
			</div>

			<div class="section-card">
				<h2 class="tm-section-header">Admin Tools</h2>
				<a href="index.php?action=commandstaff&do=roster" class="button">Roster</a>
				<a href="index.php?action=commandstaff&do=trooperconfirmation" class="button">Trooper Confirmation</a>
			</div>';
			
			// Check if squad is requested
			if(isset($_GET['squad']))
			{
				// Which club to get
				if(in_array($_GET['squad'], $validSquadIDs))
				{
					$statement = $conn->prepare("SELECT * FROM troopers WHERE troopers.squad = ? AND (p501 = 1 OR p501 = 2) AND approved = '1' AND troopers.permissions = 0 AND troopers.id NOT IN (SELECT event_sign_up.trooperid FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.status = '3' AND events.dateEnd > NOW() - INTERVAL 1 YEAR) ORDER BY troopers.name");
					$statement->bind_param("i", $_GET['squad']);

					// Check if a member of club
					if(getSquadID($_SESSION['id']) != $_GET['squad'] && hasPermission(2))
					{
						die("<p>Not a member of this squad / club.</p>");
					}
				} else {
					// Club
					$dbValue = getClubBySquadID($_GET['squad'])['db'];	

					$statement = $conn->prepare("SELECT * FROM troopers WHERE (troopers." . $dbValue . " = 1 OR troopers." .  $dbValue . " = 2) AND approved = '1' AND troopers.permissions = 0 AND troopers.id NOT IN (SELECT event_sign_up.trooperid FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.status = '3' AND events.dateEnd > NOW() - INTERVAL 1 YEAR) ORDER BY troopers.name");

					// Check if a member of club
					if(isClubMember($dbValue) == 0 && hasPermission(2))
					{
						die("<p>Not a member of this squad / club.</p>");
					}
				}
			} else {
				$statement = $conn->prepare("SELECT * FROM troopers WHERE approved = '1' AND troopers.permissions = 0 AND troopers.id NOT IN (SELECT event_sign_up.trooperid FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.status = '3' AND events.dateEnd > NOW() - INTERVAL 1 YEAR) ORDER BY troopers.name");
			}
			
			// Query count
			$i = 0;

			$statement->execute();
			
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// If first data
					if($i == 0)
					{
						echo '
						<form action="process.php?do=troopercheck" method="POST" name="trooperCheckForm" id="trooperCheckForm">';
						
						// If squad set, have hidden field
						if(isset($_GET['squad']))
						{
							echo '
							<input type="hidden" name="club" value="'.cleanInput($_GET['squad']).'" />';
						}
						
						echo '
						<div style="overflow-x: auto;">
						<table>
							<tr>';
								// If squad set
								if(isset($_GET['squad']))
								{
									echo '
									<th>Selection</th>';
								}
								
								echo '
								<th>Name</th>	<th>Board Name</th>	<th>TKID</th>';
								
								// If squad set
								if(isset($_GET['squad']))
								{
									echo '
									<th>Tracker Status</th>';
								}
							
							echo '
							</tr>';
					}
					
					echo '
					<tr name="row_'.$db->id.'">';
					
						// If squad set
						if(isset($_GET['squad']))
						{
							echo '
							<td>
								<input type="checkbox" name="trooper[]" value="'.$db->id.'" />
							</td>';
						}
						
						echo '
						<td>
							<a href="index.php?profile='.$db->id.'" target="_blank">'.$db->name.'</a>
						</td>

						<td>
							'.$db->forum_id.'
						</td>
						
						<td>
							'.readTKNumber($db->tkid, $db->squad, $db->id).'
						</td>';
						
						// If squad set
						if(isset($_GET['squad']))
						{
							// Set up permission variable
							$permission = "";
							
							// Which club to get
							if(in_array($_GET['squad'], $validSquadIDs))
							{
								$permission = $db->p501;
							}
							
							// Loop through clubs
							foreach($clubArray as $club => $club_value)
							{
								// Match
								if($_GET['squad'] == $club_value['squadID'])
								{
									// Set permission
									$permission = $db->{$club_value['db']};
								}
							}
						
							echo '
							<td name="permission">
								'.getClubPermissionName($permission).'
							</td>';
						}
						
					echo '
					</tr>';
					
					// Increment
					$i++;
				}
			}
			
			// If data exists
			if($i > 0)
			{
				echo '
				</table>
				</div>';
				
				if(isset($_GET['squad']))
				{
					echo '
					<input type="submit" name="submitTroopCheckReserve" id="submitTroopCheckReserve" value="Change to Reserve" />
					<input type="submit" name="submitTroopCheckRetired" id="submitTroopCheckRetired" value="Change to Retired" />';
				}
				
				echo '
				</form>';
			}
			else
			{
				echo '
				<p style="text-align: center;">Nothing to display for this squad / club.</p>';
			}
		}
		
		/**************************** Notifications *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "notifications")
		{
			echo '
			<h3>Notifications</h3>
			
			<p>
				<a href="index.php?action=commandstaff&do=notifications" class="button">All</a>
				<a href="index.php?action=commandstaff&do=notifications&s=system" class="button">System</a>
				<a href="index.php?action=commandstaff&do=notifications&s=troopers" class="button">Troopers</a>
			</p>';
			
			// Set results per page
			$results = 100;
		
			// Add to query if in URL
			if(isset($_GET['s']) && $_GET['s'] == "system")
			{
				// Get total results - query
				$statement2 = $conn->prepare("SELECT COUNT(id) AS total FROM notifications WHERE message NOT LIKE '%now has%'");
				$statement2->execute();
				$statement2->bind_result($rowPage);
				$statement2->fetch();
				$statement2->close();
			}
			else if(isset($_GET['s']) && $_GET['s'] == "troopers")
			{
				// Get total results - query
				$statement2 = $conn->prepare("SELECT COUNT(id) AS total FROM notifications WHERE message LIKE '%now has%'");
				$statement2->execute();
				$statement2->bind_result($rowPage);
				$statement2->fetch();
				$statement2->close();
			}
			else
			{
				// Get total results - query
				$statement2 = $conn->prepare("SELECT COUNT(id) AS total FROM notifications");
				$statement2->execute();
				$statement2->bind_result($rowPage);
				$statement2->fetch();
				$statement2->close();
			}
			
			// Set total pages
			$total_pages = ceil($rowPage / $results);
			
			// If page set
			if(isset($_GET['page']))
			{
				// Get page
				$page = $_GET['page'];
				
				// Start from
				$startFrom = ($page - 1) * $results;
			}
			else
			{
				// Default
				$page = 1;
				
				// Start from - default
				$startFrom = 0;
			}

			// Add to query if in URL
			if(isset($_GET['s']) && $_GET['s'] == "system")
			{
				// Get data
				$statement = $conn->prepare("SELECT * FROM notifications WHERE message NOT LIKE '%now has%' ORDER BY id DESC LIMIT ".$startFrom.", ".$results."");
			}
			else if(isset($_GET['s']) && $_GET['s'] == "troopers")
			{
				// Get data
				$statement = $conn->prepare("SELECT * FROM notifications WHERE message LIKE '%now has%' ORDER BY id DESC LIMIT ".$startFrom.", ".$results."");
			}
			else
			{
				// Get data
				$statement = $conn->prepare("SELECT * FROM notifications ORDER BY id DESC LIMIT ".$startFrom.", ".$results."");
			}
			
			// Set notification count
			$i = 0;

			$statement->execute();
			
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					if($i == 0)
					{
						echo '<ul>';
					}
					
					// Format Date
					$dateF = formatTime($db->datetime, "m-d-Y H:i:s");

					// Set JSON value
					$json = "";

					// Check JSON value if blank
					if($db->json == "")
					{
						// Set up
						$add = "";

						// Does not contain now has
						if(strpos($db->message, "now has") === false && strpos($db->message, "donated") === false)
						{
							$add = "Staff ";
						}

						// Nothing to show
						$json = '<a href="index.php?profile='.$db->trooperid.'" target="_blank" class="button">View '.$add.'Profile</a>';

						if (preg_match('/\[(\d+)\]/', $db->message, $matches) && strpos($db->message, "event ID") !== false) {
						    // $matches[1] will contain the event ID
						    $eventId = $matches[1];
						    $json .= ' <a href="index.php?event='.$eventId.'" target="_blank" class="button">View Troop</a>';
						}
					}
					else
					{
						// Show value
						$json = '
						<textarea width="100%" rows="5" disabled>' . $db->json . '</textarea>
						<br />
						<a href="index.php?profile='.$db->trooperid.'" target="_blank" class="button">View Staff Profile</a>';

						// Decode JSON data - Avoid null results
						$data = json_decode(htmlspecialchars_decode($db->json, ENT_QUOTES), true);

						// If array or object
						if(is_array($data) || is_object($data))
						{
							// Loop through JSON data
							foreach($data as $key => $value)
							{
								// Troop ID
								if($key == "trooperid")
								{
									$json .= ' <a href="index.php?profile='.$value.'" target="_blank" class="button">View Trooper</a>';
								}

								// Trooper ID as ID
								if($key == "id" && strpos($db->message, "user") !== false && !isset($data->trooperid))
								{
									$json .= ' <a href="index.php?profile='.$value.'" target="_blank" class="button">View Trooper</a>';
								}

								// Troop ID
								if($key == "troopid")
								{
									$json .= ' <a href="index.php?event='.$value.'" target="_blank" class="button">View Troop</a>';
								}

								// Troop ID as ID
								if($key == "id" && strpos($db->message, "event") !== false && strpos($db->message, "updating trooper") === false && strpos($db->message, "added trooper") === false && !isset($data['troopid']))
								{
									$json .= ' <a href="index.php?event='.$value.'" target="_blank" class="button">View Troop</a>';
								}
							}
						}
					}
					
					echo '
					<li>
						<a href="#/" name="jsonshow" json="'.$db->id.'">'.$db->message.'</a> on '.$dateF.'.

						<p>
							<div style="display: none;" name="json'.$db->id.'">'.$json.'</div>
						</p>
					</li>';
					
					$i++;
				}
			}
			
			// No notifications to display
			if($i == 0)
			{
				echo '<p>No notifications to display.</p>';
			}
			else
			{
				// Finish HTML list - notifications exist
				echo '</ul>';
			}
			
			// If notifications
			if($total_pages > 1)
			{
				echo '<p>Pages: ';
				
				// Loop through pages
				for ($j = 1; $j <= $total_pages; $j++)
				{
					// If we are on this page...
					if($page == $j)
					{
						echo '
						'.$j.'';
					}
					else
					{
						// Set up preference
						$s = "";
						
						// Add preference to search if set
						if(isset($_GET['s']))
						{
							$s = "&s=" . cleanInput($_GET['s']);
						}
						echo '
						<a href="index.php?action=commandstaff&do=notifications&page='.$j.''.$s.'">'.$j.'</a>';
					}
					
					// If not that last page, add a comma
					if($j != $total_pages)
					{
						echo ', ';
					}
				}
				
				echo '</p>';
			}
		}

		/**************************** COSTUMES *********************************/

		// Manage costumes - allow command staff to add, edit, and delete costumes
		if(isset($_GET['do']) && $_GET['do'] == "managecostumes" && (hasPermission(1) || hasSpecialPermission("spCostume")))
		{
			echo '
			<h3>Add Costume</h3>
			
			<form action="process.php?do=managecostumes" method="POST" name="addCostumeForm" id="addCostumeForm">
			
				<p>
					<b>Costume Name:</b></br />
					<input type="text" name="costumeName" id="costumeName" />
				</p>
				
				<p>
					<b>Costume Club:</b></br />
					<select name="costumeClub" id="costumeClub">
						<option value="0" SELECTED>501st Legion</option>';

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							echo '
							<option value="'.$club_value['costumes'][0].'">'.$club_value['name'].'</option>';
						}

						// Loop through dual costume labels for dual club options
						foreach ($dualCostumeLabels as $id => $label) {
							echo '<option value="'.htmlspecialchars($id).'">'.htmlspecialchars($label).'</option>';
						}

					echo '
					</select>
				</p>
				
				<input type="submit" name="addCostumeButton" id="addCostumeButton" value="Add Costume" />
				
			</form>
				
			<br />
			<hr />
			<br />
			
			<div id="costumearea" name="costumearea">
			
			<h3>Edit Costume</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM costumes ORDER BY costume");
			$statement->execute();

			$i = 0;
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=managecostumes" method="POST" name="costumeEditForm" id="costumeEditForm">

						<select name="costumeIDEdit" id="costumeIDEdit">

							<option value="0" SELECTED>Please select a costume...</option>';
					}

					echo '
					<option value="'.$db->id.'" costumeName="'.$db->costume.'" costumeID="'.$db->id.'" costumeClub="'.$db->club.'">'.getCostumeAbbreviation($db->club).' '.$db->costume.'</option>';


					// Increment
					$i++;
				}
			}

			if($i == 0)
			{
				echo 'No costumes to display.';
			}
			else
			{
				echo '
				</select>

				<br /><br />

				<div id="editCostumeList" name="editCostumeList" style="display: none;">

				<p>
					<b>Costume Name:</b></br />
					<input type="text" name="costumeNameEdit" id="costumeNameEdit" />
				</p>

				<p>
					<b>Costume Club:</b></br />
					<select name="costumeClubEdit" id="costumeClubEdit">
						<option value="0" SELECTED>501st Legion</option>';

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							echo '
							<option value="'.$club_value['costumes'][0].'">'.$club_value['name'].'</option>';
						}

						// Loop through dual costume labels for dual club options
						foreach ($dualCostumeLabels as $id => $label) {
							echo '<option value="'.htmlspecialchars($id).'">'.htmlspecialchars($label).'</option>';
						}

					echo '
					</select>
				</p>

				<input type="submit" name="submitEditCostume" id="submitEditCostume" value="Edit Costume" />

				</div>
				</form>';
			}
			
			echo '
			<br />
			<hr />
			<br />
		
			<h3>Delete Costume</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM costumes ORDER BY costume");
			$statement->execute();

			$i = 0;
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=managecostumes" method="POST" name="costumeDeleteForm" id="costumeDeleteForm">

						<select name="costumeID" id="costumeID">';
					}

					echo '<option value="'.$db->id.'">'.getCostumeAbbreviation($db->club).' '.$db->costume.'</option>';

					// Increment
					$i++;
				}
			}

			if($i == 0)
			{
				echo 'No costumes to display.';
			}
			else
			{
				echo '
				</select>

				<input type="submit" name="submitDeleteCostume" id="submitDeleteCostume" value="Delete Costume" />
				</form>';
			}
			
			echo '
			</div>';
		}
		
		/**************************** AWARDS *********************************/

		// Assign an award to users
		if(isset($_GET['do']) && $_GET['do'] == "assignawards" && (hasPermission(1) || hasSpecialPermission("spTrooper")))
		{
			echo '<h3>Assign Awards</h3>
			
			<div name="assignarea" id="assignarea">';

			// Get data
			$statement = $conn->prepare("SELECT * FROM troopers WHERE approved = 1 ORDER BY name");
			$statement->execute();

			// Amount of users
			$i = 0;
			$getId = 0;

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						$getId = $db->id;

						echo '
						<form action="process.php?do=assignawards" method="POST" name="awardUser" id="awardUser">

						<select name="userIDAward" id="userIDAward">';
					}

					echo '<option value="'.$db->id.'">'.readInput($db->name).' - '.readTKNumber($db->tkid, $db->squad, $db->id).' - '.$db->forum_id.'</option>';

					// Increment
					$i++;
				}
			}

			// If no events
			if($i == 0)
			{
				echo 'There are no troopers to display.';
			}
			else
			{
				echo '
				</select>

				<br /><br />';

				// Get data
				$statement = $conn->prepare("SELECT * FROM awards ORDER BY title");
				$statement->execute();

				// Amount of awards
				$j = 0;

				if ($result2 = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result2))
					{
						// Formatting
						if($j == 0)
						{
							$getId2 = $db->id;

							echo '<select id="awardIDAssign" name="awardIDAssign">';
						}

						echo '<option value="'.$db->id.'">'.readInput($db->title).'</option>';

						// Increment $j
						$j++;
					}
				}

				// If awards exist
				if($j > 0)
				{
					echo '
					</select>

					<input type="submit" name="award" id="award" value="Assign" '.hasAward($getId, $getId2, true).' /> <input type="submit" name="awardRemove" id="awardRemove" value="Remove" '.hasAward($getId, $getId2, true, true).' />';
				}
				else
				{
					echo 'No awards to display.';
				}
			}

			echo '</form></div>';

			echo '<br /><hr /><br /><h3>Create Award</h3>

			<form action="process.php?do=assignawards" method="POST" name="addAward" id="addAward">
				<p>
					<b>Award Name:</b></br />
					<input type="text" name="awardName" id="awardName" />
				</p>

				<p>
					<b>Award Image (example.png):</b></br />
					<input type="text" name="awardImage" id="awardImage" />
				</p>
				<input type="submit" name="submitAwardAdd" id="submitAwardAdd" value="Add Award" />
			</form>';

			echo '
			<div id="awardarea">
			<br /><hr /><br />
			<h3>Edit Award</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM awards ORDER BY title");
			$statement->execute();

			$i = 0;
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=assignawards" method="POST" name="awardEdit" id="awardEdit">

						<select name="awardIDEdit" id="awardIDEdit">

							<option value="0" SELECTED>Please select an award...</option>';
					}

					echo '<option value="'.$db->id.'" awardTitle="'.readInput($db->title).'" awardID="'.$db->id.'" awardImage="'.$db->icon.'">'.readInput($db->title).'</option>';

					// Increment
					$i++;
				}
			}

			if($i == 0)
			{
				echo 'No awards to display.';
			}
			else
			{
				echo '
				</select>

				<br /><br />

				<div id="editAwardList" name="editAwardList" style="display: none;">

				<p>
					<b>Award Title:</b><br />
					<input type="text" name="editAwardTitle" id="editAwardTitle" />
				</p>

				<p>
					<b>Award Image:</b><br />
					<input type="text" name="editAwardImage" id="editAwardImage" />
				</p>

				<input type="submit" name="submitEditAward" id="submitEditAward" value="Edit Award" />

				</div>
				</form>';
			}

			echo '<br /><hr /><br /><h3>Delete Award</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM awards ORDER BY title");
			$statement->execute();

			$i = 0;
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=assignawards" method="POST" name="awardUserDelete" id="awardUserDelete">
						<select name="awardID" id="awardID">';
					}

					echo '<option value="'.$db->id.'">'.readInput($db->title).'</option>';

					// Increment
					$i++;
				}
			}

			if($i == 0)
			{
				echo 'No awards to display.';
			}
			else
			{
				echo '
				</select>

				<input type="submit" name="submitDeleteAward" id="submitDeleteAward" value="Delete Award" />
				</form>';
			}
			
			echo '
			</div>';
		}
		
		/************** EVENTS ******************/

		// Update an event form
		if(isset($_GET['do']) && $_GET['do'] == "editevent")
		{
			echo '
			<h3>Edit an Event</h3>';
			
			// If admin is visting this page from the event page
			
			// We use this value to determine which event is selected
			$eid = -1;
			
			// If eid set, set eid
			if(isset($_GET['eid']))
			{
				$eid = $_GET['eid'];
			}

			// Get data
			// If edid set - this makes sure that old events are shown in the edit screen
			if($eid >= 0)
			{
				$statement = $conn->prepare("(SELECT * FROM events WHERE id = ? LIMIT 1) UNION (SELECT * FROM events ORDER BY dateStart DESC LIMIT 500)");
				$statement->bind_param("i", $eid);
			}
			else
			{
				// If eid is not set
				$statement = $conn->prepare("SELECT * FROM events ORDER BY dateStart DESC LIMIT 500");
			}

			// Amount of events
			$i = 0;

			$statement->execute();

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=editevent" method="POST" name="editEvents" id="editEvents">
						<select name="eventId" id="eventId">';
					}
					
					// Set up string to add to title if a linked event
					$add = "";
					
					// If this a linked event?
					if(isLink($db->id) > 0)
					{
						$add .= "[" . date("l", strtotime($db->dateStart)) . " : " . date("m/d - h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "] ";
					}

					echo '<option value="'.$db->id.'" link="'.isLink($db->id).'" '.echoSelect($db->id, $eid).'>'.$add.''.$db->name.' '.(($db->latitude == 0 || $db->longitude == 0) ? '[LOCATION ERROR]' : '').'</option>';

					// Increment
					$i++;
				}
			}

			// If no events
			if($i == 0)
			{
				echo 'There are no events to display.';
			}
			else
			{
				echo '
				</select>

				<br /><br />';

				if(hasPermission(1))
				{
					echo '
					<input type="submit" name="submitDelete" id="submitDelete" value="Delete" />';
				}
				
				echo '
				<input type="submit" name="submitEventStatus" id="submitEventStatus" value="Event Status" /> <input type="submit" name="submitEdit" id="submitEdit" value="Edit" /> <input type="submit" name="submitRoster" id="submitRoster" value="Roster" /> <input type="submit" name="submitCharity" id="submitCharity" value="Charity" /> <input type="submit" name="submitAdvanced" id="submitAdvanced" value="Advanced Options" /> <input type="submit" name="viewEvent" id="viewEvent" value="View Event" />
				</form>

				<div name="editEventStatus" id="editEventStatus" style="display:none;">
					<br />
					<form action="process.php?do=editeventstatus" method="POST">
						<select name="eventStatus">
							<option value="0">Open</option>
							<option value="3">Lock</option>
							<option value="2">Cancel</option>
							<option value="4">Full</option>
							<option value="1">Finish</option>
						</select>
						<br />
						<input type="submit" id="editEventStatusSave" value="Set" />
					</form>
				</div>

				<div name="advancedOptions" id="advancedOptions" style="display:none;">
					<br />
					<form action="process.php?do=editadvanced" method="POST">
						Thread ID: <input type="number" id="threadIDA" name="threadIDA" />
						<br /><br />
						Post ID: <input type="number" id="postIDA" name="postIDA" />
						<br />
						<input type="submit" id="advancedOptionsSave" value="Set" />
					</form>
				</div>
				
				<div name="charityAmount" id="charityAmount" style="display:none;">
					<br />
					<form action="process.php?do=editevent" id="editcharityForm" name="editcharityForm" method="POST">
						<p>
							Direct Charity Raised: $<input type="number" name="charityDirectFunds" id="charityDirectFunds" />
						</p>
						<p>
							Indirect Charity Raised: $<input type="number" name="charityIndirectFunds" id="charityIndirectFunds" />
						</p>
						<p>
							Charity Name: <input type="text" name="charityName" id="charityName" />
						</p>
						<p>
							Charity Add Hours: <input type="number" name="charityAddHours" id="charityAddHours" />
						</p>
						<p>
							Charity Note: <textarea width="100%" rows="5" name="charityNote" id="charityNote"></textarea>
						</p>

						<input type="submit" name="charityAmountSave" id="charityAmountSave" value="Set" />
					</form>
				</div>

				<div name="rosterInfo" id="rosterInfo" style="display:none;">
				</div>

				<div name="editEventInfo" id="editEventInfo" style="display:none;">
					<form action="process.php?do=editevent" id="editEventForm" name="editEventForm" method="POST">
						<input type="hidden" name="eventLink" id="eventLink" value="" />
						<input type="hidden" name="eventIdE" id="eventIdE" value="" />

						<p>Name of the event:</p>
						<input type="text" name="eventName" id="eventName" />

						<p>Venue of the event:</p>
						<input type="text" name="eventVenue" id="eventVenue" />

						<p>Location:</p>
						<input type="hidden" name="locationChangeCheck" id="locationChangeCheck" />
						<input type="text" name="location" id="location" />
						<input type="button" name="getLocation" id="getLocation" value="Get Squad Based On Location" />
						
						<p>Squad</p>
						<select name="squadm" id="squadm">
							<option value="null" SELECTED>Please choose an option...</option>
							'.squadSelectList(false).'
							<option value="0">'.garrison.'</option>
						</select>		

						<p>Date/Time Start:</p>
						<input type="text" name="dateStart" id="datepicker" />

						<p>Date/Time End:</p>
						<input type="text" name="dateEnd" id="datepicker2" />
						
						<div name="datetimeadd" id="datetimeadd"></div>
						
						<input type="submit" name="addshift" id="addshift" value="Add Shift" />
						
						<p>
							<i>Please note: This is to add additional shifts. To edit a shift, go to the shift event page, and edit it there.</i>
						</p>

						<div id="options">
							<p>Website:</p>
							<input type="text" name="website" id="website" />

							<p>Number of Attendees:</p>
							<input type="number" name="numberOfAttend" id="numberOfAttend" />

							<p>Requested Number of Characters:</p>
							<input type="number" name="requestedNumber" id="requestedNumber" />

							<p>Requested Character Types:</p>
							<input type="text" name="requestedCharacter" id="requestedCharacter" />

							<p>Secure Changing?</p>
							<select name="secure" id="secure">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>

							<p>Blasters Allowed?</p>
							<select name="blasters" id="blasters">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>

							<p>Lightsabers Allowed?</p>
							<select name="lightsabers" id="lightsabers">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>

							<p>Parking?</p>
							<select name="parking" id="parking">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>

							<p>People with limited mobility access?</p>
							<select name="mobility" id="mobility">
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>
						</div>

						<p>Amenities?</p>
						<input type="text" name="amenities" id="amenities" />

						<p>Additional Comments:</p>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'B\', \'comments\')" class="button">Bold</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'I\', \'comments\')" class="button">Italic</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'U\', \'comments\')" class="button">Underline</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'Q\', \'comments\')" class="button">Quote</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'COLOR\', \'comments\')" class="button">Color</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'SIZE\', \'comments\')" class="button">Size</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'URL\', \'comments\')" class="button">URL</a>
						<a href="#/" class="button" name="addSmiley">Add Smiley</a>
						<textarea rows="10" cols="50" name="comments" id="comments"></textarea>

						<span name="smileyarea" style="display: block;">
						</span>

						<p>Label:</p>
						<select name="label" id="label">
							<option value="0">Regular</option>
							<option value="10">Armor Party</option>
							<option value="1">Charity</option>
							<option value="2">PR</option>
							<option value="3">Disney</option>
							<option value="11">LFL</option>
							<option value="4">Convention</option>
							<option value="9">Hospital</option>
							<option value="5">Wedding</option>
							<option value="6">Birthday Party</option>
							<option value="7">Virtual Troop</option>
							<option value="8">Other</option>
						</select>
						
						<p>
							<a href="#/" class="button" id="limitChange">Change Limits</a>
						</p>
						
						<div id="limitChangeArea" style="display: none;">
						
						<p>How many friends can a trooper add?</p>
						<input type="text" name="friendLimit" id="friendLimit" />
						
						<p>Allow tentative troopers?</p>
						<select name="allowTentative" id="allowTentative">
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

						<p>Is this a manual selection event?</p>
						<select name="limitedEvent" id="limitedEvent">
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>

						<p>
							<hr />
						</p>

						<p>Limit of 501st Troopers:</p>
						<input type="number" name="limit501st" value="500" id="limit501st" class="limitClass" />';

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							echo '
							<p>Limit of '.$club_value['name'].':</p>
							<input type="number" name="'.$club_value['dbLimit'].'" value="500" id="'.$club_value['dbLimit'].'" class="limitClass" />';
						}

						echo '
						<p>
							<i>Use the above limit club feature to limit the amount of troopers on a per club basis. When all values are 500, there is no limit. Changing one club limit, will limit all to the set value.</i>
						</p>

						<p>
							<hr />
						</p>

						<p>Limit of Total Handlers:</p>
						<input type="number" name="limitHandlers" value="500" id="limitHandlers" class="limitClass" />

						<p>
							<i>If you limit handlers, the handlers will not count towards the total trooper count or club count. Handlers will be handled as a seperate count. When value is 500, handlers will be counted in the trooper total or club total.</i>
						</p>

						<p>
							<hr />
						</p>
						
						<p>Limit of Total Troopers:</p>
						<input type="number" name="limitTotalTroopers" value="500" id="limitTotalTroopers" class="limitClass" />

						<p>
							<i>This will limit the total amount of troopers and/or handlers to an event. This value should NOT be changed if you are using the limit clubs feature. When value is 500, there is no total trooper limit.</i>
						</p>

						<p>
							<a href="#/" class="button" id="resetDefaultCount">Reset Default</a>
						</p>
						
						</div>

						<p>Referred By:</p>
						<input type="text" name="referred" id="referred" />
						
						<p>Point of Contact (Name & Contact):</p>
						<input type="text" name="poc" id="poc" />

						<input type="submit" name="submitEventEdit" id="submitEventEdit" value="Edit!" />
					</form>
				</div>';
			}
		}

		/**************************** EVENT LINK MANAGER *********************************/

		if(isset($_GET['do']) && $_GET['do'] == "eventlinkmanager" && hasPermission(1, 2))
		{
			echo '<br /><hr /><br /><h3>Create Event Link</h3>

			<form action="process.php?do=eventlink" method="POST" name="addEventLink" id="addEventLink">
				<p>
					<b>Event Link Name:</b></br />
					<input type="text" name="eventLinkName" id="eventLinkName" />
				</p>

				<p>
					<b>Max Sign Ups Per Trooper:</b></br />
					<input type="number" name="allowed_sign_ups" id="allowed_sign_ups" value="500" />
				</p>

				<p>';

				// Get data
				$statement = $conn->prepare("SELECT * FROM events ORDER BY id DESC LIMIT 1000");
				$statement->execute();
				$i = 0;

				if ($result = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						if($i == 0) {
							echo '
							<b>Events:</b></br />
							<select id="multiple_event_select" name="events[]" multiple="multiple">';
						}

						echo '<option value="' . $db->id . '">' . (isLink($db->id) > 0 ? "[" . date("M d, Y h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "] " : date('M d, Y', strtotime($db->dateStart))) . ': ' . $db->name;

						$i++;
					}
				}

				if($i > 0) {
					echo '</select>';
				} else {
					echo 'No events to display.';
				}

				echo '
				</p>
				<input type="submit" name="submitEventLinkAdd" id="submitEventLinkAdd" value="Add Event Link" />
			</form>';

			echo '
			<div id="eventlinkarea">
			<br /><hr /><br />
			<h3>Edit Event Link</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM event_link ORDER BY name");
			$statement->execute();

			$i = 0;
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=eventlink" method="POST" name="eventLinkEdit" id="eventLinkEdit">

						<select name="eventLinkIDEdit" id="eventLinkIDEdit">
							<option value="0" SELECTED>Please select an event link...</option>';
					}

					echo '<option value="'.$db->id.'" eventLinkName="'.readInput($db->name).'" eventLinkID="'.$db->id.'" allowed_sign_ups="'.$db->allowed_sign_ups.'">' . $db->name . '</option>';

					// Increment
					$i++;
				}
			}

			if($i == 0) {
				echo 'No event links to display.<br />';
			} else {
				echo '
				</select>

				<br /><br />

				<div id="editEventLinkList" name="editEventLinkList" style="display: none;">

				<p>
					<b>Event Link Name:</b><br />
					<input type="text" name="editEventLinkName" id="editEventLinkName" />
				</p>

				<p>
					<b>Max Sign Ups Per Trooper:</b></br />
					<input type="number" name="allowed_sign_ups_edit" id="allowed_sign_ups_edit" value="500" />
				</p>';

				// Get data
				$statement = $conn->prepare("SELECT * FROM events ORDER BY id DESC LIMIT 1000");
				$statement->execute();
				$i = 0;

				if ($result = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						if($i == 0) {
							echo '
							<b>Events:</b></br />
							<select id="multiple_event_selectEdit" name="events[]" multiple="multiple">';
						}

						echo '<option value="' . $db->id . '">' . (isLink($db->id) > 0 ? "[" . date("M d, Y h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "] " : date('M d, Y', strtotime($db->dateStart))) . ': ' . $db->name;

						$i++;
					}
				}

				if($i > 0) {
					echo '</select>';
				} else {
					echo 'No events to display.';
				}

				echo '
				<input type="submit" name="submitEditEventLink" id="submitEditEventLink" value="Edit Event Link" />

				</div>
				</form>';
			}

			echo '<br /><hr /><br /><h3>Delete Event Link</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM event_link ORDER BY name");
			$statement->execute();

			$i = 0;
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=eventlink" method="POST" name="eventLinkDelete" id="eventLinkDelete">
						<select name="eventLinkID" id="eventLinkID">
							<option value="0" SELECTED>Please select an event link...</option>';
					}

					echo '<option value="' . $db->id . '">' . $db->name . '</option>';

					// Increment
					$i++;
				}
			}

			if($i == 0) {
				echo 'No event links to display.';
			} else {
				echo '
				</select>

				<input type="submit" name="submitDeleteEventLink" id="submitDeleteEventLink" value="Delete Event Link" />
				</form>';
			}
			
			echo '
			</div>';
		}

		/*********** APPROVE TROOPERS ***********/

		// Approve troopers
		if(isset($_GET['do']) && $_GET['do'] == "approvetroopers" && hasPermission(1, 2))
		{
			echo '
			<h3>Approve Trooper Requests</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM troopers WHERE approved = 0 ORDER BY datecreated");
			$statement->execute();

			// Amount of users
			$i = 0;

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=approvetroopers" method="POST" name="approveTroopers" id="approveTroopers">

						<select name="userID2" id="userID2">
							<option value="-1" SELECTED>Please select a trooper...</option>';
					}

					echo '<option value="'.$db->id.'">'.$db->name.' ('.getSquadName($db->squad).')'.(isHandler($db->id) ? ' **HANDLER REQUEST**' : '').'</option>';

					// Increment
					$i++;
				}
			}

			// If no events
			if($i == 0)
			{
				echo 'There are no troopers to display.';
			}
			else
			{
				echo '
				</select>

				<br /><br />

					<div id="approveButtons" style="display: none;">
						<input type="submit" name="submitApproveUser" id="submitApproveUser" value="Approve" /> <input type="submit" name="submitDenyUser" id="submitDenyUser" value="Deny" />
					</div>
				</form>

				<div style="overflow-x: auto;">
				<table border="1" id="userListTable" name="userListTable">
				<tr>
					<th>Name</th>	<th>E-mail</th>	<th>Forum ID (FG)</th>	<th>Forum ID (RL)</th>	<th>Mando CAT</th>	<th>SG #</th>	<th>Phone</th>	<th>Squad</th>	<th>TKID</th>
				</tr>
					<tr id="userList" name="userList">
						<td id="nameTable"></td>	<td id="emailTable"></td> <td id="forumTable"></td> <td id="rebelforumTable"></td> <td id="mandoidTable"></td>	<td id="sgidTable"></td>	<td id="phoneTable"></td>	<td id="squadTable"></td>	<td id="tkTable"></td>
					</tr>
				</table>
				</div>';
			}
		}

		// Manage users
		if(isset($_GET['do']) && $_GET['do'] == "managetroopers" && (hasPermission(1) || hasSpecialPermission("spTrooper")))
		{
			// If admin is visting this page from the event page
			
			// We use this value to determine which event is selected
			$uid = -1;
			
			// If eid set, set eid
			if(isset($_GET['uid']))
			{
				$uid = $_GET['uid'];
			}
			
			echo '
			<h3>Manage Troopers</h3>';

			// Get data
			$statement = $conn->prepare("SELECT * FROM troopers ORDER BY name");
			$statement->execute();

			// Amount of users
			$i = 0;

			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					// Formatting
					if($i == 0)
					{
						echo '
						<form action="process.php?do=managetroopers" method="POST" name="editUser" id="editUser">

						<select name="userID" id="userID">';
					}

					echo '<option value="'.$db->id.'" '.echoSelect($db->id, $uid).'>'.$db->name.' - '.readTKNumber($db->tkid, $db->squad, $db->id).' - '.$db->forum_id.'</option>';

					// Increment
					$i++;
				}
			}

			// If no events
			if($i == 0)
			{
				echo 'There are no troopers to display.';
			}
			else
			{
				echo '
				</select>

				<br /><br />

				<input type="submit" name="submitDeleteUser" id="submitDeleteUser" value="Delete" /> <input type="submit" name="submitViewProfile" id="submitViewProfile" value="View Profile" /> <input type="submit" name="submitEditUser" id="submitEditUser" value="Edit" />
				</form>

				<div name="editUserInfo" id="editUserInfo" style="display:none;">
					<form action="process.php?do=managetroopers" id="editUserForm" name="editUserForm" method="POST">
						<input type="hidden" name="userIDE" id="userIDE" value="" />

						<p>Name of the user:</p>
						<input type="text" name="user" id="user" />

						<p>Phone (Optional):</p>
						<input type="text" name="phone" id="phone" maxlength="10" />

						<p>Squad/Club:</p>
						<select name="squad" id="squad">
							<option value="0">'.garrison.'</option>
							'.squadSelectList().'
						</select>';

						if(hasPermission(1))
						{
							echo '
							<p>General Permissions:</p>
							<select name="permissions" id="permissions">
								<option value="0">Regular Member</option>
								<option value="3">RIP Member</option>
								<option value="2">Moderator</option>
								<option value="1">Super Admin</option>
							</select>
							
							<p>User note:</p>
							<input type="text" name="note" id="note" maxlength="255" />';
						}

						echo '
						<span name="specialPermissions" style="display: none;">';

						if(hasPermission(1))
						{
							echo '
							<p>Special Permissions:</p>

							<p>
								<input type="checkbox" name="spTrooper" /> Trooper Management
							</p>

							<p>
								<input type="checkbox" name="spCostume" /> Costume Management
							</p>

							<p>
								<input type="checkbox" name="spAward" /> Award Management
							</p>';
						}

						echo '
						</span>

						<p>
							<a href="#/" class="button" id="trooperInformationButton">Show Trooper Information</a>
						</p>

						<span name="trooperInformation" style="display: none;">

						<p>
							<hr />
						</p>
						
						<p>501st Member Status:</p>
						<select name="p501" id="p501">
							<option value="0">Not A Member</option>
							<option value="1">Regular Member</option>
							<option value="2">Reserve Member</option>
							<option value="3">Retired Member</option>
							<option value="4">Handler</option>
						</select>';
						
						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							echo '
							<p>'.$club_value['name'].' Member Status:</p>
							<select name="'.$club_value['db'].'" id="'.$club_value['db'].'" class="clubs">
								<option value="0">Not A Member</option>
								<option value="1">Regular Member</option>
								<option value="2">Reserve Member</option>
								<option value="3">Retired Member</option>
								<option value="4">Handler</option>
							</select>';
						}

						echo '
						<p>TKID:</p>
						<input type="text" name="tkid" id="tkid" />
						
						<p>Forum ID ('.garrison.'):</p>
						<input type="text" name="forumid" id="forumid" />';

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							// If DB3 defined
							if($club_value['db3Name'] != "")
							{
								echo '
								<p>'.$club_value['db3Name'].':</p>
								<input type="text" name="'.$club_value['db3'].'" id="'.$club_value['db3'].'" />';
							}
						}
						
						echo '
						<p>
							<hr />
						</p>
						</span>

						<p>Supporter:</p>
						<select name="supporter" id="supporter">
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>
						
						<br /><br />

						<input type="submit" name="submitUserEdit" id="submitUserEdit" value="Edit!" />
					</form>
				</div>';
			}
		}

		// Create an event form
		if(isset($_GET['do']) && $_GET['do'] == "createevent")
		{
			// Set up eid
			$eid = 0;
			
			// Set up variables for copy feature
			$name = "";
			$venue = "";
			$dateStart = "";
			$dateEnd = "";
			$website = "";
			$numberOfAttend = "";
			$requestNumber = "";
			$requestedCharacter = "";
			$secureChanging = "";
			$blasters = "";
			$lightsabers = "";
			$parking = "";
			$mobility = "";
			$amenities = "";
			$referred = "";
			$poc = "";
			$comments = "";
			$location = "";
			$label = "";
			$postComment = "";
			$notes = "";
			$limitedEvent = "";
			$limit501st = "";
			$limitTotalTroopers = "";
			$limitHandlers = "";
			$friendLimit = "";
			$allowTentative = "";

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Add vars
				${$club_value['dbLimit']} = "";
			}

			$closed = "";
			$charityDirectFunds = "";
			$squad = "";
			
			// If edid set - lets load events
			if(isset($_GET['eid']) && $_GET['eid'] >= 0)
			{
				// Get data for copy troop
				$eid = $_GET['eid'];
				
				$statement = $conn->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
				$statement->bind_param("i", $eid);
				$statement->execute();
				
				// Event found
				$i = 0;

				if ($result = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						$name = $db->name;
						$venue = $db->venue;
						$dateStart = $db->dateStart;
						$dateEnd = $db->dateEnd;
						$website = $db->website;
						$numberOfAttend = $db->numberOfAttend;
						$requestNumber = $db->requestedNumber;
						$requestedCharacter = $db->requestedCharacter;
						$secureChanging = $db->secureChanging;
						$blasters = $db->blasters;
						$lightsabers = $db->lightsabers;
						$parking = $db->parking;
						$mobility = $db->mobility;
						$amenities = $db->amenities;
						$referred = $db->referred;
						$poc = $db->poc;
						$comments = $db->comments;
						$location = $db->location;
						$label = $db->label;
						$postComment = $db->postComment;
						$notes = $db->notes;
						$limitedEvent = $db->limitedEvent;
						$limit501st = $db->limit501st;
						$limitTotalTroopers = $db->limitTotalTroopers;
						$limitHandlers = $db->limitHandlers;
						$friendLimit = $db->friendLimit;
						$allowTentative = $db->allowTentative;

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							${$club_value['dbLimit']} = $db->{$club_value['dbLimit']};
						}

						$closed = $db->closed;
						$squad = $db->squad;

						// Increment
						$i++;
					}
				}
			}

			// JQUERY Easy Form Filler
			echo '
			<a href="#/" class="button" id="easyfilltoolbutton" name="easyfilltoolbutton">Easy Fill Tool</a>
			
			<div name="easyfilltoolarea" id="easyfilltoolarea" style="display: none;">
			<p>Easy Fill Tool:</p>
			<form action="index.php?action=commandstaff&do=createevent" method="POST" name="easyFillTool" id="easyFillTool">
				<textarea rows="10" cols="50" name="easyFill" id="easyFill"></textarea>
				<br />
				<input type="submit" name="submit" name="easyFillButton" id="easyFillButton" value="Fill!" />
			</form>

			<p><i>Copy and paste event requests in the textbox above. Please ensure all information is accurate.</i></p>
			</div>';

			// Set up show options
			$style = '';

			// If armor party
			if($label == 10)
			{
				$style = 'style="display: none;"';
			}

			// Display create event form
			echo '
			<h3>Create an Event</h3>

			<form action="process.php?do=createevent" id="createEventForm" name="createEventForm" method="POST">
				<p>Name of the event:</p>
				<input type="text" name="eventName" id="eventName" value="'.copyEvent($eid, $name).'" />

				<p>Venue of the event:</p>
				<input type="text" name="eventVenue" id="eventVenue" value="'.copyEvent($eid, $venue).'" />

				<p>Location:</p>
				<input type="text" name="location" id="location" value="'.copyEvent($eid, $location).'" />
				<input type="button" name="getLocation" id="getLocation" value="Get Squad Based On Location" />
				
				<p>Squad</p>
				<select name="squadm" id="squadm">
					<option value="null" '.copyEventSelect($eid, $squad, "null").'>Please choose an option...</option>
					'.squadSelectList(false, "copy", $eid, $squad).'
					<option value="0" '.copyEventSelect($eid, $squad, 0).'>'.garrison.'</option>
				</select>

				<p>Date/Time Start:</p>
				<input type="text" name="dateStart" id="datepicker" value="'.copyEvent($eid, $dateStart).'" />

				<p>Date/Time End:</p>
				<input type="text" name="dateEnd" id="datepicker2" value="'.copyEvent($eid, $dateEnd).'" />
				
				<div name="datetimeadd" id="datetimeadd"></div>
				
				<input type="submit" name="addshift" id="addshift" value="Add Shift" />
				
				<p>
					<i>Please note: The first date and time is considered a shift. If you add another shift with the same date and time, it will be a duplicate.</i>
				</p>

				<div id="options" '.$style.'>
					<p>Website:</p>
					<input type="text" name="website" id="website" value="'.copyEvent($eid, $website).'" />

					<p>Number of Attendees:</p>
					<input type="number" name="numberOfAttend" id="numberOfAttend" value="'.copyEvent($eid, $numberOfAttend).'" />

					<p>Requested Number of Characters:</p>
					<input type="number" name="requestedNumber" id="requestedNumber" value="'.copyEvent($eid, $requestNumber).'" />

					<p>Requested Character Types:</p>
					<input type="text" name="requestedCharacter" id="requestedCharacter" value="'.copyEvent($eid, $requestedCharacter).'" />

					<p>Secure Changing?</p>
					<select name="secure" id="secure">
						<option value="null" '.copyEventSelect($eid, $secureChanging, "null").'>Please choose an option...</option>
						<option value="1" '.copyEventSelect($eid, $secureChanging, 1).'>Yes</option>
						<option value="0" '.copyEventSelect($eid, $secureChanging, 0).'>No</option>
					</select>

					<p>Blasters Allowed?</p>
					<select name="blasters" id="blasters">
						<option value="null" '.copyEventSelect($eid, $blasters, "null").'>Please choose an option...</option>
						<option value="1" '.copyEventSelect($eid, $blasters, 1).'>Yes</option>
						<option value="0" '.copyEventSelect($eid, $blasters, 0).'>No</option>
					</select>
					
					<p>Lightsabers Allowed?</p>
					<select name="lightsabers" id="lightsabers">
						<option value="null" '.copyEventSelect($eid, $lightsabers, "null").'>Please choose an option...</option>
						<option value="1" '.copyEventSelect($eid, $lightsabers, 1).'>Yes</option>
						<option value="0" '.copyEventSelect($eid, $lightsabers, 0).'>No</option>
					</select>

					<p>Parking?</p>
					<select name="parking" id="parking">
						<option value="null" '.copyEventSelect($eid, $parking, "null").'>Please choose an option...</option>
						<option value="1" '.copyEventSelect($eid, $parking, 1).'>Yes</option>
						<option value="0" '.copyEventSelect($eid, $parking, 0).'>No</option>
					</select>

					<p>People with limited mobility access?</p>
					<select name="mobility" id="mobility">
						<option value="null" '.copyEventSelect($eid, $mobility, "null").'>Please choose an option...</option>
						<option value="1" '.copyEventSelect($eid, $mobility, 1).'>Yes</option>
						<option value="0" '.copyEventSelect($eid, $mobility, 0).'>No</option>
					</select>
				</div>

				<p>Amenities?</p>
				<input type="text" name="amenities" id="amenities" value="'.copyEvent($eid, $amenities).'" />

				<p>Additional Comments:</p>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'B\', \'comments\')" class="button">Bold</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'I\', \'comments\')" class="button">Italic</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'U\', \'comments\')" class="button">Underline</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'Q\', \'comments\')" class="button">Quote</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'COLOR\', \'comments\')" class="button">Color</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'SIZE\', \'comments\')" class="button">Size</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'URL\', \'comments\')" class="button">URL</a>
				<a href="#/" class="button" name="addSmiley">Add Smiley</a>
				<textarea rows="10" cols="50" name="comments" id="comments">'.copyEvent($eid, $comments).'</textarea>

				<span name="smileyarea" style="display: block;">
				</span>

				<p>Label:</p>
				<select name="label" id="label">
					<option value="null" '.copyEventSelect($eid, $label, "null").'>Please choose an option...</option>
					<option value="0" '.copyEventSelect($eid, $label, 0).'>Regular</option>
					<option value="10" '.copyEventSelect($eid, $label, 10).'>Armor Party</option>
					<option value="1" '.copyEventSelect($eid, $label, 1).'>Charity</option>
					<option value="2" '.copyEventSelect($eid, $label, 2).'>PR</option>
					<option value="3" '.copyEventSelect($eid, $label, 3).'>Disney</option>
					<option value="11" '.copyEventSelect($eid, $label, 11).'>LFL</option>
					<option value="4" '.copyEventSelect($eid, $label, 4).'>Convention</option>
					<option value="9" '.copyEventSelect($eid, $label, 9).'>Hospital</option>
					<option value="5" '.copyEventSelect($eid, $label, 5).'>Wedding</option>
					<option value="6" '.copyEventSelect($eid, $label, 6).'>Birthday Party</option>
					<option value="7" '.copyEventSelect($eid, $label, 7).'>Virtual Troop</option>
					<option value="8" '.copyEventSelect($eid, $label, 8).'>Other</option>
				</select>
				
				<p>
					<a href="#/" class="button" id="limitChange">Change Limits</a>
				</p>

				<div id="limitChangeArea" style="display: none;">

				<p>Post to boards?</p>
				<select name="postToBoards" id="postToBoards">
					<option value="1">Yes</option>
					<option value="0">No</option>
				</select>
				
				<p>How many friends can a trooper add?</p>
				<input type="text" name="friendLimit" id="friendLimit" value="'.copyEvent($eid, $friendLimit, 4).'" />
				
				<p>Allow tentative troopers?</p>
				<select name="allowTentative" id="allowTentative">
					<option value="1" '.copyEventSelect($eid, $allowTentative, 1).'>Yes</option>
					<option value="0" '.copyEventSelect($eid, $allowTentative, 0).'>No</option>
				</select>

				<p>Is this a manual selection event?</p>
				<select name="limitedEvent" id="limitedEvent">
					<option value="0" '.copyEventSelect($eid, $limitedEvent, 0).'>No</option>
					<option value="1" '.copyEventSelect($eid, $limitedEvent, 1).'>Yes</option>
				</select>

				<p>
					<hr />
				</p>
				
				<p>Limit of 501st Troopers:</p>
				<input type="number" name="limit501st" value="'.copyEvent($eid, $limit501st, 500).'" id="limit501st" class="limitClass" />';

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					echo '
					<p>Limit of '.$club_value['name'].':</p>
					<input type="number" name="'.$club_value['dbLimit'].'" value="'.copyEvent($eid, ${$club_value['dbLimit']}, 500).'" id="'.$club_value['dbLimit'].'" class="limitClass" />';
				}

				echo '
				<p>
					<i>Use the above limit club feature to limit the amount of troopers on a per club basis. When all values are 500, there is no limit. Changing one club limit, will limit all to the set value.</i>
				</p>

				<p>
					<hr />
				</p>

				<p>Limit of Total Handlers:</p>
				<input type="number" name="limitHandlers" id="limitHandlers" class="limitClass" value="'.copyEvent($eid, $limitHandlers, 500).'" />

				<p>
					<i>If you limit handlers, the handlers will not count towards the total trooper count or club count. Handlers will be handled as a seperate count. When value is 500, handlers will be counted in the trooper total or club total.</i>
				</p>

				<p>
					<hr />
				</p>
				
				<p>Limit of Total Troopers:</p>
				<input type="number" name="limitTotalTroopers" id="limitTotalTroopers" class="limitClass" value="'.copyEvent($eid, $limitTotalTroopers, 500).'" />

				<p>
					<i>This will limit the total amount of troopers and/or handlers to an event. This value should NOT be changed if you are using the limit clubs feature. When value is 500, there is no total trooper limit.</i>
				</p>

				<p>
					<a href="#/" class="button" id="resetDefaultCount">Reset Default</a>
				</p>
				
				</div>

				<p>Referred By:</p>
				<input type="text" name="referred" id="referred" value="'.copyEvent($eid, $referred).'" />
				
				<p>Point of Contact (Name & Contact):</p>
				<input type="text" name="poc" id="poc" value="'.copyEvent($eid, $poc).'" />

				<input type="submit" name="submitEvent" value="Create!" />
				
				<div id="create_event_area"></div>
			</form>';
		}
	}
	else
	{
		// If the user is not an admin or logged in
		echo 'You are not command staff. Access denied!';
	}
}

// Show the map page
if(isset($_GET['action']) && $_GET['action'] == "mapview" && loggedIn())
{
	echo '
	<h3>Map View</h3>
	<iframe src="map/" style="width: 100%; height: 400px; border: none;" frameborder:="0"></iframe>';
}

// Show the FAQ page
if(isset($_GET['action']) && $_GET['action'] == "faq")
{
	echo '
	<h3>Troop Tracker - Overview Video (After Account Setup)</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/vFP6posJt70" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>Troop Tracker - How To Video</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/j0Z5SB6TVOg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>Troop Tracker - For Command Staff</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/ycwFdQQvGoc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to add an iOS shortcut</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/_UhtyHbL8uY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to add an Android shortcut</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/S4Xu_N4ByBs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to upload photos</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/aODHyWMMVUQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to view milestones and awards</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/W-wcceu6xzI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to add a friend to a troop</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/C0WCxIRZafQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>
	
	<h3>How to add someone to a troop without them being a member or having tracker access</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/mDeJaANqLIk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to search for troops and troop counts</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/-pXqGZLiVpM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to view past troops</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/17UPK4AoKxg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to sort by squad</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/H-nnM5jndZA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to show troops your signed up for</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/Rn3EnhudHyc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to use calendar view</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/02ERoFw7XlY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to search troops on the homepage</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/y_I8ssRjek8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to subscribe to events</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/5pp7_FKg7cI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to add an event to your calendar</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/cefnojYUy-Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to add discussion to a troop</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/tS-bCXbCzs4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to change the theme</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/IPykBoeDGcg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>How to search costumes</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/YLjiVGgqe-Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>Command Staff - Create an Event</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/MFdLu9aWwlI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>Command Staff - Edit an Event</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/WADjtDcmeJo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>Command Staff - Edit Roster</h3>
	<p>
		<iframe width="100%" height="315" src="https://www.youtube.com/embed/c8IT_s0qSxc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</p>

	<h3>Troop Tracker Manual</h3>
	<p>
		<a href="https://github.com/MattDrennan/501-troop-tracker/blob/master/manual/troop_tracker_manual.pdf" target="_blank">Click here to view PDF manual.</a>
	</p>
	
	<h3>I cannot login / I forgot my password</h3>
	<p>
		The Troop Tracker has been integrated with the boards. You must use your '.garrison.' boards username and password to login to Troop Tracker. To recover your password, use password recovery on the '.garrison.' forum. If you continue to have issues logging into your account, your '.garrison.' forum username, may not match the Troop Tracker records. Contact the '.garrison.' Webmaster or post a help thread on the forums to get this corrected.
	</p>

	<h3>I am missing troop data / My troop data is incorrect</h3>
	<p>
		Please refer to your squad leader to get this corrected.
	</p>
	
	<h3>I am now a member of another club and need access to their costumes.</h3>
	<p>
		Please refer to your squad / club leader to get added to the roster.
	</p>

	<h3>My costumes are not showing on my profile / I am missing a costume on my profile</h3>
	<p>
		The troop tracker automatically scrapes several different club databases for your costume data. If your costume data is not showing, make sure your ID numbers and forum username\'s are accurate. If the aforementioned information is correct, than refer to your squad / club leadership, as this data is missing on their end.
	</p>

	<h3>How do I know I confirmed a troop?</h3>
	<p>
		The troop will be listed on your troop tracker profile, or under your stats on the troop tracker page. When you confirm a troop, your status will change from "Going" to "Attended".
	</p>

	<h3>I need a costume added to the troop tracker.</h3>
	<p>
		Please notify your squad leader, or e-mail the Garrison Web Master directly. See below for e-mail.
	</p>

	<h3>I need information on joining the 501st Legion.</h3>
	<p>
		<a href="https://databank.501st.com/databank/Join_Us" target="_blank">Click here to learn how to join.</a>
	</p>

	<h3>Contact Garrison Web Master</h3>
	<p>If you have read and reviewed all the material above and are still experiencing issues, or have noticed a bug on the website, please <a href="mailto: '.$webmasterEmail.'">send an e-mail here</a>.</p>';
}

/**************************** Edit Photo *********************************/

if(isset($_GET['action']) && $_GET['action'] == "editphoto" && loggedIn())
{
	$photoid = $_GET['id'];
	
	// Fetch photo details in one query
	$statement = $conn->prepare("SELECT * FROM uploads WHERE id = ?");
	$statement->bind_param("i", $photoid);
	$statement->execute();
	$result = $statement->get_result();

	if ($db = $result->fetch_object())
	{
		echo '
		<h3>Edit Photo: '.$db->filename.'</h3>
		
		<p>
			<img src="images/uploads/'.$db->filename.'" width="200px" height="200px" />
		</p>

		<p>
			<b>Uploaded by:</b> <a href="index.php?profile='.$db->trooperid.'">'.getName($db->trooperid).' - '.getTKNumber($db->trooperid, true).'</a>
		</p>';

		// Fetch all troopers in one query
		echo '
		<p>
			<b>Tagged troopers:</b>

			<div id="tagged-troopers" photoid="'.$photoid.'">
				<div style="text-align: center;">
					<img src="images/loading.gif" />
				</div>
			</div>
		</p>
		<hr />
		<p style="text-align: center">';

		// If admin or uploader
		if(isAdmin() || $db->trooperid == $_SESSION['id'])
		{
			$buttonText = $db->admin == 0 ? "Make Troop Instruction Photo" : "Make Regular Photo";
			echo '
			<a href="#/" photoid="'.$db->id.'" class="button" name="adminphoto">'.$buttonText.'</a>
			<a href="#/" photoid="'.$db->id.'" troopid="'.$db->troopid.'" class="button" name="deletephoto">Delete Photo</a>';
		}

		echo '<a href="index.php?event='.$db->troopid.'" class="button">View Event</a></p>';
	}
}

// Show the login page
if(isset($_GET['action']) && $_GET['action'] == "login" && !loggedIn())
{
	echo '
	<h2 class="tm-section-header">Login</h2>';

	// Display submission for register account, otherwise show the form
	if(isset($_POST['loginWithTK']))
	{
		// Login with forum
		$forumLogin = loginWithForum($_POST['tkid'], $_POST['password']);
		
		// Check credentials
		if(isset($forumLogin['success']) && $forumLogin['success'] == 1)
		{
			// Update username if changed
			$statement = $conn->prepare("UPDATE troopers SET forum_id = ? WHERE user_id = ?");
			$statement->bind_param("si", $forumLogin['user']['username'], $forumLogin['user']['user_id']);
			$statement->execute();
		}

		// Get data
		$statement = $conn->prepare("SELECT * FROM troopers WHERE forum_id = ? LIMIT 1");
		$statement->bind_param("s", $_POST['tkid']);
		$statement->execute();
		
		// Trooper count
		$i = 0;

		if ($result = $statement->get_result())
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Increment trooper count
				$i++;

				// Check if banned
				if(isset($forumLogin['success']) && $forumLogin['user']['is_banned'] == 1) {
					echo '
					<p>
						You are currently banned. Please refer to command staff for additional information.
					</p>';

					break;
				}

				// Check if RIP trooper
				if($db->permissions == 3) {
					echo '
					<p>
						You cannot access this account.
					</p>';

					break;
				}

				// Check credentials
				if(isset($forumLogin['success']) && $forumLogin['success'] == 1 || (password_verify($_POST['password'], $db->password) && $db->permissions == 1))
				{
					if($db->approved != 0)
					{
						if(canAccess($db->id))
						{
							// Set session
							$_SESSION['id'] = $db->id;
							$_SESSION['tkid'] = $db->tkid;

							// If logged in with forum details, and password does not match
							if(isset($forumLogin['success']) && $forumLogin['success'] == 1)
							{
								// Update password, e-mail, and user ID
								$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

								$statement = $conn->prepare("UPDATE troopers SET password = ?, email = ?, user_id = ? WHERE id = ?");
								$statement->bind_param("ssii", $password, $forumLogin['user']['email'], $forumLogin['user']['user_id'], $db->id);
								$statement->execute();
							}
							
							// Set log in cookie, if set to keep logged in
							if(isset($_POST['keepLog']) && $_POST['keepLog'] == 1)
							{
								// Set cookies
								setcookie("TroopTrackerUsername", $db->forum_id, time() + (10 * 365 * 24 * 60 * 60));
								setcookie("TroopTrackerPassword", $_POST['password'], time() + (10 * 365 * 24 * 60 * 60));
							}

							// Cookie set
							if(isset($_COOKIE["TroopTrackerLastEvent"]))
							{
								echo '
								<meta http-equiv="refresh" content="5; URL=index.php?event='.cleanInput($_COOKIE["TroopTrackerLastEvent"]).'" />

								<div style="margin-top: 25px; color: green; text-align: center; font-weight: bold;">
								You have now logged in!
								<br /><br />
								<a href="index.php?event='.cleanInput($_COOKIE["TroopTrackerLastEvent"]).'">Click here to view the event</a> or you will be redirected shortly.
								</div>';
								
								// Clear cookie
								setcookie("TroopTrackerLastEvent", "", time() - 3600);
							}
							else
							{
								// Cookie not set
								echo '
								<div style="margin-top: 25px; color: green; text-align: center; font-weight: bold;">
								You have now logged in!
								<br /><br />
								<a href="index.php">Click here to go home.</a>
								</div>';
							}
						}
						else
						{
							echo '
							Your account is retired. Please contact your squad / club leader for further instructions on how to get re-approved.';
						}
					}
					else
					{
						echo '
						Your access has not been approved yet.';
					}
				}
				else
				{
					echo '
					<p>
						Incorrect username or password. <a href="index.php?action=login">Try again?</a>
					</p>

					<p>
						If you are unable to access your account, please contact the '.garrison.' Webmaster, or post a help request on the forums. Your FL Garrison boards name may not match the Troop Tracker records.
					</p>';
				}
			}
		}

		// An account does not exist
		if($i == 0)
		{
			echo '
			<p>Account not found. <a href="index.php?action=login">Try again?</a></p>
			
			<p>Please contact the Garrison Webmaster or post a help request on the forums, if you continue to have issues. Your FL Garrison boards name may not match the Troop Tracker records.</p>';
		}
	}
	else
	{
		echo '
		<form action="index.php?action=login" method="POST" name="loginForm" id="loginForm">
			<p>Board Name:</p>
			<input type="text" name="tkid" id="tkid" />

			<p>Password:</p>
			<input type="password" name="password" id="password" />
			
			<br /><br />
			
			<input type="checkbox" name="keepLog" value="1" /> Keep me logged in

			<br /><br />

			<input type="submit" value="Login!" name="loginWithTK" />
		</form>
		
		<p>
			<small>
				<b>Remember:</b><br />Login with your '.garrison.' board username and password.
			</small>
		</p>';
	}
}

// Show the setup page
if(isset($_GET['action']) && $_GET['action'] == "setup" && !isSignUpClosed() && !loggedIn())
{
	echo '
	<h2 class="tm-section-header">Set Up Your Account</h2>';

	// Display submission for register account, otherwise show the form
	if(isset($_POST['registerAccount']))
	{
		// Does this TK ID exist?
		if(doesTKExist($_POST['tkid'], $_POST['squad']))
		{
			// Is this TK ID registered?
			if(!isTKRegistered($_POST['tkid'], $_POST['squad']))
			{
				// Login with forum
				$forumLogin = loginWithForum($_POST['forum_id'], $_POST['password']);

				// Verify forum login
				if(isset($forumLogin['success']) && $forumLogin['success'] == 1)
				{
					// If 501st
					if(in_array($_POST['squad'], $validSquadIDs))
					{
						// Query the database
						$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
						$forum_id = filter_var($_POST['forum_id'], FILTER_SANITIZE_ADD_SLASHES);

						$statement = $conn->prepare("UPDATE troopers SET user_id = ?, email = ?, password = ?, squad = ? WHERE forum_id = ?");
						$statement->bind_param("issis", $forumLogin['user']['user_id'], $forumLogin['user']['email'], $password, $_POST['squad'], $forum_id);
						$statement->execute();
						
						// Display output
						echo 'Your account has been registered. Please <a href="index.php?action=login">login</a>.';
					}
					else
					{
						// If a club
						// Query the database
						$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
						$rebelforum = filter_var($_POST['tkid'], FILTER_SANITIZE_ADD_SLASHES);

						$statement = $conn->prepare("UPDATE troopers SET user_id = ?, email = ?, password = ?, squad = ? WHERE rebelforum = ?");
						$statement->bind_param("issis", $forumLogin['user']['user_id'], $forumLogin['user']['email'], $password, $_POST['squad'], $rebelforum);
						$statement->execute();
						
						// Display output
						echo 'Your account has been registered. Please <a href="index.php?action=login">login</a>.';
					}
				}
				else
				{
					echo 'Incorrect username or password.';
				}
			}
			else
			{
				echo 'This TK ID or Rebel Legion user is already registered, or there is an issue with your input! Please contact an admin if this issue persists.';
			}
		}
		else
		{
			echo 'This TK ID or Rebel Legion user does not exist! Please contact an admin if this issue persists.';
		}
	}
	else
	{
		// Display form to register an account
		echo '
		<p style="text-align: center; border: dashed white;">Were you already using the old trooper tracker? Set up your account by using the form below.</p>
		
		<form method="POST" action="index.php?action=setup" name="registerForm" id="registerForm">
			<p>What is your TKID (numbers only) or Rebel Forum username (if Rebel Legion member only):</p>
			<input type="text" name="tkid" id="tkid" />

			<p>'.garrison.' Board Username:</p>
			<input type="text" name="forum_id" id="forum_id" />

			<p>'.garrison.' Board Password:</p>
			<input type="password" name="password" id="password" />
			
			<p>Squad/Club</p>
			
			<select name="squad" id="squad">
				'.squadSelectList(true, "", 0, 0, true).'
			</select>
			
			<br /><br />

			<input type="submit" value="Set Up!" name="registerAccount" />
		</form>';
	}
}
else
{
	if(isset($_GET['action']) && $_GET['action'] == "setup")
	{
		echo '<p style="text-align: center;"><b>You are already logged in.</b></p>';
	}
}

// Show the logout page
if(isset($_GET['action']) && $_GET['action'] == "logout")
{
	// Destroy session
	session_destroy();
	
	// Make sure cookies are set before destroying
	if(isset($_COOKIE['TroopTrackerUsername']) && isset($_COOKIE['TroopTrackerPassword']))
	{
		// Destroy cookies
		setcookie("TroopTrackerUsername", "", time() - 3600);
		setcookie("TroopTrackerPassword", "", time() - 3600);
	}

	// Show logout message
	echo '
	<div style="margin-top: 25px; color: red; text-align: center;">
		<b>You have logged out!
		<br /><br />
		<a href="index.php">Click here to go home.</a></b>
	</div>';
}

// If we are viewing an event, hide all other info
if(isset($_GET['event']) && loggedIn())
{	
	// Delete Comment
	if(isset($_POST['deleteComment']) && isAdmin())
	{
		// Delete from thread forum
		deletePost(getCommentPostID(cleanInput($_POST['comment'])), true);

		// Delete
		$statement = $conn->prepare("DELETE FROM comments WHERE id = ?");
		$statement->bind_param("i", $_POST['comment']);
		$statement->execute();
	}

	// Globals
	$eventClosed = 0;
	$limitedEvent = 0;
	$limitTotal = 0;
	$friendLimit = 0;
	$allowTentative = 0;
	
	// Merged troop
	$isMerged = false;
	
	// Does the event exist
	$eventExist = false;
			
	// Query database for event info
	$statement = $conn->prepare("SELECT * FROM events WHERE id = ?");
	$statement->bind_param("i", $_GET['event']);
	$statement->execute();

	if ($result = $statement->get_result())
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Update globals
			$eventClosed = $db->closed;
			$limitedEvent = $db->limitedEvent;
			$friendLimit = $db->friendLimit;
			$allowTentative = $db->allowTentative;
			$thread_id = $db->thread_id;
			
			// Set total
			$limitTotal = $db->limit501st;

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Add
				$limitTotal += $db->{$club_value['dbLimit']};
			}

			// Check for total limit set, if it is, replace limit with it
			if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
			{
				$limitTotal = $db->limitTotalTroopers;
			}
			
			// Set event exist
			$eventExist = true;
					
			// Admin Area
			if(isAdmin())
			{
				echo '
				<div class="section-card">
					<h2 class="tm-section-header">Admin Controls</h2>
					<p style="text-align: center;">
						<a href="index.php?action=commandstaff&do=editevent&eid='.$db->id.'" class="button">Edit/View Event in Command Staff Area</a>
						<a href="index.php?action=commandstaff&do=createevent&eid='.$db->id.'" class="button">Copy Event in Command Staff Area</a>
					</p>
					'.(($db->latitude == 0 || $db->longitude == 0) ? '<span style="display: flex; justify-content: center; align-items: center; height: 100%; color: red; font-weight: bold;" aria-label="To resolve this issue, verify that the location address is accurate; otherwise, the system will not function properly." data-balloon-pos="down" data-balloon-length="fit">[LOCATION ERROR]</span>' : '').'
				</div>';
			}
			
			// Format dates
			$date1 = date("m/d/Y - h:i A", strtotime($db->dateStart)); 
			$date2 = date("m/d/Y - h:i A", strtotime($db->dateEnd));

			echo '<input type="hidden" name="troopid" id="troopid" value="'.cleanInput($_GET['event']).'" />';
			
			// Is this merged data?
			if($db->venue == NULL && $db->numberOfAttend == NULL && $db->requestedCharacter == NULL && $db->secureChanging == NULL && $db->lightsabers == NULL && $db->parking == NULL && $db->mobility == NULL && $db->amenities == NULL && $db->referred == NULL)
			{	
				echo '
				<div class="section-card">
					<h2 class="tm-section-header">'.$db->name.'</h2>
					<p><b>Event Date:</b> '.$date1.' ('.date('l', strtotime($db->dateStart)).')</p>
				</div>

				<div class="section-card">
					<h2 class="tm-section-header">Description</h2>
					<p>'.ifEmpty($db->comments, "N/A").'</p>
				</div>';
				
				// Set is merged
				$isMerged = true;
			}
			else
			{
				// If this event is over, don't show it
				if(strtotime($db->dateEnd) >= strtotime("-1 day") && $db->closed != 1)
				{					
					// Subscribe button
					echo '
					<div class="section-card">
					<h2 class="tm-section-header">Event Tools</h2>

					<div id="subscribe-area">
						<div style="text-align: center;">
							<img src="images/loading.gif" />
						</div>
					</div>';
					
					// Add to calendar links
					echo showCalendarLinks($db->name, $db->location, "Troop Tracker Event", $db->dateStart, $db->dateEnd);

					echo '</div>';
				}

				// If canceled, show trooper
				if($db->closed == 2)
				{
					echo '
					<div class="alert-box">
						<b>This event was CANCELED by Command Staff.</b>
					</div>';
				}
				// If locked, show trooper
				else if($db->closed == 3)
				{
					echo '
					<div class="alert-box">
						<b>This event was LOCKED by Command Staff.</b>
						<br />
						<i>You will be unable to sign up at this time.</i>
					</div>';
				}
				// If full, show trooper
				else if($db->closed == 4)
				{
					echo '
					<div class="alert-box">
						<b>This event was marked FULL by Command Staff.</b>
						<br />
						<i>You will be unable to sign up at this time.</i>
					</div>';
				}
				
				// Set add to title
				$add = "";
				
				if(isLink($db->id) > 0)
				{
					$add = "<b>" . date("l", strtotime($db->dateStart)) . "</b><br />".date("m/d - h:i A", strtotime($db->dateStart))." - ".date("h:i A", strtotime($db->dateEnd))."<br />";
				}
			
				// Display event info
				echo '
				<div class="section-card">
				<h2 class="tm-section-header">'.$add.''.$db->name.'</h2>';
				
				// If event closed
				if($db->closed == 1)
				{
					$hours = timeBetweenDates($db->dateStart, $db->dateEnd);
					$hours += intval($db->charityAddHours);

					echo '
					<div class="charitybox"><b>Event Raised:</b><br />Direct: $'.number_format($db->charityDirectFunds).'<br />Indirect: $'.number_format($db->charityIndirectFunds).'<br />Charity Name: '.ifEmpty($db->charityName, "N/A").'<br />Charity Hours: '.$hours.'<br />Charity Note:<br />'.ifEmpty(nl2br($db->charityNote ?? ''), "N/A").'</div>';
				}

				// Set up show options
				$style = '';

				// If armor party
				if($db->label == 10)
				{
					$style = 'style="display: none;"';
				}
				
				echo '
				<p><b>Venue:</b> '.$db->venue.'</p>
				<p><b>Address:</b> <a href="https://www.google.com/maps/search/?api=1&query='.$db->location.'" target="_blank">'.$db->location.'</a></p>
				<p><b>Event Start:</b> '.$date1.' ('.date('l', strtotime($db->dateStart)).')</p>
				<p><b>Event End:</b> '.$date2.' ('.date('l', strtotime($db->dateEnd)).')</p>
				<div id="options" '.$style.'>
					<p><b>Website:</b> '.validate_url($db->website).'</p>
					<p><b>Expected number of attendees:</b> '.number_format($db->numberOfAttend).'</p>
					<p><b>Requested number of characters:</b> '.number_format($db->requestedNumber).'</p>
					<p><b>Requested character types:</b> '.$db->requestedCharacter.'</p>
					<p><b>Secure changing/staging area:</b> '.yesNo($db->secureChanging).'</p>
					<p><b>Can troopers bring blasters:</b> '.yesNo($db->blasters).'</p>
					<p><b>Can troopers bring/carry prop like lightsabers:</b> '.yesNo($db->lightsabers).'</p>
					<p><b>Is parking available:</b> '.yesNo($db->parking).'</p>
					<p><b>Is venue accessible to those with limited mobility:</b> '.yesNo($db->mobility).'</p>
				</div>
				<p><b>Amenities available at venue:</b> '.ifEmpty($db->amenities, "No amenities for this event.").'</p>
				<p><b>Referred by:</b> '.ifEmpty($db->referred, "Not available").'</p>
				'.(isAdmin() ? '<p><b>Point of Contact:</b> '.ifEmpty($db->poc, "Not available").'</p>' : '').'';

				// If attached to a forum thread
				if($db->thread_id > 0)
				{
					echo '
					<p><b>View post on forum:</b> <a href="'.$forumURL.'threads/'.$db->thread_id.'" target="_blank">'.$forumURL.'threads/'.$db->thread_id.'</a></p>';
				}

				echo '</div>
				
				<div class="section-card">
					<h2 class="tm-section-header">Description</h2>
					<p>'.ifEmpty(nl2br(showBBcodes($db->comments) ?? ''), "No comments for this event.").'</p>
				</div>';
			
				// Get linked event
				$link = isLink($db->id);
				
				// If has links to event, or is linked, show shift data
				if($link > 0)
				{						
					echo '
					<div class="section-card">
					<h2 class="tm-section-header" id="shifts-link">Shifts</h2>';
					
					// Query database for shifts
					$statement = $conn->prepare("SELECT * FROM events WHERE (id = ? OR link = ?) ORDER BY dateStart ASC");
					$statement->bind_param("ii", $link, $link);
					$statement->execute();
					
					if ($result2 = $statement->get_result())
					{
						while ($db2 = mysqli_fetch_object($result2))
						{
							if($db->id == $db2->id) {
								echo '
								<div style="border: 1px solid gray; margin-bottom: 10px; text-align: center; color: orange; font-weight: bold;">
								<u>Currently Viewing:</u>
								<br />
								<b>'.date('l', strtotime($db2->dateStart)).'</b> - ' . date('M d, Y', strtotime($db2->dateStart)) . '
								<br />' .
								date('h:i A', strtotime($db2->dateStart)) . ' - ' . date('h:i A', strtotime($db2->dateEnd)) .
								'</div>';
							} else {
								echo '
								<div style="border: 1px solid gray; margin-bottom: 10px; text-align: center;">
								<a href="index.php?event=' . $db2->id . '#shifts-link"><b>'.date('l', strtotime($db2->dateStart)).'</b> - ' . date('M d, Y', strtotime($db2->dateStart)) . '
								<br />' .
								date('h:i A', strtotime($db2->dateStart)) . ' - ' . date('h:i A', strtotime($db2->dateEnd)) .
								'</a>
								</div>';
							}
						}
					}
					echo '</div>';
				}

				// Get link2
				$link2 = isLink2($db->id);

				// Show linked events
				if($link == 0 && $link2 > 0) {
					echo '
					<div class="section-card">
					<h2 class="tm-section-header">Related Troops</h2>';

					// Query database for linked events
					$statement = $conn->prepare("SELECT * FROM events WHERE link2 = ? AND id != ? ORDER BY dateStart DESC");
					$statement->bind_param("ii", $link2, $db->id);
					$statement->execute();
					
					if ($result2 = $statement->get_result())
					{
						while ($db2 = mysqli_fetch_object($result2))
						{
							echo '
							<div style="border: 1px solid gray; margin-bottom: 10px; text-align: center;">
							<a href="index.php?event=' . $db2->id . '">' . (isLink($db2->id) > 0 ? '<b>'.date('l', strtotime($db2->dateStart)).'</b> - ' . date('M d, Y', strtotime($db2->dateStart)) . '
							<br />' .
							date('h:i A', strtotime($db2->dateStart)) . ' - ' . date('h:i A', strtotime($db2->dateEnd)) .
							'
							<br />
							'. $db2->name : date('M d, Y', strtotime($db2->dateStart)) . '<br />' . $db2->name) .'</a>
							</div>';
						}
					}

					echo '
					</div>

					<div class="alert-box">
						<b>This event is connected to other related events; therefore, sign-up limits may apply.</b>
					</div>';
				} else if($link > 0 && $link2 > 0) {
					// Only show the disclaimer text, if the event is already a shift.
					echo '
					<div class="alert-box">
						<b>This event is connected to other related events; therefore, sign-up limits may apply.</b>
					</div>';
				}
				
				// Don't show photos, if merged data
				if(!$isMerged)
				{
					// Query count
					$j = 0;

					// Query for photos
					$statement = $conn->prepare("
					    SELECT * FROM uploads 
					    WHERE admin = '1' 
					      AND troopid IN (
					          SELECT id FROM events 
					          WHERE id = ? 
					            OR (link = ? AND link != 0) 
					            OR (link2 = ? AND link2 != 0)
					            OR id = ?
					            OR id = ?
					      ) 
					    ORDER BY date DESC
					");

					$statement->bind_param("iiiii", $_GET['event'], $link, $link2, $link, $link2);
					$statement->execute();
					
					if ($result2 = $statement->get_result())
					{
						while ($db2 = mysqli_fetch_object($result2))
						{
							// If first result...
							if($j == 0)
							{
								echo '
								<div class="section-card">
								<h2 class="tm-section-header">Instructional Photos</h2>';
							}
							
							echo '
							<div class="container-image">
								<a href="images/uploads/'.$db2->filename.'" data-lightbox="photosadmin" data-title="Uploaded by '.getName($db2->trooperid).'" id="photo'.$db2->id.'"><img src="images/uploads/'.$db2->filename.'" width="200px" height="200px" class="image-c" /></a>
								
								<p class="container-text">';
								
									// If owned by trooper or admin
									if(isAdmin() || $db2->trooperid == $_SESSION['id'])
									{
										echo '<a href="index.php?action=editphoto&id='.$db2->id.'">Edit</a>';
									}

								echo '
								</p>
							</div>';
							
							// Increment photo count
							$j++;
						}
					}

					if($j > 0) {
						echo '</div>';
					}
				}

				// Set up is limited event?
				$isLimited = false;

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// Check if limited
					if($db->{$club_value['dbLimit']} < 500)
					{
						$isLimited = true;
					}
				}

				// Check for total limit set, if it is, set event as limited
				if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
				{
					$isLimited = true;
				}
				
				// Check for total limit set, if it is, set event as limited
				if($db->limitHandlers > 500 || $db->limitHandlers < 500)
				{
					$isLimited = true;
				}
			
				// If this event is limited in troopers
				if($db->limit501st < 500 || $isLimited)
				{
					echo '
					<div class="event-limit-alert" name="troopersRemainingDisplay">
						<ul class="event-limit-list">
							<li>This event is limited to '.$limitTotal.' troopers. ';

							// Check for total limit set, if it is, add remaining troopers
							if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
							{
								echo '
								' . troopersRemaining($limitTotal, eventClubCount($db->id, "all")) . '</li>';
							}
							else
							{
								echo '
								</li>
								<li>This event is limited to '.$db->limit501st.' 501st troopers. '.troopersRemaining($db->limit501st, eventClubCount($db->id, 0)).' </li>';

								// Loop through clubs
								foreach($clubArray as $club => $club_value)
								{
									echo '
									<li>This event is limited to '.$db->{$club_value['dbLimit']}.' '. $club_value['name'] .' troopers. '.troopersRemaining($db->{$club_value['dbLimit']}, eventClubCount($db->id, $club_value['squadID'])).'</li>';
								}
							}
							
							// Check for total limit set, if it is, set event as limited
							if($db->limitHandlers > 500 || $db->limitHandlers < 500)
							{
								echo '
								<li>This event is limited to '.$db->limitHandlers.' handlers. <b>'.($db->limitHandlers - handlerEventCount($db->id)).' handlers remaining.</b></li>';
							}
					echo '
						</ul>
					</div>';
				}
				
				// If is a admin and a limited event
				if(isAdmin() && $db->limitedEvent == 1)
				{
					// All other events show counts of sign ups
					echo '
					<div class="section-card">
					<div name="troopersRemainingDisplayAdmin">
						<h2 class="tm-section-header">Admin Trooper Counts</h2>

						<ul style="display:inline-table;">
							<li>501st: '.eventClubCount($db->id, 0).' </li>';
							
							// Loop through clubs
							foreach($clubArray as $club => $club_value)
							{
								echo '<li>' . $club_value['name'] . ': ' . eventClubCount($db->id, $club_value['squadID']) . '</li>';
							}
							
						echo '
						</ul>
					</div>
					</div>';
				}
				
				if($db->limitedEvent == 1)
				{
					echo '
					<div class="alert-box">
						<b>Reminder:</b> This event has been set as a <i>manual selection</i> event. When a trooper needs to make a change to their attending status or costume, troopers must comment below what changes need to be made, and command staff will make the changes. Please note, this only applies to manual selection events.
					</div>';
				}
			}
			
			echo '
			<div class="section-card">
			<h2 class="tm-section-header">Roster</h2>
			<div style="overflow-x: auto;" id="signuparea1" name="signuparea1">
				<div style="text-align: center;">
					<img src="images/loading.gif" />
				</div>
			</div>
			<p>
				<a href="script/php/gencsv.php?troopid='.cleanInput($_GET['event']).'" class="button">Generate CSV</a>
			</p>
			</div>';

			// For rosterTableNoData - If no data, this is for the AJAX of a submitted sign up form
			if($i == 0)
			{
				echo '</div>';
			}

			// If logged in and assigned to event
			if(!$isMerged)
			{
				// Is the user in the event?
				$eventCheck = inEvent($_SESSION['id'], strip_tags(addslashes($_GET['event'])));

				if(strtotime($db->dateEnd) < strtotime("NOW"))
				{
					echo '
					<div class="alert-box">
						<b>This event is closed for editing.</b>
					</div>';
				}
				else
				{
					// TROOPER IN TROOP
					if($eventCheck['inTroop'] == 1)
					{
						if($eventCheck['status'] == 4)
						{	
							echo '
							<div name="signeduparea" id="signeduparea">
								<div class="alert-box">
									<b>You have canceled this troop.</b>
								</div>
							</div>';
						}
						else
						{
							// If open or locked
							if($db->closed == 0 || $db->closed == 3)
							{
								echo '
								<div name="signeduparea" id="signeduparea">
									<div class="alert-box">
										<b>You are signed up for this troop!</b>
									</div>
								</div>';
							}
							else
							{
								// Closed for editing
								echo '
								<div class="alert-box">This event is closed for editing.</div>';
							}
						}
					}
					else
					{
						// Sign up area - NOT IN TROOP
						echo '
						<div name="signuparea" id="signuparea">
							<div class="section-card">
							<h2 class="tm-section-header">Sign Up</h2>';
						
						// If event is not closed...
						if($db->closed == 0)
						{
							if(hasPermission(0, 1, 2, 3))
							{
								// Is this a hand picked event?
								if($db->limitedEvent == 1)
								{
									echo '<div class="alert-box"><b>This is a locked event. When you sign up, you will be placed in a pending status until command staff approves you. Please check for updates.</b></div>';
								}

								// Get troop count
								$statement = $conn->prepare("SELECT id FROM event_sign_up WHERE troopid = ? AND status != '4' AND status != '1'");
								$statement->bind_param("i", $_GET['event']);
								$statement->execute();
								$statement->store_result();
								$getNumOfTroopers = $statement->num_rows;

								// Set up total troopers
								$totalTroopers = $db->limit501st;

								// Loop through clubs
								foreach($clubArray as $club => $club_value)
								{
									$totalTroopers += $db->{$club_value['dbLimit']};
								}

								// Is the event full?
								if($getNumOfTroopers >= $totalTroopers)
								{
									echo '
									<b>This event is full, you will be placed on the stand by list.</b>';
								}
								
								echo '
									<form action="process.php?do=signup" method="POST" name="signupForm2" id="signupForm2">
										<input type="hidden" name="event" value="'.cleanInput($_GET["event"]).'" />
										
										<p>What costume will you wear?</p>
										<select name="costume" id="costume">
											<option value="null" SELECTED>Please choose an option...</option>';

										$statement = $conn->prepare("SELECT * FROM costumes WHERE club NOT IN (".implode(",", $dualCostume).") AND " . costume_restrict_query($_SESSION['id'], false, false) . " ORDER BY FIELD(costume, ".$mainCostumes."".mainCostumesBuild($_SESSION['id'])."".getMyCostumes(getTKNumber($_SESSION['id']), getTrooperSquad($_SESSION['id'])).") DESC, costume");
										$statement->execute();
																				
										if ($result3 = $statement->get_result())
										{
											while ($db3 = mysqli_fetch_object($result3))
											{
												echo '
												<option value="'. $db3->id .'"  club="'. $db3->club .'">'.getCostumeAbbreviation($db3->club).' '.$db3->costume.'</option>';
											}
										}

									echo '
										</select>

										<br />

										<p>Select a status:</p>

										<select name="status">
											<option value="null" SELECTED>Please choose an option...</option>';

										if($db->limitedEvent != 1)
										{
											echo '
												<option value="0">I\'ll be there!</option>';
												
											// Check if tentative allowed
											if($db->allowTentative == 1)
											{
												echo '
												<option value="2">Tentative</option>';
											}
										}
										else
										{
											echo '
												<option value="5">Request to attend (Pending)</option>';								
										}

										echo '
										</select>

										<p>Back up costume (if applicable):</p>

										<select name="backupcostume" id="backupcostume">';

										// Display costumes
										$statement = $conn->prepare("SELECT * FROM costumes WHERE club NOT IN (".implode(",", $dualCostume).") AND " . costume_restrict_query($_SESSION['id'], false, false) . " ORDER BY FIELD(costume, ".$mainCostumes."".mainCostumesBuild($_SESSION['id'])."".getMyCostumes(getTKNumber($_SESSION['id']), getTrooperSquad($_SESSION['id'])).") DESC, costume");
										$statement->execute();

										// Amount of costumes
										$c = 0;
										if ($result2 = $statement->get_result())
										{
											while ($db2 = mysqli_fetch_object($result2))
											{
												if($c == 0)
												{
													echo '<option value="0">Select a costume...</option>';
												}

												// Display costume
												echo '<option value="'.$db2->id.'">'.getCostumeAbbreviation($db2->club).' '.$db2->costume.'</option>';

												$c++;
											}
										}

										echo '
										</select>
										
										<br />
										<br />

										<input type="submit" value="Sign Up!" name="submitSignUp" />
									</form>
								</div>';
							}
							else
							{
								echo '
								You do not have permission to sign up for events. Please refer to the boards for assistance.';
							}
							echo '</div>';	// Section card
						}
						else
						{
							echo '
							<div class="alert-box">This event is closed for editing.</div>';
						}
					}
				}
			}
		}
		
		// If event does not exist
		if(!$eventExist)
		{
			echo '
			<div class="alert-box">
				<b>This event does not exist.</b>
			</div>';
		}
		else
		{
			// Don't show photos, if merged data
			if(!$isMerged)
			{				
				// Set results per page
				$results = 5;
				
				// Get total results - query
				$statement = $conn->prepare("
				    SELECT COUNT(id) AS total 
				    FROM uploads 
				    WHERE admin = 0 
				      AND troopid IN (
				          SELECT id FROM events 
				          WHERE id = ? 
				            OR (link = ? AND link != 0)
				            OR (link2 = ? AND link2 != 0)
				            OR id = ?
				            OR id = ?
				      )
				");

				$statement->bind_param("iiiii", $_GET['event'], $link, $link2, $link, $link2);
				$statement->execute();
				$statement->bind_result($rowPage);
				$statement->fetch();
				$statement->close();
				
				// Set total pages
				$total_pages = ceil($rowPage / $results);
				
				// If page set
				if(isset($_GET['page']))
				{
					// Get page
					$page = intval($_GET['page']);
					
					// Start from
					$startFrom = ($page - 1) * $results;
				}
				else
				{
					// Default
					$page = 1;
					
					// Start from - default
					$startFrom = 0;
				}
				
				// Query database for photos
				$statement = $conn->prepare("
				    SELECT * FROM uploads 
				    WHERE admin = '0' 
				      AND troopid IN (
				          SELECT id FROM events 
				          WHERE id = ? 
				            OR (link = ? AND link != 0)
				            OR (link2 = ? AND link2 != 0)
				            OR id = ?
				            OR id = ?
				      )
				    ORDER BY date DESC 
				    LIMIT ?, ?
				");

				$statement->bind_param("iiiiiii", $_GET['event'], $link, $link2, $link, $link2, $startFrom, $results);
				$statement->execute();
				
				// Count photos
				$i = 0;
				
				if ($result = $statement->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						// If first result
						if($i == 0)
						{
							echo '
							<div class="section-card">
							<h2 class="tm-section-header" id="photo_section">Photos</h2>';
						}
						
						echo '
						<div class="container-image">
							<a href="images/uploads/'.$db->filename.'" data-lightbox="photosadmin" data-title="Uploaded by '.getName($db->trooperid).'" id="photo'.$db->id.'"><img src="images/uploads/resize/'.getFileName($db->filename).'.jpg" width="200px" height="200px" class="image-c" /></a>
							
							<p class="container-text">
								<a href="index.php?action=editphoto&id='.$db->id.'">Edit</a>
								<br />
								<a href="#" photoid="'.$db->id.'" name="tagged">' . (isInPhoto($db->id, $_SESSION['id']) ? 'Untag Me' : 'Tag Me') . '</a>
							</p>
						</div>';
						
						$i++;
					}

					// If photos
					if($i > 0)
					{
						echo '
						<p class="center-content">
							<i>Press photos for full resolution version.</i>
						</p>';
					}
				}
				
				// If photos
				if($total_pages > 1)
				{
					echo '<p>Pages: ';
					
					// Loop through pages
					for ($j = 1; $j <= $total_pages; $j++)
					{
						// If we are on this page...
						if($page == $j)
						{
							echo '
							'.$j.'';
						}
						else
						{
							echo '
							<a href="index.php?event='.cleanInput($_GET['event']).'&page='.$j.'#photo_section">'.$j.'</a>';
						}
						
						// If not that last page, add a comma
						if($j != $total_pages)
						{
							echo ', ';
						}
					}
					
					echo '</p>';
				}
				
				// If photos exist
				if($i > 0) {
					echo '</div>';	// Section card
				}
				
				// If trooper logged in show uploader
				$dateCheck = DateTime::createFromFormat("m/d/Y - h:i A", $date1);
				$now = new DateTime();

				echo '
				<div class="section-card">
					<h2 class="tm-section-header">Photo Upload</h2>

					<form action="script/php/upload.php" class="dropzone" id="photoupload">
						<input type="hidden" name="admin" value="' . (($dateCheck && $now < $dateCheck) ? 1 : 0) . '" />
						<input type="hidden" name="troopid" value="'.cleanInput($_GET['event']).'" />
						<input type="hidden" name="trooperid" value="'.cleanInput($_SESSION['id']).'" />
					</form>

					<!-- Image Uploader JS -->
					<script type="text/javascript">
					  
					    Dropzone.autoDiscover = false;
					  
					    var myDropzone = new Dropzone(".dropzone", { 
							maxFilesize: 10,
							acceptedFiles: ".jpeg,.jpg,.png,.gif",
							dictDefaultMessage: "Drop images here",
							// Error Handling Events
							init: function() {
							    // Handle errors on client-side (e.g., invalid file type or size)
							    this.on("error", function(file, message) {
							        if (file.size > this.options.maxFilesize * 1024 * 1024) {
							            alert("Error: File size exceeds 10MB!");
							        } else if (message.includes("You can\'t upload files of this type.")) {
							            alert("Error: Invalid file type. Please upload JPEG, PNG, or GIF.");
							        } else {
							            alert("Upload Error: " + message);
							        }
							        console.error("Client-side error:", message);
							    });

							    // Handle server-side errors (e.g., HTTP errors)
							    this.on("error", function(file, response) {
							        if (response.status === 413) {
							            alert("Error: File too large to process on the server.");
							        } else if (response.status >= 500) {
							            alert("Server Error: Please try again later.");
							        }
							        console.error("Server-side error:", response);
							    });

							    // Handle network issues and timeout
							    this.on("timeout", function(file) {
							        alert("Error: Upload timed out. Please try again.");
							        console.error("Upload timeout for:", file.name);
							    });

							    // Handle successful uploads
							    this.on("success", function(file, response) {
							        console.log("Upload successful:", response);
							        //"alert("File uploaded successfully!");
							    });
							}
					    });
					      
					</script>
				</div>';
			}

			if(!$isMerged)
			{
				// Check to see if this event is full
				$statement = $conn->prepare("SELECT id FROM event_sign_up WHERE troopid = ? AND status != '4' AND status != '1'");
				$statement->bind_param("i", $_GET['event']);
				$statement->execute();
				$statement->store_result();
				$getNumOfTroopers = $statement->num_rows;

				if($eventClosed == 0)
				{
					if(hasPermission(0, 1, 2, 3))
					{
						// Get number of troopers that trooper signed up for event
						$statement = $conn->prepare("SELECT id FROM event_sign_up WHERE addedby = ? AND troopid = ?");
						$statement->bind_param("ii", $_SESSION['id'], $_GET['event']);
						$statement->execute();
						$statement->store_result();
						$numFriends = $statement->num_rows;
						
						// Only show add a friend if main user is in event
						if(inEvent($_SESSION['id'], $_GET['event'])["inTroop"] == 1 && $numFriends < $friendLimit)
						{
							echo '
							<div class="section-card" id="addfriend" name="addfriend">';
						}
						else
						{
							echo '
							<div class="section-card" id="addfriend" name="addfriend" style="display: none;">';
						}

						// If event is full
						if($getNumOfTroopers >= $limitTotal)
						{
							echo '
							<div class="alert-box"><b>This event is full. Your friend will be placed on the stand by list.</b></div>';
						}
						
						echo '
						<h2 class="tm-section-header">Add a Friend</h2>

						<div id="add-friend-form">
						</div>
						</div>';
					}
					else
					{
						echo '
						<div class="alert-box">You do not have permission to sign up for events. Please refer to the boards for assistance.</div>';
					}
				}
				else
				{
					//echo '
					//<p>This event is closed for editing.</p>';
				}
				
				if($thread_id > 0)
				{
					echo '
					<div class="section-card">
					<form aciton="process.php?do=postcomment" name="commentForm" id="commentForm" method="POST">
						<input type="hidden" name="thread_id" id="thread_id" value="'.$thread_id.'" />
						<input type="hidden" name="eventId" id="eventId" value="'.cleanInput($_GET['event']).'" />

						<h2 class="tm-section-header">Discussion</h2>
						<div style="text-align: center;">

						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'B\', \'comment\')" class="button">Bold</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'I\', \'comment\')" class="button">Italic</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'U\', \'comment\')" class="button">Underline</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'COLOR\', \'comment\')" class="button">Color</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'SIZE\', \'comment\')" class="button">Size</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'URL\', \'comment\')" class="button">URL</a>
						<a href="#/" class="button" name="addSmiley">Add Smiley</a>

						<textarea cols="30" rows="10" name="comment" id="comment"></textarea>

						<br />

						<p aria-label="This will notify command staff of your comment." data-balloon-pos="up" data-balloon-length="fit">Notify command staff?</p>
						<select name="important" id="important">
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>

						<br /><br />

						<span name="smileyarea" style="display: block;">
						</span>

						<input type="submit" name="submitComment" value="Quick Reply!" /><br /><a href="'.$forumURL.'threads/'.$thread_id.'/reply?" class="button" target="_blank">Reply On Forum</a>
						</div>
					</form>

					<div name="commentArea" id="commentArea">

						<div style="text-align: center;">
							<img src="images/loading.gif" />
						</div>

					</div>
					</div>';
				}
				else
				{
						echo '
						<br />
						<b>A corresponding forum thread does not exist for this event.</b>';
				}
			}
		}
	}
}
// If we are viewing an event, hide all other info
else if(isset($_GET['event']) && !loggedIn())
{
	// Set cookie for login
	if(!loggedIn())
	{
		setcookie("TroopTrackerLastEvent", cleanInput($_GET['event']), time() + 3600);
	}

	echo '
	<p style="text-align: center;"><b>Please login to view this event.</b></p>';
}
else
{
	// Only show home page when it is loaded
	if(!isset($_GET['action']) && !isset($_GET['profile']) && !isset($_GET['tkid']) && !isset($_GET['event']))
	{
		if(!isWebsiteClosed())
		{
			// Show options for squad choice
			if(!loggedIn())
			{
				echo '
				<h2 class="tm-section-header">Welcome to the '.garrison.' Troop Tracker!</h2>';
				
				// If sign ups are not closed
				if(!isSignUpClosed())
				{
					echo '
					<p style="text-align: center; border: dashed white;">
						<a href="index.php?action=requestaccess">Are you new to the '.garrison.' and/or 501st? Or are you solely a member of another club? Click here.</a>
					</p>

					<p style="text-align: center; border: dashed white;">
						<a href="index.php?action=setup">Have you used the old troop tracker and need to set up your account? Click here.</a>
					</p>';
				}
				else
				{
					// If sign ups are closed
					echo '
					<p style="text-align: center;">Sign ups are currently locked for maintenance and testing. You can view your troops <a href="index.php?action=trooptracker">here</a>.</p>';
				}
			}
			
			if(loggedIn())
			{
				echo '
				<h2 class="tm-section-header">Troops</h2>
				<div style="text-align: center;" aria-label="Press an image to sort by squad / garrison." data-balloon-pos="down" data-balloon-length="fit">'
					. showSquadButtons() . '';

					foreach ($specialLinks as $name => $link) {
					    echo $link . "<br />";
					}

				echo '
				</div>
				
				<p style="text-align: center;">
					<a href="index.php?squad=mytroops" class="button">My Troops</a>
				</p>

				<hr /><br />

				<div style="text-align: center;">';
				
				// Get number of troops that need confirmed
				$statement = $conn->prepare("SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = ? AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1");
				$statement->bind_param("i", $_SESSION['id']);
				$statement->execute();
				$statement->store_result();
				$numberOfConfirmTroops = $statement->num_rows;
				
				// Show need to confirm if exist
				if($numberOfConfirmTroops > 0)
				{
					echo '
					<div class="alert-box" id="confirmTroopNotification">
  						<a href="#confirmtroops">⚠️ You have '.$numberOfConfirmTroops.' troops to confirm. Click to confirm.</a>
					</div>';
				}

				// Set up add to query
				$addToQuery = "";

				// Loop through clubs
				foreach($clubArray as $club => $club_value)
				{
					// Add
					$addToQuery .= "events.".$club_value['dbLimit'].", ";
				}
				
				// Was a squad defined? (Prevents displays div when not needed)
				if(isset($_GET['squad']) && $_GET['squad'] == "mytroops")
				{
					// Query
					$statement = $conn->prepare("SELECT events.id AS id, events.name, events.location, events.dateStart, events.dateEnd, events.squad, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, events.link, events.limit501st, ".$addToQuery."events.limitTotalTroopers, events.limitHandlers, events.closed FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = ? AND events.dateEnd > NOW() - INTERVAL 1 DAY AND (event_sign_up.status < 3 OR event_sign_up.status = 5) AND (events.closed = 0 OR events.closed = 3 OR events.closed = 4) ORDER BY events.dateStart");
					$statement->bind_param("i", $_SESSION['id']);
				}
				else if(isset($_GET['special']))
				{
					// Query
					$special = '%' . cleanInput($_GET['special']) . '%';

					$statement = $conn->prepare("SELECT * FROM events WHERE dateStart >= CURDATE() AND name LIKE ? AND (closed = '0' OR closed = '3' OR closed = '4') ORDER BY dateStart");
					$statement->bind_param("s", $special);
				}
				else if(isset($_GET['squad']) && $_GET['squad'] == "canceledtroops")
				{
					// Query
					$statement = $conn->prepare("SELECT * FROM events WHERE dateStart >= CURDATE() AND (closed = '2') ORDER BY dateStart");
				}
				else if(isset($_GET['squad']))
				{
					// Query
					$statement = $conn->prepare("SELECT * FROM events WHERE dateStart >= CURDATE() AND squad = ? AND (closed = '0' OR closed = '3' OR closed = '4') ORDER BY dateStart");
					$statement->bind_param("i", $_GET['squad']);
				}
				else
				{
					// Query
					$statement = $conn->prepare("SELECT * FROM events WHERE dateStart >= CURDATE() AND (closed = '0' OR closed = '3' OR closed = '4') ORDER BY dateStart");
				}

				// Number of events loaded
				$i = 0;
				
				// Number of canceled events loaded
				$j = 0;
				
				// Get canceled events
				$statement2 = $conn->prepare("SELECT * FROM events WHERE DATE_FORMAT(dateStart, '%Y-%m-%d') = CURDATE() AND (closed = '2') ORDER BY dateStart");
				$statement2->execute();
				
				// Load events that are today or in the future
				if ($result = $statement2->get_result())
				{
					while ($db = mysqli_fetch_object($result))
					{
						// If first result
						if($j == 0)
						{
							// Show message
							echo '
							<p>
								<b>NOTICE!! The following troops have been canceled:</b>
							</p>
							
							<ul style="display:inline-table;">';
						}
						
						echo '
						<li><a href="index.php?event='.$db->id.'">'.$db->name.'</a></li>';
						
						// Increment canceled events
						$j++;
					}
				}
				
				// If canceled event results
				if($j > 0)
				{
					echo '
					</ul>';
				}
				
				echo '
				<p>
					<a href="#/" id="changeview" class="button">Calendar View</a> 
					<a href="index.php?action=mapview" class="button">Map View</a>
				</p>
				
				<div id="listview">

				<p><input type="text" id="controlf" placeholder="Type your search here..." style="text-align: center;" /></p>';
				
				// Event calendar
				$events = array();

				$statement->execute();

				// Load events that are today or in the future
				if ($result = $statement->get_result())
				{
					while($db = mysqli_fetch_object($result))
					{	
						// Get number of troopers at event
						$statement2 = $conn->prepare("SELECT id FROM event_sign_up WHERE troopid = ? AND (status = '0' OR status = '2')");
						$statement2->bind_param("i", $db->id);
						$statement2->execute();
						$statement2->store_result();
						$getNumOfTroopers = $statement2->num_rows;
						
						// Get number of events with link
						$statement2 = $conn->prepare("SELECT id FROM events WHERE link = ?");
						$statement2->bind_param("i", $db->id);
						$statement2->execute();
						$statement2->store_result();
						$getNumOfLinks = $statement2->num_rows;

						echo '
						<div class="troop-card">

						'.(!isset($_GET['squad']) || (isset($_GET['squad']) && $_GET['squad'] == "mytroops") || (isset($_GET['squad']) && $_GET['squad'] == "canceledtroops") ? '<span style="margin-top: 5px; display: block;">' . getSquadLogo($db->squad) . '</span>' : '').'

						<a href="index.php?event=' . $db->id . '">' . date('M d, Y', strtotime($db->dateStart)) . '' . '<br />';
						
						// If has links to event, or is linked, show shift data
						if($getNumOfLinks > 0 || $db->link != 0)
						{
							echo '
							<b>' . date('l', strtotime($db->dateStart)) . '</b> - ' . date('h:i A', strtotime($db->dateStart)) . ' - ' . date('h:i A', strtotime($db->dateEnd)) .
							'<br />';
						}
						
						echo '
						' . $db->name . '</a>
						<br />
						<span style="font-size: 11px;"><a href="https://www.google.com/maps/search/?api=1&query='.$db->location.'" target="_blank">'.$db->location.'</a></span>';

						// Prevent on canceled events
						if($db->closed != 2)
						{
							// Set total
							$limitTotal = $db->limit501st;

							// Loop through clubs
							foreach($clubArray as $club => $club_value)
							{
								// Add
								$limitTotal += $db->{$club_value['dbLimit']};
							}

							// Check for total limit set, if it is, replace limit with it
							if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
							{
								$limitTotal = $db->limitTotalTroopers;
							}

							// Troop set to full
							if($db->closed == 4) {
								echo '
								<br />
								<span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';		
							}
							// If not enough troopers
							else if($getNumOfTroopers <= 1)
							{
								echo '
								<br />
								<span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS EVENT!</b></span>';
							}
							// If full (w/ handlers)
							else if(($getNumOfTroopers - handlerEventCount($db->id)) >= $limitTotal && ($db->limitHandlers > 500 || $db->limitHandlers < 500) && (handlerEventCount($db->id) >= $db->limitHandlers))
							{
								echo '
								<br />
								<span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';
							}
							// If full
							else if(($getNumOfTroopers - handlerEventCount($db->id)) >= $limitTotal && $db->limitHandlers == 500)
							{
								// Check handler count
								if($db->limitHandlers == 500) {
									echo '
									<br />
									<span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';
								} else {
									$statement2 = $conn->prepare("SELECT id FROM event_sign_up WHERE (status = '0' OR status = '2') AND troopid = ? AND (SELECT costume FROM costumes WHERE id = event_sign_up.costume) LIKE '%handler%'");
									$statement2->bind_param("i", $db->id);
									$statement2->execute();
									$statement2->store_result();
									$getNumOfHandlers = $statement2->num_rows;

									// Check if handlers full
									if($getNumOfHandlers >= $db->limitHandlers) {
										echo '
										<br />
										<span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';
									} else {
										// Show troopers attending
										echo '
										<br />
										<span>'.$getNumOfTroopers.' Troopers Attending</span>';
									}
								}
							}
							// Everything else
							else
							{
								echo '
								<br />
								<span>'.$getNumOfTroopers.' Troopers Attending</span>';
							}
						}

						// Increment number of events
						$i++;
						
						// Set up string to add to title if a linked event
						$add = "";
						
						// If this a linked event?
						if(isLink($db->id) > 0)
						{
							$add .= "[" . date("h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "] ";
						}
						
						// Add to calendar
						$events[] = array(
							'start' => date('Y-m-d', strtotime($db->dateStart)),
							'end' => date('Y-m-d', strtotime($db->dateEnd)),
							'summary' => '<a href="index.php?event='.$db->id.'" title="'.$db->name.'">' . $add . '' . $db->name . '</a><br /><br />',
							'mask' => true,
						);

						echo '
						</div>';
					}
				}
				
				// Add events to calendar
				$calendar->addEvents($events);
				
				// One month from today
				$datec1 = date('Y-m-d', strtotime('first day of +1 month'));
				
				// Two months from today
				$datec2 = date('Y-m-d', strtotime('first day of +2 month'));
				
				// Show calendars
				echo '
				</div>
				
				<div id="calendarview" style="display: none;">'
				. $calendar->draw(date('Y-m-d'))
				. '<br />' .
				$calendar->draw($datec1)
				. '<br />' .
				$calendar->draw($datec2)
				. '</div>';

				// Home page, no events
				if($i == 0)
				{
					echo 'There are no events to display.';
				}			

				echo '
				</div>';
			}

			if(loggedIn())
			{
				// Hide canceled troops button if we are on the page
				if((isset($_GET['squad']) && $_GET['squad'] != "canceledtroops") || !isset($_GET['squad']))
				{
					echo '
					<p style="text-align: center;">
						<a href="index.php?squad=canceledtroops" class="button">Canceled Troop Noticeboard</a>
					</p>';

					echo '
					<h2 class="tm-section-header">Recently Finished</h2>
					
					<ul class="event-grid">';
					
					// If on my troops
					if(isset($_GET['squad']) && $_GET['squad'] == "mytroops")
					{
						// Set up add to query
						$addToQuery = "";

						// Loop through clubs
						foreach($clubArray as $club => $club_value)
						{
							// Add
							$addToQuery .= "events.".$club_value['dbLimit'].", ";
						}

						// Query
						$statement = $conn->prepare("SELECT events.squad, events.id AS id, events.name, events.dateStart, events.dateEnd, events.squad, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status, events.link, ".$addToQuery."events.limit501st FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = ? AND events.closed = 1 ORDER BY dateEnd DESC LIMIT 20");
						$statement->bind_param("i", $_SESSION['id']);
					}
					// If on squad
					else if(isset($_GET['squad']))
					{
						// Get recently closed troops by squad
						$statement = $conn->prepare("SELECT * FROM events WHERE closed = '1' AND squad = ? ORDER BY dateEnd DESC LIMIT 20");
						$statement->bind_param("i", $_GET['squad']);
					}
					// If on default
					else
					{
						// Get recently closed troops
						$statement = $conn->prepare("SELECT * FROM events WHERE closed = '1' ORDER BY dateEnd DESC LIMIT 20");
					}

					$statement->execute();
					
					// Load events that are today or in the future
					if ($result = $statement->get_result())
					{
						while ($db = mysqli_fetch_object($result))
						{
							echo '
							<li class="event-item">'.(!isset($_GET['squad']) || (isset($_GET['squad']) && $_GET['squad'] == "mytroops") || (isset($_GET['squad']) && $_GET['squad'] == "canceledtroops") ? getSquadLogo($db->squad) : '').' <a href="index.php?event='.$db->id.'" '. (isset($db->status) && $db->status == 2 ? 'class = "tenative-troop"' : '') . (isset($db->status) && $db->status == 4 ? 'class = "canceled-troop"' : '') .'>'. (isLink($db->id) > 0 ? '[<b>' . date("l", strtotime($db->dateStart)) . '</b> : <i>' . date("m/d - h:i A", strtotime($db->dateStart)) . ' - ' . date("h:i A", strtotime($db->dateEnd)) . '</i>] ' : '') .''.$db->name.'</a></li>';
						}
					}
					
					echo '
					</ul>';
				}
				
				// Load events that need confirmation
				$statement = $conn->prepare("SELECT events.squad, events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = ? AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1 ORDER BY events.dateEnd DESC");
				$statement->bind_param("i", $_SESSION['id']);
				$statement->execute();

				if ($result = $statement->get_result())
				{
					// Number of results total
					$i = 0;

					while ($db = mysqli_fetch_object($result))
					{
						// If data
						if($i == 0)
						{
							echo '
							<br />
							<hr />
							<div name="confirmArea" id="confirmArea">
							<h2 class="tm-section-header" id="confirmtroops">Confirm Troops</h2>
							<form action="process.php?do=confirmList" method="POST" name="confirmListForm" id="confirmListForm">
							<div name="confirmArea2" id="confirmArea2">';
						}

						echo '
						<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">
							<input type="checkbox" name="confirmList[]" id="confirmList_'.$db->eventId.'" value="'.$db->eventId.'" /> ' . getSquadLogo($db->squad) . ' ' . (isLink($db->eventId) > 0 ? '[<b>' . date("l", strtotime($db->dateStart)) . '</b> : <i>' . date("m/d - h:i A", strtotime($db->dateStart)) . ' - ' . date("h:i A", strtotime($db->dateEnd)) . '</i>] ' : '') . ''.$db->name.'<br /><br />
						</div>';
						
						// If a shift exists to attest to
						$i++;
					}
				}

				// If data
				if($i > 0)
				{
					echo '
						</div>
						<input type="submit" name="submitConfirmList" id="submitConfirmList" value="I attended these troops" />
						<input type="submit" name="submitConfirmListDelete" id="submitConfirmListDelete" value="I did NOT attend these troops" />
						<p>Attended Costume:</p>
						<select name="costume" id="costumeChoice">';

						$statement = $conn->prepare("SELECT * FROM costumes " . costume_restrict_query($_SESSION['id'], true) . " ORDER BY FIELD(costume, ".$mainCostumes."".mainCostumesBuild($_SESSION['id'])."".getMyCostumes(getTKNumber($_SESSION['id']), getTrooperSquad($_SESSION['id'])).") DESC, costume");
						$statement->execute();
						
						$l = 0;
						if ($result3 = $statement->get_result())
						{
							while ($db3 = mysqli_fetch_object($result3))
							{
								if($l == 0)
								{
									echo '
									<option value="">Please choose an option...</option>';
								}

								echo '
								<option value="'. $db3->id .'">'.getCostumeAbbreviation($db3->club).' '.$db3->costume.'</option>';

								$l++;
							}
						}

					echo '
						</select>
					</form>
					<p>If your costume is not listed, please notify the garrison web master before confirming.</p>
					</div>';
				}
				
				// Load events that need confirmation
				$statement = $conn->prepare("SELECT events.squad, events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status, event_sign_up.addedby, event_sign_up.costume, event_sign_up.note FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.addedby = ? AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1 ORDER BY events.dateEnd DESC");
				$statement->bind_param("i", $_SESSION['id']);
				$statement->execute();

				if ($result = $statement->get_result())
				{
					// Number of results total
					$i = 0;

					while ($db = mysqli_fetch_object($result))
					{
						// If data
						if($i == 0)
						{
							echo '
							<br />
							<div name="confirmArea3" id="confirmArea3">
							<h2 class="tm-section-header" id="confirmtroops">Confirm Friends</h2>
							<div name="confirmArea4" id="confirmArea4">';
						}
						
						// Set up string to add to title if a linked event
						$add = "";
						
						// If added by friend, add name
						if($db->addedby != 0)
						{
							$add .= '<a href="#/" trooperid="'.$db->trooperid.'" troopid="'.$db->eventId.'" signid="'.$db->signupId.'" class="button" name="attendFriend">Attended</a> <a href="#/" trooperid="'.$db->trooperid.'" troopid="'.$db->eventId.'" signid="'.$db->signupId.'" class="button" name="didNotFriend">Did Not Attend</a>';
							
							// If note left, add note
							if($db->note != "")
							{
								$add .= '<b>[' . $db->note . ']</b> ';
							}
							
							$add .= '<b>' . getName($db->trooperid) . ' as ' . getCostume($db->costume) . '</b>: ';
						}
						
						// If this a linked event?
						if(isLink($db->eventId) > 0)
						{
							$add .= "[<b>" . date("l", strtotime($db->dateStart)) . "</b> : <i>" . date("m/d - h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "</i>] ";
						}

						echo '
						<div name="confirmListBox_'.$db->eventId.'_'.$db->trooperid.'" id="confirmListBox_'.$db->eventId.'_'.$db->trooperid.'">
							'.$add.' ' . getSquadLogo($db->squad) . ' '.$db->name.'<br /><br />
						</div>';
						
						// If a shift exists to attest to
						$i++;
					}
				}

				// If data
				if($i > 0)
				{
					echo '
					</div>
					<p>If confirming for another trooper, you cannot set a new attended costume. Refer to a squad leader to change costume set.</p>
					</div>';
				}
			}
			
			echo '
			<h2 class="tm-section-header">Recent Photos</h2>';
			
			// Load photos
			$statement = $conn->prepare("SELECT * FROM uploads WHERE admin = '0' ORDER BY id DESC LIMIT 10");
			$statement->execute();
			
			// Setup count
			$i = 0;
			
			// Loop through photos
			if ($result = $statement->get_result())
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<a href="images/uploads/'.$db->filename.'" data-lightbox="photo" data-title="Uploaded by '.getName($db->trooperid).' on '.getEventTitle($db->troopid, true).'."><img src="images/uploads/resize/'.getFileName($db->filename).'.jpg" width="200px" height="200px" /></a>';
					
					// Increment
					$i++;
				}
			}
			
			// If no photos
			if($i == 0)
			{
				echo '
				<p style="text-align: center;">
					No photos to display.
				</p>';
			}
			else
			{
				echo '
				<p style="text-align: center;">
					<i>Press photos for full resolution version.</i>
					<br />
					<a href="index.php?action=photos" class="button">Recent events with photos</a>
				</p>';
			}
		}
		else
		{
			echo '
			<h2 class="tm-section-header">Sorry...</h2>
			
			<p style="text-align: center;">
				The Troop Tracker is closed at this time.
				<p>'.getSiteMessage().'</p>
			</p>';
		}
	}
}

echo '
</section>';

if(!isWebsiteClosed())
{
	if(loggedIn())
	{
		echo '
		<section class="users-online-box">
		  <h2 class="box-section-header">👥 Users Online</h2>
		  <p class="users-inline-list">';

		$statement = $conn->prepare("SELECT * FROM troopers WHERE last_active >= NOW() - INTERVAL 5 MINUTE ORDER BY tkid");
		$statement->execute();

		if ($result = $statement->get_result()) {
			$i = 0;
			while ($db = mysqli_fetch_object($result)) {
				if ($i > 0) {
					echo ', ';
				}

				$tk = readTKNumber($db->tkid, $db->squad, $db->id);
				$profile = 'index.php?profile=' . $db->id;
				$username = htmlspecialchars($db->forum_id);

				echo '<a href="'.$profile.'">'.$username.' ('.$tk.')</a>';
				$i++;
			}
		}

		if ($i == 0) {
			echo 'No users online!';
		}

		echo '</p></section>';
	}
}

echo '
<section class="tm-section tm-section-small">
<p class="tm-mb-0">
Website created by <a href="https://mattdrennan.com">Matthew Drennan (TK52233)</a>. If you encounter any technical issues with this site, please refer to the <a href="index.php?action=faq">FAQ page</a> for guidance.
</p>

<p class="tm-mb-0">
If you are missing troops or notice incorrect data, please refer to your squad leader.
</p>

<p class="footer-icons">
	<a href="https://github.com/MattDrennan/501-troop-tracker" target="_blank"><img src="images/github.png" alt="GitHub" title="Help contribute to the Troop Tracker project!" /></a> ';
	// Discord
	if(loggedIn() && discordInviteLink != '')
	{
		echo '
		<a href="'.discordInviteLink.'" target="_blank"><img src="images/discord.png" alt="Discord" title="Get event notifications and more on Discord!" /></a>';
	}
echo '
</p>
</section>

<script>
$(document).ready(function()
{';

// Loop through clubs - add rules for validation
foreach($clubArray as $club => $club_value)
{
	// Get array
	$getArray = explode(",", $club_value['db3Require']);

	// If squad set
	if($getArray[2] != "0")
	{
		// Get squad
		$getSquad = explode(":", $getArray[2])[1];

		echo '
	    // Add rules to clubs - IDs
	    $(\'#'.$club_value['db3'].'\').each(function()
	    {
	        $(this).rules(\'add\',
	        {';
	        	// If digits
	        	if($getArray[1] == "digits")
	        	{
					echo '
					digits: true,';
				}
				else
				{
					echo '
					digits: false,';
				}

				// If squad
				if($getArray[2] != "0")
				{
					echo '
					required: function()
					{
						return $(\'#squad\').val() == '.$getSquad.';
					}';
				}
				else
				{
					echo '
					required: false';
				}

			echo '
	        })
	    });';
	}
	else
	{
		// If value set
		if($club_value['db3'] != "")
		{
			// Squad not set
			echo '
		    // Add rules to clubs - IDs
		    $(\'#'.$club_value['db3'].'\').each(function()
		    {
		        $(this).rules(\'add\',
		        {';
		        	// If digits
		        	if($getArray[1] == "digits")
		        	{
						echo '
						digits: true,';
					}
					else
					{
						echo '
						digits: false,';
					}

					echo '
					required: false
		        })
		    });';
		}
	}
}

echo '
});
</script>';

echo '
<!-- External JS File -->
<script type="text/javascript" src="script/js/main.js?v=8"></script>
</body>
</html>';

?>