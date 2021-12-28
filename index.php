<?php

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
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	
	<!-- Title -->
	<title>501st '.garrison.' - Troop Tracker</title>
	
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap" rel="stylesheet" />
	
	<!-- Main Style Sheets -->
	<link href="fontawesome/css/all.min.css" rel="stylesheet" />';
	
	if(loggedIn())
	{
		if(myTheme() == 0)
		{
			echo '
			<link href="css/main2.css" rel="stylesheet" />
			<link rel="stylesheet" href="css/nav2.css">';
		}
		else if(myTheme() == 1)
		{
			echo '
			<link href="css/main.css" rel="stylesheet" />
			<link rel="stylesheet" href="css/nav.css">';
		}
		else if(myTheme() == 2)
		{
			echo '
			<link href="css/main1.css" rel="stylesheet" />
			<link rel="stylesheet" href="css/nav1.css">';
		}
	}
	else
	{
		echo '
		<link href="css/main2.css" rel="stylesheet" />
		<link rel="stylesheet" href="css/nav2.css">';
	}
	
	echo '
	<!-- Style Sheets -->
	<link rel="stylesheet" href="script/lib/jquery-ui.min.css">
	<link rel="stylesheet" href="script/lib/jquery-ui-timepicker-addon.css">
	<link href="css/dropzone.min.css" type="text/css" rel="stylesheet" />
	<link href="css/lightbox.min.css" rel="stylesheet" />
	<link href="css/calendar.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link href="css/all.css" rel="stylesheet" />
	
	<!-- Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	
	<!-- Setup Variable -->
	<script>var placeholder = '.placeholder.';</script>

	<!-- JQUERY -->
	<script src="script/lib/jquery-3.4.1.min.js"></script>

	<!-- JQUERY UI -->
	<script src="script/lib/jquery-ui.min.js"></script>

	<!-- JQUERY SELECT -->
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<!-- Addons -->
	<script src="script/lib/jquery-ui-timepicker-addon.js"></script>
	<script src="script/js/validate/jquery.validate.min.js"></script>
	<script src="script/js/validate/validate.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
	
	<!-- Drop Zone -->
	<script src="script/lib/dropzone.min.js"></script>
	
	<!-- LightBox -->
	<script src="script/lib/lightbox.min.js"></script>

	<script>
 	$( function() {
		$("#datepicker").datetimepicker();
		$("#datepicker2").datetimepicker();
		$("#datepicker3").datetimepicker();
		$("#datepicker4").datetimepicker();
	} );
	</script>
</head>

<body>

<div class="tm-container">
<div class="tm-text-white tm-page-header-container">
<h1 class="tm-page-header">501st '.garrison.' - Troop Tracker</h1>
</div>
<div class="tm-main-content">
<section class="tm-section">

<div class="topnav" id="myTopnav">
<a href="index.php" '.isPageActive("home").'>Home</a>';

if(!isWebsiteClosed() || isAdmin())
{
	echo '
	<a href="index.php?action=trooptracker" '.isPageActive("trooptracker").'>Troop Tracker</a>';
}

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
</div>';

// Show support graph
echo drawSupportGraph();

// Show the account page
if(isset($_GET['action']) && $_GET['action'] == "account" && loggedIn())
{
	// Theme Button Submit
	if(isset($_POST['themeButton']))
	{
		$conn->query("UPDATE troopers SET theme = '".cleanInput($_POST['themeselect'])."' WHERE id = '".$_SESSION['id']."'");
		
		echo '<p>Your theme has been changed. Please <a href="index.php?action=account">refresh</a> the page to see the changes.</p>';
	}
	
	// Account Page
	echo '
	<h2 class="tm-section-header">Manage Account</h2>

	<a href="#/" id="emailSettingLink" class="button">E-mail Settings</a> 
	<a href="#/" id="changeemailLink" class="button">Change E-mail</a> 
	<a href="#/" id="changephoneLink" class="button">Change Phone</a> 
	<a href="#/" id="changenameLink" class="button">Change Name</a> 
	<a href="#/" id="changepasswordLink" class="button">Change Password</a>
	<a href="#/" id="changethemeLink" class="button">Change Theme</a> 
	<a href="index.php?action=donation" class="button">Donate</a> 
	<a href="index.php?profile='.$_SESSION['id'].'" class="button">View Your Profile</a>
	<br /><br />

	<div id="unsubscribe" style="display:none;">
		<h2 class="tm-section-header">E-mail Subscription</h2>
		<form action="process.php?do=unsubscribe" method="POST" name="unsubscribeForm" id="unsubscribeForm">';
		$query = "SELECT subscribe FROM troopers WHERE id = '".$_SESSION['id']."'";
		
		// Is the trooper subscribed to e-mail?
		$subscribe = "";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
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
			<h3>Squads</h3>
			<form action="process.php?do=emailsettings" method="POST" id="emailsettingsForm" name="emailsettingsForm">';
			
			// Squad count
			$i = 1;
			
			// Loop through squads
			foreach($squadArray as $squad => $squad_value)
			{
				echo '
				<input type="checkbox" name="esquad'.$i.'" id="esquad'.$i.'" ' . emailSettingStatus("esquad" . $i, true) . ' />' . $squad . '<br />';
				
				// Increment squad count
				$i++;
			}
			
			echo '
				<h3>Website</h3>
				<input type="checkbox" name="efast" id="efast" ' . emailSettingStatus("efast", true) . ' />Instant Event Notification<br />
				<input type="checkbox" name="ecomments" id="ecomments" ' . emailSettingStatus("ecomments", true) . ' />Comments<br />
				<input type="checkbox" name="econfirm" id="econfirm" ' . emailSettingStatus("econfirm", true) . ' />Confirm Attendance Notification<br />
				<input type="checkbox" name="ecommandnotify" id="ecommandnotify" ' . emailSettingStatus("ecommandnotify", true) . ' />Command Staff Notifications<br />
			</form>
		</div>
	</div>
	
	<div id="changetheme" style="display:none;">
		<h2 class="tm-section-header">Change Theme</h2>
		<form action="index.php?action=account" method="POST" name="changethemeForm" id="changethemeForm">
			<select name="themeselect" id="themeselect">';
			$query = "SELECT theme FROM troopers WHERE id = '".$_SESSION['id']."'";

			if ($result = mysqli_query($conn, $query) or die($conn->error))
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<option value="0" '.echoSelect(0, $db->theme).'>Florida Garrison Theme (Default)</option>
					<option value="1" '.echoSelect(1, $db->theme).'>Everglades Theme</option>
					<option value="2" '.echoSelect(2, $db->theme).'>Makaze Theme</option>';
				}
			}
		echo '
				<input type="submit" name="themeButton" id="themeButton" value="Change Theme" />
			</select>
		</form>
	</div>

	<div id="changeemail" style="display:none;">
		<h2 class="tm-section-header">Change Your E-mail</h2>

		<form action="process.php?do=changeemail" method="POST" name="changeEmailForm" id="changeEmailForm">';
		$query = "SELECT email FROM troopers WHERE id = '".$_SESSION['id']."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				echo '
				<input type="text" name="email" id="email" value="'.$db->email.'" />
				<input type="submit" name="emailButton" id="emailButton" value="Update" />';
			}
		}
		echo '
		</form>
	</div>

	<div id="changename" style="display:none;">
		<h2 class="tm-section-header">Change Your Name</h2>

		<form action="process.php?do=changename" method="POST" name="changeNameForm" id="changeNameForm">';
		$query = "SELECT name FROM troopers WHERE id = '".$_SESSION['id']."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				echo '
				<input type="text" name="name" id="name" value="'.$db->name.'" />
				<input type="submit" name="nameButton" id="nameButton" value="Update" />';
			}
		}
		echo '
		</form>
	</div>

	<div id="changepassword" style="display:none;">
		<h2 class="tm-section-header">Change Your Password</h2>

		<form action="process.php?do=changepassword" method="POST" name="changePasswordForm" id="changePasswordForm">
			<p>Old Password:</p>
			<input type="password" name="oldpassword" id="oldpassword" />

			<p>New Password:</p>
			<input type="password" name="newpassword" id="newpassword" />

			<p>Re-enter New Password:</p>
			<input type="password" name="newpassword2" id="newpassword2" />

			<br /><br />

			<input type="submit" value="Submit!" name="changePasswordSend" id="changePasswordSend" />
		</form>
	</div>

	<div id="changephone" style="display:none;">
		<h2 class="tm-section-header">Change Phone Number</h2>
		<form action="process.php?do=changephone" method="POST" name="changePhoneForm" id="changePhoneForm">';
		$query = "SELECT phone FROM troopers WHERE id = '".$_SESSION['id']."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				echo '
				<input type="text" name="phone" id="phone" value="'.$db->phone.'" />
				<input type="submit" name="phoneButton" id="phoneButton" value="Update" />';
			}
		}
		echo '
		</form>
	</div>';
}

// Show the request access page
if(isset($_GET['action']) && $_GET['action'] == "requestaccess" && !isSignUpClosed())
{
	echo '
	<h2 class="tm-section-header">Request Access</h2>
	
	<div name="requestAccessFormArea" id="requestAccessFormArea">
		<p style="text-align: center;">New to the 501st and/or '.garrison.'? Or are you solely a member of another club? Use this form below to start signing up for troops. Command Staff will need to approve your account prior to use.</p>
		
		<form action="process.php?do=requestaccess" name="requestAccessForm" id="requestAccessForm" method="POST">
			First & Last Name: <input type="text" name="name" id="name" />
			<br /><br />
			TKID (numbers only): <input type="text" name="tkid" id="tkid" />
			<p><i>Non-501st clubs, please enter an ID number of your choosing.</i></p>
			E-mail: <input type="text" name="email" id="email" />
			<br /><br />
			Phone (Optional): <input type="text" name="phone" id="phone" />
			<br /><br />
			FL Garrison Forum Username: <input type="text" name="forumid" id="forumid" />
			<br /><br />
			Rebel Legion Forum Username (if applicable): <input type="text" name="rebelforum" id="rebelforum" />
			<br /><br />
			Mando Mercs CAT # (if applicable): <input type="text" name="mandoid" id="mandoid" />
			<br /><br />
			Saber Guild SG # (if applicable): <input type="text" name="sgid" id="sgid" />
			<br /><br />
			Password: <input type="password" name="password" id="password" />
			<br /><br />
			Password (Confirm): <input type="password" name="passwordC" id="passwordC" />
			<br /><br />
			<p>Squad/Club:</p>
			<select name="squad" id="squad">
				'.squadSelectList().'
			</select>
			<br /><br />
			<input type="submit" name="submitRequest" value="Request" />
			<br />
			<b>If you are a dual member, you will only need one account. Make sure your account is registered as a 501st Legion member.</b>
		</form>
	</div>

	<div name="requestAccessFormArea2" id="requestAccessFormArea2"></div>';
}

// Show the profile page
if(isset($_GET['profile']))
{
	// Convert TKID to profile
	if(isset($_GET['tkid']))
	{
		$_GET['profile'] = getIDFromTKNumber($_GET['tkid']);
	}
	
	// Get data
	$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd, troopers.id, troopers.name, troopers.forum_id, troopers.tkid, troopers.squad, troopers.phone FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopers.id = '".cleanInput($_GET['profile'])."' AND troopers.id != ".placeholder." AND events.closed = '1' AND event_sign_up.status = '3' ORDER BY events.dateEnd DESC";
	$i = 0;
	
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				// Show profile information
				echo 
				profileTop($db->id, $db->tkid, $db->name, $db->squad, $db->forum_id, $db->phone);
				
				echo  '
				<span style="text-align: center;">' . getTroopCounts(cleanInput($_GET['profile'])) . '</span>
				<div style="overflow-x: auto;">
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
				<td><a href="index.php?event='.$db->troopid.'">'.$add.''.$db->eventName.'</a></td>';
				
			$dateFormat = date('m-d-Y', strtotime($db->dateEnd));

			echo '
				<td>'.$dateFormat.'</td>	<td>'.ifEmpty(getCostume($db->costume), "N/A").'</td>
			</tr>';

			// Increment i
			$i++;
		}
	}

	// If profile does not exist
	if(!profileExist($_GET['profile']))
	{
		echo '
		<p style="text-align: center;">
			<b>This trooper does not exist.</b>
		</p>';
	}
	// Check if placeholder
	else if($_GET['profile'] == placeholder)
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
			echo profileTop($_GET['profile'], getTKNumber($_GET['profile']), getName($_GET['profile']), getTrooperSquad($_GET['profile']), getTrooperForum($_GET['profile']), getPhone($_GET['profile']));
		}
		else
		{
			// Get count for awards
			$troops_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE status = '3' AND trooperid = '".cleanInput($_GET['profile'])."'") or die($conn->error);
			$count = $troops_get->fetch_row();

			// Set up award count
			$j = 0;

			echo '
			</table>
			</div>

			<h2 class="tm-section-header">Awards</h2>';
			
			// Check if supporter
			if(isSupporter(cleanInput($_GET['profile'])))
			{
				echo '<img src="images/flgdonate.png" />';
			}
			
			echo'
			<ul>';

			if($count[0] >= 1)
			{
				echo '<li>First Troop Completed!</li>';
				$j++;
			}

			if($count[0] >= 10)
			{
				echo '<li>10 Troops</li>';
			}

			if($count[0] >= 25)
			{
				echo '<li>25 Troops</li>';
			}

			if($count[0] >= 50)
			{
				echo '<li>50 Troops</li>';
			}

			if($count[0] >= 75)
			{
				echo '<li>75 Troops</li>';
			}

			if($count[0] >= 100)
			{
				echo '<li>100 Troops</li>';
			}

			if($count[0] >= 150)
			{
				echo '<li>150 Troops</li>';
			}

			if($count[0] >= 200)
			{
				echo '<li>200 Troops</li>';
			}

			if($count[0] >= 250)
			{
				echo '<li>250 Troops</li>';
			}

			if($count[0] >= 300)
			{
				echo '<li>300 Troops</li>';
			}

			if($count[0] >= 400)
			{
				echo '<li>400 Troops</li>';
			}

			if($count[0] >= 500)
			{
				echo '<li>500 Troops</li>';
			}

			if($count[0] >= 501)
			{
				echo '<li>501 Troops Award</li>';
			}

			// Get data from custom awards - load award user data
			//$query2 = "SELECT * FROM awards_troopers WHERE trooperid = '".cleanInput($_GET['profile'])."'";
			$query2 = "SELECT award_troopers.awardid, award_troopers.trooperid, awards.id, awards.title, awards.icon FROM award_troopers LEFT JOIN awards ON awards.id = award_troopers.awardid WHERE award_troopers.trooperid = '".cleanInput($_GET['profile'])."'";
			if ($result2 = mysqli_query($conn, $query2))
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
						echo '<li><img src="images/icons/'.$db2->icon.'" alt="'.$db2->title.'" /> '.$db2->title.'</li>';
					}

					$j++;
				}
			}

			if($j == 0)
			{
				echo '<li>No awards yet!</li>';
			}
			echo '
			</ul>';
		}
		
		echo '
		<h2 class="tm-section-header">Costumes</h2>';
		
		// Show 501st costumes
		showCostumes(getTKNumber($_GET['profile']), getTrooperSquad($_GET['profile']));
		
		// Show Rebel Legion costumes
		showRebelCostumes(getRebelInfo(getRebelLegionUser($_GET['profile']))['id']);
		
		// Show Mando Mercs costumes
		showMandoCostumes(getMandoLegionUser($_GET['profile']));

		// Show Saber Guild costumes
		showSGCostumes(getSGUser($_GET['profile']));
		
		// Show Droid Builder costumes
		showDroids(getTrooperForum($_GET['profile']));
	}
}

