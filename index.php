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
	<title>501st Florida Garrison - Troop Tracker</title>
	
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap" rel="stylesheet" />
	
	<!-- Main Style Sheets -->
	<link href="fontawesome/css/all.min.css" rel="stylesheet" />';
	
	if(loggedIn())
	{
		if(myTheme() == 0)
		{
			echo '
			<link href="css/main.css" rel="stylesheet" />
			<link rel="stylesheet" href="css/nav.css">';
		}
		else if(myTheme() == 1)
		{
			echo '
			<link href="css/main1.css" rel="stylesheet" />
			<link rel="stylesheet" href="css/nav1.css">';
		}
		else if(myTheme() == 2)
		{
			echo '
			<link href="css/main2.css" rel="stylesheet" />
			<link rel="stylesheet" href="css/nav2.css">';
		}
	}
	else
	{
		echo '
		<link href="css/main.css" rel="stylesheet" />
		<link rel="stylesheet" href="css/nav.css">';
	}
	
	echo '
	<!-- Style Sheets -->
	<link rel="stylesheet" href="script/lib/jquery-ui.min.css">
	<link rel="stylesheet" href="script/lib/jquery-ui-timepicker-addon.css">
	<link href="css/dropzone.min.css" type="text/css" rel="stylesheet" />
	<link href="css/lightbox.min.css" rel="stylesheet" />
	
	<!-- Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<!-- JQUERY -->
	<script src="script/lib/jquery-3.4.1.min.js"></script>

	<!-- JQUERY UI -->
	<script src="script/lib/jquery-ui.min.js"></script>

	<!-- Addons -->
	<script src="script/lib/jquery-ui-timepicker-addon.js"></script>
	<script src="script/js/validate/jquery.validate.min.js"></script>
	<script src="script/js/validate/validate.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
	
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
<h1 class="tm-page-header">501st Florida Garrison - Troop Tracker</h1>
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
			<a href="index.php?action=setup" '.isPageActive("setup").'>Account Setup</a>';
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

	<a href="#" id="unsubscribeLink" class="button">Unsubscribe From E-mail</a> 
	<a href="#" id="changeemailLink" class="button">Change E-mail</a> 
	<a href="#" id="changephoneLink" class="button">Change Phone</a> 
	<a href="#" id="changenameLink" class="button">Change Name</a> 
	<a href="#" id="changepasswordLink" class="button">Change Password</a>
	<a href="#" id="changethemeLink" class="button">Change Theme</a> 
	<a href="index.php?profile='.$_SESSION['id'].'" class="button">View Your Profile</a>
	<br /><br />

	<div id="unsubscribe" style="display:none;">
		<h2 class="tm-section-header">E-mail Subscription</h2>
		<form action="process.php?do=unsubscribe" method="POST" name="unsubscribeForm" id="unsubscribeForm">';
		$query = "SELECT subscribe FROM troopers WHERE id = '".$_SESSION['id']."'";

		if ($result = mysqli_query($conn, $query) or die($conn->error))
		{
			while ($db = mysqli_fetch_object($result))
			{
				if($db->subscribe == 1)
				{
					echo '
					<input type="submit" name="unsubscribeButton" id="unsubscribeButton" value="Unsubscribe" />';
				}
				else
				{
					echo '
					<input type="submit" name="unsubscribeButton" id="unsubscribeButton" value="Subscribe" />';
				}
			}
		}
		echo '
		</form>
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
					<option value="0" '.echoSelect(0, $db->theme).'>Everglades Theme (Default)</option>
					<option value="1" '.echoSelect(1, $db->theme).'>Makaze Theme</option>
					<option value="2" '.echoSelect(2, $db->theme).'>Florida Garrison Theme</option>';
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
		<p style="text-align: center;">New to the 501st and/or Florida Garrison? Use this form below to start signing up for troops. Command Staff will need to approve your account prior to use.</p>
		
		<form action="process.php?do=requestaccess" name="requestAccessForm" id="requestAccessForm" method="POST">
			First & Last Name: <input type="text" name="name" id="name" />
			<br /><br />
			TKID: <input type="text" name="tkid" id="tkid" />
			<p><i>Non-501st clubs, please enter an ID number of your choosing.</i></p>
			E-mail: <input type="text" name="email" id="email" />
			<br /><br />
			Phone (Optional): <input type="text" name="phone" id="phone" />
			<br /><br />
			Forum Username: <input type="text" name="forumid" id="forumid" />
			<br /><br />
			Password: <input type="password" name="password" id="password" />
			<br /><br />
			Password (Confirm): <input type="password" name="passwordC" id="passwordC" />
			<br /><br />
			<p>Squad/Club:</p>
			<select name="squad" id="squad">
				<option value="1">Everglades Squad</option>
				<option value="2">Makaze Squad</option>
				<option value="3">Parjai Squad</option>
				<option value="4">Squad 7</option>
				<option value="5">Tampa Bay Squad</option>
				<option value="6">Rebel Legion</option>
				<option value="7">Droid Builders</option>
				<option value="8">Mandos</option>
				<option value="9">Other</option>
			</select>
			<br /><br />
			<input type="submit" name="submitRequest" value="Request" />
		</form>
	</div>

	<div name="requestAccessFormArea2" id="requestAccessFormArea2"></div>';
}