// Show the donation page
if(isset($_GET['action']) && $_GET['action'] == "donation" && loggedIn())
{
	// If donated...
	if(isSupporter($_SESSION['id']))
	{
		echo '
		<p style="text-align: center;">
			<b>Thank you for supporting the '.garrison.'!</b>
		</p>
		<hr />';
	}
	
	echo '
	<h2 class="tm-section-header">Support the '.garrison.'!</h2>
	
	<p style="text-align: center;">With a monthly contribution of only $5.00, you can help support paying for the website server, prop storage, and general expenses of the '.garrison.'. Without your assistance, other members are left to pay for all expenses out of their pocket. Donations are only available to '.garrison.' members, and all the money goes to garrison expenses.</p>
	
	<h2 class="tm-section-header">What you get...</h2>
	
	<ul>
		<li>"'.garrison.' Supporter" award on your troop tracker profile</li>
		<li>"'.garrison.' Supporter" icon on troop sign ups</li>
	</ul>
	
	<h2 class="tm-section-header">Donate Below!</h2>
	<form action="https://www.paypal.com/donate" method="post" target="_top" style="text-align: center;">
		<input type="hidden" name="notify_url" value="'.ipn.'?trooperid='.$_SESSION['id'].'">
		<input type="hidden" name="custom" value="'.$_SESSION['id'].'">
		<input type="hidden" name="hosted_button_id" value="ULH54MMQKGL5Q" />
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
	</form>';
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
	$query = "SELECT uploads.troopid, events.dateStart, events.dateEnd FROM uploads LEFT JOIN events ON uploads.troopid = events.id WHERE admin = '0' GROUP BY uploads.troopid ORDER BY events.dateEnd DESC LIMIT 100";

	// Loop through query
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// First loop
			if($i == 0)
			{
				echo '
				<ul>';
			}

			$troopCount = $conn->query("SELECT id FROM uploads WHERE troopid = '".$db->troopid."' AND admin = '0'");

			echo '
			<li>
				<a href="index.php?event='.$db->troopid.'"><b>('.$troopCount->num_rows.')</b> ['.date("m-d-Y h:i A", strtotime($db->dateStart)).' - '.date("h:i A", strtotime($db->dateEnd)).'] - '.getEventTitle($db->troopid).'</a>
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

// Show the search page
if(isset($_GET['action']) && $_GET['action'] == "search")
{
	echo '
	<h2 class="tm-section-header">Search</h2>
	<div name="searchForm" id="searchForm">
		<form action="index.php?action=search" method="POST">';
			// Get our search type, and show certain fields
			if($_POST['searchType'] == "regular")
			{
				echo '
				Search Troop Name: <input type="text" name="searchName" id="searchName" value="'.cleanInput($_POST['searchName']).'" />
				<br /><br />
				Search Trooper Name: <input type="text" name="searchTrooperName" id="searchTrooperName" value="'.cleanInput($_POST['searchTrooperName']).'" />
				<br /><br />';
			}
			
			echo '
			Date Start: <input type="text" name="dateStart" id="datepicker3" value="'.cleanInput($_POST['dateStart']).'" />
			<br /><br />
			Date End: <input type="text" name="dateEnd" id="datepicker4" value="'.cleanInput($_POST['dateEnd']).'" />
			<br /><br />';
			
			// Get our search type, and show certain fields
			if($_POST['searchType'] == "regular")
			{
				echo '
				Search TKID: <input type="text" name="tkID" id="tkID" value="'.cleanInput($_POST['tkID']).'" />
				<br /><br />';
			}
			
			// Set search type
			if($_POST['searchType'] == "regular")
			{
				echo '
				<input type="hidden" name="searchType" value="regular" />';
			}
			else
			{
				echo '
				<input type="hidden" name="searchType" value="trooper" />';
			}
			
			// If trooper search, include searchType for another search
			if($_POST['searchType'] == "trooper")
			{
				echo '
				<input type="hidden" name="searchType" value="trooper" />
				
				<select name="squad" id="squad">
					<option value="0" '.echoSelect(0, cleanInput($_POST['squad'])).'>All</option>
					'.squadSelectList(true, "select").'
				</select>	
				<br /><br />';				
			}
			
			echo '
			<input type="submit" name="submitSearch" id="submitSearch" value="Search!" />
		</form>
	</div>
	
	<br /><br />
	<hr />
	<br /><br />';
	
	// Regular search
	if($_POST['searchType'] == "regular")
	{
		// Query for search
		$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE";
		
		if(strlen($_POST['tkID']) > 0)
		{
			$query .= " troopers.tkid = '".cleanInput($_POST['tkID'])."'";
		}

		if(strlen($_POST['searchName']) > 0)
		{
			if(strlen($_POST['tkID']) > 0)
			{
				$query .= " AND";
			}

			$query .= " events.name LIKE '%".cleanInput($_POST['searchName'])."%'";
		}

		if(strlen($_POST['dateStart']) > 0)
		{
			if(strlen($_POST['searchName']) > 0)
			{
				$query .= " AND";
			}

			$date = strtotime(cleanInput($_POST['dateStart']));
			$dateF = date('Y-m-d H:i:s', $date);

			$query .= " events.dateStart >= '".$dateF."'";
		}

		if(strlen($_POST['dateEnd']) > 0)
		{
			if(strlen($_POST['dateStart']) > 0)
			{
				$query .= " AND";
			}

			$date = strtotime(cleanInput($_POST['dateEnd']));
			$dateF = date('Y-m-d H:i:s', $date);

			$query .= " events.dateEnd <= '".$dateF."'";
		}
		
		if(strlen($_POST['searchTrooperName']) > 0)
		{
			if(strlen($_POST['dateEnd']) > 0)
			{
				$query .= " AND";
			}

			$query .= " troopers.name LIKE '%".cleanInput($_POST['searchTrooperName'])."%'";
		}
	}
	else if($_POST['searchType'] == "trooper")
	{
		// Query for search
		$query = "SELECT * FROM troopers";
		
		// Format date start
		$date = strtotime(cleanInput($_POST['dateStart']));
		$dateF = date('Y-m-d H:i:s', $date);
		
		// Format date end
		$date = strtotime(cleanInput($_POST['dateEnd']));
		$dateE = date('Y-m-d H:i:s', $date);
		
		// Get the squad search type
		// If All
		if($_POST['squad'] == 0)
		{
			// Get troop counts
			$troops_get = $conn->query("SELECT COUNT(id) FROM events WHERE dateStart >= '".$dateF."' AND dateEnd <= '".$dateE."'") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts
			$charity_get = $conn->query("SELECT SUM(moneyRaised) FROM events WHERE dateStart >= '".$dateF."' AND dateEnd <= '".$dateE."'") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If 501st
		if(($_POST['squad'] >= 1 && $_POST['squad'] <= 5))
		{
			// Add to query
			$query .= " WHERE squad = '".cleanInput($_POST['squad'])."'";
			
			// Get troop counts
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND squad = '".cleanInput($_POST['squad'])."' AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND squad = '".cleanInput($_POST['squad'])."' AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If Rebel Legion
		else if($_POST['squad'] == 6)
		{
			// Get troop counts - Rebel Legion
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('1' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts - Rebel Legion
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('1' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If Mando Mercs
		else if($_POST['squad'] == 7)
		{
			// Get troop counts - Mando Mercs
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('2' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts - Mando Mercs
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('2' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If Droid Builders
		else if($_POST['squad'] == 8)
		{
			// Get troop counts - Droid Builders
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('3' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts - Droid Builders
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('3' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If Other
		else if($_POST['squad'] == 9)
		{
			// Get troop counts - Other
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('4' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts - Other
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('4' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
	}

	// Get our search type, and show certain fields
	// Regular search
	if($_POST['searchType'] == "regular")
	{
		// Get data
		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($i == 0)
				{
					echo '
					<div style="overflow-x: auto;">
					<table border="1">
					<tr>
						<th>Event Name</th>	<th>Date</th>	<th>Trooper TKID</th>	<th>Attended Costume</th>	<th>Status</th>
					</tr>';
				}
				
				$dateFormat = date('m-d-Y', strtotime($db->dateEnd));

				echo '
				<tr>
					<td><a href="index.php?event='.$db->eventId.'">'.$db->eventName.'</a></td>	<td>'.$dateFormat.'</td>';
					
					// Prevent search from showing blank trooper
					if(isset($db->trooperid))
					{
						echo '
						<td><a href="index.php?profile='.$db->trooperid.'">'.readTKNumber(getTKNumber($db->trooperid), getTrooperSquad($db->trooperid)).'</a></td>';
					}
					else
					{
						echo '
						<td>N/A</td>';
					}
					
					echo '
					<td>'.ifEmpty(getCostume($db->costume), "N/A").'</td>	<td>'.getStatus($db->status).'</td>
				</tr>';

				$i++;
			}
		}
	}
	// Trooper search
	else if($_POST['searchType'] == "trooper")
	{
		// Get data
		$i = 0;
		
		// Trooper array
		$troopArray = array();
		
		// Format numbers to prevent errors - charity
		if(!isset($charity_count[0]))
		{
			$charity_count[0] = 0;
		}
		
		// Format numbers to prevent errors - troop
		if(!isset($troop_count[0]))
		{
			$troop_count[0] = 0;
		}
		
		// Start going through troopers
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Show table
				if($i == 0)
				{
					echo '
					<p>
						Total Troops: '.$troop_count[0].'
					</p>
					
					<p>
						Total Money Raised: $'.number_format($charity_count[0]).'
					</p>
					
					<div style="overflow-x: auto;">
					<table border="1">
					<tr>
						<th>Trooper TKID</th>	<th>Troop Count</th>
					</tr>';
				}

				// Increment $i
				$i++;
				
				// If All
				if($_POST['squad'] == 0)
				{
					// Get troop counts - All
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."'") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If 501st
				else if(($_POST['squad'] >= 1 && $_POST['squad'] <= count($squadArray)))
				{
					// Get troop counts - 501st
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR EXISTS(SELECT events.id, events.oldid FROM events WHERE events.oldid != 0 AND events.id = event_sign_up.troopid))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If Rebel Legion
				else if(getSquadName($_POST['squad']) == "Rebel Legion")
				{
					// Get troop counts - Rebel Legion
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('1' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If Mando Mercs
				else if(getSquadName($_POST['squad']) == "Mando Mercs")
				{
					// Get troop counts - Mando Mercs
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('2' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If Droid Builders
				else if(getSquadName($_POST['squad']) == "Droid Builders")
				{
					// Get troop counts - Droid Builders
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('3' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If Other
				else if(getSquadName($_POST['squad']) == "Other")
				{
					// Get troop counts - Other
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('4' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume) OR '5' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.costume))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// Create an array of our count
				$tempArray = array($db->tkid, $count[0], $db->name, $db->id, $db->squad);
				
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
			// Display
			echo '
			<tr>
				<td><a href="index.php?profile='.$value[3].'">'.readTKNumber($value[0], $value[4]).'</a> - '.$value[2].'</td>	<td>'.$value[1].'</td>
			</tr>';
		}
	}

	// What to do if we have more than one field
	if($i > 0)
	{
		echo '
		</table>
		</div>';
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

// Show the troop tracker page
if(isset($_GET['action']) && $_GET['action'] == "trooptracker")
{
	// If logged in
	if(loggedIn())
	{
		echo '
		<h2 class="tm-section-header">My Stats</h2>
		
		<p style="text-align: center;">
			<a href="#/" class="button" id="showstats" name="showstats">Show My Stats</a> 
			<a href="index.php?profile='.$_SESSION['id'].'" class="button">View My Profile</a>
		</p>
		
		<div id="mystats" name="mystats" style="display: none;">';

		// Get data
		$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND status = 3 AND events.closed = '1' ORDER BY events.dateEnd DESC";

		// Troop count
		$i = 0;

		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($i == 0)
				{
					echo getTroopCounts(cleanInput($_SESSION['id'])) . '
					<div style="overflow-x: auto;">
					<table border="1">
					<tr>
						<th>Troop</th>	<th>Costume</th>	<th>Money Raised</th>
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
					<td><a href="index.php?event='.$db->eventId.'">'.$add.''.$db->eventName.'</a></td>	<td>'.ifEmpty(getCostume($db->costume), "N/A").'</td>	<td>$'.number_format($db->moneyRaised).'</td>
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
	}

	// Show troop tracker for everyone
	echo '
	<h2 class="tm-section-header">Troop Tracker</h2>';

	// If squad is not set
	if(!isset($_GET['squad']))
	{
		// Get data
		$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND events.dateEnd > CURRENT_DATE - INTERVAL 60 DAY GROUP BY events.id ORDER BY events.dateEnd DESC LIMIT 20";
	}
	else
	{
		// Set up query for specific squad
		$add = "";
		$add2 = "";
		
		// Check if squad is not all
		if($_GET['squad'] != 0)
		{
			// If is a squad, add to query
			$add = "WHERE squad = '".cleanInput($_GET['squad'])."'";
			$add2 = "AND events.squad = '".cleanInput($_GET['squad'])."'";
		}
		
		// Set results per page
		$results = 20;
		
		// Get total results - query
		$sql = "SELECT COUNT(id) AS total FROM events ".$add.""; 
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		
		// Set total pages
		$total_pages = ceil($row["total"] / $results);
		
		// If page set
		if(isset($_GET['page']))
		{
			// Get page
			$page = cleanInput($_GET['page']);
			
			// Start from
			$startFrom = ($page - 1) * $results;
		}
		else
		{
			// Default page
			$page = 1;
			
			// Start from - default
			$startFrom = ($page - 1) * $results;
		}
		
		// Squad is set, show only that data
		$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE events.closed = '1' ".$add2." GROUP BY events.id ORDER BY events.dateEnd DESC LIMIT ".$startFrom.", ".$results."";
	}

	// Query count
	$i = 0;
	
	// Total time spent
	$timeSpent = 0;
	
	// Query
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				echo '
				<div style="overflow-x: auto;">
				<table border="1">
				<tr>
					<th>Troop</th>	<th>Troopers Attended</th>	<th>Money Raised</th>
				</tr>';
			}

			// How many troopers attended
			$trooperCount_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE troopid = '".$db->troopid."' AND status = '3'") or die($conn->error);
			
			$count = $trooperCount_get->fetch_row();
			
			// Set up text for linked event
			$add = "";
			
			// If linked event
			if(isLink($db->eventId) > 0)
			{
				$add = "[<b>" . date("l", strtotime($db->dateStart)) . "</b> : ".date("m/d - h:i A", strtotime($db->dateStart))." - ".date("h:i A", strtotime($db->dateEnd))."] ";
			}

			echo '
			<tr>
				<td><a href="index.php?event='.$db->eventId.'">'.$add.''.$db->eventName.'</a></td>	<td>'.$count[0].'</td>	<td>$'.number_format($db->moneyRaised).'</td>
			</tr>';

			$i++;
		}
	}

	if($i > 0)
	{
		// How many troops did the user attend
		$favoriteCostume_get = $conn->query("SELECT costume, COUNT(*) FROM event_sign_up WHERE costume != 706 AND costume != 720 AND costume != 721 GROUP BY costume ORDER BY COUNT(costume) DESC LIMIT 1") or die($conn->error);
		$favoriteCostume = mysqli_fetch_array($favoriteCostume_get);

		// Prevent notice error
		if($favoriteCostume == "")
		{
			$favoriteCostume['costume'] = 0;
		}

		// How many troops did the user attend
		$attended_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE status = '3'") or die($conn->error);
		$count1 = $attended_get->fetch_row();
		// How many regular troops
		$regular_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '0'") or die($conn->error);
		$count2 = $regular_get->fetch_row();
 		// How many armor party troops
		$armorparty_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '10'") or die($conn->error);
		$count13 = $armorparty_get->fetch_row();
 		// How many PR troops
		$charity_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '1'") or die($conn->error);
		$count3 = $charity_get->fetch_row();
		// How many Disney troops
		$pr_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '2'") or die($conn->error);
		$count4 = $pr_get->fetch_row();
		// How many convention troops
		$disney_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '3'") or die($conn->error);
		$count5 = $disney_get->fetch_row();
		// How many hospital troops
		$hospital_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '9'") or die($conn->error);
		$count12 = $hospital_get->fetch_row();
		// How many wedding troops
		$convention_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '4'") or die($conn->error);
		$count6 = $convention_get->fetch_row();
		// How many birthday party troops
		$wedding_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '5'") or die($conn->error);
		$count7 = $wedding_get->fetch_row();
		// How many wedding troops
		$birthday_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '6'") or die($conn->error);
		$count8 = $birthday_get->fetch_row();
		// How many virtual troops
		$virtual_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '7'") or die($conn->error);
		$count9 = $virtual_get->fetch_row();
		// How many other troops
		$other_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '8'") or die($conn->error);
		$count10 = $other_get->fetch_row();
		// How many total troops
		$total_get = $conn->query("SELECT COUNT(*) FROM events WHERE closed = '1'") or die($conn->error);
		$count11 = $total_get->fetch_row();
		// How much total money was raised
		$money_get = $conn->query("SELECT SUM(moneyRaised) FROM events WHERE closed = '1'") or die($conn->error);
		$countMoney = $money_get->fetch_row();
		
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
		</table>
		</div>
		
		<p style="text-align: right;">
			'.displaySquadLinks($squadLink).'
		</p>';
		
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
			<p><b>Favorite Costume:</b> '.ifEmpty(getCostume($favoriteCostume['costume']), "N/A").'</p>
			<p><b>Volunteers at Troops:</b> '.number_format($count1[0]).'</p>
			<p><b>Money Raised:</b> $'.number_format($countMoney[0]).'</p>
			<p><b>Regular Troops:</b> '.number_format($count2[0]).'</p>
			<p><b>Armor Parties:</b> '.number_format($count13[0]).'</p>
			<p><b>Charity Troops:</b> '.number_format($count3[0]).'</p>
			<p><b>PR Troops:</b> '.number_format($count4[0]).'</p>
			<p><b>Disney Troops:</b> '.number_format($count5[0]).'</p>
			<p><b>Convention Troops:</b> '.number_format($count6[0]).'</p>
			<p><b>Hospital Troops:</b> '.number_format($count12[0]).'</p>
			<p><b>Wedding Troops:</b> '.number_format($count7[0]).'</p>
			<p><b>Birthday Troops:</b> '.number_format($count8[0]).'</p>
			<p><b>Virtual Troops:</b> '.number_format($count9[0]).'</p>
			<p><b>Other Troops:</b> '.number_format($count10[0]).'</p>
			<p><b>Total Finished Troops:</b> '.number_format($count11[0]).'</p>';
		}
	}
	else
	{
		// No troops attended
		echo '
		<p><b>No one has attended a troop recently!</b></p>';
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
			<br /><br />
			<div id="trooper_count_radio" style="display: none;">
				<select name="squad" id="squad">
					<option value="0" SELECTED>All</option>
					'.squadSelectList().'
				</select>	
				<br /><br />
			</div>
			<input type="submit" name="submitSearch" id="submitSearch" value="Search!" />
		</form>
	</div>';
}

// Show the command staff page
if(isset($_GET['action']) && $_GET['action'] == "commandstaff")
{
	// If the user is logged in and is an admin
	if(loggedIn() && isAdmin())
	{
		$getTrooperNotifications = $conn->query("SELECT id FROM troopers WHERE approved = '0'");

		echo '
		<h2 class="tm-section-header">Command Staff Welcome Area</h2>

		<p>
			<a href="index.php?action=commandstaff&do=createevent" class="button">Create an Event</a> 
			<a href="index.php?action=commandstaff&do=editevent" class="button">Edit an Event</a> 
			<a href="index.php?action=commandstaff&do=roster" class="button">Roster</a> 
			<a href="index.php?action=commandstaff&do=notifications" class="button">Notifications</a> ';
			
			if(hasPermission(1))
			{
				echo '
				<a href="index.php?action=commandstaff&do=troopercheck" class="button">Trooper Check</a> 
				<a href="index.php?action=commandstaff&do=managecostumes" class="button">Costume Management</a> 
				<a href="index.php?action=commandstaff&do=createuser" class="button">Create Trooper</a> 
				<a href="index.php?action=commandstaff&do=managetroopers" class="button">Trooper Management</a> 
				<a href="index.php?action=commandstaff&do=approvetroopers" class="button" id="trooperRequestButton" name="trooperRequestButton">Approve Trooper Requests - ('.$getTrooperNotifications->num_rows.')</a> 
				<a href="index.php?action=commandstaff&do=assignawards" class="button">Award Management</a>
				<a href="index.php?action=commandstaff&do=stats" class="button">Statistics</a>
				<a href="index.php?action=commandstaff&do=sitesettings" class="button">Site Settings</a>';
			}
			
		echo '
		</p>';
		
		/**************************** Site Settings *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "sitesettings")
		{
			echo '
			<h3>Site Settings</h3>';
			
			// Get data
			$query = "SELECT * FROM settings LIMIT 1";
			$i = 0;
			if ($result = mysqli_query($conn, $query))
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
					
					// Change donation support
					echo '
					<input type="submit" name="submitSupportGoal" id="submitSupportGoal" value="Change Support Goal" />
					
					<div id="settingsEditArea" name="settingsEditArea"></div>';
						
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
			// Count number of users with set up accounts
			$totalAccountsSetUp = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1'");

			// Count number of users with set up accounts - 501
			$totalAccountsSetUp501 = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad <= 5");

			// Count number of users with set up accounts - Everglades
			$totalAccountsSetUpE = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 1");

			// Count number of users with set up accounts - Makaze
			$totalAccountsSetUpM = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 2");

			// Count number of users with set up accounts - Parjai
			$totalAccountsSetUpP = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 3");

			// Count number of users with set up accounts - Squad 7
			$totalAccountsSetUpS = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 4");

			// Count number of users with set up accounts - Tampa Bay
			$totalAccountsSetUpT = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 5");

			// Count number of users with set up accounts - Rebel
			$totalAccountsSetUpRebel = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 6");

			// Count number of users with set up accounts - Mando
			$totalAccountsSetUpMando = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 8");

			// Count number of users with set up accounts - Droid
			$totalAccountsSetUpDroid = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 7");

			// Count number of users with set up accounts - Other
			$totalAccountsSetUpOther = $conn->query("SELECT id FROM troopers WHERE password != '' AND approved = '1' AND squad = 9");

			// Total number of accounts
			$totalAccounts = $conn->query("SELECT id FROM troopers");

			$totalNotSet = $totalAccounts->num_rows - $totalAccountsSetUp->num_rows;

			echo '
			<h2>Important People</h2>
			<h3>Super Admin</h3>
			<ul>';

			// Show all super admins
			$query = "SELECT * FROM troopers WHERE permissions = '1' ORDER BY name";

			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '<li><a href="index.php?profile='.$db->id.'" target="_blank">'.$db->name.' - '.readTKNumber($db->tkid, $db->squad).'</a></li>';
				}
			}

			echo '</ul>';

			echo '
			<h3>Moderator</h3>
			<ul>';

			// Show all super admins
			$query = "SELECT * FROM troopers WHERE permissions = '2' ORDER BY name";

			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '<li><a href="index.php?profile='.$db->id.'" target="_blank">'.$db->name.' - '.readTKNumber($db->tkid, $db->squad).'</a></li>';
				}
			}

			echo '</ul>

			<h2>Statistics</h2>

			<p><b>501st Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUp501->num_rows).'</p>
			<p><b>Everglades Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpE->num_rows).'</p>
			<p><b>Makaze Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpM->num_rows).'</p>
			<p><b>Parjai Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpP->num_rows).'</p>
			<p><b>Squad 7 Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpS->num_rows).'</p>
			<p><b>Tampa Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpT->num_rows).'</p>
			<p><b>Rebel Legion Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpRebel->num_rows).'</p>
			<p><b>Mando Mercs Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpMando->num_rows).'</p>
			<p><b>Droid Builders Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpDroid->num_rows).'</p>
			<p><b>Other Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUpOther->num_rows).'</p>
			<p><b>Total Accounts (Set Up):</b> '.number_format($totalAccountsSetUp->num_rows).'</p>
			<p><b>Total Accounts (Not Set Up):</b> '.number_format($totalNotSet).'</p>
			<p><b>Total Accounts:</b> '.number_format($totalAccounts->num_rows).'</p>';
		}

		/**************************** Roster *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "roster" && isAdmin())
		{
			echo '
			<h3>Roster</h3>';
			
			// Squad count
			$i = 1;
			
			echo '
			<a href="index.php?action=commandstaff&do=roster" class="button">All</a>';
			
			foreach($squadArray as $squad => $squad_value)
			{
				echo '<a href="index.php?action=commandstaff&do=roster&squad='.$i.'" class="button">' . $squad . '</a> ';
				$i++;
			}
			
			foreach($clubArray as $club => $club_value)
			{
				echo '<a href="index.php?action=commandstaff&do=roster&squad='.$i.'" class="button">' . $club . '</a> ';
				$i++;
			}
			
			echo '<br /><hr />';
			
			// Set up
			$queryAdd = "";
			
			// Check if squad is requested
			if(isset($_GET['squad']))
			{
				$queryAdd = "WHERE squad = '".cleanInput($_GET['squad'])."'";
			}
			
			// Query database
			$query = "SELECT * FROM troopers ".$queryAdd." ORDER BY name";
			
			// Query count
			$i = 0;
			
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					// If first data
					if($i == 0)
					{
						echo '
						<div style="overflow-x: auto;">
						<table>
							<tr>
								<th>Name</th>	<th>TKID</th>
							</tr>';
					}
					
					echo '
					<tr name="row_'.$db->id.'">
						<td>
							<a href="index.php?profile='.$db->id.'">'.$db->name.'</a>
						</td>
						
						<td>
							'.readTKNumber($db->tkid, $db->squad).'
						</td>
					</tr>
					';
					
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
			}
			else
			{
				echo '
				<p style="text-align: center;">Nothing to display for this squad / club.</p>';
			}
		}
		
		/**************************** Trooper Check *********************************/
		
		if(isset($_GET['do']) && $_GET['do'] == "troopercheck" && hasPermission(1))
		{
			echo '
			<h3>Trooper Check</h3>
			
			<p>
				<i>The following troopers do not have a documented troop from the past year. Retired members do not show on this list.</i>
			</p>';
			
			// Squad count
			$i = 1;
			
			echo '
			<a href="index.php?action=commandstaff&do=troopercheck" class="button">All</a>';
			
			foreach($squadArray as $squad => $squad_value)
			{
				echo '<a href="index.php?action=commandstaff&do=troopercheck&squad='.$i.'" class="button">' . $squad . '</a> ';
				$i++;
			}
			
			foreach($clubArray as $club => $club_value)
			{
				echo '<a href="index.php?action=commandstaff&do=troopercheck&squad='.$i.'" class="button">' . $club . '</a> ';
				$i++;
			}
			
			echo '<br /><hr />';
			
			// Set up
			$queryAdd = "";
			
			// Check if squad is requested
			if(isset($_GET['squad']))
			{
				$queryAdd = "troopers.squad = '".cleanInput($_GET['squad'])."' AND";
			}
			
			// Query database
			$query = "SELECT * FROM troopers WHERE ".$queryAdd." troopers.permissions != 4 AND troopers.id NOT IN (SELECT event_sign_up.trooperid FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.status = '3' AND events.dateEnd > NOW() - INTERVAL 1 YEAR) ORDER BY troopers.name";
			
			// Query count
			$i = 0;
			
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					// If first data
					if($i == 0)
					{
						echo '
						<form action="process.php?do=troopercheck" method="POST" name="trooperCheckForm" id="trooperCheckForm">
						<div style="overflow-x: auto;">
						<table>
							<tr>
								<th>Selection</th>	<th>Name</th>	<th>TKID</th>	<th>Tracker Status</th>
							</tr>';
					}
					
					echo '
					<tr name="row_'.$db->id.'">
						<td>
							<input type="checkbox" name="trooper[]" value="'.$db->id.'" />
						</td>
						
						<td>
							<a href="index.php?profile='.$db->id.'">'.$db->name.'</a>
						</td>
						
						<td>
							'.readTKNumber($db->tkid, $db->squad).'
						</td>
						
						<td name="permission">
							'.getPermissionName($db->permissions).'
						</td>
					</tr>
					';
					
					// Increment
					$i++;
				}
			}
			
			// If data exists
			if($i > 0)
			{
				echo '
				</table>
				</div>
				
				<input type="submit" name="submitTroopCheckReserve" id="submitTroopCheckReserve" value="Change to Reserve" />
				<input type="submit" name="submitTroopCheckRetired" id="submitTroopCheckRetired" value="Change to Retired" />
				
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
				// Get data
				$query = "SELECT * FROM notifications WHERE message NOT LIKE '%now has%' ORDER BY id DESC ";
				
				// Get total results - query
				$sqlPage = "SELECT COUNT(id) AS total FROM notifications WHERE message NOT LIKE '%now has%'";
			}
			else if(isset($_GET['s']) && $_GET['s'] == "troopers")
			{
				// Get data
				$query = "SELECT * FROM notifications WHERE message LIKE '%now has%' ORDER BY id DESC ";
				
				// Get total results - query
				$sqlPage = "SELECT COUNT(id) AS total FROM notifications WHERE message LIKE '%now has%'";
			}
			else
			{
				// Get data
				$query = "SELECT * FROM notifications ORDER BY id DESC ";
				
				// Get total results - query
				$sqlPage = "SELECT COUNT(id) AS total FROM notifications";
			}
			
			// Page SQL
			$resultPage = $conn->query($sqlPage);
			$rowPage = $resultPage->fetch_assoc();
			
			// Set total pages
			$total_pages = ceil($rowPage["total"] / $results);
			
			// If page set
			if(isset($_GET['page']))
			{
				// Get page
				$page = cleanInput($_GET['page']);
				
				// Start from
				$startFrom = ($page - 1) * $results;
			}
			else
			{
				// Default page
				$page = 1;
				
				// Start from - default
				$startFrom = ($page - 1) * $results;
			}
			
			// Add to query
			$query .= "LIMIT ".$startFrom.", ".$results."";
			
			// Set notification count
			$i = 0;
			
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					if($i == 0)
					{
						echo '<ul>';
					}
					
					// Format Date
					$dateF = date('m-d-Y H:i:s', strtotime($db->datetime));

					// Set JSON value
					$json = "";

					// Check JSON value if blank
					if($db->json == "")
					{
						// Set up
						$add = "";

						// Does not contain now has
						if(strpos($db->message, "now has") === false)
						{
							$add = "Staff ";
						}

						// Nothing to show
						$json = '<a href="index.php?profile='.$db->trooperid.'" target="_blank" class="button">View '.$add.'Profile</a>';
					}
					else
					{
						// Show value
						$json = '
						<textarea width="100%" rows="5" disabled>' . $db->json . '</textarea>
						<br />
						<a href="index.php?profile='.$db->trooperid.'" target="_blank" class="button">View Staff Profile</a>';

						// Decode JSON data - Avoid null results
						$data = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $db->json));

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

								// Troop ID
								if($key == "troopid")
								{
									$json .= ' <a href="index.php?event='.$value.'" target="_blank" class="button">View Troop</a>';
								}
								// Troop ID #2
								else if($key == "id" && ($db->type == 13 || $db->type == 14 || $db->type == 19 || $db->type == 17) && !isset($data->troopid))
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
						echo '
						<a href="index.php?action=commandstaff&do=notifications&page='.$j.'">'.$j.'</a>';
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
		if(isset($_GET['do']) && $_GET['do'] == "managecostumes" && hasPermission(1))
		{
			echo '
			<h3>Add Costume</h3>
			
			<form action="process.php?do=managecostumes" method="POST" name="addCostumeForm" id="addCostumeForm">
			
				<b>Costume Name:</b></br />
				<input type="text" name="costumeName" id="costumeName" />
				
				<b>Costume Era:</b></br />
				<select name="costumeEra" id="costumeEra">
					<option value="0">Prequel</option>
					<option value="1" SELECTED>Original</option>
					<option value="2">Sequel</option>
					<option value="3">Expanded</option>
					<option value="4">All</option>
				</select>
				
				<b>Costume Club:</b></br />
				<select name="costumeClub" id="costumeClub">
					<option value="0" SELECTED>501st Legion</option>
					<option value="1">Rebel Legion</option>
					<option value="2">Mando Mercs</option>
					<option value="3">Droid Builders</option>
					<option value="4">Other</option>
					<option value="5">Dual (501st + Rebel)</option>
				</select>
				
				<input type="submit" name="addCostumeButton" id="addCostumeButton" value="Add Costume" />
				
			</form>
				
			<br />
			<hr />
			<br />
			
			<div id="costumearea" name="costumearea">
			
			<h3>Edit Costume</h3>';

			// Get data
			$query = "SELECT * FROM costumes ORDER BY costume";

			$i = 0;
			if ($result = mysqli_query($conn, $query))
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
					<option value="'.$db->id.'" costumeName="'.$db->costume.'" costumeID="'.$db->id.'" costumeEra="'.$db->era.'" costumeClub="'.$db->club.'">'.$db->costume.'</option>';


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

				<b>Costume Name:</b></br />
				<input type="text" name="costumeNameEdit" id="costumeNameEdit" />

				<br />
				
				<b>Costume Era:</b></br />
				<select name="costumeEraEdit" id="costumeEraEdit">
					<option value="0">Prequel</option>
					<option value="1" SELECTED>Original</option>
					<option value="2">Sequel</option>
					<option value="3">Expanded</option>
					<option value="4">All</option>
				</select>
				
				<br />

				<b>Costume Club:</b></br />
				<select name="costumeClubEdit" id="costumeClubEdit">
					<option value="0" SELECTED>501st Legion</option>
					<option value="1">Rebel Legion</option>
					<option value="2">Mando Mercs</option>
					<option value="3">Droid Builders</option>
					<option value="4">Other</option>
					<option value="5">Dual (501st + Rebel)</option>
				</select>

				<br />

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
			$query = "SELECT * FROM costumes ORDER BY costume";

			$i = 0;
			if ($result = mysqli_query($conn, $query))
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

					echo '<option value="'.$db->id.'">'.$db->costume.'</option>';

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
		if(isset($_GET['do']) && $_GET['do'] == "assignawards")
		{
			echo '<h3>Assign Awards</h3>
			
			<div name="assignarea" id="assignarea">';

			// Get data
			$query = "SELECT * FROM troopers WHERE approved = 1 ORDER BY name";

			// Amount of users
			$i = 0;
			$getId = 0;

			if ($result = mysqli_query($conn, $query))
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

					echo '<option value="'.$db->id.'">'.$db->name.' - '.readTKNumber($db->tkid, $db->squad).'</option>';

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
				$query2 = "SELECT * FROM awards ORDER BY title";

				// Amount of awards
				$j = 0;

				if ($result2 = mysqli_query($conn, $query2))
				{
					while ($db = mysqli_fetch_object($result2))
					{
						// Formatting
						if($j == 0)
						{
							$getId = $db->id;

							echo '<select id="awardIDAssign" name="awardIDAssign">';
						}

						echo '<option value="'.$db->id.'">'.$db->title.'</option>';

						// Increment $j
						$j++;
					}
				}

				// If awards exist
				if($j > 0)
				{
					echo '
					</select>

					<input type="submit" name="award" id="award" value="Assign!" />';
				}
				else
				{
					echo 'No awards to display.';
				}
			}

			echo '</form></div>';

			echo '<br /><hr /><br /><h3>Create Award</h3>

			<form action="process.php?do=assignawards" method="POST" name="addAward" id="addAward">
				<b>Award Name:</b></br />
				<input type="text" name="awardName" id="awardName" />
				<b>Award Image (example.png):</b></br />
				<input type="text" name="awardImage" id="awardImage" />
				<input type="submit" name="submitAwardAdd" id="submitAwardAdd" value="Add Award" />
			</form>';

			echo '
			<div id="awardarea">
			<br /><hr /><br />
			<h3>Edit Award</h3>';

			// Get data
			$query = "SELECT * FROM awards ORDER BY title";

			$i = 0;
			if ($result = mysqli_query($conn, $query))
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

					echo '<option value="'.$db->id.'" awardTitle="'.$db->title.'" awardID="'.$db->id.'" awardImage="'.$db->icon.'">'.$db->title.'</option>';

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

				<b>Award Title:</b><br />
				<input type="text" name="editAwardTitle" id="editAwardTitle" />

				<br /><b>Award Image:</b><br />
				<input type="text" name="editAwardImage" id="editAwardImage" />

				<br />

				<input type="submit" name="submitEditAward" id="submitEditAward" value="Edit Award" />

				</div>
				</form>';
			}

			echo '<br /><hr /><br /><h3>Delete Award</h3>';

			// Get data
			$query = "SELECT * FROM awards ORDER BY title";

			$i = 0;
			if ($result = mysqli_query($conn, $query))
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

					echo '<option value="'.$db->id.'">'.$db->title.'</option>';

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
				$query = "(SELECT * FROM events WHERE id = ".$eid." LIMIT 1) UNION (SELECT * FROM events ORDER BY dateStart DESC LIMIT 150)";
			}
			else
			{
				// If eid is not set
				$query = "SELECT * FROM events ORDER BY dateStart DESC LIMIT 150";
			}

			// Amount of events
			$i = 0;

			if ($result = mysqli_query($conn, $query))
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

					echo '<option value="'.$db->id.'" '.echoSelect($db->id, $eid).'>'.$add.''.$db->name.'</option>';

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
				<input type="submit" name="submitCancel" id="submitCancel" value="Mark Canceled" /> <input type="submit" name="submitFinish" id="submitFinish" value="Mark Finished" /> <input type="submit" name="submitOpen" id="submitOpen" value="Mark Open" /> <input type="submit" name="submitLock" id="submitLock" value="Mark Locked" /> <input type="submit" name="submitEdit" id="submitEdit" value="Edit" /> <input type="submit" name="submitRoster" id="submitRoster" value="Roster" /> <input type="submit" name="submitCharity" id="submitCharity" value="Set Charity Amount" /> <input type="submit" name="viewEvent" id="viewEvent" value="View Event" />

				</form>
				
				<div name="charityAmount" id="charityAmount" style="display:none;">
					<br />
					<form action="process.php?do=editcharity" id="editcharityForm" name="editcharityForm" method="POST">
						Charity Amount Raised: $<input type="number" name="charityAmountField" id="charityAmountField" />
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
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'B\')" class="button">Bold</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'I\')" class="button">Italic</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'U\')" class="button">Underline</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'Q\')" class="button">Quote</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'COLOR\')" class="button">Color</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'SIZE\')" class="button">Size</a>
						<a href="javascript:void(0);" onclick="javascript:bbcoder(\'URL\')" class="button">URL</a>
						<textarea rows="10" cols="50" name="comments" id="comments"></textarea>

						<p>Label:</p>
						<select name="label" id="label">
							<option value="0">Regular</option>
							<option value="10">Armor Party</option>
							<option value="1">Charity</option>
							<option value="2">PR</option>
							<option value="3">Disney</option>
							<option value="4">Convention</option>
							<option value="9">Hospital</option>
							<option value="5">Wedding</option>
							<option value="6">Birthday Party</option>
							<option value="7">Virtual Troop</option>
							<option value="8">Other</option>
						</select>

						<p>Is this a manual selection event?</p>
						<select name="limitedEvent" id="limitedEvent">
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>
						
						<p>
							<a href="#/" class="button" id="limitChange">Change Limits</a>
						</p>
						
						<div id="limitChangeArea" style="display: none;">

						<p>Do you wish to limit the era of the costume?</p>
						<select name="era" id="era">
							<option value="0">Prequel</option>
							<option value="1" SELECTED>Original</option>
							<option value="2">Sequel</option>
							<option value="3">Expanded</option>
							<option value="4" SELECTED>All</option>
						</select>

						<p>Limit of 501st Troopers:</p>
						<input type="number" name="limit501st" value="500" id="limit501st" />

						<p>Limit of Rebels:</p>
						<input type="number" name="limitRebels" value="500" id="limitRebels" />

						<p>Limit of Mandos:</p>
						<input type="number" name="limitMando" value="500" id="limitMando" />

						<p>Limit of Droid Builders:</p>
						<input type="number" name="limitDroid" value="500" id="limitDroid" />
						
						<p>Limit of Others:</p>
						<input type="number" name="limitOther" value="500" id="limitOther" />

						<p>
							<a href="#/" class="button" id="resetDefaultCount">Reset Default</a>
						</p>
						
						</div>

						<p>Referred By:</p>
						<input type="text" name="referred" id="referred" />

						<input type="submit" name="submitEventEdit" id="submitEventEdit" value="Edit!" />
					</form>
				</div>';
			}
		}

		// Approve troopers
		if(isset($_GET['do']) && $_GET['do'] == "approvetroopers")
		{
			echo '
			<h3>Approve Trooper Requests</h3>';

			// Get data
			$query = "SELECT * FROM troopers WHERE approved = 0 ORDER BY datecreated";

			// Amount of users
			$i = 0;

			if ($result = mysqli_query($conn, $query))
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

					echo '<option value="'.$db->id.'">'.$db->name.'</option>';

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

				<input type="submit" name="submitApproveUser" id="submitApproveUser" value="Approve" /> <input type="submit" name="submitDenyUser" id="submitDenyUser" value="Deny" />
				</form>

				<div style="overflow-x: auto;">
				<table border="1" id="userListTable" name="userListTable">
				<tr>
					<th>Name</th>	<th>E-mail</th>	<th>Forum ID (FG)</th>	<th>Forum ID (RL)</th>	<th>Mando CAT</th>	<th>SG #</th>	<th>Phone</th>	<th>Squad</th>	<th>TKID</th>
				</tr>
					<tr id="userList" name="userList">
						<td id="nameTable"></td>	<td id="emailTable"></td> <td id="forumTable"></td> <td id="forumRebelTable"></td> <td id="mandoTable"></td>	<td id="sgTable"></td>	<td id="phoneTable"></td>	<td id="squadTable"></td>	<td id="tkTable"></td>
					</tr>
				</table>
				</div>';
			}
		}

		// Manage users
		if(isset($_GET['do']) && $_GET['do'] == "managetroopers" && hasPermission(1))
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
			$query = "SELECT * FROM troopers ORDER BY name";

			// Amount of users
			$i = 0;

			if ($result = mysqli_query($conn, $query))
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

					echo '<option value="'.$db->id.'" '.echoSelect($db->id, $uid).'>'.$db->name.' - '.readTKNumber($db->tkid, $db->squad).'</option>';

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

				<input type="submit" name="submitDeleteUser" id="submitDeleteUser" value="Delete" /> <input type="submit" name="submitResetPasswordUser" id="submitResetPasswordUser" value="Reset Password" /> <input type="submit" name="submitViewProfile" id="submitViewProfile" value="View Profile" /> <input type="submit" name="submitEditUser" id="submitEditUser" value="Edit" />
				</form>

				<div name="editUserInfo" id="editUserInfo" style="display:none;">
					<form action="process.php?do=managetroopers" id="editUserForm" name="editUserForm" method="POST">
						<input type="hidden" name="userIDE" id="userIDE" value="" />

						<p>Name of the user:</p>
						<input type="text" name="user" id="user" />

						<p>E-mail:</p>
						<input type="text" name="email" id="email" />

						<p>Phone (Optional):</p>
						<input type="text" name="phone" id="phone" />

						<p>Squad/Club:</p>
						<select name="squad" id="squad">
							'.squadSelectList().'
						</select>

						<p>Permissions:</p>
						<select name="permissions" id="permissions">
							<option value="0">Regular Member</option>
							<option value="3">Reserve Member</option>
							<option value="4">Retired Member</option>
							<option value="5">Handler</option>
							<option value="2">Moderator</option>
							<option value="1">Super Admin</option>
						</select>

						<p>TKID:</p>
						<input type="text" name="tkid" id="tkid" />
						
						<p>Forum ID ('.garrison.'):</p>
						<input type="text" name="forumid" id="forumid" />
						
						<p>Forum ID (Rebel Legion):</p>
						<input type="text" name="rebelforum" id="rebelforum" />
						
						<p>Mando Mercs CAT #:</p>
						<input type="text" name="mandoid" id="mandoid" />

						<p>Saber Guild SG #:</p>
						<input type="text" name="sgid" id="sgid" />
						
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

		// Create a user
		if(isset($_GET['do']) && $_GET['do'] == "createuser" && hasPermission(1))
		{
			// Display create user form
			echo '
			<h3>Create a user</h3>

			<form action="process.php?do=createuser" id="createUserForm" name="createUserForm" method="POST">
				<p>Name:</p>
				<input type="text" name="name" id="name" />

				<p>E-mail:</p>
				<input type="text" name="email" id="email" />

				<p>Phone (Optional):</p>
				<input type="text" name="phone" id="phone" />
				
				<p>FLG Forum Username:</p>
				<input type="text" name="forumid" id="forumid" />
				
				<p>Rebel Legion Forum Username (if applicable):</p>
				<input type="text" name="rebelforum" id="rebelforum" />
				
				<p>Mando Mercs CAT # (if applicable):</p>
				<input type="text" name="mandoid" id="mandoid" />

				<p>Saber Guild SG # (if applicable):</p>
				<input type="text" name="sgid" id="sgid" />

				<p>Squad/Club:</p>
				<select name="squad" id="squad">
					'.squadSelectList().'
				</select>

				<p>Permissions:</p>
				<select name="permissions" id="permissions">
					<option value="0">Regular Member</option>
					<option value="3">Reserve Member</option>
					<option value="4">Retired Member</option>
					<option value="5">Handler</option>
					<option value="2">Moderator</option>
					<option value="1">Super Admin</option>
				</select>

				<p>TKID:</p>
				<input type="text" name="tkid" id="tkid" />
				
				<p>Password:</p>
				<input type="text" name="password" id="password" />

				<br /><br />

				<input type="submit" name="submitUser" value="Create!" />
			</form>';
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
			$comments = "";
			$location = "";
			$label = "";
			$postComment = "";
			$notes = "";
			$limitedEvent = "";
			$limitTo = "";
			$limitRebels = "";
			$limit501st = "";
			$limitMando = "";
			$limitDroid = "";
			$limitOther = "";
			$closed = "";
			$moneyRaised = "";
			$squad = "";
			
			// If edid set - lets load events
			if(isset($_GET['eid']) && $_GET['eid'] >= 0)
			{
				// Get data for copy troop
				$eid = cleanInput($_GET['eid']);
				
				$query = "SELECT * FROM events WHERE id = '".$eid."' LIMIT 1";
				
				// Event found
				$i = 0;

				if ($result = mysqli_query($conn, $query))
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
						$comments = $db->comments;
						$location = $db->location;
						$label = $db->label;
						$postComment = $db->postComment;
						$notes = $db->notes;
						$limitedEvent = $db->limitedEvent;
						$limitTo = $db->limitTo;
						$limitRebels = $db->limitRebels;
						$limit501st = $db->limit501st;
						$limitMando = $db->limitMando;
						$limitDroid = $db->limitDroid;
						$limitOther = $db->limitOther;
						$closed = $db->closed;
						$moneyRaised = $db->moneyRaised;
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
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'B\')" class="button">Bold</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'I\')" class="button">Italic</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'U\')" class="button">Underline</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'Q\')" class="button">Quote</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'COLOR\')" class="button">Color</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'SIZE\')" class="button">Size</a>
				<a href="javascript:void(0);" onclick="javascript:bbcoder(\'URL\')" class="button">URL</a>
				<textarea rows="10" cols="50" name="comments" id="comments">'.copyEvent($eid, $comments).'</textarea>

				<p>Label:</p>
				<select name="label" id="label">
					<option value="null" '.copyEventSelect($eid, $label, "null").'>Please choose an option...</option>
					<option value="0" '.copyEventSelect($eid, $label, 0).'>Regular</option>
					<option value="10" '.copyEventSelect($eid, $label, 10).'>Armor Party</option>
					<option value="1" '.copyEventSelect($eid, $label, 1).'>Charity</option>
					<option value="2" '.copyEventSelect($eid, $label, 2).'>PR</option>
					<option value="3" '.copyEventSelect($eid, $label, 3).'>Disney</option>
					<option value="4" '.copyEventSelect($eid, $label, 4).'>Convention</option>
					<option value="9" '.copyEventSelect($eid, $label, 9).'>Hospital</option>
					<option value="5" '.copyEventSelect($eid, $label, 5).'>Wedding</option>
					<option value="6" '.copyEventSelect($eid, $label, 6).'>Birthday Party</option>
					<option value="7" '.copyEventSelect($eid, $label, 7).'>Virtual Troop</option>
					<option value="8" '.copyEventSelect($eid, $label, 8).'>Other</option>
				</select>

				<p>Is this a manual selection event?</p>
				<select name="limitedEvent" id="limitedEvent">
					<option value="null" '.copyEventSelect($eid, $limitedEvent, "null").'>Please choose an option...</option>
					<option value="1" '.copyEventSelect($eid, $limitedEvent, 1).'>Yes</option>
					<option value="0" '.copyEventSelect($eid, $limitedEvent, 0).'>No</option>
				</select>
				
				<p>
					<a href="#/" class="button" id="limitChange">Change Limits</a>
				</p>

				<div id="limitChangeArea" style="display: none;">

				<p>Do you wish to limit the era of the costume?</p>
				<select name="era" id="era">
					<option value="0" '.copyEventSelect($eid, $limitTo, 0).'>Prequel</option>
					<option value="1" '.copyEventSelect($eid, $limitTo, 1).'>Original</option>
					<option value="2" '.copyEventSelect($eid, $limitTo, 2).'>Sequel</option>
					<option value="3" '.copyEventSelect($eid, $limitTo, 3).'>Expanded</option>
					<option value="4" '.copyEventSelect($eid, $limitTo, 4, 4).'>All</option>
				</select>
				
				<p>Limit of 501st Troopers:</p>
				<input type="number" name="limit501st" value="'.copyEvent($eid, $limit501st, 500).'" id="limit501st" />

				<p>Limit of Rebels:</p>
				<input type="number" name="limitRebels" value="'.copyEvent($eid, $limitRebels, 500).'" id="limitRebels" />

				<p>Limit of Mandos:</p>
				<input type="number" name="limitMando" value="'.copyEvent($eid, $limitMando, 500).'" id="limitMando" />

				<p>Limit of Droid Builders:</p>
				<input type="number" name="limitDroid" value="'.copyEvent($eid, $limitDroid, 500).'" id="limitDroid" />
				
				<p>Limit of Others:</p>
				<input type="number" name="limitOther" value="'.copyEvent($eid, $limitOther, 500).'" id="limitOther" />

				<p>
					<a href="#/" class="button" id="resetDefaultCount">Reset Default</a>
				</p>
				
				</div>

				<p>Referred By:</p>
				<input type="text" name="referred" id="referred" value="'.copyEvent($eid, $referred).'" />

				<input type="submit" name="submitEvent" value="Create!" />
			</form>';
		}
	}
	else
	{
		// If the user is not an admin or logged in
		echo 'You are not command staff. Access denied!';
	}
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

	<h3>Troop Tracker Manual</h3>
	<p>
		<a href="https://github.com/MattDrennan/501-troop-tracker/blob/master/manual/troop_tracker_manual.pdf" target="_blank">Click here to view PDF manual.</a>
	</p>

	<h3>I am missing troop data / My troop data is incorrect</h3>
	<p>
		Please refer to your squad leader to get this corrected.
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
	<p>If you have read and reviewed all the material above and are still experiencing issues, or have noticed a bug on the website, please <a href="mailto: drennanmattheww@gmail.com">send an e-mail here</a>. <b>If you ask a question that is listed above or is answered on a video on this page, you will receive an e-mail advising to review the FAQ page.</b></p>';
}

/**************************** Edit Photo *********************************/

if(isset($_GET['action']) && $_GET['action'] == "editphoto")
{
	// Get data
	$query = "SELECT * FROM uploads WHERE id = '".cleanInput($_GET['id'])."'";
	$i = 0;
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// If admin or trooper that uploaded it
			if(isAdmin() || $db->trooperid == $_SESSION['id'])
			{
				echo '
				<h3>Edit Photo: '.$db->filename.'</h3>
				
				<p>
					<img src="images/uploads/'.$db->filename.'" width="200px" height="200px" />
				</p>';

				// If is admin
				if(isAdmin())
				{
					echo '
					<p>
						<b>Uploaded by:</b> <a href="index.php?profile='.$db->trooperid.'">'.getName($db->trooperid).' - '.getTKNumber($db->trooperid, true).'</a>
					</p>';
				
					// Check if admin photo
					if($db->admin == 0)
					{
						echo '
						<p>
							<a href="#/" photoid="'.$db->id.'" class="button" name="adminphoto">Make Admin Photo</a>
						</p>';
					}
					else
					{
						echo '
						<p>
							<a href="#/" photoid="'.$db->id.'" class="button" name="adminphoto">Make Regular Photo</a>
						</p>';
					}
				}
				
				echo '
				<p>
					<a href="#/" photoid="'.$db->id.'" troopid="'.$db->troopid.'" class="button" name="deletephoto">Delete Photo</a>
				</p>
				
				<p>
					<a href="index.php?event='.$db->troopid.'" class="button">View Event</a>
				</p>';
			}
		}
	}
}

// Show the login page
if(isset($_GET['action']) && $_GET['action'] == "login")
{
	echo '
	<h2 class="tm-section-header">Login</h2>';

	// Display submission for register account, otherwise show the form
	if(isset($_POST['loginWithTK']))
	{
		// Get TKID
		$tkid = cleanInput($_POST['tkid']);

		// Get squad from TKID
		$squad = loginWithTKID($tkid);

		// Get data
		$query = "SELECT * FROM troopers WHERE (tkid = '".removeLetters($tkid)."' AND squad = '".$squad."') OR forum_id = '".cleanInput($_POST['tkid'])."' OR rebelforum = '".cleanInput($_POST['tkid'])."'";
		$i = 0;
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				$i++;
				
				// Check if old MD5 password
				if(cleanInput(md5($_POST['password'])) == $db->password)
				{
					// Update MySQL password
					$conn->query("UPDATE troopers SET password = '".password_hash(cleanInput($_POST['password']), PASSWORD_DEFAULT)."' WHERE id = '".$db->id."'");
				}

				// Check credentials
				if(password_verify(cleanInput($_POST['password']), $db->password))
				{
					if($db->approved != 0)
					{
						// Set session
						$_SESSION['id'] = $db->id;
						$_SESSION['tkid'] = $db->tkid;
						
						// Set log in cookie, if set to keep logged in
						if(isset($_POST['keepLog']) && $_POST['keepLog'] == 1)
						{
							// Set cookies
							setcookie("TroopTrackerUsername", $db->forum_id, time() + (10 * 365 * 24 * 60 * 60));
							setcookie("TroopTrackerPassword", cleanInput($_POST['password']), time() + (10 * 365 * 24 * 60 * 60));
						}

						// Cookie set
						if(isset($_COOKIE["TroopTrackerLastEvent"]))
						{
							echo '
							<meta http-equiv="refresh" content="5; URL=index.php?event='.cleanInput($_COOKIE["TroopTrackerLastEvent"]).'" />
							You have now logged in! <a href="index.php?event='.cleanInput($_COOKIE["TroopTrackerLastEvent"]).'">Click here to view the event</a> or you will be redirected shortly.';
							
							// Clear cookie
							setcookie("TroopTrackerLastEvent", "", time() - 3600);
						}
						else
						{
							// Cookie not set
							echo 'You have now logged in! <a href="index.php">Click here to go home.</a>';
						}
					}
					else
					{
						echo 'Your access has not been approved yet.';
					}
				}
				else
				{
					echo '
					<p>Incorrect username or password. <a href="index.php?action=login">Try again?</a></p>
					
					<p><a href="index.php?action=forgotpassword">Need to recover your password? Click here.</a>';
				}
			}
		}

		// An account does not exist
		if($i == 0)
		{
			echo '
			<p>Account not found. <a href="index.php?action=login">Try again?</a> - <span style="color: red;">BEFORE E-MAILING COMMAND STAFF FOR HELP, READ BELOW:</span></p>
			
			<p><b>IF YOU ARE USING YOUR TKID, NUMBERS ONLY! DO NOT USE A PREFIX</b></p>
			
			<p><b>YOU CAN USE YOUR FLORIDA GARRISON BOARDS OR REBEL LEGION FORUM USERNAME AS YOUR TKID, IF YOU DON\'T KNOW OR FORGOT YOUR TKID</b></p>
			
			<p><b>IF YOU HAD TROOPS ON THE OLD TROOP TRACKER, USE <a href="index.php?action=setup">ACCOUNT SETUP</a>. IF YOU ARE NEW, PLEASE <a href="index.php?action=requestaccess">REQUEST ACCESS</a>.</b></p>
			
			<p><b>IF THE ABOVE DID NOT SOLVE YOUR ISSUES, WHEN SENDING AN E-MAIL FOR HELP, INCLUDE (TKID, FL 501ST BOARDS NAME, REBEL LEGION FORUM NAME (IF APPLICABLE), AND AS MUCH DETAIL AS POSSIBLE. PLEASE DO NOT SEND AN E-MAIL ASKING FOR HELP AND NOTHING FURTHER.</b></p>';
		}
	}
	else
	{
		echo '
		<form action="index.php?action=login" method="POST" name="loginForm" id="loginForm">
			<p>TKID / Forum Name:</p>
			<input type="text" name="tkid" id="tkid" />

			<p>Password:</p>
			<input type="password" name="password" id="password" />
			
			<br /><br />
			
			<input type="checkbox" name="keepLog" value="1" /> Keep me logged in

			<br /><br />

			<input type="submit" value="Login!" name="loginWithTK" />
		</form>

		<p><a href="index.php?action=forgotpassword" class="button">Forgot Your Password</a><p>
		
		<p>
			<small>
				<b>Remember:</b><br />If you are in a club other than the 501st, enter the first letter of your club, and then your TKID, or use your forum username.
				<br />
				<b>Example:</b><br />Rebel Legion: R1234
			</small>
		</p>';
	}
}

// Show the setup page
if(isset($_GET['action']) && $_GET['action'] == "setup" && !isSignUpClosed())
{
	echo '
	<h2 class="tm-section-header">Set Up Your Account</h2>';

	// Display submission for register account, otherwise show the form
	if(isset($_POST['registerAccount']))
	{
		// Does this TK ID exist?
		if(doesTKExist(cleanInput($_POST['tkid']), cleanInput($_POST['squad'])))
		{
			// Is this TK ID registered?
			if(!isTKRegistered(cleanInput($_POST['tkid']), cleanInput($_POST['squad'])))
			{
				if(strlen(cleanInput($_POST['password'])) >= 6)
				{
					if(cleanInput($_POST['password']) == cleanInput($_POST['password2']))
					{
						// Verify emails
						include("script/lib/EmailAddressValidator.php");

						$validator = new EmailAddressValidator;
						if ($validator->check_email_address(cleanInput($_POST['email'])))
						{
							// If 501st
							if(cleanInput($_POST['squad']) <= count($squadArray))
							{
								// Query the database
								$conn->query("UPDATE troopers SET email = '".cleanInput($_POST['email'])."', password = '".password_hash(cleanInput($_POST['password']), PASSWORD_DEFAULT)."', squad = '".cleanInput($_POST['squad'])."' WHERE tkid = '".cleanInput($_POST['tkid'])."'");
								
								// Display output
								echo 'Your account has been registered. Please <a href="index.php?action=login">login</a>.';
							}
							else
							{
								$tkIDTaken = $conn->query("SELECT id FROM troopers WHERE tkid = '".cleanInput($_POST['tkid2'])."' AND squad = '".cleanInput($_POST['squad'])."'");
								
								if($tkIDTaken->num_rows == 0)
								{
									if(cleanInput($_POST['tkid2']) != "")
									{
										// If a club
										// Query the database
										$conn->query("UPDATE troopers SET email = '".cleanInput($_POST['email'])."', tkid = '".cleanInput($_POST['tkid2'])."', password = '".password_hash(cleanInput($_POST['password']), PASSWORD_DEFAULT)."', squad = '".cleanInput($_POST['squad'])."' WHERE rebelforum = '".cleanInput($_POST['tkid'])."'");
										
										// Display output
										echo 'Your account has been registered. Please <a href="index.php?action=login">login</a>.';
									}
									else
									{
										echo 'Please enter an ID.';
									}
								}
								else
								{
									echo 'The ID you have chosen is already in use. Please pick another.';
								}
							}
						}
						else
						{
						echo 'Please enter a valid e-mail address.';
						}
					}
					else
					{
						echo 'Your passwords do not match.';
					}
				}
				else
				{
					echo 'Your password must be at least six (6) characters long.';
				}
			}
			else
			{
				echo 'This TK ID or Rebel Legion user is already registred! Please contact an admin if this issue persists.';
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
		<p style="text-align: center;">Were you already using the old trooper tracker? Set up your account by using the form below.</p>
		
		<form method="POST" action="index.php?action=setup" name="registerForm" id="registerForm">
			<p>What is your TKID (numbers only) or Rebel Forum username (if Rebel Legion member only):</p>
			<input type="text" name="tkid" id="tkid" />

			<p>What is your e-mail:</p>
			<input type="text" name="email" id="email" />

			<p>What do you want your password to be?</p>
			<input type="password" name="password" id="password" />

			<p>Please re-enter your password:</p>
			<input type="password" name="password2" id="password2" />
			
			<p>Squad/Club</p>
			
			<select name="squad" id="squad">
				'.squadSelectList(true, "", 0, 0, true).'
			</select>

			<div style="display: none;" id="rebelid">
				<p>Choose an ID number (1 to 11 digits):</p>
				<input type="text" maxlength="11" name="tkid2" id="tkid2" />
			</div>
			
			<br /><br />

			<input type="submit" value="Set Up!" name="registerAccount" />
		</form>';
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
	<div style="margin-top: 25px;">
		You have logged out! <a href="index.php">Click here to go home.</a>
	</div>';
}

// Show the forgot your password page
if(isset($_GET['action']) && $_GET['action'] == "forgotpassword")
{
	// Display submission for forgot your password, otherwise show the form
	if(isset($_POST['forgotPasswordSend']))
	{
		// Does data exist
		$i = 0;

		// Get data
		$query = "SELECT * FROM troopers WHERE (tkid = '".cleanInput($_POST['tkid'])."' OR forum_id = '".cleanInput($_POST['tkid'])."' OR rebelforum = '".cleanInput($_POST['tkid'])."') AND email != '' AND password != ''";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Generate a new password
				$newPassword = rand(100000, 900000);
				
				// Query the database
				$conn->query("UPDATE troopers SET password = '".password_hash($newPassword, PASSWORD_DEFAULT)."' WHERE id = '".$db->id."'");
				
				// Send e-mail
				sendEmail($db->email, readTKNumber($db->tkid, $db->squad), "FL 501st Troop Software Password Reset", "Your new password is:\n\n" . $newPassword . "\n\nPlease change your password as soon as possible.");
				
				echo '
				<p>
					An e-mail has been sent to your inbox with your new password. Be sure to check your spam folder. If an e-mail does not appear in your inbox within thirty minutes, please contact command staff for assistance.
				</p>';

				// Increment data exist
				$i++;
			}
		}

		if($i == 0)
		{
			echo '
			<p>
				Account not found.
			</p>
			
			<p>
				If you were on the old troop tracker, please do <a href="index.php?action=setup">account setup</a>. If you never trooped before, please do <a href="index.php?action=requestaccess">request access.</a> If you continue to have issues, please contact command staff for assistance.
			</p>';
		}
	}
	else
	{
		echo '
		<h2 class="tm-section-header">Forgot Your Password</h2>

		<form action="index.php?action=forgotpassword" method="POST" name="forgotPasswordForm" id="forgotPasswordForm">
			<p>TKID / Forum Name:</p>
			<input type="text" name="tkid" id="tkid" />

			<br /><br />

			<input type="submit" value="Retrieve Password!" name="forgotPasswordSend" />
		</form>';	
	}
}

if(isset($_POST['submitCancelTroop']))
{
	// Query the database
	$conn->query("UPDATE event_sign_up SET status = '4' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($_POST['troopidC'])."'") or die($conn->error);
}

// If we are viewing an event, hide all other info
if(isset($_GET['event']))
{
	// Set cookie for login
	if(!loggedIn())
	{
		setcookie("TroopTrackerLastEvent", cleanInput($_GET['event']), time() + 3600);
	}
	
	// Delete Comment
	if(isset($_POST['deleteComment']) && isAdmin())
	{
		$conn->query("DELETE FROM comments WHERE id = '".cleanInput($_POST['comment'])."'") or die($conn->error);
	}

	// Globals
	$eventClosed = 0;
	$limitedEvent = 0;
	$limitTotal = 0;
	
	// Merged troop
	$isMerged = false;
	
	// Does the event exist
	$eventExist = false;
			
	// Query database for event info
	$query = "SELECT * FROM events WHERE id = '".strip_tags(addslashes($_GET['event']))."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Update globals
			$eventClosed = $db->closed;
			$limitedEvent = $db->limitedEvent;
			$limitTo = $db->limitTo;
			
			// Set total
			$limitTotal = $db->limit501st + $db->limitRebels + $db->limitMando + $db->limitDroid + $db->limitOther;
			
			// Set event exist
			$eventExist = true;
					
			// Admin Area
			if(isAdmin())
			{
				echo '
				<h2 class="tm-section-header">Admin Controls</h2>
				<p style="text-align: center;"><a href="index.php?action=commandstaff&do=editevent&eid='.$db->id.'">Edit/View Event in Command Staff Area</a></p>
				<p style="text-align: center;"><a href="index.php?action=commandstaff&do=createevent&eid='.$db->id.'">Copy Event in Command Staff Area</a></p>
				<br />
				<hr />';
			}
			
			// Format dates
			$date1 = date("m/d/Y - h:i A", strtotime($db->dateStart)); 
			$date2 = date("m/d/Y - h:i A", strtotime($db->dateEnd));

			// Only show if logged in
			if(loggedIn())
			{
				// Query to see if trooper is subscribed
				$isSubscribed = $conn->query("SELECT * FROM event_notifications WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($_GET['event'])."'");

				// Set default subscribe button text
				$subscribeText = "Subscribe Updates";

				// Check if we are subscribed
				if($isSubscribed->num_rows > 0)
				{
					$subscribeText = "Unsubscribe Updates";
				}
				
				// Create button variable
				$button = '
				<p style="text-align: center;">
					<a href="#/" class="button" id="subscribeupdates" event="'.cleanInput($_GET['event']).'">'.$subscribeText.'</a>
					<br />
					<i>Get updates on sign ups and cancellations.</i>
				</p>';

				// If this event is over, don't show it
				if(strtotime($db->dateEnd) >= strtotime("-1 day") && $db->closed == 0)
				{					
					// Subscribe button
					echo $button;
					
					// Add to calendar links
					echo showCalendarLinks($db->name, $db->location, "Troop Tracker Event", $db->dateStart, $db->dateEnd);
				}
				// If subscribed, allow to unsubscribe
				else if($subscribeText == "Unsubscribe Updates")
				{
					// Subscribe button
					echo $button;
				}
			}
			
			// Is this merged data?
			if($db->venue == NULL && $db->numberOfAttend == NULL && $db->requestedCharacter == NULL && $db->secureChanging == NULL && $db->lightsabers == NULL && $db->parking == NULL && $db->mobility == NULL && $db->amenities == NULL && $db->referred == NULL)
			{	
				echo '
				<h2 class="tm-section-header">'.$db->name.'</h2>';
				
				if(loggedIn())
				{
					echo '
					<p><b>Event Date:</b> '.$date1.' ('.date('l', strtotime($db->dateStart)).')</p>
					<p><b>Comments:</b> '.ifEmpty($db->comments, "N/A").'</p>';
				}
				
				// Set is merged
				$isMerged = true;
			}
			else
			{
				// If canceled, show trooper
				if($db->closed == 2)
				{
					echo '
					<div style="text-align:center; color: red; margin-top: 25px;">
						<b>This event was CANCELED by Command Staff.</b>
					</div>';
				}
				// If locked, show trooper
				else if($db->closed == 3)
				{
					echo '
					<div style="text-align:center; color: red; margin-top: 25px;">
						<b>This event was LOCKED by Command Staff.</b>
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
				<h2 class="tm-section-header">'.$add.''.$db->name.'</h2>';
				
				if(loggedIn())
				{
					// If event closed
					if($db->closed == 1)
					{
						echo '
						<p><b>Event Raised:</b> $'.number_format($db->moneyRaised).'</p>';
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
					<p><b>Comments:</b><br />'.ifEmpty(nl2br(showBBcodes($db->comments)), "No comments for this event.").'</p>
					<p><b>Referred by:</b> '.ifEmpty($db->referred, "Not available").'</p>';
				
					// Get number of events with link
					$getNumOfLinks = $conn->query("SELECT id FROM events WHERE link = '".$db->id."'");
					
					// If has links to event, or is linked, show shift data
					if($getNumOfLinks->num_rows > 0 || $db->link != 0)
					{
						// Set link
						$link = -1;
						
						// If this event is the link
						if($getNumOfLinks->num_rows > 0)
						{
							$link = $db->id;
						}
						else if($db->link != 0)
						{
							$link = $db->link;
						}
						
						echo '
						<h2 class="tm-section-header">Shifts</h2>';
						
						// Query database for photos
						$query2 = "SELECT * FROM events WHERE (id = '".$link."' OR link = '".$link."') AND id != '".$db->id."' ORDER BY dateStart DESC";
						
						if ($result2 = mysqli_query($conn, $query2))
						{
							while ($db2 = mysqli_fetch_object($result2))
							{
								echo '
								<div style="border: 1px solid gray; margin-bottom: 10px; text-align: center;">
								<a href="index.php?event=' . $db2->id . '"><b>'.date('l', strtotime($db2->dateStart)).'</b> - ' . date('M d, Y', strtotime($db2->dateStart)) . '
								<br />' .
								date('h:i A', strtotime($db2->dateStart)) . ' - ' . date('h:i A', strtotime($db2->dateEnd)) .
								'</a>
								</div>';
							}
						}
					}

					// Set up add to query
					$add = "";

					// Add to query if this is a linked event
					if(isLink(cleanInput($_GET['event'])) > 0)
					{
						$add = " OR troopid = '".isLink(cleanInput($_GET['event']))."'";
					}
					
					// Don't show photos, if merged data
					if(!$isMerged)
					{
						// Query database for photos
						$query2 = "SELECT * FROM uploads WHERE admin = '1' AND (troopid = '".cleanInput($_GET['event'])."'".$add.") ORDER BY date DESC";
						
						// Query count
						$j = 0;
						
						if ($result2 = mysqli_query($conn, $query2))
						{
							while ($db2 = mysqli_fetch_object($result2))
							{
								// If first result...
								if($j == 0)
								{
									echo '
									<hr />';
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
					}
			
					// If this event is limited to era
					if($db->limitTo != 4)
					{
						echo '
						<br />
						<hr />
						<br />
						
						<div style="color: red;">
							This event is limited to ' . getEra($db->limitTo) . ' era.
						</div>';
					}
				
					// If this event is limited in troopers
					if($db->limit501st < 500 || $db->limitRebels < 500 || $db->limitMando < 500 || $db->limitDroid < 500 || $db->limitOther < 500)
					{
						echo '
						<br />
						<hr />
						<br />
						
						<div style="color: red;" name="troopersRemainingDisplay">
							<ul>
								<li>This event is limited to '.$limitTotal.' troopers.</li>
								<li>This event is limited to '.$db->limit501st.' 501st troopers. '.troopersRemaining($db->limit501st, eventClubCount($db->id, 0)).' </li>
								<li>This event is limited to '.$db->limitRebels.' Rebel Legion troopers. '.troopersRemaining($db->limitRebels, eventClubCount($db->id, 1)).'</li>
								<li>This event is limited to '.$db->limitMando.' Mando Merc troopers. '.troopersRemaining($db->limitMando, eventClubCount($db->id, 2)).'</li>
								<li>This event is limited to '.$db->limitDroid.' Droid Builder troopers. '.troopersRemaining($db->limitDroid, eventClubCount($db->id, 3)).'</li>
								<li>This event is limited to '.$db->limitOther.' Other troopers. '.troopersRemaining($db->limitOther, eventClubCount($db->id, 4)).'</li>
							</ul>
						</div>';
					}
					else
					{
						// If is a admin and a limited event
						if(isAdmin() && $db->limitedEvent == 1)
						{
							// All other events show counts of sign ups
							echo '
							<br />
							<hr />
							<br />
							
							<div name="troopersRemainingDisplay" style="justify-content: center; text-align: center;">
								<h3>Admin Trooper Counts</h3>

								<ul style="display:inline-table;">
									<li>501st troopers: '.eventClubCount($db->id, 0).' </li>
									<li>Rebel Legion: '.eventClubCount($db->id, 1).' </li>
									<li>Mando Mercs: '.eventClubCount($db->id, 2).' </li>
									<li>Droid Builders: '.eventClubCount($db->id, 3).' </li>
									<li>Other troopers: '.eventClubCount($db->id, 4).' </li>
								</ul>
							</div>';
						}
					}
				}
			}
			
			echo '
			<div id="hr1" name="hr1">
				<br />
				<hr />
				<br />
			</div>

			<div style="overflow-x: auto;" id="signuparea1" name="signuparea1">';

			// Query database for roster info
			$query2 = "SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, event_sign_up.status, troopers.id AS trooperId, troopers.name, troopers.tkid, troopers.squad FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".strip_tags(addslashes($_GET['event']))."' ORDER BY event_sign_up.id ASC";
			$i = 0;
			
			if ($result2 = mysqli_query($conn, $query2))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// Use this for later to determine which select box to show...
					$status = $db2->status;

					// If no events to show...
					if($i == 0)
					{
						echo '
						<form action="process.php?do=modifysignup" method="POST" name="modifysignupForm" id="modifysignupForm">
						
						<!-- Hidden variables -->
						<input type="hidden" name="modifysignupTroopIdForm" id="modifysignupTroopIdForm" value="'.$db->id.'" />
						<input type="hidden" name="limitedEventCancel" id="limitedEventCancel" value="'.$db->limitedEvent.'" />
						<input type="hidden" name="troopidC" id="troopidC" value="'.cleanInput($_GET['event']).'" />';
						
						// If user logged in
						if(loggedIn())
						{
							// Show user ID in hidden input field
							echo '
							<input type="hidden" name="myId" id="myId" value="'.strip_tags(addslashes($_SESSION['id'])).'" />';
						}
						
						echo '
						<table border="1">
						<tr>
							<th>Trooper Name</th>	<th>TKID</th>	<th>Costume</th>	<th>Backup Costume</th>	<th>Status</th>
						</tr>';
					}

					// Allow for users to edit their status from the event, and make sure the event is not closed
					if(loggedIn() && ($db2->trooperId == $_SESSION['id'] || $_SESSION['id'] == $db2->addedby) && ($db->closed == 0 || $db->closed == 3))
					{
						echo '
						<tr>
							<td>
								'.drawSupportBadge($db2->trooperId).'
								<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>';

								// Show who added the trooper
								if($db2->addedby != 0)
								{
									echo '
									<br /><small>Added by:<br />' . getName($db2->addedby) . '</small>';
								}

							echo '
							</td>
								
							<td>
								'.readTKNumber($db2->tkid, $db2->squad).'
							</td>';
							
							// If not a limited event, show select boxes to change costumes
							if($db->limitedEvent != 1)
							{
								echo '
								<td name="'.$db2->trooperId.'trooperRosterCostume" id="'.$db2->trooperId.'trooperRosterCostume">
									<select name="modifysignupFormCostume" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

									$query3 = "SELECT * FROM costumes";

									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
									}
									
									$query3 .= " ORDER BY FIELD(costume, ".$mainCostumes."".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume";
									
									if ($result3 = mysqli_query($conn, $query3))
									{
										while ($db3 = mysqli_fetch_object($result3))
										{
											if($db2->costume == $db3->id)
											{
												// If this is the selected costume, make it selected
												echo '
												<option value="'. $db3->id .'" club="'. $db3->club .'" SELECTED>'.$db3->costume.'</option>';
											}
											else
											{
												// Default
												echo '
												<option value="'. $db3->id .'" club="'. $db3->club .'">'.$db3->costume.'</option>';
											}
										}
									}

									echo '
									</select>
								</td>
								
								<td name="'.$db2->trooperId.'trooperRosterBackup" id="'.$db2->trooperId.'trooperRosterBackup">
									<select name="modiftybackupcostumeForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

									// Display costumes
									$query3 = "SELECT * FROM costumes";
									
									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
									}
									
									$query3 .= " ORDER BY FIELD(costume, ".$mainCostumes."".getMyCostumes(getTKNumber($db2->trooperId), getTrooperSquad($db2->trooperId)).") DESC, costume";
									
									// Count results
									$c = 0;
									
									// Amount of costumes
									if ($result3 = mysqli_query($conn, $query3))
									{
										while ($db3 = mysqli_fetch_object($result3))
										{
											// If costume set to backup and first result
											if($db2->costume_backup == 0 && $c == 0)
											{
												echo '
												<option value="0" SELECTED>N/A</option>';
											}
											// Make sure this is a first result otherwise
											else if($c == 0)
											{
												echo '
												<option value="0">N/A</option>';
											}
											
											// If a costume matches
											if($db2->costume_backup == $db3->id)
											{
												echo '
												<option value="'.$db3->id.'" SELECTED>'.$db3->costume.'</option>';
											}
											// Start showing costumes
											else
											{
												echo '
												<option value="'.$db3->id.'">'.$db3->costume.'</option>';
											}
											
											// Increment
											$c++;
										}
									}

									echo '
									</select>
								</td>';
							}
							else
							{
								// This is a limited event, show costume without ability to edit
								echo '
								<td name="'.$db2->trooperId.'trooperRosterCostume" id="'.$db2->trooperId.'trooperRosterCostume">
									'.getCostume($db2->costume).'
								</td>
								
								<td name="'.$db2->trooperId.'trooperRosterBackup" id="'.$db2->trooperId.'trooperRosterBackup">
									'.ifEmpty(getCostume($db2->costume_backup), "N/A").'
								</td>';
							}
							
							echo '
							<td id="'.$db2->trooperId.'Status">
							<div name="'.$db2->trooperId.'trooperRosterStatus" id="'.$db2->trooperId.'trooperRosterStatus">';
							
								// If not a limited event
								if($db->limitedEvent != 1)
								{
									// If on stand by
									if($db2->status == 1)
									{
										echo '
										<select name="modifysignupStatusForm" id="modifysignupStatusForm" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
											<option value="0" '.echoSelect(1, $db2->status).'>Stand By</option>
											<option value="4" '.echoSelect(4, $db2->status).'>Cancel</option>
										</select>';
									}
									// Regular
									else
									{
										echo '
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
										echo '
										<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">
										(Pending Command Staff Approval)';

										// If is admin and limited event
										if(isAdmin() && $db->limitedEvent == 1 && $db->closed == 0)
										{
											// Set status
											echo '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
										}

										echo '</div>';
									}
									else
									{
										echo '
										<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

										echo getStatus($db2->status);

										// If is admin and limited event
										if(isAdmin() && $db->limitedEvent == 1 && $db->closed == 0)
										{
											// If set to going
											if($db2->status == 0)
											{
												// Set status
												echo '
												<br />
												<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
											}
											// If set to not picked
											else if($db2->status == 6)
											{
												// Set status
												echo '
												<br />
												<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>';
											}
										}

										echo '</div>';
									}
								}

							echo '
							</div>
							</td>
						</tr>';
					}
					else
					{
						// If a user other than the current user
						echo '
						<tr>
							<td>
								'.drawSupportBadge($db2->trooperId).'
								<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>';

								// Show who added the trooper
								if($db2->addedby != 0)
								{
									echo '
									<br /><small>Added by:<br />' . getName($db2->addedby) . '</small>';
								}

							echo '
							</td>
								
							<td>
								'.readTKNumber($db2->tkid, $db2->squad).'
							</td>
							
							<td>
								'.ifEmpty(getCostume($db2->costume), "N/A").'
							</td>
							
							<td>
								'.ifEmpty(getCostume($db2->costume_backup), "N/A").'
							</td>
							
							<td id="'.$db2->trooperId.'Status">';
								echo '
								<div name="changestatusarea" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'">';

								// Limited event - If pending approval
								if($db2->status == 5)
								{
									echo '
									(Pending Command Staff Approval)';

									// If is admin and limited event
									if(isAdmin() && $db->limitedEvent == 1 && $db->closed == 0)
									{
										// Set status
										echo '
										<br />
										<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>
										<br />
										<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
									}
								}
								else
								{
									echo getStatus($db2->status);

									// If is admin and limited event
									if(isAdmin() && $db->limitedEvent == 1 && $db->closed == 0)
									{
										// If set to going
										if($db2->status == 0)
										{
											// Set status
											echo '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="0">Reject</a>';
										}
										// If set to not picked
										else if($db2->status == 6)
										{
											// Set status
											echo '
											<br />
											<a href="#/" class="button" name="changestatus" trooperid="'.$db2->trooperId.'" signid="'.$db2->signId.'" buttonid="1">Approve</a>';
										}
									}
								}

							echo '
							</div>
							</td>
						</tr>';
					}

					$i++;
				}
			}

			if($i == 0)
			{
				echo '
				</div>
				<div id="rosterTableNoData" name="rosterTableNoData">
				<b>No troopers have signed up for this event!</b>
				<br />
				<br />';
			}
			else
			{
				echo '
				</table>
				</form>
				</div>';
			}

			// HR Fix for formatting
			if(loggedIn())
			{
				echo '<hr />';
			}

			// For rosterTableNoData - If no data, this is for the AJAX of a submitted sign up form
			if($i == 0)
			{
				echo '</div>';
			}

			// If logged in and assigned to event
			if(loggedIn() && !$isMerged)
			{
				// Is the user in the event?
				$eventCheck = inEvent($_SESSION['id'], strip_tags(addslashes($_GET['event'])));

				if(strtotime($db->dateEnd) < strtotime("NOW"))
				{
					echo '
					<br />
					<b>This event is closed for editing.</b>
					<br /><br />';
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
								<p>
									<b>You have canceled this troop.</b>
								</p>
							</div>';
						}
						else
						{
							// If open or locked
							if($db->closed == 0 || $db->closed == 3)
							{
								echo '
								<div name="signeduparea" id="signeduparea">
									<p>
										<b>You are signed up for this troop!</b>
									</p>
								</div>';
							}
							else
							{
								// Closed for editing
								echo '
								<p>This event is closed for editing.</p>';
							}
						}
					}
					else
					{
						// Sign up area - NOT IN TROOP
						echo '
						<div name="signuparea" id="signuparea">
							<h2 class="tm-section-header">Sign Up</h2>';
						
						// If event is not closed...
						if($db->closed == 0)
						{
							if(hasPermission(0, 1, 2, 3))
							{
								// Is this a hand picked event?
								if($db->limitedEvent == 1)
								{
									echo '<b>This is a locked event. When you sign up, you will be placed in a pending status until command staff approves you. Please check for updates.</b>';
								}

								// Get troop count
								$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".strip_tags(addslashes($_GET['event']))."' AND status != '4' AND status != '1'");

								// Is the event full?
								if($getNumOfTroopers->num_rows >= ($db->limit501st + $db->limitRebels + $db->limitMando + $db->limitDroid + $db->limitOther))
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

										$query3 = "SELECT * FROM costumes";
										
										// If limited to certain costumes, only show certain costumes...
										if($db->limitTo < 4)
										{
											$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
										}
										
										$query3 .= " ORDER BY FIELD(costume, ".$mainCostumes."".getMyCostumes(getTKNumber($_SESSION['id']), getTrooperSquad($_SESSION['id'])).") DESC, costume";
										
										echo $query3;
										
										if ($result3 = mysqli_query($conn, $query3))
										{
											while ($db3 = mysqli_fetch_object($result3))
											{
												echo '
												<option value="'. $db3->id .'"  club="'. $db3->club .'">'.$db3->costume.'</option>';
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
												<option value="0">I\'ll be there!</option>
												<option value="2">Tentative</option>';
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
										$query2 = "SELECT * FROM costumes";
										
										// If limited to certain costumes, only show certain costumes...
										if($db->limitTo < 4)
										{
											$query2 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
										}
										
										$query2 .= " ORDER BY FIELD(costume, ".$mainCostumes."".getMyCostumes(getTKNumber($_SESSION['id']), getTrooperSquad($_SESSION['id'])).") DESC, costume";
										// Amount of costumes
										$c = 0;
										if ($result2 = mysqli_query($conn, $query2))
										{
											while ($db2 = mysqli_fetch_object($result2))
											{
												if($c == 0)
												{
													echo '<option value="0">Select a costume...</option>';
												}

												// Display costume
												echo '<option value="'.$db2->id.'">'.$db2->costume.'</option>';

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
						}
						else
						{
							echo '
							<p>This event is closed for editing.</p>';
						}
					}
				}
			}
		}
		
		// If event does not exist
		if(!$eventExist)
		{
			echo '
			<p style="text-align: center;">
				<b>This event does not exist.</b>
			</p>';
		}
		else
		{
			// Don't show photos, if merged data
			if(!$isMerged)
			{
				// Set up add to query
				$add = "";

				// Add to query if this is a linked event
				if(isLink(cleanInput($_GET['event'])) > 0)
				{
					$add = " OR troopid = '".isLink(cleanInput($_GET['event']))."'";
				}
				
				// Set results per page
				$results = 5;
				
				// Get total results - query
				$sqlPage = "SELECT COUNT(id) AS total FROM uploads WHERE admin = 0 AND (troopid = '".cleanInput($_GET['event'])."'".$add.")"; 
				$resultPage = $conn->query($sqlPage);
				$rowPage = $resultPage->fetch_assoc();
				
				// HR Fix for formatting
				if(loggedIn() || $rowPage["total"] > 0)
				{
					echo '<hr />';
				}
				
				// Set total pages
				$total_pages = ceil($rowPage["total"] / $results);
				
				// If page set
				if(isset($_GET['page']))
				{
					// Get page
					$page = cleanInput($_GET['page']);
					
					// Start from
					$startFrom = ($page - 1) * $results;
				}
				else
				{
					// Default page
					$page = 1;
					
					// Start from - default
					$startFrom = ($page - 1) * $results;
				}
				
				// Query database for photos
				$query = "SELECT * FROM uploads WHERE admin = '0' AND (troopid = '".cleanInput($_GET['event'])."'".$add.") ORDER BY date DESC LIMIT ".$startFrom.", ".$results."";
				
				// Count photos
				$i = 0;
				
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						// If first result
						if($i == 0)
						{
							echo '
							<h2 class="tm-section-header">Photos</h2>';
						}
						
						echo '
						<div class="container-image">
							<a href="images/uploads/'.$db->filename.'" data-lightbox="photosadmin" data-title="Uploaded by '.getName($db->trooperid).'" id="photo'.$db->id.'"><img src="images/uploads/'.$db->filename.'" width="200px" height="200px" class="image-c" /></a>
							
							<p class="container-text">';
							
								// If owned by trooper or is admin
								if(isAdmin() || $db->trooperid == $_SESSION['id'])
								{
									echo '<a href="index.php?action=editphoto&id='.$db->id.'">Edit</a>';
								}
								
							echo '
							</p>
						</div>';
						
						$i++;
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
							<a href="index.php?event='.cleanInput($_GET['event']).'&page='.$j.'">'.$j.'</a>';
						}
						
						// If not that last page, add a comma
						if($j != $total_pages)
						{
							echo ', ';
						}
					}
					
					echo '</p>';
				}
				
				// If trooper logged in show uploader
				if(loggedIn())
				{
					echo '
					<p>';
						// If logged in and command staff
						if(loggedIn() && isAdmin())
						{
							echo '
							<a href="#/" class="button" id="changeUpload">Change To: Troop Instructional Image Upload</a>';
						}

						echo '
						<form action="script/php/upload.php" class="dropzone" id="photoupload">
							<input type="hidden" name="admin" value="0" />
							<input type="hidden" name="troopid" value="'.cleanInput($_GET['event']).'" />
							<input type="hidden" name="trooperid" value="'.$_SESSION['id'].'" />
						</form>
					</p>';
				}
			}

			// HR Fix for formatting
			if(loggedIn())
			{
				echo '<hr />';
			}

			if(loggedIn() && !$isMerged)
			{
				// Check to see if this event is full
				$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".cleanInput($_GET['event'])."' AND status != '4' AND status != '1'");

				if($eventClosed == 0)
				{
					if(hasPermission(0, 1, 2, 3))
					{
						// Only show add a friend if main user is in event
						if(inEvent($_SESSION['id'], cleanInput($_GET['event']))["inTroop"] == 1)
						{
							echo '
							<div id="addfriend" name="addfriend">';
						}
						else
						{
							echo '
							<div id="addfriend" name="addfriend" style="display: none;">';
						}

						// If event is full
						if($getNumOfTroopers->num_rows >= $limitTotal)
						{
							echo '
							<b>This event is full. Your friend will be placed on the stand by list.</b>';
						}
						
						echo '
						<h2 class="tm-section-header">Add a Friend</h2>';
						
						echo '
						<form action="process.php?do=signup" method="POST" name="signupForm3" id="signupForm3">
							<input type="hidden" name="event" value="'.cleanInput($_GET["event"]).'" />';
								
						// Load all users
						$query = "SELECT troopers.id AS troopida, troopers.name AS troopername, troopers.tkid, troopers.squad FROM troopers WHERE NOT EXISTS (SELECT event_sign_up.trooperid FROM event_sign_up WHERE event_sign_up.trooperid = troopers.id AND event_sign_up.troopid = '".cleanInput($_GET['event'])."' AND event_sign_up.trooperid != ".placeholder.") AND troopers.approved = 1 ORDER BY troopers.name";

						$i = 0;
						if ($result = mysqli_query($conn, $query) or die($conn->error))
						{
							while ($db = mysqli_fetch_object($result))
							{
								// First add this to make a list
								if($i == 0)
								{
									echo '
									<form action="process.php?do=editevent" method="POST" name="troopRosterFormAdd" id="troopRosterFormAdd">
										<input type="hidden" name="troopid" id="troopid" value="'.cleanInput($_GET['event']).'" />

										<p>Select a trooper to add:</p>
										<select name="trooperSelect" id="trooperSelect">';
								}
								
								// Get TKID
								$tkid = readTKNumber($db->tkid, $db->squad);

								// List troopers
								echo '
								<option value="'.$db->troopida.'" tkid="'.$tkid.'" troopername="'.$db->troopername.'">'.$db->troopername.' - '.$tkid.'</option>';
								$i++;
							}
						}

						// If no troopers
						if($i == 0)
						{
							echo 'No troopers to add.';
						}
						else
						{
							echo '
							</select>';
						}
								
						echo '
						<p>What costume will they wear?</p>
						<select name="costume" id="costume">
							<option value="null" SELECTED>Please choose an option...</option>';

						$query3 = "SELECT * FROM costumes";
						
						// If limited to certain costumes, only show certain costumes...
						if($limitTo < 4)
						{
							$query3 .= " WHERE era = '".$limitTo."' OR era = '4'";
						}
						
						$query3 .= " ORDER BY FIELD(costume, ".$mainCostumes.") DESC, costume";
						
						if ($result3 = mysqli_query($conn, $query3))
						{
							while ($db3 = mysqli_fetch_object($result3))
							{
								echo '
								<option value="'. $db3->id .'" club="'. $db3->club .'">'.$db3->costume.'</option>';
							}
						}

						echo '
						</select>

						<br />

						<p>Select a status:</p>

						<select name="status">
							<option value="null" SELECTED>Please choose an option...</option>';

						if($limitedEvent != 1)
						{
							echo '
							<option value="0">I\'ll be there!</option>
							<option value="2">Tentative</option>';
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
						$query2 = "SELECT * FROM costumes";
						
						// If limited to certain costumes, only show certain costumes...
						if($limitTo < 4)
						{
							$query2 .= " WHERE era = '".$limitTo."' OR era = '4'";
						}
						
						$query2 .= " ORDER BY FIELD(costume, ".$mainCostumes.") DESC, costume";
						// Amount of costumes
						$c = 0;
						if ($result2 = mysqli_query($conn, $query2))
						{
							while ($db2 = mysqli_fetch_object($result2))
							{
								if($c == 0)
								{
									echo '<option value="0">Select a costume...</option>';
								}

								// Display costume
								echo '<option value="'.$db2->id.'">'.$db2->costume.'</option>';

								$c++;
							}
						}

						echo '
						</select>
						
						<br />
						<br />

						<input type="submit" value="Add Friend" name="submitSignUp" />
						</form>
						
						<hr />
						</div>';
					}
					else
					{
						echo '
						You do not have permission to sign up for events. Please refer to the boards for assistance.';
					}
				}
				else
				{
					//echo '
					//<p>This event is closed for editing.</p>';
				}

				echo '
				<form aciton="process.php?do=postcomment" name="commentForm" id="commentForm" method="POST">
					<input type="hidden" name="eventId" id="eventId" value="'.cleanInput($_GET['event']).'" />

					<h2 class="tm-section-header">Discussion</h2>
					<div style="text-align: center;">
					<textarea cols="30" rows="10" name="comment" id="comment"></textarea>

					<br />

					<p>Is this an important message?</p>
					<select name="important" id="important">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>

					<br /><br />

					<input type="submit" name="submitComment" value="Post!" />
					</div>
				</form>

				<div name="commentArea" id="commentArea">';
				
				// Set up query string
				$troops = "";
				
				// Make sure this is a linked event
				if(isset($link))
				{
					// Query database for shifts to display all comments for linked events
					$query = "SELECT * FROM events WHERE link = '".$link."'";
					
					// Set up count so we don't start with an operator
					$j = 0;
					
					// Query loop
					if ($result = mysqli_query($conn, $query))
					{
						while ($db = mysqli_fetch_object($result))
						{
							// If not first result
							if($j != 0)
							{
								$troops .= "OR ";
							}
							
							// Add to query
							$troops .= "troopid = '".$db->id."' ";
							
							// Increment j
							$j++;
						}
					}

					// Add a last OR at end if linked data
					if($j > 0)
					{
						$troops .= "OR ";
					}
				}
				
				// Check which type of link
				if(!isset($link) || isset($link) && $link <= 0)
				{
					$link = cleanInput($_GET['event']);
				}

				// Query database for event info
				$query = "SELECT * FROM comments WHERE ".$troops."troopid = '".$link."' ORDER BY posted DESC";
				
				// Count comments
				$i = 0;
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						echo '
						<div style="overflow-x: auto;" style="text-align: center;">
						<table border="1" name="comment_'.$db->id.'" id="comment_'.$db->id.'">';
						
						// Set up admin variable
						$admin = '';
						
						// If is admin, set up admin options
						if(isAdmin())
						{
							$admin = '<span style="margin-right: 15px;"><a href="#/" id="deleteComment_'.$db->id.'" name="'.$db->id.'"><img src="images/trash.png" alt="Delete Comment" /></a></span>';
						}
						
						// Convert date/time
						$date = strtotime($db->posted);
						$newdate = date("F j, Y, g:i a", $date);

						echo '
						<tr>
							<td><span style="float: left;">'.$admin.'<a href="#/" id="quoteComment_'.$db->id.'" name="'.$db->id.'" troopername="'.getName($db->trooperid).'" tkid="'.getTKNumber($db->trooperid, true).'" trooperid="'.$db->trooperid.'"><img src="images/quote.png" alt="Quote Comment"></a></span> <a href="index.php?profile='.$db->trooperid.'">'.getName($db->trooperid).' - '.readTKNumber(getTKNumber($db->trooperid), getTrooperSquad($db->trooperid)).'</a><br />'.$newdate.'</td>
						</tr>
						
						<tr>
							<td name="insideComment">'.nl2br(isImportant($db->important, showBBcodes($db->comment))).'</td>
						</tr>

						</table>
						</div>

						<br />';

						// Increment
						$i++;
					}
				}

				if($i == 0)
				{
					echo '
					<br />
					<b>No discussion to display.</b>';
				}
			}
		}
	}
}
else
{
	// Only show home page when it is loaded
	if(!isset($_GET['action']) && !isset($_GET['profile']) && !isset($_GET['event']))
	{
		if(!isWebsiteClosed())
		{
			// Show options for squad choice
			if(!loggedIn())
			{
				echo '
				<h2 class="tm-section-header">Welcome</h2>';
				
				// If sign ups are not closed
				if(!isSignUpClosed())
				{
					echo '
					<p style="text-align: center;">Welcome to the '.garrison.' troop tracker!<br /><br /><a href="index.php?action=requestaccess">Are you new to the '.garrison.' and/or 501st? Or are you solely a member of another club? Click here.</a><br /><br /><a href="index.php?action=setup">Have you used the old troop tracker and need to set up your account? Click here.</a></p>';
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
				<h2 class="tm-section-header">Troops</h2>'
				
				. showSquadButtons() . '
				
				<p style="text-align: center;">
					<small>Press a squad above to sort by squad.</small>
					<br />
					<br />
					<a href="index.php?squad=mytroops" class="button">My Troops</a>
				</p>

				<hr /><br />

				<div style="text-align: center;">';
				
				// Get number of troops that need confirmed
				$numberOfConfirmTroops = $conn->query("SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1");
				
				// Show need to confirm if exist
				if($numberOfConfirmTroops->num_rows > 0)
				{
					echo '
					<p>
						<a href="#confirmtroops">You have '.$numberOfConfirmTroops->num_rows.' troops to confirm. Click to confirm.</a>
					</p>
					<br />
					<hr />';
				}
				
				// Was a squad defined? (Prevents displays div when not needed)
				if(isset($_GET['squad']) && $_GET['squad'] == "mytroops")
				{
					// Query
					$query = "SELECT events.id AS id, events.name, events.dateStart, events.dateEnd, events.squad, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, events.link, events.limit501st, events.limitRebels, events.limitMando, events.limitDroid, events.limitOther FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd > NOW() - INTERVAL 1 DAY AND event_sign_up.status < 3 AND (events.closed = 0 OR events.closed = 3)";
				}
				else if(isset($_GET['squad']) && $_GET['squad'] == "canceledtroops")
				{
					// Query
					$query = "SELECT * FROM events WHERE dateStart >= CURDATE() AND (closed = '2') ORDER BY dateStart";
				}
				else if(isset($_GET['squad']))
				{
					// Query
					$query = "SELECT * FROM events WHERE dateStart >= CURDATE() AND squad = '".cleanInput($_GET['squad'])."' AND (closed = '0' OR closed = '3') ORDER BY dateStart";
				}
				else
				{
					// Query
					$query = "SELECT * FROM events WHERE dateStart >= CURDATE() AND (closed = '0' OR closed = '3') ORDER BY dateStart";
				}

				// Number of events loaded
				$i = 0;
				
				// Number of canceled events loaded
				$j = 0;
				
				// Get canceled events
				$query2 = "SELECT * FROM events WHERE DATE_FORMAT(dateStart, '%Y-%m-%d') = CURDATE() AND (closed = '2') ORDER BY dateStart";
				
				// Load events that are today or in the future
				if ($result = mysqli_query($conn, $query2))
				{
					while ($db = mysqli_fetch_object($result))
					{
						// If first result
						if($j == 0)
						{
							// Show message
							echo '
							<b>NOTICE!! The following troops have been canceled:</b>
							<ul>';
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
				<p><a href="#/" id="changeview" class="button">Calendar View</a></p>
				
				<div id="listview">

				<p><input type="text" id="controlf" placeholder="Type your search here..." style="text-align: center;" /></p>';
				
				// Event calendar
				$events = array();

				// Load events that are today or in the future
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						// Get number of troopers at event
						$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".$db->id."' AND status != '4' AND status != '1'");
						
						// Get number of events with link
						$getNumOfLinks = $conn->query("SELECT id FROM events WHERE link = '".$db->id."'");

						echo '
						<div style="border: 1px solid gray; margin-bottom: 10px;">
						
						<a href="index.php?event=' . $db->id . '">' . date('M d, Y', strtotime($db->dateStart)) . '' . '<br />';
						
						// If has links to event, or is linked, show shift data
						if($getNumOfLinks->num_rows > 0 || $db->link != 0)
						{
							echo '
							<b>' . date('l', strtotime($db->dateStart)) . '</b> - ' . date('h:i A', strtotime($db->dateStart)) . ' - ' . date('h:i A', strtotime($db->dateEnd)) .
							'<br />';
						}
						
						echo '
						' . $db->name . '</a>';

						// If not enough troopers
						if($getNumOfTroopers->num_rows <= 1)
						{
							echo '
							<br />
							<span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS EVENT!</b></span>';
						}
						// If full
						else if($getNumOfTroopers->num_rows >= ($db->limit501st + $db->limitRebels + $db->limitMando + $db->limitDroid + $db->limitOther))
						{
							echo '
							<br />
							<span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';
						}
						// Everything else
						else
						{
							echo '
							<br />
							<span>'.$getNumOfTroopers->num_rows.' Troopers Attending</span>';
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
				echo '
				<p style="text-align: center;">
					<a href="index.php?squad=canceledtroops" class="button">Canceled Troop Noticeboard</a>
				</p>
				
				<h2 class="tm-section-header">Recently Finished</h2>
				
				<ul>';
				
				// If on my troops
				if(isset($_GET['squad']) && $_GET['squad'] == "mytroops")
				{
					// Query
					$query = "SELECT events.id AS id, events.name, events.dateStart, events.dateEnd, events.squad, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, events.link, events.limit501st, events.limitRebels, events.limitMando, events.limitDroid, events.limitOther FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.closed = 1 ORDER BY dateEnd DESC LIMIT 20";
				}
				// If on squad
				else if(isset($_GET['squad']))
				{
					// Get recently closed troops by squad
					$query = "SELECT * FROM events WHERE closed = '1' AND squad = '".cleanInput($_GET['squad'])."' ORDER BY dateEnd DESC LIMIT 20";
				}
				// If on default
				else
				{
					// Get recently closed troops
					$query = "SELECT * FROM events WHERE closed = '1' ORDER BY dateEnd DESC LIMIT 20";
				}
				
				// Load events that are today or in the future
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						// Set up string to add to title if a linked event
						$add = "";
						
						// If this a linked event?
						if(isLink($db->id) > 0)
						{
							$add .= "[<b>" . date("l", strtotime($db->dateStart)) . "</b> : <i>" . date("m/d - h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "</i>] ";
						}
						
						echo '
						<li><a href="index.php?event='.$db->id.'">'.$add.''.$db->name.'</a></li>';
					}
				}
				
				echo '
				</ul>';
				
				// Load events that need confirmation
				$query = "SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1";

				if ($result = mysqli_query($conn, $query))
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
						
						// Set up string to add to title if a linked event
						$add = "";
						
						// If this a linked event?
						if(isLink($db->eventId) > 0)
						{
							$add .= "[<b>" . date("l", strtotime($db->dateStart)) . "</b> : <i>" . date("m/d - h:i A", strtotime($db->dateStart)) . " - " . date("h:i A", strtotime($db->dateEnd)) . "</i>] ";
						}

						echo '
						<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">
							<input type="checkbox" name="confirmList[]" id="confirmList_'.$db->eventId.'" value="'.$db->eventId.'" /> '.$add.''.$db->name.'<br /><br />
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

						$query3 = "SELECT * FROM costumes ORDER BY FIELD(costume, ".$mainCostumes."".getMyCostumes(getTKNumber($_SESSION['id']), getTrooperSquad($_SESSION['id'])).") DESC, costume";
						
						$l = 0;
						if ($result3 = mysqli_query($conn, $query3))
						{
							while ($db3 = mysqli_fetch_object($result3))
							{
								if($l == 0)
								{
									echo '
									<option value="">Please choose an option...</option>';
								}

								echo '
								<option value="'. $db3->id .'">'.$db3->costume.'</option>';

								$l++;
							}
						}

					echo '
						</select>
					</form>
					</div>';
				}
			}

			// If logged in, show Discord server
			if(loggedIn())
			{
				echo '
				<h2 class="tm-section-header">Tracker Updates</h2>
				<iframe src="https://titanembeds.com/embed/911999467908132866?defaultchannel=920863548173873263&fixedsidenav=false" height="600" width="100%" frameborder="0"></iframe>';
			}
			
			echo '
			<h2 class="tm-section-header">Recent Photos</h2>';
			
			// Load photos
			$query = "SELECT * FROM uploads WHERE admin = '0' ORDER BY id DESC LIMIT 10";
			
			// Setup count
			$i = 0;
			
			// Loop through photos
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<a href="images/uploads/'.$db->filename.'" data-lightbox="photo" data-title="Uploaded by '.getName($db->trooperid).' on '.getEventTitle($db->troopid, true).'."><img src="images/uploads/'.$db->filename.'" width="200px" height="200px" /></a>';
					
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
					<a href="index.php?action=photos">[Recent events with photos]</a>
				</p>';
			}
		}
		else
		{
			echo '
			<h2 class="tm-section-header">Sorry...</h2>
			
			<p style="text-align: center;">
				The Troop Tracker is temporarily down for maintenance. Please check back later.
			</p>';
		}
	}
}

echo '
</section>';

if(!isWebsiteClosed())
{
	// Discord link - logged in only
	if(loggedIn())
	{
		echo '
		<p style="text-align: center;">
			<a href="https://discord.gg/C6bCB33gp3" target="_blank">Join '.garrison.' on Discord for event notifications and more! Click here to join.</a>
			<br /><br />
			<a href="https://twitter.com/FLTroopUpdates" target="_blank">Follow @FLTroopUpdates on Twitter for event notifications and updates!</a>
		</p>';
	}

	// User's online
	echo '
	<hr />
	<section class="tm-section tm-section-small">
	<h2 class="tm-section-header">Users Online</h2>
	<p style="text-align: center;">';

	// Load users online
	$query = "SELECT * FROM troopers WHERE last_active >= NOW() - INTERVAL 5 MINUTE ORDER BY tkid";
	if ($result = mysqli_query($conn, $query))
	{
		$i = 0;
		while ($db = mysqli_fetch_object($result))
		{
			if($i != 0)
			{
				echo ', ';
			}

			echo '<a href="index.php?profile='.$db->id.'">' . readTKNumber($db->tkid, $db->squad) . '</a>';

			$i++;
		}
	}

	if($i == 0)
	{
		echo 'No users online!';
	}

	echo '
	</p>
	</section>';
}

echo '
<hr />

<section class="tm-section tm-section-small">
<p class="tm-mb-0">
Website created by <a href="index.php?profile=644">Matthew Drennan (TK52233)</a>. If you encounter any technical issues with this site, please refer to the <a href="index.php?action=faq">FAQ page</a> for guidance.
</p>

<p class="tm-mb-0">
If you are missing troops or notice incorrect data, please refer to your squad leader.
</p>

<p style="text-align: center;">
<a href="https://github.com/MattDrennan/501-troop-tracker" target="_blank">Help contribute on GitHub.com!</a>
</p>
</section>

<!-- Image Uploader JS -->
<script type="text/javascript">
  
    Dropzone.autoDiscover = false;
  
    var myDropzone = new Dropzone(".dropzone", { 
       maxFilesize: 10,
       acceptedFiles: ".jpeg,.jpg,.png,.gif"
    });
      
</script>

<!-- External JS File -->
<script type="text/javascript" src="script/js/main.js"></script>
</body>
</html>';

?>