// Show the search page
if(isset($_GET['profile']))
{
	// Get data
	$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, event_sign_up.attend, event_sign_up.attended_costume, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd, troopers.id, troopers.name, troopers.forum_id, troopers.tkid FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopers.id = '".cleanInput($_GET['profile'])."' AND events.closed = '1' AND event_sign_up.status = '3' ORDER BY events.dateEnd DESC";
	$i = 0;
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				// Command Staff Edit Link
				if(isAdmin())
				{
					echo '
					<h2 class="tm-section-header">Admin Controls</h2>
					<p style="text-align: center;"><a href="index.php?action=commandstaff&do=managetroopers&uid='.$db->id.'">Edit/View Member in Command Staff Area</a></p>';
				}
				
				// Get 501st Info
				$thumbnail_get = $conn->query("SELECT thumbnail FROM 501st_troopers WHERE legionid = '".$db->tkid."'");
				$thumbnail = $thumbnail_get->fetch_row();
				
				echo '
				<h2 class="tm-section-header">'.$db->name.' - '.readTKNumber($db->tkid).'</h2>';
				
				// Avatar
				
				if(isset($thumbnail[0]))
				{
					echo '
					<p style="text-align: center;">
						<img src="'.$thumbnail[0].'" />
					</p>';
				}
				else
				{
					echo '
					<p style="text-align: center;">
						<img src="https://www.501st.com/memberdata/templates/tk_head.jpg" />
					</p>';
				}
				
				echo '
				<p style="text-align: center;"><a href="https://www.fl501st.com/boards/memberlist.php?mode=viewprofile&un='.urlencode($db->forum_id).'" target="_blank">View Boards Profile</a></p>
				
				<div style="overflow-x: auto;">
				<table border="1">
				<tr>
					<th>Event Name</th>	<th>Date</th>	<th>Attended Costume</th>
				</tr>';
			}

			echo '
			<tr>
				<td><a href="index.php?event='.$db->troopid.'">'.$db->eventName.'</a></td>';
				
			$dateFormat = date('m-d-Y', strtotime($db->dateEnd));

			echo '
				<td>'.$dateFormat.'</td>	<td>'.ifEmpty(getCostume($db->attended_costume), "N/A").'</td>
			</tr>';

			// Increment i
			$i++;
		}
	}

	if($i == 0)
	{
		echo '<br /><b>Nothing to show yet!</b>';
	}
	else
	{
		$troops_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE attend = '1' AND trooperid = '".cleanInput($_GET['profile'])."'") or die($conn->error);
		$count = $troops_get->fetch_row();
		$j = 0;

		echo '
		</table>
		</div>

		<br />

		<b>Total Finished Troops:</b> ' . number_format($i) . '

		<br />

		<h2 class="tm-section-header">Awards</h2>
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
			echo '<li>Mr./Ms. 501 Award</li>';
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
	
	showCostumes(getTKNumber($_GET['profile']));
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
					<option value="1" '.echoSelect(1, cleanInput($_POST['squad'])).'>Everglades Squad</option>
					<option value="5" '.echoSelect(5, cleanInput($_POST['squad'])).'>Tampa Bay Squad</option>
					<option value="2" '.echoSelect(2, cleanInput($_POST['squad'])).'>Makaze Squad</option>
					<option value="4" '.echoSelect(4, cleanInput($_POST['squad'])).'>Squad 7 Squad</option>
					<option value="3" '.echoSelect(3, cleanInput($_POST['squad'])).'>Parjai Squad</option>
					<option value="6" '.echoSelect(6, cleanInput($_POST['squad'])).'>Rebel Legion</option>
					<option value="7" '.echoSelect(7, cleanInput($_POST['squad'])).'>Mando Mercs</option>
					<option value="8" '.echoSelect(8, cleanInput($_POST['squad'])).'>Droid Builders</option>
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
		$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.attended_costume, event_sign_up.status, event_sign_up.attend, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE";
		
		if(strlen($_POST['tkID']) > 0)
		{
			$query .= " event_sign_up.trooperid = '".getIDNumberFromTK(cleanInput($_POST['tkID']))."'";
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
			$troops_get = $conn->query("SELECT COUNT(id) FROM events WHERE dateStart >= '".$dateF."' AND dateEnd <= '".$dateE."' AND squad = '".cleanInput($_POST['squad'])."'") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts
			$charity_get = $conn->query("SELECT SUM(moneyRaised) FROM events WHERE dateStart >= '".$dateF."' AND dateEnd <= '".$dateE."' AND squad = '".cleanInput($_POST['squad'])."'") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If Rebel Legion
		else if($_POST['squad'] == 6)
		{
			// Get troop counts - Rebel Legion
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('1' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '4' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts - Rebel Legion
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('1' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '4' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If Mando Mercs
		else if($_POST['squad'] == 7)
		{
			// Get troop counts - Mando Mercs
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('2' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts - Mando Mercs
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('2' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$charity_count = $charity_get->fetch_row();
		}
		
		// If Droid Builders
		else if($_POST['squad'] == 8)
		{
			// Get troop counts - Droid Builders
			$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('3' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
			$troop_count = $troops_get->fetch_row();
			
			// Get charity counts - Droid Builders
			$charity_get = $conn->query("SELECT SUM(events.moneyRaised), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('3' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume)) GROUP BY events.id, event_sign_up.id") or die($conn->error);
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
						<th>Event Name</th>	<th>Date</th>	<th>Trooper TKID</th>	<th>Attended Costume</th>	<th>Attended</th>
					</tr>';
				}
				
				$dateFormat = date('m-d-Y', strtotime($db->dateEnd));

				echo '
				<tr>
					<td><a href="index.php?event='.$db->troopid.'">'.$db->eventName.'</a></td>	<td>'.$dateFormat.'</td>	<td><a href="index.php?profile='.$db->trooperid.'">'.getTKNumber($db->trooperid).'</a></td>	<td>'.ifEmpty(getCostume($db->attended_costume), "N/A").'</td>	<td>'.didAttend($db->attend).'</td>
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
				else if(($_POST['squad'] >= 1 && $_POST['squad'] <= 5))
				{
					// Get troop counts - 501st
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('0' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '4' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR EXISTS(SELECT events.id, events.oldid FROM events WHERE events.oldid != 0 AND events.id = event_sign_up.troopid))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If Rebel Legion
				else if($_POST['squad'] == 6)
				{
					// Get troop counts - Rebel Legion
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('1' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '4' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If Mando Mercs
				else if($_POST['squad'] == 7)
				{
					// Get troop counts - Mando Mercs
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('2' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// If Droid Builders
				else if($_POST['squad'] == 8)
				{
					// Get troop counts - Droid Builders
					$troops_get = $conn->query("SELECT COUNT(event_sign_up.id), events.id FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$db->id."' AND events.dateStart >= '".$dateF."' AND events.dateEnd <= '".$dateE."' AND ('3' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume) OR '6' = (SELECT costumes.club FROM costumes WHERE id = event_sign_up.attended_costume))") or die($conn->error);
					$count = $troops_get->fetch_row();
				}
				
				// Create an array of our count
				$tempArray = array($db->tkid, $count[0], $db->name, $db->id);
				
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
				<td><a href="index.php?profile='.$value[3].'">'.readTKNumber($value[0]).'</a> - '.$value[2].'</td>	<td>'.$value[1].'</td>
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
			<a href="#" class="button" id="showstats" name="showstats">Show My Stats</a>
		</p>
		
		<div id="mystats" name="mystats" style="display: none;">';

		// Get data
		$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, event_sign_up.attend, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND attend = 1 AND events.closed = '1' ORDER BY events.dateEnd DESC";
		$i = 0;
		$troopsAttended = 0;
		$moneyRaised = 0;
		$timeSpent = 0;
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
						<th>Troop</th>	<th>Costume</th>	<th>Money Raised</th>	<th>Time Spent</th>
					</tr>';
				}

				// Output data - calculate time spent at troops
				$date1 = new DateTime($db->dateStart);
				$date2 = new DateTime($db->dateEnd);
				$getDiff = $date1->diff($date2);
				$time = $getDiff->days * 24 * 60;
				$time += $getDiff->h * 60;
				$time += $getDiff->i;

				echo '
				<tr>
					<td><a href="index.php?event='.$db->eventId.'">'.$db->eventName.'</a></td>	<td>'.ifEmpty(getCostume($db->costume), "N/A").'</td>	<td>$'.number_format($db->moneyRaised).'</td>	<td>'.floor($time/60).'H '.($time % 60).'M</td>
				</tr>';

				$troopsAttended++;
				$moneyRaised += $db->moneyRaised;
				$timeSpent += $time;
				$i++;
			}
		}

		if($i > 0)
		{
			// How many troops did the user attend
			$favoriteCostume_get = $conn->query("SELECT costume, COUNT(*) FROM event_sign_up WHERE trooperid = '".$_SESSION['id']."' GROUP BY costume ORDER BY COUNT(costume) DESC LIMIT 1") or die($conn->error);

			$favoriteCostume = mysqli_fetch_array($favoriteCostume_get);

			echo '
			</table>
			</div>

			<p><b>Favorite Costume:</b> '.ifEmpty(getCostume($favoriteCostume['costume']), "N/A").'</p>
			<p><b>Attended:</b> '.number_format($troopsAttended).'</p>
			<p><b>Money Raised:</b> $'.number_format($moneyRaised).'</p>
			<p><b>Time Spent:</b> '.floor($timeSpent/60).'H '.($timeSpent % 60).'</p>';
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

	// Get data
	$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, event_sign_up.attend, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE events.closed = '1' AND events.dateEnd > CURRENT_DATE - INTERVAL 60 DAY GROUP BY events.id ORDER BY events.dateEnd DESC LIMIT 20";
	$i = 0;
	$timeSpent = 0;
	$moneyRaised = 0;
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
					<th>Troop</th>	<th>Troopers Attended</th>	<th>Money Raised</th>	<th>Time Spent</th>
				</tr>';
			}

			// Output data
			$date1 = new DateTime($db->dateStart);
			$date2 = new DateTime($db->dateEnd);
			$getDiff = $date1->diff($date2);
			$time = $getDiff->days * 24 * 60;
			$time += $getDiff->h * 60;
			$time += $getDiff->i;

			$timeSpent += $time;
			$moneyRaised += $db->moneyRaised;

			// How many troopers attended
			$trooperCount_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE troopid = '".$db->troopid."' AND attend = '1'") or die($conn->error);
			
			$count = $trooperCount_get->fetch_row();

			echo '
			<tr>
				<td><a href="index.php?event='.$db->eventId.'">'.$db->eventName.'</a></td>	<td>'.$count[0].'</td>	<td>$'.number_format($db->moneyRaised).'</td>	<td>'.floor($time/60).'H '.($time % 60).'M</td>
			</tr>';

			$i++;
		}
	}

	if($i > 0)
	{
		// How many troops did the user attend
		$favoriteCostume_get = $conn->query("SELECT costume, COUNT(*) FROM event_sign_up GROUP BY costume ORDER BY COUNT(costume) DESC LIMIT 1") or die($conn->error);
		$favoriteCostume = mysqli_fetch_array($favoriteCostume_get);

		// How many troops did the user attend
		$attended_get = $conn->query("SELECT COUNT(*) FROM event_sign_up WHERE attend = '1'") or die($conn->error);
		$count1 = $attended_get->fetch_row();
		// How many regular troops
		$regular_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '0'") or die($conn->error);
		$count2 = $regular_get->fetch_row();
 		// How many PR troops
		$charity_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '1'") or die($conn->error);
		$count3 = $charity_get->fetch_row();
		// How many Disney troops
		$pr_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '2'") or die($conn->error);
		$count4 = $pr_get->fetch_row();
		// How many convention troops
		$disney_get = $conn->query("SELECT COUNT(*) FROM events WHERE label = '3'") or die($conn->error);
		$count5 = $disney_get->fetch_row();
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
 
		echo '
		</table>
		</div>

		<p><b>Favorite Costume:</b> '.ifEmpty(getCostume($favoriteCostume['costume']), "N/A").'</p>
		<p><b>Volunteers at Troops:</b> '.number_format($count1[0]).'</p>
		<p><b>Money Raised:</b> $'.number_format($moneyRaised).'</p>
		<p><b>Time Spent:</b> '.floor($timeSpent/60).'H '.($timeSpent % 60).'M</p>
		<p><b>Regular Troops:</b> '.number_format($count2[0]).'</p>
 		<p><b>Charity Troops:</b> '.number_format($count3[0]).'</p>
		<p><b>PR Troops:</b> '.number_format($count4[0]).'</p>
		<p><b>Disney Troops:</b> '.number_format($count5[0]).'</p>
		<p><b>Convention Troops:</b> '.number_format($count6[0]).'</p>
		<p><b>Wedding Troops:</b> '.number_format($count7[0]).'</p>
		<p><b>Birthday Troops:</b> '.number_format($count8[0]).'</p>
		<p><b>Virtual Troops:</b> '.number_format($count9[0]).'</p>
		<p><b>Other Troops:</b> '.number_format($count10[0]).'</p>
		<p><b>Total Finished Troops:</b> '.number_format($count11[0]).'</p>';
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
					<option value="1">Everglades Squad</option>
					<option value="5">Tampa Bay Squad</option>
					<option value="2">Makaze Squad</option>
					<option value="4">Squad 7 Squad</option>
					<option value="3">Parjai Squad</option>
					<option value="6">Rebel Legion</option>
					<option value="7">Mando Mercs</option>
					<option value="8">Droid Builders</option>
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
			<a href="index.php?action=commandstaff&do=notifications" class="button">Notifications</a> 
			<a href="index.php?action=commandstaff&do=managecostumes" class="button">Costume Management</a> ';
			
			if(hasPermission(1))
			{
				echo '
				<a href="index.php?action=commandstaff&do=createuser" class="button">Create Trooper</a> 
				<a href="index.php?action=commandstaff&do=managetroopers" class="button">Manage Troopers</a> 
				<a href="index.php?action=commandstaff&do=approvetroopers" class="button" id="trooperRequestButton" name="trooperRequestButton">Approve Trooper Requests - ('.$getTrooperNotifications->num_rows.')</a> 
				<a href="index.php?action=commandstaff&do=assignawards" class="button">Assign Awards</a>
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
		
			// Add to query if in URL
			if(isset($_GET['s']) && $_GET['s'] == "system")
			{
				// Get data
				$query = "SELECT * FROM notifications WHERE message NOT LIKE '%now has%' ORDER BY id DESC LIMIT 100";
			}
			else if(isset($_GET['s']) && $_GET['s'] == "troopers")
			{
				// Get data
				$query = "SELECT * FROM notifications WHERE message LIKE '%now has%' ORDER BY id DESC LIMIT 100";
			}
			else
			{
				// Get data
				$query = "SELECT * FROM notifications ORDER BY id DESC LIMIT 100";
			}
			
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
					
					echo '
					<li>
						<a href="index.php?profile='.$db->trooperid.'">'.$db->message.'</a> on '.$dateF.'.
					</li>';
					
					$i++;
				}
			}
			
			if($i == 0)
			{
				echo '<p>No notifications to display.</p>';
			}
			else
			{
				echo '</ul>';
			}
		}

		/**************************** COSTUMES *********************************/

		// Manage costumes - allow command staff to add, edit, and delete costumes
		if(isset($_GET['do']) && $_GET['do'] == "managecostumes")
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
					<option value="4">Rebel + 501st</option>
					<option value="5">Other</option>
					<option value="6">All</option>
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

				<div id="editCostumeList" name="editCostumeList" style="display: none;">

				<b>Costume Name:</b></br />
				<input type="text" name="costumeNameEdit" id="costumeNameEdit" />
				
				<b>Costume Era:</b></br />
				<select name="costumeEraEdit" id="costumeEraEdit">
					<option value="0">Prequel</option>
					<option value="1" SELECTED>Original</option>
					<option value="2">Sequel</option>
					<option value="3">Expanded</option>
					<option value="4">All</option>
				</select>
				
				<b>Costume Club:</b></br />
				<select name="costumeClubEdit" id="costumeClubEdit">
					<option value="0" SELECTED>501st Legion</option>
					<option value="1">Rebel Legion</option>
					<option value="2">Mando Mercs</option>
					<option value="3">Droid Builders</option>
					<option value="4">Rebel + 501st</option>
					<option value="5">Other</option>
					<option value="6">All</option>
				</select>

				<input type="submit" name="submitEditCostume" id="submitEditCostume" value="Edit Costume" />

				</div>
				</form>';
			}
			
			if(hasPermission(1))
			{
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
					<input type="submit" name="submitDeleteCostume" id="submitDeleteCostume" value="Delete Costume" />
					</form>';
				}
				
				echo '
				</div>';
			}
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

					echo '<option value="'.$db->id.'" '.echoSelect($db->id, $eid).'>'.$db->name.'</option>';

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
				<input type="submit" name="submitCancel" id="submitCancel" value="Mark Canceled" /> <input type="submit" name="submitFinish" id="submitFinish" value="Mark Finished" /> <input type="submit" name="submitOpen" id="submitOpen" value="Mark Open" /> <input type="submit" name="submitEdit" id="submitEdit" value="Edit" /> <input type="submit" name="submitRoster" id="submitRoster" value="Roster" /> <input type="submit" name="submitCharity" id="submitCharity" value="Set Charity Amount" />

				</form>

				<div name="rosterInfo" id="rosterInfo" style="display:none;">
				</div>

				<div name="editEventInfo" id="editEventInfo" style="display:none;">
					<form action="process.php?do=editevent" id="editEventForm" name="editEventForm" method="POST">
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
							<option value="1">Everglades Squad</option>
							<option value="5">Tampa Bay Squad</option>
							<option value="2">Makaze Squad</option>
							<option value="4">Squad 7 Squad</option>
							<option value="3">Parjai Squad</option>
							<option value="0">Florida Garrison</option>
						</select>		

						<p>Date/Time Start:</p>
						<input type="text" name="dateStart" id="datepicker" />

						<p>Date/Time End:</p>
						<input type="text" name="dateEnd" id="datepicker2" />

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
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

						<p>Blasters Allowed?</p>
						<select name="blasters" id="blasters">
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

						<p>Lightsabers Allowed?</p>
						<select name="lightsabers" id="lightsabers">
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

						<p>Parking?</p>
						<select name="parking" id="parking">
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

						<p>People with limited mobility access?</p>
						<select name="mobility" id="mobility">
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

						<p>Amenities?</p>
						<input type="text" name="amenities" id="amenities" />

						<p>Additional Comments:</p>
						<textarea rows="10" cols="50" name="comments" id="comments"></textarea>

						<p>Label:</p>
						<select name="label" id="label">
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="0">Regular</option>
							<option value="1">Charity</option>
							<option value="2">PR</option>
							<option value="3">Disney</option>
							<option value="4">Convention</option>
							<option value="5">Wedding</option>
							<option value="6">Birthday Party</option>
							<option value="7">Virtual Troop</option>
							<option value="8">Other</option>
						</select>

						<p>Do you wish to lock this event?</p>
						<select name="limitedEvent" id="limitedEvent">
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

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
						
						<p>Limit Total:</p>
						<input type="number" name="limitTotal" value="500" id="limitTotal" />

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
					<th>Name</th>	<th>E-mail</th>	<th>Forum ID</th>	<th>Phone</th>	<th>Squad</th>	<th>TKID</th>
				</tr>
					<tr id="userList" name="userList">
						<td id="nameTable"></td>	<td id="emailTable"></td> <td id="forumTable"></td>	<td id="phoneTable"></td>	<td id="squadTable"></td>	<td id="tkTable"></td>
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

					echo '<option value="'.$db->id.'" '.echoSelect($db->id, $uid).'>'.$db->name.' - '.readTKNumber($db->tkid).'</option>';

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

				<input type="submit" name="submitDeleteUser" id="submitDeleteUser" value="Delete" /> <input type="submit" name="submitEditUser" id="submitEditUser" value="Edit" />
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
							<option value="1">Everglades Squad</option>
							<option value="2">Makaze Squad</option>
							<option value="3">Parjai Squad</option>
							<option value="4">Squad 7</option>
							<option value="5">Tampa Bay Squad</option>
							<option value="6">Rebel Legion</option>
							<option value="7">Droid Builders</option>
							<option value="8">Mandos</option>
							<option value="9">Other</option>
						</select>

						<p>Permissions:</p>
						<select name="permissions" id="permissions">
							<option value="0">501st Member</option>
							<option value="3">Reserve Member</option>
							<option value="4">Retired Member</option>
							<option value="5">Handler</option>
							<option value="2">Squad Leader</option>
							<option value="1">Super Admin</option>
						</select>

						<p>TKID:</p>
						<input type="text" name="tkid" id="tkid" />
						
						<p>Forum ID:</p>
						<input type="text" name="forumid" id="forumid" />
						
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
				
				<p>Forum Username:</p>
				<input type="text" name="forumid" id="forumid" />

				<p>Squad/Club:</p>
				<select name="squad" id="squad">
					<option value="1">Everglades Squad</option>
					<option value="2">Makaze Squad</option>
					<option value="3">Parjai Squad</option>
					<option value="4">Squad 7</option>
					<option value="5">Tampa Bay Squad</option>
					<option value="6">Rebel Legion</option>
					<option value="7">Droid Builders</option>
					<option value="8">Mandos</option>
					<option value="9">Other</option>
				</select>

				<p>Permissions:</p>
				<select name="permissions" id="permissions">
					<option value="0">501st Member</option>
					<option value="3">Reserve Member</option>
					<option value="4">Retired Member</option>
					<option value="5">Handler</option>
					<option value="2">Squad Leader</option>
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
			
			// Set up variables
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
			$limitTotal = "";
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
						$limitTotal = $db->limitTotal;
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
			<a href="#" class="button" id="easyfilltoolbutton" name="easyfilltoolbutton">Easy Fill Tool</a>
			
			<div name="easyfilltoolarea" id="easyfilltoolarea" style="display: none;">
			<p>Easy Fill Tool:</p>
			<form action="index.php?action=commandstaff&do=createevent" method="POST" name="easyFillTool" id="easyFillTool">
				<textarea rows="10" cols="50" name="easyFill" id="easyFill"></textarea>
				<br />
				<input type="submit" name="submit" name="easyFillButton" id="easyFillButton" value="Fill!" />
			</form>

			<p><i>Make sure there is a ":" in the time for both datetime values</i></p>
			</div>';

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
					<option value="1" '.copyEventSelect($eid, $squad, 1).'>Everglades Squad</option>
					<option value="5" '.copyEventSelect($eid, $squad, 5).'>Tampa Bay Squad</option>
					<option value="2" '.copyEventSelect($eid, $squad, 2).'>Makaze Squad</option>
					<option value="4" '.copyEventSelect($eid, $squad, 4).'>Squad 7 Squad</option>
					<option value="3" '.copyEventSelect($eid, $squad, 3).'>Parjai Squad</option>
					<option value="0" '.copyEventSelect($eid, $squad, 0).'>Florida Garrison</option>
				</select>				

				<p>Date/Time Start:</p>
				<input type="text" name="dateStart" id="datepicker" value="'.copyEvent($eid, $dateStart).'" />

				<p>Date/Time End:</p>
				<input type="text" name="dateEnd" id="datepicker2" value="'.copyEvent($eid, $dateEnd).'" />

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

				<p>Amenities?</p>
				<input type="text" name="amenities" id="amenities" value="'.copyEvent($eid, $amenities).'" />

				<p>Additional Comments:</p>
				<textarea rows="10" cols="50" name="comments" id="comments">'.copyEvent($eid, $comments).'</textarea>

				<p>Label:</p>
				<select name="label" id="label">
					<option value="null" '.copyEventSelect($eid, $label, "null").'>Please choose an option...</option>
					<option value="0" '.copyEventSelect($eid, $label, 0).'>Regular</option>
					<option value="1" '.copyEventSelect($eid, $label, 1).'>Charity</option>
					<option value="2" '.copyEventSelect($eid, $label, 2).'>PR</option>
					<option value="3" '.copyEventSelect($eid, $label, 3).'>Disney</option>
					<option value="4" '.copyEventSelect($eid, $label, 4).'>Convention</option>
					<option value="5" '.copyEventSelect($eid, $label, 5).'>Wedding</option>
					<option value="6" '.copyEventSelect($eid, $label, 6).'>Birthday Party</option>
					<option value="7" '.copyEventSelect($eid, $label, 7).'>Virtual Troop</option>
					<option value="8" '.copyEventSelect($eid, $label, 8).'>Other</option>
				</select>

				<p>Do you wish to lock this event?</p>
				<select name="limitedEvent" id="limitedEvent">
					<option value="null" '.copyEventSelect($eid, $limitedEvent, "null").'>Please choose an option...</option>
					<option value="1" '.copyEventSelect($eid, $limitedEvent, 1).'>Yes</option>
					<option value="0" '.copyEventSelect($eid, $limitedEvent, 0).'>No</option>
				</select>

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
				
				<p>Limit Total:</p>
				<input type="number" name="limitTotal" value="'.copyEvent($eid, $limitTotal, 500).'" id="limitTotal" />

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

// Show the login page
if(isset($_GET['action']) && $_GET['action'] == "login")
{
	echo '
	<h2 class="tm-section-header">Login</h2>';

	// Display submission for register account, otherwise show the form
	if(isset($_POST['loginWithTK']))
	{
		$tkid = cleanInput($_POST['tkid']);

		// Format id for non members
		// Rebel
		if(substr(cleanInput($_POST['tkid']), 0, 1) === 'R')
		{
			$tkid = substr($tkid, 1);
			$tkid = "111111" . $tkid;
		}
		// Droid
		else if(substr(cleanInput($_POST['tkid']), 0, 1) === 'D')
		{
			$tkid = substr($tkid, 1);
			$tkid = "222222" . $tkid;
		}
		// Mandos
		else if(substr(cleanInput($_POST['tkid']), 0, 1) === 'M')
		{
			$tkid = substr($tkid, 1);
			$tkid = "333333" . $tkid;
		}
		// Other
		else if(substr(cleanInput($_POST['tkid']), 0, 1) === 'O')
		{
			$tkid = substr($tkid, 1);
			$tkid = "444444" . $tkid;
		}
		// TK
		else if(substr(cleanInput($_POST['tkid']), 0, 2) === 'TK')
		{
			$tkid = substr($tkid, 2);
			$tkid = $tkid;
		}

		// Get data
		$query = "SELECT * FROM troopers WHERE tkid='".$tkid."'";
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
						$_SESSION['id'] = $db->id;
						$_SESSION['tkid'] = $db->tkid;

						echo 'You have now logged in! <a href="index.php">Click here to go home.</a>';
					}
					else
					{
						echo 'Your access has not been approved yet.';
					}
				}
				else
				{
					echo 'Incorrect username or password. <a href="index.php?action=login">Try again?</a>';
				}
			}
		}

		// An account does not exist
		if($i == 0)
		{
			echo 'Incorrect username or password. <a href="index.php?action=login">Try again?</a>';
		}
	}
	else
	{
		echo '
		<form action="index.php?action=login" method="POST" name="loginForm" id="loginForm">
			<p>TKID:</p>
			<input type="text" name="tkid" id="tkid" />

			<p>Password:</p>
			<input type="password" name="password" id="password" />

			<br /><br />

			<input type="submit" value="Login!" name="loginWithTK" />
		</form>

		<p><a href="index.php?action=forgotpassword" class="button">Forgot Your Password</a><p>
		
		<p>
			<small>
				<b>Remember:</b><br />If you are in a club other than the 501st, enter the first letter of your club, and then your TKID.
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
		if(doesTKExist(cleanInput($_POST['tkid'])))
		{
			// Is this TK ID registered?
			if(!isTKRegistered(cleanInput($_POST['tkid'])))
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
							// Query the database
							$conn->query("UPDATE troopers SET email = '".$_POST['email']."', password = '".password_hash(cleanInput($_POST['password']), PASSWORD_DEFAULT)."', squad = '".cleanInput($_POST['squad'])."' WHERE tkid = '".cleanInput($_POST['tkid'])."'");

							// Display output
							echo 'Your account has been registered. Please <a href="index.php?action=login">login</a>.';
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
				echo 'This TK ID is already registred! Please contact an admin if this issue persists.';
			}
		}
		else
		{
			echo 'This TK ID does not exist! Please contact an admin if this issue persists.';
		}
	}
	else
	{
		// Display form to register an account
		echo '
		<p style="text-align: center;">Were you already using the old trooper tracker? Set up your account by using the form below.</p>
		
		<form method="POST" action="index.php?action=setup" name="registerForm" id="registerForm">
			<p>What is your TKID:</p>
			<input type="text" name="tkid" id="tkid" />

			<p>What is your e-mail:</p>
			<input type="text" name="email" id="email" />

			<p>What do you want your password to be?</p>
			<input type="password" name="password" id="password" />

			<p>Please re-enter your password:</p>
			<input type="password" name="password2" id="password2" />
			
			<p>Squad/Club</p>
			
			<select name="squad" id="squad">
				<option value="1">Everglades Squad</option>
				<option value="2">Makaze Squad</option>
				<option value="3">Parjai Squad</option>
				<option value="4">Squad 7</option>
				<option value="5">Tampa Bay Squad</option>
				<option value="6">Rebel Legion</option>
				<option value="7">Droid Builders</option>
				<option value="8">Mandos</option>
				<option value="9">Other</option>
			</select>
			
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
		// Get data
		$query = "SELECT * FROM troopers WHERE tkid='".cleanInput($_POST['tkid'])."'";
		if ($result = mysqli_query($conn, $query))
		{
			while ($db = mysqli_fetch_object($result))
			{
				// Does the password match from what the user provided
				if($db->email == cleanInput($_POST['email']))
				{
					// Generate a new password
					$newPassword = rand(100000, 900000);
					
					// Query the database
					$conn->query("UPDATE troopers SET password = '".password_hash($newPassword, PASSWORD_DEFAULT)."' WHERE id = '".$db->id."'");
					
					// Send e-mail
					sendEmail($db->email, readTKNumber($db->tkid), "FL 501st Troop Software Password Reset", "Your new password is:\n\n" . $newPassword . "\n\nPlease change your password as soon as possible.");
					
					echo '
					<p>
						An e-mail has been sent to your inbox with your new password. Be sure to check your spam folder. If an e-mail does not appear in your inbox within ten minutes, please contact command staff for assistance.
					</p>';
				}
			}
		}
	}
	else
	{
		echo '
		<h2 class="tm-section-header">Forgot Your Password</h2>

		<form action="index.php?action=forgotpassword" method="POST" name="forgotPasswordForm" id="forgotPasswordForm">
			<p>TKID:</p>
			<input type="text" name="tkid" id="tkid" />

			<p>E-mail:</p>
			<input type="text" name="email" id="email" />

			<br /><br />

			<input type="submit" value="Submit!" name="forgotPasswordSend" />
		</form>';	
	}
}

if(isset($_POST['submitCancelTroop']))
{
	// Query the database
	$conn->query("UPDATE event_sign_up SET status = '4', reason = '".cleanInput($_POST['cancelReason'])."' WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".cleanInput($_POST['troopidC'])."'") or die($conn->error);
}

// If we are viewing an event, hide all other info
if(isset($_GET['event']))
{
	// Delete Comment
	if(isset($_POST['deleteComment']) && isAdmin())
	{
		$conn->query("DELETE FROM comments WHERE id = '".cleanInput($_POST['comment'])."'") or die($conn->error);
	}

	// Globals
	$eventClosed = 0;
	$limitedEvent = 0;
	$limitTotal = 0;
			
	// Query database for event info
	$query = "SELECT * FROM events WHERE id = '".strip_tags(addslashes($_GET['event']))."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Update globals
			$eventClosed = $db->closed;
			$limitedEvent = $db->limitedEvent;
			$limitTotal = $db->limitTotal;
			$limitTo = $db->limitTo;
					
			// Admin Area
			if(isAdmin())
			{
				echo '
				<h2 class="tm-section-header">Admin Controls</h2>
				<p style="text-align: center;"><a href="index.php?action=commandstaff&do=editevent&eid='.$db->id.'">Edit/View Event in Command Staff Area</a></p>
				<p style="text-align: center;"><a href="index.php?action=commandstaff&do=createevent&eid='.$db->id.'">Copy Event in Command Staff Area</a></p>';
			}
			
			// Format dates
			$date1 = date("m/d/Y - H:i", strtotime($db->dateStart)); 
			$date2 = date("m/d/Y - H:i", strtotime($db->dateEnd)); 
			
			// Merged troop
			$isMerged = false;
			
			// Is this merged data?
			if($db->venue == NULL && $db->numberOfAttend == NULL && $db->requestedCharacter == NULL && $db->secureChanging == NULL && $db->lightsabers == NULL && $db->parking == NULL && $db->mobility == NULL && $db->amenities == NULL && $db->referred == NULL)
			{
				echo '
				<h2 class="tm-section-header">'.$db->name.'</h2>
				<p><b>Event Date:</b> '.$date1.' ('.date('l', strtotime($db->dateStart)).')</p>
				<p><b>Comments:</b> '.ifEmpty($db->comments, "N/A").'</p>';
				
				// Set is merged
				$isMerged = true;
			}
			else
			{
				// If canceled, show user
				if($db->closed == 2)
				{
					echo '
					<div style="text-align:center; color: red; margin-top: 25px;">
						<b>This event was canceled by Command Staff. See comments for more details.</b>
					</div>';
				}
			
				// Display event info
				echo '
				<h2 class="tm-section-header">'.$db->name.'</h2>
				<p><b>Venue:</b> '.$db->venue.'</p>
				<p><b>Address:</b> <a href="https://www.google.com/maps/search/?api=1&query='.$db->location.'" target="_blank">'.$db->location.'</a></p>
				<p><b>Event Start:</b> '.$date1.' ('.date('l', strtotime($db->dateStart)).')</p>
				<p><b>Event End:</b> '.$date2.' ('.date('l', strtotime($db->dateEnd)).')</p>
				<p><b>Website:</b> '.validate_url($db->website).'</p>
				<p><b>Expected number of attendees:</b> '.number_format($db->numberOfAttend).'</p>
				<p><b>Requested number of characters:</b> '.number_format($db->requestedNumber).'</p>
				<p><b>Requested character types:</b> '.$db->requestedCharacter.'</p>
				<p><b>Secure changing/staging area:</b> '.yesNo($db->secureChanging).'</p>
				<p><b>Can troopers bring blasters:</b> '.yesNo($db->blasters).'</p>
				<p><b>Can troopers bring/carry prop like lightsabers:</b> '.yesNo($db->lightsabers).'</p>
				<p><b>Is parking available:</b> '.yesNo($db->parking).'</p>
				<p><b>Is venue accessible to those with limited mobility:</b> '.yesNo($db->mobility).'</p>
				<p><b>Amenities available at venue:</b> '.$db->amenities.'</p>
				<p><b>Comments:</b><br />'.ifEmpty(nl2br($db->comments), "No comments for this event.").'</p>
				<p><b>Referred by:</b> '.ifEmpty($db->referred, "Not available").'</p>';
			
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
				if($db->limitTotal < 500)
				{
					echo '
					<br />
					<hr />
					<br />
					
					<div style="color: red;">
						<ul>
							<li>This event is limited to '.$db->limitTotal.' troopers.</li>
							<li>This event is limited to '.convertNumber($db->limit501st, $db->limitTotal).' 501st troopers.</li>
							<li>This event is limited to '.convertNumber($db->limitRebels, $db->limitTotal).' Rebel Legion troopers.</li>
							<li>This event is limited to '.convertNumber($db->limitMando, $db->limitTotal).' Mando Merc troopers.</li>
							<li>This event is limited to '.convertNumber($db->limitDroid, $db->limitTotal).' Droid Builder troopers.</li>
						</ul>
					</div>';
				}
			}
			
			echo '
			<div id="hr1" name="hr1">
				<br />
				<hr />
				<br />
			</div>';

			// Query database for roster info
			$query2 = "SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.reason, event_sign_up.attend, event_sign_up.attended_costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, troopers.id AS trooperId, troopers.name, troopers.tkid FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".strip_tags(addslashes($_GET['event']))."' ORDER BY status";
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
						<div style="overflow-x: auto;" id="signuparea1" name="signuparea1">
						
						<form action="process.php?do=modifysignup" method="POST" name="modifysignupForm" id="modifysignupForm">
						
						<!-- Hidden variables -->
						<input type="hidden" name="modifysignupTroopIdForm" id="modifysignupTroopIdForm" value="'.$db->id.'" />
						<input type="hidden" name="limitedEventCancel" id="limitedEventCancel" value="'.$db->limitedEvent.'" />
						<input type="hidden" name="troopidC" id="troopidC" value="'.strip_tags(addslashes($_GET['event'])).'" />';
						
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

					// Allow for users to edit their status from the event, and make sure the event is not closed, and the user did not cancel
					if(loggedIn() && ($db2->trooperId == $_SESSION['id'] || $_SESSION['id'] == $db2->addedby) && $db->closed == 0)
					{
						echo '
						<tr>
							<td>
								<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>
							</td>
								
							<td>
								'.readTKNumber($db2->tkid).'
							</td>';
							
							// If not a limited event, show select boxes to change costumes
							if($db->limitedEvent != 1)
							{
								echo '
								<td name="'.$db2->trooperId.'trooperRosterCostume" id="'.$db2->trooperId.'trooperRosterCostume">
									<select name="modifysignupFormCostume" id="modifysignupFormCostume" trooperid="'.$db2->trooperId.'">';

									$query3 = "SELECT * FROM costumes";
									
									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
									}
									
									$query3 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler'".getMyCostumes(getTKNumber($db2->trooperId)).") DESC, costume";
									
									if ($result3 = mysqli_query($conn, $query3))
									{
										while ($db3 = mysqli_fetch_object($result3))
										{
											if($db2->costume == $db3->id)
											{
												// If this is the selected costume, make it selected
												echo '
												<option value="'. $db3->id .'" SELECTED>'.$db3->costume.'</option>';
											}
											else
											{
												// Default
												echo '
												<option value="'. $db3->id .'">'.$db3->costume.'</option>';
											}
										}
									}

									echo '
									</select>
								</td>
								
								<td name="'.$db2->trooperId.'trooperRosterBackup" id="'.$db2->trooperId.'trooperRosterBackup">
									<select name="modiftybackupcostumeForm" id="modiftybackupcostumeForm" trooperid="'.$db2->trooperId.'">';

									// Display costumes
									$query3 = "SELECT * FROM costumes";
									
									// If limited to certain costumes, only show certain costumes...
									if($db->limitTo < 4)
									{
										$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
									}
									
									$query3 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler'".getMyCostumes(getTKNumber($db2->trooperId)).") DESC, costume";
									
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
							
								if($db->limitedEvent != 1)
								{
									echo '
									<select name="modifysignupStatusForm" id="modifysignupStatusForm" trooperid="'.$db2->trooperId.'">
										<option value="0" '.echoSelect(0, $db2->status).'>I\'ll be there!</option>
										<option value="1" '.echoSelect(1, $db2->status).'>Tentative</option>
										<option value="4" '.echoSelect(4, $db2->status).'>Cancel</option>
									</select>';
								}
								else
								{
									if($db2->status == 5)
									{
										echo '
										(Pending Command Staff Approval)';
									}
									else
									{
										echo getStatus($db2->status);
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
								<a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a>
							</td>
								
							<td>
								'.readTKNumber($db2->tkid).'
							</td>
							
							<td>
								'.ifEmpty(getCostume($db2->costume), "N/A").'
							</td>
							
							<td>
								'.ifEmpty(getCostume($db2->costume_backup), "N/A").'
							</td>
							
							<td id="'.$db2->trooperId.'Status">
								'.getStatus($db2->status).'
							</td>
						</tr>';
					}

					$i++;
				}
			}

			if($i == 0)
			{
				echo '
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

			echo '<hr />';

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
					if($eventCheck['inTroop'] == 1)
					{
						if($eventCheck['status'] == 4)
						{
							$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".strip_tags(addslashes($_GET['event']))."' AND status != '4'");
							
							echo '
							<div name="signeduparea" id="signeduparea">
								<p>
									<b>You have canceled this troop.</b>
								</p>
							</div>';
						}
						else
						{
							if($db->closed == 0)
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
								echo '
								<p>This event is closed for editing.</p>';
							}
						}
					}
					else
					{
						// Sign up area
						echo '
						<div name="signuparea" id="signuparea">
							<h2 class="tm-section-header">Sign Up</h2>';
								
						// Check to see if this event is full
						$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".$db->id."' AND status != '4'");
						
						if($db->closed == 0)
						{
							if(hasPermission(0, 1, 2, 3))
							{
								if($getNumOfTroopers->num_rows < $db->limitTotal)
								{
									if($db->limitedEvent == 1)
									{
										echo '<b>This is a locked event. When you sign up, you will be placed in a pending status until command staff approves you. Please check for updates.</b>';
									}
									
									echo '
										<form action="process.php?do=signup" method="POST" name="signupForm2" id="signupForm2">
											<input type="hidden" name="event" value="'.cleanInput($_GET["event"]).'" />
											
											<p>What costume will you wear?</p>
											<select name="costume">
												<option value="null" SELECTED>Please choose an option...</option>';

											$query3 = "SELECT * FROM costumes";
											
											// If limited to certain costumes, only show certain costumes...
											if($db->limitTo < 4)
											{
												$query3 .= " WHERE era = '".$db->limitTo."' OR era = '4'";
											}
											
											$query3 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler'".getMyCostumes(getTKNumber($_SESSION['id'])).") DESC, costume";
											
											echo $query3;
											
											if ($result3 = mysqli_query($conn, $query3))
											{
												while ($db3 = mysqli_fetch_object($result3))
												{
													echo '
													<option value="'. $db3->id .'">'.$db3->costume.'</option>';
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
													<option value="1">Tentative</option>';
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
											
											$query2 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler'".getMyCostumes(getTKNumber($_SESSION['id'])).") DESC, costume";
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
									This event is full.';
								}
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
			else
			{
				// If not merged and not logged in
				if(!$isMerged)
				{
					echo '
					<br />
					<b>You must <a href="index.php?action=login">login</a> to sign up for a troop.</b>
					<br /><br />';
				}
				else
				{
					// Is merged
					echo '
					<br />
					<b>You are unable to sign up for this event.</b>
					<br /><br />';
				}
			}
		}
		
		// Don't show photos, if merged data
		if(!$isMerged)
		{
			echo '
			<hr />
			<h2 class="tm-section-header">Photos</h2>';
			
			// Query database for photos
			$query = "SELECT * FROM uploads WHERE troopid = '".cleanInput($_GET['event'])."' AND admin = '0' ORDER BY date DESC";
			
			// Count photos
			$i = 0;
			$j = 0;
			
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<a href="images/uploads/'.$db->filename.'" data-lightbox="photos" data-title="Uploaded by '.getName($db->trooperid).'" id="photo'.$db->id.'"><img src="images/uploads/'.$db->filename.'" width="200px" height="200px" /></a>';
					
					// If owned by trooper
					if(loggedIn() && ($db->trooperid == $_SESSION['id'] || isAdmin()))
					{
						echo '<a href="process.php?do=deletephoto&id='.$db->id.'" name="deletephoto" photoid="'.$db->id.'">Delete</a>';
					}
					
					$i++;
				}
			}
			
			// No photos found
			if($i == 0)
			{
				echo '
				<b>There are no photos to display.</b>';
			}
			
			// If trooper logged in show uploader
			if(loggedIn())
			{
				echo '
				<p>
					<form action="script/php/upload.php" class="dropzone" id="photoupload">
						<input type="hidden" name="troopid" value="'.cleanInput($_GET['event']).'" />
						<input type="hidden" name="trooperid" value="'.$_SESSION['id'].'" />
					</form>
				</p>';
			}
		}

		echo '
		<hr />';

		if(loggedIn() && !$isMerged)
		{	
			// Check to see if this event is full
			$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".cleanInput($_GET['event'])."' AND status != '4'");
			
			if($eventClosed == 0)
			{
				if(hasPermission(0, 1, 2, 3))
				{
					if($getNumOfTroopers->num_rows < $limitTotal)
					{
						if($limitedEvent == 1)
						{
							echo '<b>This is a locked event. When you sign up, you will be placed in a pending status until command staff approves you. Please check for updates.</b>';
						}
						
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
						
						echo '
						<h2 class="tm-section-header">Add a Friend</h2>';
						
						echo '
						<form action="process.php?do=signup" method="POST" name="signupForm3" id="signupForm3">
							<input type="hidden" name="event" value="'.cleanInput($_GET["event"]).'" />';
								
						// Load all users
						$query = "SELECT troopers.id AS troopida, troopers.name AS troopername, troopers.tkid FROM troopers WHERE NOT EXISTS (SELECT event_sign_up.trooperid FROM event_sign_up WHERE event_sign_up.trooperid = troopers.id AND event_sign_up.troopid = '".cleanInput($_GET['event'])."') ORDER BY troopers.name";

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
										
										<br />
										
										Trooper Search: <input type="text" name="trooperSearch" id="trooperSearch" style="width: 50%;" />

										<p>Select a trooper to add:</p>
										<select name="trooperSelect" id="trooperSelect">';
								}
								
								// Get TKID
								$tkid = readTKNumber($db->tkid);

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
						<select name="costume">
							<option value="null" SELECTED>Please choose an option...</option>';

						$query3 = "SELECT * FROM costumes";
						
						// If limited to certain costumes, only show certain costumes...
						if($limitTo < 4)
						{
							$query3 .= " WHERE era = '".$limitTo."' OR era = '4'";
						}
						
						$query3 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler') DESC, costume";
						
						if ($result3 = mysqli_query($conn, $query3))
						{
							while ($db3 = mysqli_fetch_object($result3))
							{
								echo '
								<option value="'. $db3->id .'">'.$db3->costume.'</option>';
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
							<option value="1">Tentative</option>';
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
						
						$query2 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler') DESC, costume";
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
						This event is full.';
					}
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

				<h2 class="tm-section-header">Comments</h2>
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

			// Query database for event info
			$query = "SELECT * FROM comments WHERE troopid = '".cleanInput($_GET['event'])."' ORDER BY posted DESC";
			// Count comments
			$i = 0;
			if ($result = mysqli_query($conn, $query))
			{
				while ($db = mysqli_fetch_object($result))
				{
					echo '
					<div style="overflow-x: auto;" style="text-align: center;">
					<table border="1" name="comment_'.$db->id.'" id="comment_'.$db->id.'">';

					echo '
					<tr>
						<td><a href="index.php?profile='.$db->trooperid.'">'.getName($db->trooperid).' - '.getTKNumber($db->trooperid).'</a></td>
					</tr>';

					if(isAdmin())
					{
						echo '
						<tr>
							<td><a href="#" id="deleteComment_'.$db->id.'" name="'.$db->id.'" class="button">Delete Comment</a></td>
						</tr>';
					}

					echo '
					<tr>
						<td>'.$db->posted.'</td>
					</tr>

					<tr>
						<td>'.isImportant($db->important, $db->comment).'</td>
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
				<b>No comments to display.</b>';
			}
		}
		else
		{
			if(!$isMerged)
			{
				echo '
				<br />
				<b>You must <a href="index.php?action=login">login</a> to view comments.</b>';
			}
			else
			{
				echo '
				<br />
				<b>You are unable to comment on this event.</b>';
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
					<p style="text-align: center;">Welcome to the Florida Garrison troop tracker!<br /><br /><a href="index.php?action=requestaccess">Are you new to the Florida Garrison and/or 501st? Click here.</a><br /><br /><a href="index.php?action=setup">Have you used the old troop tracker and need to set up your account? Click here.</a></p>';
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

				<a href="index.php"><img src="images/garrison_emblem.png" alt="Florida Garrison Troops" '.isSquadActive(0).' /></a> <a href="index.php?squad=1"><img src="images/everglades_emblem.png" alt="Everglades Squad Troops" '.isSquadActive(1).' /></a> <a href="index.php?squad=2"><img src="images/makaze_emblem.png" alt="Makaze Squad Troops" '.isSquadActive(2).' /></a> <a href="index.php?squad=3"><img src="images/parjai_emblem.png" alt="Parjai Squad Troops" '.isSquadActive(3).' /></a> <a href="index.php?squad=4"><img src="images/squad7_emblem.png" alt="Squad 7 Troops" '.isSquadActive(4).' /></a> <a href="index.php?squad=5"><img src="images/tampabay_emblem.png" alt="Tampa Bay Squad Troops" '.isSquadActive(5).' /></a>
				<p style="text-align: center;">
				<a href="index.php?squad=mytroops" class="button">My Troops</a>
				</p>

				<hr /><br />

				<div style="text-align: center;">';

				// Was a squad defined? (Prevents displays div when not needed)
				if(isset($_GET['squad']) && $_GET['squad'] == "mytroops")
				{
					// Query
					$query = "SELECT events.id AS id, events.name, events.dateStart, events.dateEnd, events.squad, events.limitTotal, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd < NOW() AND attend = 0 AND events.closed = 0";
				}
				else if(isset($_GET['squad']))
				{
					// Query
					$query = "SELECT * FROM events WHERE dateStart >= CURDATE() AND squad = '".cleanInput($_GET['squad'])."' AND closed = '0' ORDER BY dateStart";
				}
				else
				{
					// Query
					$query = "SELECT * FROM events WHERE dateStart >= CURDATE() AND closed = '0' ORDER BY dateStart";
				}

				// Number of events loaded
				$i = 0;
				// Number of squad events loaded
				$i2 = 0;

				// Load events that are today or in the future
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						// Get number of troopers at event
						$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".$db->id."' AND status != '4'");

						echo '<div style="border: 1px solid gray; margin-bottom: 10px;">';

						// No squad set
						if(!isset($_GET['squad']))
						{
							echo '<a href="index.php?event=' . $db->id . '">' .date('M d, Y', strtotime($db->dateStart)). ''.'<br />' . $db->name . '</a>';

							// If not enough troopers
							if($getNumOfTroopers->num_rows <= 1)
							{
								echo '<br /><span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS EVENT!</b></span>';
							}
							
							// If full
							if($getNumOfTroopers->num_rows >= $db->limitTotal)
							{
								echo '<br /><span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';
							}

							$i++;
						}
						else if(isset($_GET['squad']) && $_GET['squad'] == "mytroops")
						{
							echo '<a href="index.php?event=' . $db->id . '">' .date('M d, Y', strtotime($db->dateStart)). ''.'<br />' . $db->name . '</a>';

							// If not enough troopers...
							if($getNumOfTroopers->num_rows <= 1)
							{
								echo '<br /><span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS EVENT!</b></span>';
							}
							
							// If full
							if($getNumOfTroopers->num_rows >= $db->limitTotal)
							{
								echo '<br /><span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';
							}

							$i2++;
						}
						else
						{
							// Squad set
							if($db->squad == cleanInput($_GET['squad']))
							{
								echo '<a href="index.php?event=' . $db->id . '">' .date('M d, Y', strtotime($db->dateStart)). ''.'<br />' . $db->name . '</a>';

								// If not enough troopers...
								if($getNumOfTroopers->num_rows <= 1)
								{
									echo '<br /><span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS EVENT!</b></span>';
								}
								
								// If full
								if($getNumOfTroopers->num_rows >= $db->limitTotal)
								{
									echo '<br /><span style="color:green;"><b>THIS TROOP IS FULL!</b></span>';
								}

								$i2++;
							}
						}

						echo '</div>';
					}
				}

				// If squad pressed
				if(isset($_GET['squad']))
				{
					if($i2 == 0)
					{
						echo 'There are no events to display.';
					}
				}
				else
				{
					// Home page, no events
					if($i == 0)
					{
						echo 'There are no events to display.';
					}			
				}

				echo '
				</div>';
			}

			if(loggedIn())
			{
				// Load events that need confirmation
				$query = "SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd < NOW() AND attend = 0 AND events.closed = 1";

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
							<h2 class="tm-section-header">Confirm Troops</h2>
							<form action="process.php?do=confirmList" method="POST" name="confirmListForm" id="confirmListForm">
							<div name="confirmArea2" id="confirmArea2">';
						}

						echo '
						<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">
							<input type="checkbox" name="confirmList[]" id="confirmList_'.$db->eventId.'" value="'.$db->eventId.'" /> '.$db->name.'<br /><br />
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

						$query3 = "SELECT * FROM costumes";
						
						// If limited to certain costumes, only show certain costumes...
						if($limitTo < 4)
						{
							$query3 .= " WHERE era = '".$limitTo."' OR era = '4'";
						}
						
						$query3 .= " ORDER BY FIELD(costume, 'N/A', 'Command Staff', 'Handler'".getMyCostumes(getTKNumber($_SESSION['id'])).") DESC, costume";
						
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

			echo '<a href="index.php?profile='.$db->id.'">' . readTKNumber($db->tkid) . '</a>';

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
Website created and maintained by Matthew Drennan (TK52233). If you encounter any issues with this site, please
<a href="mailto:drennanmattheww@gmail.com" class="tm-contact-link">email</a> me here.
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