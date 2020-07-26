<?php

// Include config file
include 'config.php';

// Include Scripts
echo '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<title>501st Florida Garrison - Troop Tracker</title>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap" rel="stylesheet" />
	<link href="fontawesome/css/all.min.css" rel="stylesheet" />
	<link href="css/main.css" rel="stylesheet" />
	<!-- Style Sheets -->
	<link rel="stylesheet" href="script/lib/jquery-ui.min.css">
	<link rel="stylesheet" href="script/lib/jquery-ui-timepicker-addon.css">
	<link rel="stylesheet" href="css/nav.css">
	<!--<link rel="stylesheet" href="script/js/validate/validate.css">-->

	<!-- JQUERY -->
	<script src="script/lib/jquery-3.4.1.min.js"></script>

	<!-- JQUERY UI -->
	<script src="script/lib/jquery-ui.min.js"></script>

	<!-- Addons -->
	<script src="script/lib/jquery-ui-timepicker-addon.js"></script>
	<script src="script/js/validate/jquery.validate.min.js"></script>
	<script src="script/js/validate/validate.js"></script>
	<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validation-unobtrusive/3.2.11/jquery.validate.unobtrusive.min.js"></script>-->

	<script>
	$( function() {
		$("#datepicker").datetimepicker();
		$("#datepicker2").datetimepicker();
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
<a href="index.php" '.isPageActive("home").'>Home</a>
<a href="index.php?action=trooptracker" '.isPageActive("trooptracker").'>Troop Tracker</a>';

// If not logged in
if(!loggedIn())
{
	echo '
	<a href="index.php?action=requestaccess" '.isPageActive("requestaccess").'>Request Access</a>
	<a href="index.php?action=setup" '.isPageActive("setup").'>Account Setup</a>
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
	echo '
	<h2 class="tm-section-header">Manage Account</h2>

	<a href="#" id="unsubscribeLink" class="button">Unsubscribe From E-mail</a> 
	<a href="#" id="changeemailLink" class="button">Change E-mail</a> 
	<a href="#" id="changephoneLink" class="button">Change Phone</a> 
	<a href="#" id="changenameLink" class="button">Change Name</a> 
	<a href="#" id="changepasswordLink" class="button">Change Password</a>
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
if(isset($_GET['action']) && $_GET['action'] == "requestaccess")
{
	echo '
	<h2 class="tm-section-header">Request Access</h2>
	<div name="requestAccessFormArea" id="requestAccessFormArea">
		<form action="process.php?do=requestaccess" name="requestAccessForm" id="requestAccessForm" method="POST">
			First & Last Name: <input type="text" name="name" id="name" />
			<br /><br />
			TKID: <input type="text" name="tkid" id="tkid" />
			<p><i>Non-501st clubs, please enter an ID number of your choosing.</i></p>
			E-mail: <input type="text" name="email" id="email" />
			<br /><br />
			Phone (Optional): <input type="text" name="phone" id="phone" />
			<br /><br />
			Password: <input type="password" name="password" id="password" />
			<br /><br />
			Password (Confirm): <input type="password" name="passwordC" id="passwordC" />
			<br /><br />
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
	$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, event_sign_up.attend, event_sign_up.attended_costume, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd, troopers.id, troopers.name, troopers.tkid FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopers.id = '".cleanInput($_GET['profile'])."' AND events.closed = '1' ORDER BY events.dateEnd";
	$i = 0;
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			if($i == 0)
			{
				echo '
				<h2 class="tm-section-header">'.$db->name.' - '.readTKNumber($db->tkid).'</h2>

				<div style="overflow-x: auto;">
				<table border="1">
				<tr>
					<th>Event Name</th>	<th>Signed Up With Costume</th>	<th>Attended Costume</th>
				</tr>';
			}

			// If multiple days
			$date1 = date('Y-m-d H:i:s', strtotime($db->dateStart));
			$date2 = date('Y-m-d H:i:s', strtotime($db->dateEnd));

			$days = getDatesFromRange($date1, $date2);

			if($days > 1)
			{
				// Multiple day query
				$query2 = "SELECT shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid, shift_trooper.attend, shift_trooper.costume FROM shift_trooper WHERE shift_trooper.trooperid = '".cleanInput($_GET['profile'])."' AND shift_trooper.troopid = '".$db->eventId."'";

				if ($result2 = mysqli_query($conn, $query2))
				{
					while ($db2 = mysqli_fetch_object($result2))
					{

						$shiftString = explode(",", substr($db2->attend, 3));
						$shiftStringCostume = explode(",", substr($db2->costume, 3));
						$l = 0;

						for($n = 0; $n <= count($shiftString) - 1; $n += 2)
						{
							$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

							$shift = mysqli_fetch_array($shiftGet);

							// Convert times
							$readTime1 = date('h:i A', strtotime($shift[1]));
							$readTime2 = date('h:i A', strtotime($shift[2]));

							echo '
							<tr>
								<td><a href="index.php?event='.$db->troopid.'">'.$db->eventName.' ['.$days[$shiftString[$n + 1]].' '.$readTime1.'-'.$readTime2.']</a></td>';

							echo '
								<td>'.getCostume($db->costume).'</td>	<td>'.getCostume($shiftStringCostume[$l + $n + 2]).'</td>
							</tr>';

							// Increment i, l
							$i++;
							$l++;
						}
					}
				}
			}
			else
			{
				echo '
				<tr>
					<td><a href="index.php?event='.$db->troopid.'">'.$db->eventName.'</a></td>';

				echo '
					<td>'.getCostume($db->costume).'</td>	<td>'.getCostume($db->attended_costume).'</td>
				</tr>';

				// Increment i
				$i++;
			}
		}
	}

	if($i == 0)
	{
		echo '<b>Nothing to show yet!</b>';
	}
	else
	{
		$troops_get = $conn->query("SELECT id FROM event_sign_up WHERE attend = '1' AND trooperid = '".cleanInput($_GET['profile'])."'") or die($conn->error);
		$j = 0;

		echo '
		</table>
		</div>

		<br />

		<b>Total Finished Troops:</b> ' . $i . '

		<br />

		<h2 class="tm-section-header">Awards</h2>
		<ul>';

		if($troops_get->num_rows >= 1)
		{
			echo '<li>First Troop Completed!</li>';
			$j++;
		}

		if($troops_get->num_rows >= 10)
		{
			echo '<li>10 Troops</li>';
		}

		if($troops_get->num_rows >= 25)
		{
			echo '<li>25 Troops</li>';
		}

		if($troops_get->num_rows >= 50)
		{
			echo '<li>50 Troops</li>';
		}

		if($troops_get->num_rows >= 75)
		{
			echo '<li>75 Troops</li>';
		}

		if($troops_get->num_rows >= 100)
		{
			echo '<li>100 Troops</li>';
		}

		if($troops_get->num_rows >= 150)
		{
			echo '<li>150 Troops</li>';
		}

		if($troops_get->num_rows >= 200)
		{
			echo '<li>200 Troops</li>';
		}

		if($troops_get->num_rows >= 250)
		{
			echo '<li>250 Troops</li>';
		}

		if($troops_get->num_rows >= 300)
		{
			echo '<li>300 Troops</li>';
		}

		if($troops_get->num_rows >= 400)
		{
			echo '<li>400 Troops</li>';
		}

		if($troops_get->num_rows >= 500)
		{
			echo '<li>500 Troops</li>';
		}

		if($troops_get->num_rows >= 501)
		{
			echo '<li>Mr./Ms. 501 Award</li>';
		}

		// Get data from custom awards
		$query2 = "SELECT * FROM awards WHERE trooperid = '".cleanInput($_GET['profile'])."'";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				echo '<li>'.$db2->title.'</li>';
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
}

// Show the search page
if(isset($_GET['action']) && $_GET['action'] == "search")
{
	echo '
	<h2 class="tm-section-header">Search</h2>
	<div name="searchForm" id="searchForm">
		<form action="index.php?action=search" method="POST">
			Search Troop Name: <input type="text" name="searchName" id="searchName" value="'.cleanInput($_POST['searchName']).'" />
			<br /><br />
			Date Start: <input type="text" name="dateStart" id="datepicker" value="'.cleanInput($_POST['dateStart']).'" />
			<br /><br />
			Date End: <input type="text" name="dateEnd" id="datepicker2" value="'.cleanInput($_POST['dateEnd']).'" />
			<br /><br />
			Search TKID: <input type="text" name="tkID" id="tkID" value="'.cleanInput($_POST['tkID']).'" />
			<br /><br />
			<input type="submit" name="submitSearch" id="submitSearch" value="Search!" />
		</form>
	</div>';

	$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.attended_costume, event_sign_up.status, event_sign_up.attend, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE";

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
		if(strlen($_POST['dateEnd']) > 0)
		{
			$query .= " AND";
		}

		$date = strtotime(cleanInput($_POST['dateEnd']));
		$dateF = date('Y-m-d H:i:s', $date);

		$query .= " events.dateEnd <= '".$dateF."'";
	}

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
					<th>Event Name</th>	<th>Trooper TKID</th>	<th>Signed Up With Costume</th>	<th>Attended Costume</th>	<th>Attended</th>
				</tr>';
			}

			echo '
			<tr>
				<td><a href="index.php?event='.$db->troopid.'">'.$db->eventName.'</a></td>	<td><a href="index.php?profile='.$db->trooperid.'">'.getTKNumber($db->trooperid).'</a></td>	<td>'.getCostume($db->costume).'</td>	<td>'.getCostume($db->attended_costume).'</td>	<td>'.didAttend($db->attend).'</td>
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

// Show the troop tracker page
if(isset($_GET['action']) && $_GET['action'] == "trooptracker")
{
	// If logged in
	if(loggedIn())
	{
		echo '
		<h2 class="tm-section-header">My Stats</h2>';

		// Get data
		$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, event_sign_up.attend, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND attend = 1 AND events.closed = '1' ORDER BY events.dateEnd";
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

				// Output data
				$date1 = new DateTime($db->dateStart);
				$date2 = new DateTime($db->dateEnd);
				$getDiff = $date1->diff($date2);
				$time = $getDiff->days * 24 * 60;
				$time += $getDiff->h * 60;
				$time += $getDiff->i;

				// How many shifts
				$shift_get = $conn->query("SELECT * FROM shift_trooper WHERE trooperid = '".$_SESSION['id']."' AND troopid = '".$db->eventId."'") or die($conn->error);

				// Conversion

				$shifts = mysqli_fetch_array($shift_get);

				$shifts = explode(",", $shifts['attend']);

				$shifts = (count($shifts)) - 1;

				// End conversion

				echo '
				<tr>
					<td><a href="index.php?event='.$db->eventId.'">'.$db->eventName.'</a></td>	<td>'.getCostume($db->costume).'</td>	<td>$'.$db->moneyRaised.'</td>	<td>'.floor($time/60).'H '.($time % 60).'M</td>
				</tr>';


				if($shifts > 0)
				{
					$troopsAttended += $shifts;
				}
				else
				{
					$troopsAttended++;
				}
				$moneyRaised += $db->moneyRaised;
				$timeSpent += $time;
				$i++;
			}
		}

		if($i > 0)
		{
			// How many troops did the user attend
			$favoriteCostume_get = $conn->query("SELECT costume, COUNT(*) FROM event_sign_up WHERE trooperid = '".$_SESSION['id']."' GROUP BY costume ORDER BY costume DESC LIMIT 1") or die($conn->error);

			$favoriteCostume = mysqli_fetch_array($favoriteCostume_get);

			echo '
			</table>
			</div>

			<p><b>Favorite Costume:</b> '.getCostume($favoriteCostume['costume']).'</p>
			<p><b>Attended:</b> '.$troopsAttended.'</p>
			<p><b>Money Raised:</b> $'.$moneyRaised.'</p>
			<p><b>Time Spent:</b> '.floor($timeSpent/60).'H '.($timeSpent % 60).'</p>';
		}
		else
		{
			// No troops attended
			echo '
			<p><b>You have not attended any troops. Get out there and troop!</b></p>';
		}
	}

	// Show troop tracker for everyone
	echo '
	<h2 class="tm-section-header">Troop Tracker</h2>';

	// Get data
	$query = "SELECT event_sign_up.trooperid, event_sign_up.troopid, event_sign_up.costume, event_sign_up.status, event_sign_up.attend, events.name AS eventName, events.id AS eventId, events.moneyRaised, events.dateStart, events.dateEnd FROM events LEFT JOIN event_sign_up ON events.id = event_sign_up.troopid WHERE events.closed = '1' GROUP BY events.id ORDER BY events.dateEnd LIMIT 20";
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
			$trooperCount_get = $conn->query("SELECT id, COUNT(*) FROM event_sign_up WHERE troopid = '".$db->troopid."' AND attend = '1'") or die($conn->error);

			echo '
			<tr>
				<td><a href="index.php?event='.$db->eventId.'">'.$db->eventName.'</a></td>	<td>'.$trooperCount_get->num_rows.'</td>	<td>$'.$db->moneyRaised.'</td>	<td>'.floor($time/60).'H '.($time % 60).'M</td>
			</tr>';

			$i++;
		}
	}

	if($i > 0)
	{
		// How many troops did the user attend
		$favoriteCostume_get = $conn->query("SELECT costume, COUNT(*) FROM event_sign_up GROUP BY costume ORDER BY costume DESC LIMIT 1") or die($conn->error);
		$favoriteCostume = mysqli_fetch_array($favoriteCostume_get);

		// How many troops did the user attend
		$attended_get = $conn->query("SELECT id FROM event_sign_up WHERE attend = '1'") or die($conn->error);

		// How many regular troops
		$regular_get = $conn->query("SELECT id FROM events WHERE label = '0'") or die($conn->error);
		// How many regular troops
		$charity_get = $conn->query("SELECT id FROM events WHERE label = '1'") or die($conn->error);
		// How many regular troops
		$pr_get = $conn->query("SELECT id FROM events WHERE label = '2'") or die($conn->error);
		// How many regular troops
		$disney_get = $conn->query("SELECT id FROM events WHERE label = '3'") or die($conn->error);
		// How many regular troops
		$convention_get = $conn->query("SELECT id FROM events WHERE label = '4'") or die($conn->error);
		// How many regular troops
		$wedding_get = $conn->query("SELECT id FROM events WHERE label = '5'") or die($conn->error);
		// How many regular troops
		$birthday_get = $conn->query("SELECT id FROM events WHERE label = '6'") or die($conn->error);
		// How many regular troops
		$other_get = $conn->query("SELECT id FROM events WHERE label = '7'") or die($conn->error);

		echo '
		</table>
		</div>

		<p><b>Favorite Costume:</b> '.getCostume($favoriteCostume['costume']).'</p>
		<p><b>Volunteers at Troops:</b> '.$attended_get->num_rows.'</p>
		<p><b>Money Raised:</b> $'.$moneyRaised.'</p>
		<p><b>Time Spent:</b> '.floor($timeSpent/60).'H '.($timeSpent % 60).'M</p>
		<p><b>Regular Troops:</b> '.$regular_get->num_rows.'</p>
		<p><b>Charity Troops:</b> '.$charity_get->num_rows.'</p>
		<p><b>PR Troops:</b> '.$pr_get->num_rows.'</p>
		<p><b>Disney Troops:</b> '.$disney_get->num_rows.'</p>
		<p><b>Convention Troops:</b> '.$convention_get->num_rows.'</p>
		<p><b>Wedding Troops:</b> '.$wedding_get->num_rows.'</p>
		<p><b>Birthday Troops:</b> '.$birthday_get->num_rows.'</p>
		<p><b>Other Troops:</b> '.$other_get->num_rows.'</p>
		<p><b>Total Finished Troops:</b> '.$i.'</p>';
	}
	else
	{
		// No troops attended
		echo '
		<p><b>No one has attended a troop!</b></p>';
	}

	echo '
	<hr />
	<h2 class="tm-section-header">Search</h2>
	<div name="searchForm" id="searchForm">
		<form action="index.php?action=search" method="POST">
			Search Troop Name: <input type="text" name="searchName" id="searchName" />
			<br /><br />
			Date Start: <input type="text" name="dateStart" id="datepicker" />
			<br /><br />
			Date End: <input type="text" name="dateEnd" id="datepicker2" />
			<br /><br />
			Search TKID: <input type="text" name="tkID" id="tkID" />
			<br /><br />
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
		<a href="index.php?action=commandstaff&do=createuser" class="button">Create Trooper</a> 
		<a href="index.php?action=commandstaff&do=managetroopers" class="button">Manage Troopers</a> 
		<a href="index.php?action=commandstaff&do=approvetroopers" class="button">Approve Trooper Requests - ('.$getTrooperNotifications->num_rows.')</a> 
		<a href="index.php?action=commandstaff&do=assignawards" class="button">Assign Awards</a>
		</p>

';

		// Assign an award to users
		if(isset($_GET['do']) && $_GET['do'] == "assignawards")
		{
			// Get data
			$query = "SELECT * FROM troopers WHERE approved = 1 ORDER BY tkid";

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
						<h3>Assign Awards</h3>

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

				<br /><br />

				<input type="text" name="awardtitle" id="awardtitle" />

				<input type="submit" name="award" id="award" value="Award!" />

				</form>';
			}

			// Get data
			$query = "SELECT * FROM awards WHERE trooperid = '".$getId."' ORDER BY awarded";

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
		}

		// Update an event form
		if(isset($_GET['do']) && $_GET['do'] == "editevent")
		{
			echo '
			<h3>Edit an Event</h3>';

			// Get data
			$query = "SELECT * FROM events ORDER BY dateStart LIMIT 200";

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

					echo '<option value="'.$db->id.'">'.$db->name.'</option>';

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

				<br /><br />

				<input type="submit" name="submitDelete" id="submitDelete" value="Delete" /> <input type="submit" name="submitCancel" id="submitCancel" value="Mark Canceled" /> <input type="submit" name="submitFinish" id="submitFinish" value="Mark Finished" /> <input type="submit" name="submitEdit" id="submitEdit" value="Edit" /> <input type="submit" name="submitRoster" id="submitRoster" value="Roster" />

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
							<option value="7">Other</option>
						</select>

						<p>Do you wish for command staff to manually select people for this event?</p>
						<select name="limitedEvent" id="limitedEvent">
							<option value="null" SELECTED>Please choose an option...</option>
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>

						<p>Do you wish to limit the era of the costume?</p>
						<select name="era" id="era">
							<option value="0" SELECTED>No</option>
							<option value="1">Prequel</option>
							<option value="2">Original</option>
							<option value="3">First Order</option>
							<option value="4">Clone Wars</option>
							<option value="5">Rebels</option>
							<option value="6">Expanded Universe</option>
						</select>

						<p>Limit of 501st Troopers:</p>
						<input type="number" name="limit501st" value="9999" id="limit501st" />

						<p>Limit of Rebels:</p>
						<input type="number" name="limitRebels" value="9999" id="limitRebels" />

						<p>Limit of Mandos:</p>
						<input type="number" name="limitMando" value="9999" id="limitMando" />

						<p>Limit of Droid Builders:</p>
						<input type="number" name="limitDroid" value="9999" id="limitDroid" />

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
						<form action="process.php?do=approvetroopers" method="POST" name="approveTroopers" id="approveTroopers">

						<select name="userID" id="userID">';
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

				<input type="submit" name="submitDenyUser" id="submitDenyUser" value="Deny" /> <input type="submit" name="submitApproveUser" id="submitApproveUser" value="Approve" />
				</form>

				<div style="overflow-x: auto;">
				<table border="1">
				<tr>
					<th>Name</th>	<th>E-mail</th>	<th>Phone</th>	<th>Squad</th>	<th>TKID</th>
				</tr>';

				// Get data
				$query = "SELECT * FROM troopers WHERE id = '".$getId."'";
				if ($result = mysqli_query($conn, $query))
				{
					while ($db = mysqli_fetch_object($result))
					{
						echo '
						<tr>
							<td id="nameTable">'.$db->name.'</td>	<td id="emailTable">'.$db->email.'</td>	<td id="phoneTable">'.ifEmpty($db->phone).'</td>	<td id="squadTable">'.$db->squad.'</td>	<td id="tkTable">'.$db->tkid.'</td>
						</tr>';
					}
				}

				echo '
				</table>
				</div>';
			}
		}

		// Manage users
		if(isset($_GET['do']) && $_GET['do'] == "managetroopers")
		{
			echo '
			<h3>Manage Troopers</h3>';

			// Get data
			$query = "SELECT * FROM troopers ORDER BY tkid";

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

						<p>Squad:</p>
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
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select>

						<p>TKID:</p>
						<input type="text" name="tkid" id="tkid" />

						<input type="submit" name="submitUserEdit" id="submitUserEdit" value="Edit!" />
					</form>
				</div>';
			}
		}

		// Create a user
		if(isset($_GET['do']) && $_GET['do'] == "createuser")
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

				<p>Squad:</p>
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
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>

				<p>TKID:</p>
				<input type="text" name="tkid" id="tkid" />

				<br /><br />

				<input type="submit" name="submitUser" value="Create!" />
			</form>';
		}

		// Create an event form
		if(isset($_GET['do']) && $_GET['do'] == "createevent")
		{
			// JQUERY Easy Form Filler
			echo '
			<p>Easy Fill Tool:</p>
			<form action="index.php?action=commandstaff&do=createevent" method="POST" name="easyFillTool" id="easyFillTool">
				<textarea rows="10" cols="50" name="easyFill" id="easyFill"></textarea>
				<br />
				<input type="submit" name="submit" name="easyFillButton" id="easyFillButton" value="Fill!" />
			</form>

			<p><i>Make sure there is a ":" in the time for both datetime values</i></p>';

			// Display create event form
			echo '
			<h3>Create an Event</h3>

			<form action="process.php?do=createevent" id="createEventForm" name="createEventForm" method="POST">
				<p>Name of the event:</p>
				<input type="text" name="eventName" id="eventName" />

				<p>Venue of the event:</p>
				<input type="text" name="eventVenue" id="eventVenue" />

				<p>Location:</p>
				<input type="text" name="location" id="location" />

				<p>Date/Time Start:</p>
				<input type="text" name="dateStart" id="datepicker" />

				<p>Date/Time End:</p>
				<input type="text" name="dateEnd" id="datepicker2" />

				<div id="shiftsetup" name="shiftsetup" style="display: none;">
				<p>Shift Setup (Required for multi day troops):</p>
				<select name="time1a" id="time1a">
					<option value="" SELECTED>Please choose an option...</option>
					<option value="00:00:00">12AM</option>
					<option value="01:00:00">1AM</option>
					<option value="02:00:00">2AM</option>
					<option value="03:00:00">3AM</option>
					<option value="04:00:00">4AM</option>
					<option value="05:00:00">5AM</option>
					<option value="06:00:00">6AM</option>
					<option value="07:00:00">7AM</option>
					<option value="08:00:00">8AM</option>
					<option value="09:00:00">9AM</option>
					<option value="10:00:00">10AM</option>
					<option value="11:00:00">11AM</option>
					<option value="12:00:00">12PM</option>
					<option value="13:00:00">1PM</option>
					<option value="14:00:00">2PM</option>
					<option value="15:00:00">3PM</option>
					<option value="16:00:00">4PM</option>
					<option value="17:00:00">5PM</option>
					<option value="18:00:00">6PM</option>
					<option value="19:00:00">7PM</option>
					<option value="20:00:00">8PM</option>
					<option value="21:00:00">9PM</option>
					<option value="22:00:00">10PM</option>
					<option value="23:00:00">11PM</option>		
				</select>
				 - 
				<select name="time1b" id="time1b">
					<option value="" SELECTED>Please choose an option...</option>
					<option value="00:00:00">12AM</option>
					<option value="01:00:00">1AM</option>
					<option value="02:00:00">2AM</option>
					<option value="03:00:00">3AM</option>
					<option value="04:00:00">4AM</option>
					<option value="05:00:00">5AM</option>
					<option value="06:00:00">6AM</option>
					<option value="07:00:00">7AM</option>
					<option value="08:00:00">8AM</option>
					<option value="09:00:00">9AM</option>
					<option value="10:00:00">10AM</option>
					<option value="11:00:00">11AM</option>
					<option value="12:00:00">12PM</option>
					<option value="13:00:00">1PM</option>
					<option value="14:00:00">2PM</option>
					<option value="15:00:00">3PM</option>
					<option value="16:00:00">4PM</option>
					<option value="17:00:00">5PM</option>
					<option value="18:00:00">6PM</option>
					<option value="19:00:00">7PM</option>
					<option value="20:00:00">8PM</option>
					<option value="21:00:00">9PM</option>
					<option value="22:00:00">10PM</option>
					<option value="23:00:00">11PM</option>			
				</select>

				<div id="subshift1" style="display: none;">

				<select name="time2a" id="time2a">
					<option value="" SELECTED>Optional - Shifts</option>
					<option value="00:00:00">12AM</option>
					<option value="01:00:00">1AM</option>
					<option value="02:00:00">2AM</option>
					<option value="03:00:00">3AM</option>
					<option value="04:00:00">4AM</option>
					<option value="05:00:00">5AM</option>
					<option value="06:00:00">6AM</option>
					<option value="07:00:00">7AM</option>
					<option value="08:00:00">8AM</option>
					<option value="09:00:00">9AM</option>
					<option value="10:00:00">10AM</option>
					<option value="11:00:00">11AM</option>
					<option value="12:00:00">12PM</option>
					<option value="13:00:00">1PM</option>
					<option value="14:00:00">2PM</option>
					<option value="15:00:00">3PM</option>
					<option value="16:00:00">4PM</option>
					<option value="17:00:00">5PM</option>
					<option value="18:00:00">6PM</option>
					<option value="19:00:00">7PM</option>
					<option value="20:00:00">8PM</option>
					<option value="21:00:00">9PM</option>
					<option value="22:00:00">10PM</option>
					<option value="23:00:00">11PM</option>		
				</select>
				 - 
				<select name="time2b" id="time2b">
					<option value="" SELECTED>Optional - Shifts</option>
					<option value="00:00:00">12AM</option>
					<option value="01:00:00">1AM</option>
					<option value="02:00:00">2AM</option>
					<option value="03:00:00">3AM</option>
					<option value="04:00:00">4AM</option>
					<option value="05:00:00">5AM</option>
					<option value="06:00:00">6AM</option>
					<option value="07:00:00">7AM</option>
					<option value="08:00:00">8AM</option>
					<option value="09:00:00">9AM</option>
					<option value="10:00:00">10AM</option>
					<option value="11:00:00">11AM</option>
					<option value="12:00:00">12PM</option>
					<option value="13:00:00">1PM</option>
					<option value="14:00:00">2PM</option>
					<option value="15:00:00">3PM</option>
					<option value="16:00:00">4PM</option>
					<option value="17:00:00">5PM</option>
					<option value="18:00:00">6PM</option>
					<option value="19:00:00">7PM</option>
					<option value="20:00:00">8PM</option>
					<option value="21:00:00">9PM</option>
					<option value="22:00:00">10PM</option>
					<option value="23:00:00">11PM</option>		
				</select>

				<br />

				</div>

				<div id="subshift2" style="display: none;">

				<select name="time3a" id="time3a">
					<option value="" SELECTED>Optional - Shifts</option>
					<option value="00:00:00">12AM</option>
					<option value="01:00:00">1AM</option>
					<option value="02:00:00">2AM</option>
					<option value="03:00:00">3AM</option>
					<option value="04:00:00">4AM</option>
					<option value="05:00:00">5AM</option>
					<option value="06:00:00">6AM</option>
					<option value="07:00:00">7AM</option>
					<option value="08:00:00">8AM</option>
					<option value="09:00:00">9AM</option>
					<option value="10:00:00">10AM</option>
					<option value="11:00:00">11AM</option>
					<option value="12:00:00">12PM</option>
					<option value="13:00:00">1PM</option>
					<option value="14:00:00">2PM</option>
					<option value="15:00:00">3PM</option>
					<option value="16:00:00">4PM</option>
					<option value="17:00:00">5PM</option>
					<option value="18:00:00">6PM</option>
					<option value="19:00:00">7PM</option>
					<option value="20:00:00">8PM</option>
					<option value="21:00:00">9PM</option>
					<option value="22:00:00">10PM</option>
					<option value="23:00:00">11PM</option>		
				</select>
				 - 
				<select name="time3b" id="time3b">
					<option value="" SELECTED>Optional - Shifts</option>
					<option value="00:00:00">12AM</option>
					<option value="01:00:00">1AM</option>
					<option value="02:00:00">2AM</option>
					<option value="03:00:00">3AM</option>
					<option value="04:00:00">4AM</option>
					<option value="05:00:00">5AM</option>
					<option value="06:00:00">6AM</option>
					<option value="07:00:00">7AM</option>
					<option value="08:00:00">8AM</option>
					<option value="09:00:00">9AM</option>
					<option value="10:00:00">10AM</option>
					<option value="11:00:00">11AM</option>
					<option value="12:00:00">12PM</option>
					<option value="13:00:00">1PM</option>
					<option value="14:00:00">2PM</option>
					<option value="15:00:00">3PM</option>
					<option value="16:00:00">4PM</option>
					<option value="17:00:00">5PM</option>
					<option value="18:00:00">6PM</option>
					<option value="19:00:00">7PM</option>
					<option value="20:00:00">8PM</option>
					<option value="21:00:00">9PM</option>
					<option value="22:00:00">10PM</option>
					<option value="23:00:00">11PM</option>		
				</select>
				</div>
				</div>

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
					<option value="7">Other</option>
				</select>

				<p>Do you wish for command staff to manually select people for this event?</p>
				<select name="limitedEvent" id="limitedEvent">
					<option value="null" SELECTED>Please choose an option...</option>
					<option value="1">Yes</option>
					<option value="0">No</option>
				</select>

				<p>Do you wish to limit the era of the costume?</p>
				<select name="era" id="era">
					<option value="0" SELECTED>No</option>
					<option value="1">Prequel</option>
					<option value="2">Original</option>
					<option value="3">First Order</option>
					<option value="4">Clone Wars</option>
					<option value="5">Rebels</option>
					<option value="6">Expanded Universe</option>
				</select>

				<p>Limit of 501st Troopers:</p>
				<input type="number" name="limit501st" value="9999" id="limit501st" />

				<p>Limit of Rebels:</p>
				<input type="number" name="limitRebels" value="9999" id="limitRebels" />

				<p>Limit of Mandos:</p>
				<input type="number" name="limitMando" value="9999" id="limitMando" />

				<p>Limit of Droid Builders:</p>
				<input type="number" name="limitDroid" value="9999" id="limitDroid" />

				<p>Referred By:</p>
				<input type="text" name="referred" id="referred" />

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

				// Check credentials
				if(cleanInput(md5($_POST['password'])) == $db->password)
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

		<p><a href="index.php?action=forgotpassword" class="button">Forgot Your Password</a><p>';
	}
}

// Show the setup page
if(isset($_GET['action']) && $_GET['action'] == "setup")
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
							$conn->query("UPDATE troopers SET email = '".$_POST['email']."', password = '".md5(cleanInput($_POST['password']))."' WHERE tkid = '".cleanInput($_POST['tkid'])."'");

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
		<form method="POST" action="index.php?action=setup" name="registerForm" id="registerForm">
			<p>What is your TKID:</p>
			<input type="text" name="tkid" id="tkid" />

			<p>What is your e-mail:</p>
			<input type="text" name="email" id="email" />

			<p>What do you want your password to be?</p>
			<input type="password" name="password" id="password" />

			<p>Please re-enter your password:</p>
			<input type="password" name="password2" id="password2" />

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
	You have logged out! <a href="index.php">Click here to go home.</a>';
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
					$conn->query("UPDATE troopers SET password = '".md5($newpassword)."' WHERE id = '".$db->id."'");
					
					// Send e-mail
					sendEmail($db->email, readTKNumber($db->tkid), "FL 501st Troop Software Password Reset", "Your new password is:\n\n" . $newPassword . "\n\nPlease change your password as soon as possible.");
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

	// Query database for event info
	$query = "SELECT * FROM events WHERE id = '".strip_tags(addslashes($_GET['event']))."'";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Display event info
			echo '
			<h2 class="tm-section-header">'.$db->name.'</h2>
			<p><b>Venue:</b> '.$db->venue.'</p>
			<p><b>Address:</b> <a href="https://www.google.com/maps/search/?api=1&query='.$db->location.'" target="_blank">'.$db->location.'</a></p>
			<p><b>Event Start:</b> '.$db->dateStart.' - '.date('l', strtotime($db->dateStart)).'</p>
			<p><b>Event End:</b> '.$db->dateEnd.' - '.date('l', strtotime($db->dateEnd)).'</p>
			<p><b>Website:</b> <a href="'.addHttp($db->website).'" target="_blank">'.$db->website.'</a></p>
			<p><b>Expected number of attendees:</b> '.$db->numberOfAttend.'</p>
			<p><b>Requested number of characters:</b> '.$db->requestedNumber.'</p>
			<p><b>Requested character types:</b> '.$db->requestedCharacter.'</p>
			<p><b>Secure changing/staging area:</b> '.yesNo($db->secureChanging).'</p>
			<p><b>Can troopers bring blasters:</b> '.yesNo($db->blasters).'</p>
			<p><b>Can troopers bring/carry prop like lightsabers:</b> '.yesNo($db->lightsabers).'</p>
			<p><b>Is parking available:</b> '.yesNo($db->parking).'</p>
			<p><b>Is venue accessible to those with limited mobility:</b> '.yesNo($db->mobility).'</p>
			<p><b>Amenities available at venue:</b> '.$db->amenities.'</p>
			<p><b>Comments:</b> '.$db->comments.'</p>
			<p><b>Referred by:</b> '.$db->referred.'</p>

			<br />
			<hr />
			<br />';

			// Query database for roster info
			$query2 = "SELECT event_sign_up.id AS signId, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.reason, event_sign_up.attend, event_sign_up.attended_costume, event_sign_up.status, event_sign_up.troopid, troopers.id AS trooperId, troopers.name, troopers.tkid FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".strip_tags(addslashes($_GET['event']))."' ORDER BY status";
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
						<div style="overflow-x: auto;">
						<table border="1">
						<tr>
							<th>Trooper Name</th>	<th>TKID</th>	<th>Costume</th>	<th>Backup Costume</th>	<th>Status</th>	<th>When</th>
						</tr>';
					}

					echo '
					<tr>
						<td><a href="index.php?profile='.$db2->trooperId.'">'.$db2->name.'</a></td>	<td>'.readTKNumber($db2->tkid).'</td>	<td>'.getCostume($db2->costume).'</td>	<td>'.getCostume($db2->costume_backup).'</td>	<td id="'.$db2->trooperId.'Status">'.getStatus($db2->status).'</td>';

					// Query database for shift info
					$query3 = "SELECT shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid FROM shift_trooper WHERE shift_trooper.trooperid = '".$db2->trooperId."' AND shift_trooper.troopid = '".$db2->troopid."'";


					$date1 = date('Y-m-d H:i:s', strtotime($db->dateStart));
					$date2 = date('Y-m-d H:i:s', strtotime($db->dateEnd));

					$days = getDatesFromRange($date1, $date2);

					$l = 0;

					if ($result3 = mysqli_query($conn, $query3))
					{
						while ($db3 = mysqli_fetch_object($result3))
						{
							// Formatting
							if($l == 0)
							{
								echo '<td id="when'.$db3->trooperid.'">';
							}

							$shiftString = explode(",", $db3->shift);

							for($n = 0; $n <= count($shiftString) - 1; $n += 2)
							{
								$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

								$shift = mysqli_fetch_array($shiftGet);

								$readTime1 = date('h:i A', strtotime($shift[1]));
								$readTime2 = date('h:i A', strtotime($shift[2]));

								echo $days[$shiftString[$n + 1]] . '<br />' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';
							}

							$l++;
						}
					}

					// Formatting
					if($l == 0)
					{
						// Format for multiple days
						if(count($days) > 1)
						{
							echo '<td>Canceled</td>';
						}
						else
						{
							echo '
							<td>'.$days[0].'</td>';
						}
					}
					else
					{
						echo '</td>';
					}

					echo '
					</tr>';

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
				echo '</table>
				</div>';
			}

			echo '<hr />';

			// For rosterTableNoData - If no data, this is for the AJAX of a submitted sign up form
			if($i == 0)
			{
				echo '</div>';
			}

			// If logged in and assigned to event
			if(loggedIn())
			{
				// Is the user in the event?
				$eventCheck = inEvent($_SESSION['id'], strip_tags(addslashes($_GET['event'])));

				// Get date range
				$days = getDatesFromRange($db->dateStart, $db->dateEnd);

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
							echo '<p><b>You have canceled this event.</b></p>';
						}
						else
						{
							if(count($days) > 1)
							{
								echo '
								<h2>Make Adjustments:</h2>

								<form action="process.php?do=modifysignup" name="modifysignup" id="modifysignup" method="POST">

									<input type="hidden" name="troopidC" id="troopidC" value="'.strip_tags(addslashes($_GET['event'])).'" />
									<input type="hidden" name="days" id="days" value="1" />
									<input type="hidden" name="dateStart" id="dateStart" value="'.$db->dateStart.'" />
									<input type="hidden" name="dateEnd" id="dateEnd" value="'.$db->dateEnd.'" />
									<input type="hidden" name="limitedevent" id="limitedevent" value="'.$db->limitedEvent.'" />

									<div id="goingarea">
										<select name="status" id="status">';

									if($db->limitedEvent != 1)
									{
										echo '
											<option value="0" '.echoSelect($status, 0).'>I\'ll be there!</option>
											<option value="1" '.echoSelect($status, 1).'>Tentative</option>';
									}
									else
									{
										echo '
											<option value="5">Request to attend (Pending)</option>';								
									}

									echo '
										</select>
									</div>

									<div id="cancelarea" style="display: none;">
										<p>Reason why you are canceling:</p>
										<input type="text" name="cancelReason" id="cancelReason" />
									</div>';

								$j = 0;

								foreach ($days as $key => $value)
								{
								    echo '<p><b>' . $value . '</b></p>';

									$query3 = "SELECT shifts.id, shifts.starttime, shifts.endtime, shifts.troopid AS shiftTroop, shift_trooper.troopid, shift_trooper.shift, shift_trooper.trooperid FROM shifts LEFT JOIN shift_trooper ON shifts.troopid = shift_trooper.troopid WHERE shifts.troopid = '".$_GET['event']."' AND shift_trooper.trooperid = '".$_SESSION['id']."'";

									if ($result3 = mysqli_query($conn, $query3))
									{
										while ($db3 = mysqli_fetch_object($result3))
										{
											// The dates
											$readTime1 = date('h:i A', strtotime($db3->starttime));
											$readTime2 = date('h:i A', strtotime($db3->endtime));

											// Our string of choices from databases
											$shiftString = explode(",", $db3->shift);

											// Was this choice picked?
											$pickedChoice = false;

											// loop through and see if checked
											for($o = 0; $o <= count($shiftString) - 1; $o += 2)
											{
												// Check the choice
												if($key == $shiftString[$o + 1] && $shiftString[$o] == $db3->id)
												{
													// This is a picked choice
													$pickedChoice = true;
												}
											}

											// If picked
											if($pickedChoice)
											{
												echo '
												<input type="checkbox" name="shiftcheckbox[]" id="shiftcheckbox" value="'. $db3->id .','.$key.'" CHECKED />'.$readTime1.' - '.$readTime2.'<br />';
											}
											else
											{
												// If not picked
												echo '
												<input type="checkbox" name="shiftcheckbox[]" id="shiftcheckbox" value="'. $db3->id .','.$key.'" />'.$readTime1.' - '.$readTime2.'<br />';				
											}

											// Increment
											$j += 2;
										}
									}
								}

								echo '
									<input type="submit" name="submitModifySignUp" id="submitModifySignUp" value="Save Changes" />
								</form>';
							}
							else
							{
								echo '
								<div name="signeduparea" id="signeduparea">
									<p><b>You are signed up for this troop!</b></p>

									<form action="index.php" method="POST" name="cancelForm" id="cancelForm">
										<input type="hidden" name="troopidC" id="troopidC" value="'.strip_tags(addslashes($_GET['event'])).'" />
										<input type="hidden" name="myId" id="myId" value="'.strip_tags(addslashes($_SESSION['id'])).'" />
										<p>Reason why you are canceling:</p>
										<input type="text" name="cancelReason" id="cancelReason" />
										<input type="submit" name="submitCancelTroop" id="submitCancelTroop" value="Cancel Troop" />
									</form>
								</div>';
							}
						}
					}
					else
					{
						// Sign up area
						echo '
						<div name="signuparea" id="signuparea">
							<h2 class="tm-section-header">Sign Up</h2>';

						if($db->limitedEvent == 1)
						{
							echo '<b>This is a hand picked event. When you sign up, you will be placed in a pending status until command staff approves you. Please check for updates.</b>';
						}

						echo '
							<form action="process.php?do=signup" method="POST" name="signupForm2" id="signupForm2">
								<input type="hidden" name="event" value="'.$_GET["event"].'" />
								<p>What costume will you wear?</p>
								<select name="costume">
									<option value="null" SELECTED>Please choose an option...</option>';

								$query3 = "SELECT * FROM costumes";
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
								$query2 = "SELECT * FROM costumes ORDER BY costume";
								// Amount of costumes
								$c = 0;
								if ($result2 = mysqli_query($conn, $query2))
								{
									while ($db2 = mysqli_fetch_object($result2))
									{
										if($c == 0)
										{
											echo '<option value="99999">Select a costume...</option>';
										}

										// Display costume
										echo '<option value="'.$db2->id.'">'.$db2->costume.'</option>';

										$c++;
									}
								}

								echo '
								</select>';

								$days = getDatesFromRange($db->dateStart, $db->dateEnd);

								if(count($days) > 1)
								{
									echo '
									<p>Which day and shift are you going to attend?</p>';

									foreach ($days as $key => $value)
									{
									    echo '<p><b>' . $value . '</b></p>';

										$query3 = "SELECT * FROM shifts WHERE troopid = '".$_GET['event']."'";
										$j = 0;
										if ($result3 = mysqli_query($conn, $query3))
										{
											while ($db3 = mysqli_fetch_object($result3))
											{
												$readTime1 = date('h:i A', strtotime($db3->starttime));
												$readTime2 = date('h:i A', strtotime($db3->endtime));

												echo '
												<input type="checkbox" name="shiftcheckbox[]" id="shiftcheckbox" value="'. $db3->id .','.$key.'" />'.$readTime1.' - '.$readTime2.'<br />';
												$j++;
											}
										} 
									}
								}

								echo '
								<br /><br />

								<input type="submit" value="Submit!" name="submitSignUp" />
							</form>
						</div>';
					}
				}
			}
			else
			{
				echo '
				<br />
				<b>You must <a href="index.php?action=login">login</a> to sign up for a troop.</b>
				<br /><br />';
			}
		}

		echo '<hr />';

		if(loggedIn())
		{
			// Enter comment into database
			if(isset($_POST['submitComment']))
			{
				if(strlen($_POST['comment']) > 0 && ($_POST['important'] == 0 || $_POST['important'] == 1))
				{
					// Query the database
					$conn->query("INSERT INTO comments (troopid, trooperid, comment, important) VALUES ('".cleanInput($_GET['event'])."', '".cleanInput($_SESSION['id'])."', '".cleanInput($_POST['comment'])."', '".cleanInput($_POST['important'])."')") or die($conn->error);

					echo '<b>Comment posted!</b>';
				}
			}

			echo '
			<form aciton="index.php?event='.cleanInput($_GET['event']).'" name="commentForm" id="commentForm" method="POST">
				<h2 class="tm-section-header">Comments</h2>
				<div style="text-align: center;">
				<textarea cols="30" rows="10" name="comment" id="comment"></textarea>

				<br />

				<p>Is this an important message?</p>
				<select name="important">
					<option value="0">No</option>
					<option value="1">Yes</option>
				</select>

				<br /><br />

				<input type="submit" name="submitComment" value="Post!" />
				</div>
			</form>';

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
						<td><a href="index.php?profile='.$db->trooperid.'">'.getTKNumber($db->trooperid).'</a></td>
					</tr>';

					if(isAdmin())
					{
						echo '
						<tr>
							<td><a href="#" id="deleteComment_'.$db->id.'" name="'.$db->id.'">Delete Comment</a></td>
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
			echo '
			<br />
			<b>You must <a href="index.php?action=login">login</a> to view comments.</b>';
		}
	}
}
else
{
	// Only show home page when it is loaded
	if(!isset($_GET['action']) && !isset($_GET['profile']) && !isset($_GET['event']))
	{
		// Show options for squad choice
		echo '
		<h2 class="tm-section-header">Troops</h2>

		<a href="index.php"><img src="images/garrison_emblem.png" alt="Florida Garrison Troops" '.isSquadActive(0).' /></a> <a href="index.php?squad=1"><img src="images/everglades_emblem.png" alt="Everglades Squad Troops" '.isSquadActive(1).' /></a> <a href="index.php?squad=2"><img src="images/makaze_emblem.png" alt="Makaze Squad Troops" '.isSquadActive(2).' /></a> <a href="index.php?squad=3"><img src="images/parjai_emblem.png" alt="Parjai Squad Troops" '.isSquadActive(3).' /></a> <a href="index.php?squad=4"><img src="images/squad7_emblem.png" alt="Squad 7 Troops" '.isSquadActive(4).' /></a> <a href="index.php?squad=5"><img src="images/tampabay_emblem.png" alt="Tampa Bay Squad Troops" '.isSquadActive(5).' /></a>

		<br /><br /><hr /><br />

		<div style="text-align: center;">';

		// Was a squad defined? (Prevents displays div when not needed)
		if(isset($_GET['squad']))
		{
			// Query
			$query = "SELECT * FROM events WHERE dateStart >= CURDATE() AND squad = '".cleanInput($_GET['squad'])."' ORDER BY dateStart";
		}
		else
		{
			// Query
			$query = "SELECT * FROM events WHERE dateStart >= CURDATE() ORDER BY dateStart";
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
				$getNumOfTroopers = $conn->query("SELECT id FROM event_sign_up WHERE troopid = '".$db->id."'");

				// Get the number of days
				$date1 = date('Y-m-d H:i:s', strtotime($db->dateStart));
				$date2 = date('Y-m-d H:i:s', strtotime($db->dateEnd));

				$days = getDatesFromRange($date1, $date2);

				echo '<div style="border: 1px solid gray; margin-bottom: 10px;">';

				// No squad set
				if(!isset($_GET['squad']))
				{
					echo '<a href="index.php?event=' . $db->id . '">' .date('M d, Y', strtotime($db->dateStart)). ' - '.date('M d, Y', strtotime($db->dateEnd)).''.'<br />' . $db->name . '</a>';

					// If more than one day
					if($days > 1)
					{
						// Multiple day query
						$query2 = "SELECT shifts.starttime, shifts.endtime, shifts.id, shifts.troopid, shift_trooper.shift, shift_trooper.troopid FROM shifts LEFT JOIN shift_trooper ON shifts.troopid = shift_trooper.troopid WHERE shift_trooper.troopid = '".$db->id."'";

						// Days and shifts that exist
						$dayShiftArray = array();
						$dayShiftPickedArray = array();

						if ($result2 = mysqli_query($conn, $query2))
						{
							while ($db2 = mysqli_fetch_object($result2))
							{
								// Shift string
								$shiftString = explode(",", $db2->shift);

								for($n = 0; $n <= count($shiftString) - 1; $n += 2)
								{
									// Loop through days ($l)
									for($l = 0; $l <= count($days) - 1; $l++)
									{
										// Populate days and shifts
										if(!in_array($l . ',' . $db2->id, $dayShiftArray))
										{
											array_push($dayShiftArray, $l . ',' . $db2->id);
										}

										// If day ($l) equals day in string and also equals shift in string
										if($l == $shiftString[$n + 1] && $shiftString[$n] == $db2->id)
										{
											// Loop through days and shifts
											for($k = 0; $k <= count($dayShiftArray) - 1; $k++)
											{
												// Seperate day and shift
												$kCheck = explode(",", $dayShiftArray[$k]);

												// Check if day and shift exist
												if($kCheck[0] == $l && $kCheck[1] == $db2->id)
												{
													array_push($dayShiftPickedArray, $kCheck[0] . ',' . $kCheck[1]);
												}	
											}
										}
									}
								}
							}
						}

						// Sort the array
						sort($dayShiftArray);

						// Display
						echo '
						<br /><span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS DATE/TIME:</b></span>

						<ul>';

						// Loop through all shifts
						for($n = 0; $n <= count($dayShiftArray) - 1; $n++)
						{
							// Count how many troopers picked this shift
							$isMoreThan = 0;

							// Loop through picked shifts
							for($j = 0; $j <= count($dayShiftPickedArray) - 1; $j++)
							{
								if($dayShiftArray[$n] == $dayShiftPickedArray[$j])
								{
									// Increment $isMoreThan
									$isMoreThan++;
								}
							}

							// If less than or equal to one trooper, show message
							if($isMoreThan <= 1)
							{
								// Explode the value to get information
								$dateNeed = explode(",", $dayShiftArray[$n]);

								$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$dateNeed[1]."'") or die($conn->error);

								$shift = mysqli_fetch_array($shiftGet);

								// Convert times
								$readTime1 = date('h:i A', strtotime($shift[1]));
								$readTime2 = date('h:i A', strtotime($shift[2]));

								echo '
								<li>
								'.$days[$dateNeed[0]].' - '.$readTime1.' - '.$readTime2.'
								</li>';
							}

							$isMoreThan = 0;	
						}

						echo '
						</ul>';
					}
					else
					{
						// If one day
						if($getNumOfTroopers->num_rows <= 1)
						{
							echo '<br /><span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS EVENT!</b></span>';
						}
					}

					$i++;
				}
				else
				{
					// Squad set
					if($db->squad == cleanInput($_GET['squad']))
					{
						echo '<a href="index.php?event=' . $db->id . '">' .date('M d, Y', strtotime($db->dateStart)). ' - '.date('M d, Y', strtotime($db->dateEnd)).''.'<br />' . $db->name . '</a>';

						// If more than one day
						if($days > 1)
						{
							// Multiple day query
							$query2 = "SELECT shifts.starttime, shifts.endtime, shifts.id, shifts.troopid, shift_trooper.shift, shift_trooper.troopid FROM shifts LEFT JOIN shift_trooper ON shifts.troopid = shift_trooper.troopid WHERE shift_trooper.troopid = '".$db->id."'";

							// Days and shifts that exist
							$dayShiftArray = array();
							$dayShiftPickedArray = array();

							if ($result2 = mysqli_query($conn, $query2))
							{
								while ($db2 = mysqli_fetch_object($result2))
								{
									// Shift string
									$shiftString = explode(",", $db2->shift);

									for($n = 0; $n <= count($shiftString) - 1; $n += 2)
									{
										// Loop through days ($l)
										for($l = 0; $l <= count($days) - 1; $l++)
										{
											// Populate days and shifts
											if(!in_array($l . ',' . $db2->id, $dayShiftArray))
											{
												array_push($dayShiftArray, $l . ',' . $db2->id);
											}

											// If day ($l) equals day in string and also equals shift in string
											if($l == $shiftString[$n + 1] && $shiftString[$n] == $db2->id)
											{
												// Loop through days and shifts
												for($k = 0; $k <= count($dayShiftArray) - 1; $k++)
												{
													// Seperate day and shift
													$kCheck = explode(",", $dayShiftArray[$k]);

													// Check if day and shift exist
													if($kCheck[0] == $l && $kCheck[1] == $db2->id)
													{
														array_push($dayShiftPickedArray, $kCheck[0] . ',' . $kCheck[1]);
													}	
												}
											}
										}
									}
								}
							}

							// Sort the array
							sort($dayShiftArray);

							// Display
							echo '
							<br /><span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS DATE/TIME:</b></span>

							<ul>';

							// Loop through all shifts
							for($n = 0; $n <= count($dayShiftArray) - 1; $n++)
							{
								// Count how many troopers picked this shift
								$isMoreThan = 0;

								// Loop through picked shifts
								for($j = 0; $j <= count($dayShiftPickedArray) - 1; $j++)
								{
									if($dayShiftArray[$n] == $dayShiftPickedArray[$j])
									{
										// Increment $isMoreThan
										$isMoreThan++;
									}
								}

								// If less than or equal to one trooper, show message
								if($isMoreThan <= 1)
								{
									// Explode the value to get information
									$dateNeed = explode(",", $dayShiftArray[$n]);

									$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$dateNeed[1]."'") or die($conn->error);

									$shift = mysqli_fetch_array($shiftGet);

									// Convert times
									$readTime1 = date('h:i A', strtotime($shift[1]));
									$readTime2 = date('h:i A', strtotime($shift[2]));

									echo '
									<li>
									'.$days[$dateNeed[0]].' - '.$readTime1.' - '.$readTime2.'
									</li>';
								}

								$isMoreThan = 0;	
							}

							echo '
							</ul>';
						}
						else
						{
							// If one day
							if($getNumOfTroopers->num_rows <= 1)
							{
								echo '<br /><span style="color:red;"><b>NOT ENOUGH TROOPERS FOR THIS EVENT!</b></span>';
							}
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

		if(loggedIn())
		{
			// Load events that need confirmation
			$query = "SELECT events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = '".$_SESSION['id']."' AND events.dateEnd < NOW() AND attend = 0 AND events.closed = 1";

			if ($result = mysqli_query($conn, $query))
			{
				// Number of results total
				$i = 0;

				// Number of results within shift_trooper
				$l = 0;

				// Count how many have data from user
				$m = 0;

				while ($db = mysqli_fetch_object($result))
				{
					// Load shifts if applicable
					// Query database for shift info
					$query3 = "SELECT shift_trooper.id, shift_trooper.shift, shift_trooper.troopid, shift_trooper.trooperid, shift_trooper.attend, shift_trooper.didNotAttend FROM shift_trooper WHERE shift_trooper.trooperid = '".$_SESSION['id']."' AND shift_trooper.troopid = '".$db->eventId."'";


					$date1 = date('Y-m-d H:i:s', strtotime($db->dateStart));
					$date2 = date('Y-m-d H:i:s', strtotime($db->dateEnd));

					$days = getDatesFromRange($date1, $date2);

					$data = "";

					if($result3 = mysqli_query($conn, $query3))
					{
						while ($db3 = mysqli_fetch_object($result3))
						{
							// Array of shifts
							$shiftString = explode(",", $db3->shift);

							for($n = 0; $n <= count($shiftString) - 1; $n += 2)
							{
								$shiftGet = $conn->query("SELECT shifts.id, shifts.starttime, shifts.endtime FROM shifts WHERE shifts.id = '".$shiftString[$n]."'") or die($conn->error);

								$shift = mysqli_fetch_array($shiftGet);

								// The dates
								$readTime1 = date('h:i A', strtotime($shift[1]));
								$readTime2 = date('h:i A', strtotime($shift[2]));

								// This may be able to be deleted - keep this until further notice - 07/26/2020
								//$data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $days[$shiftString[$n + 1]] . '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="confirmShift[]" id="confirmShift_'.$shift[0].'" value="'.$shift[0].','.str_replace(" ", "", $shiftString[$n + 1]).','.$db->eventId.'" /> ' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';

								$checkAttend = explode(",", substr($db3->attend, 3));
								$checkDidNotAttend = explode(",", substr($db3->didNotAttend, 3));

								// Check 1 - check attend data
								$check1 = true;
								// Check 2 - check did not attend data
								$check2 = true;

								// Do not loop through did not attend, if no data
								if(count($checkAttend) > 1)
								{
									// Loop through attend
									for($j = 0; $j <= count($checkAttend) - 1; $j += 2)
									{
										// Inside shift
										if($checkAttend[$j] == $shiftString[$n] && $checkAttend[$j + 1] == $shiftString[$n + 1])
										{
											$check1 = false;
										}
									}
								}

								// Do not loop through did not attend, if no data
								if(count($checkDidNotAttend) > 1)
								{
									// Loop through did not attend
									for($j = 0; $j <= count($checkDidNotAttend) - 1; $j += 2)
									{
										// Inside shift
										if($checkDidNotAttend[$j] == $shiftString[$n] && $checkDidNotAttend[$j + 1] == $shiftString[$n + 1])
										{
											$check2 = false;
										}
									}
								}

								if($check1 && $check2)
								{
									$data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $days[$shiftString[$n + 1]] . '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="confirmShift[]" id="confirmShift_'.$shift[0].'" value="'.$shift[0].','.str_replace(" ", "", $shiftString[$n + 1]).','.$db->eventId.'" /> ' . $readTime1 . ' - ' . $readTime2 . '<br /><br />';

									$m++;
								}
							}

							$l++;
						}
					}

					// If a shift exists to attest to
					$i++;

					// If data
					if($i > 0 && $l == 0 || $l > 0 && $m > 0)
					{
						echo '
						<div name="confirmArea" id="confirmArea">
						<h2 class="tm-section-header">Confirm Troops</h2>
						<form action="process.php?do=confirmList" method="POST" name="confirmListForm" id="confirmListForm">
						<div name="confirmArea2" id="confirmArea2">';
					}

					if($l > 0)
					{
						// If has data
						if($m > 0)
						{
							echo '
							<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">'.$db->name.'<br /><br />' . $data;
						}
					}
					else
					{
						echo '
						<div name="confirmListBox_'.$db->eventId.'" id="confirmListBox_'.$db->eventId.'">
							<input type="checkbox" name="confirmList[]" id="confirmList_'.$db->eventId.'" value="'.$db->eventId.'" /> '.$db->name.'<br /><br />';
					}

					// End load shifts

					echo '
					</div>';
				}
			}

			// If data
			if($i > 0 && $l == 0 || $l > 0 && $m > 0)
			{
				echo '
					</div>
					<input type="submit" name="submitConfirmList" id="submitConfirmList" value="I attended these troops" />
					<input type="submit" name="submitConfirmListDelete" id="submitConfirmListDelete" value="I did NOT attend these troops" />
					<p>Attended Costume:</p>
					<select name="costume" id="costumeChoice">';

					$query3 = "SELECT * FROM costumes";
					$i = 0;
					if ($result3 = mysqli_query($conn, $query3))
					{
						while ($db3 = mysqli_fetch_object($result3))
						{
							if($i == 0)
							{
								echo '
								<option value="">Please choose an option...</option>';
							}

							echo '
							<option value="'. $db3->id .'">'.$db3->costume.'</option>';

							$i++;
						}
					}

				echo '
					</select>
				</form>
				</div>';
			}
		}
	}
}

echo '
</section>

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
</section>

<hr />

<section class="tm-section tm-section-small">
<p class="tm-mb-0">
Website created and maintained by Matthew Drennan (TK52233). If you encounter any issues with this site, please
<a href="mailto:drennanmattheww@gmail.com" class="tm-contact-link">email</a> me here.
</p>

<p style="text-align: center;">
<a href="https://github.com/MattDrennan/501-troop-tracker" target="_blank"><img src="images/github.png" alt="Help contribute on GitHub.com!" /><br />Help contribute on GitHub.com!</a>
</p>
</section>

<script>
/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myFunction() {
	var x = document.getElementById("myTopnav");
	if(x.className === "topnav")
	{
		x.className += " responsive";
	}
	else
	{
		x.className = "topnav";
	}
}

$(document).ready(function()
{
	// Show and hide cancel textbox when on a multi-day event based on # of checkboxes
	$("#modifysignup").on("change", function()
	{
		if($(this).find("input[name=\'shiftcheckbox[]\']:checked").length == 0)
		{
			$("#modifysignup #cancelarea").show();
			$("#modifysignup #goingarea").hide();
		}
		else
		{
			$("#modifysignup #cancelarea").hide();
			$("#modifysignup #goingarea").show();
		}
	});

	// Modify sign up submit button
	$("#submitModifySignUp").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#modifysignup");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitModifySignUp=1",
			success: function(data)
			{
				var json = JSON.parse(data);
				$("#when" + json.id).html(json.data);
				alert("Changes submitted!");
			}
		});
	})

	$("#submitEditUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editUser");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitEditUser=1",
			success: function(data)
			{
				if($("#editUserInfo").is(":hidden"))
				{
					$("#submitEditUser").val("Close");
					$("#editUserInfo").show();

					var json = JSON.parse(data);
					$("#userIDE").val(json.id);
					$("#user").val(json.name);
					$("#email").val(json.email);
					$("#phone").val(json.phone);
					$("#squad").val(json.squad);
					$("#permissions").val(json.permissions);
					$("#tkid").val(json.tkid);
				}
				else
				{
					$("#submitEditUser").val("Edit");
					$("#editUserInfo").hide();
				}
			}
		});
	})

	$("#submitEdit").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitEdit=1",
			success: function(data)
			{
				if($("#rosterInfo").is(":hidden"))
				{
					//$("#submitRoster").val("Close");
					//$("#rosterInfo").html(data);
					//$("#rosterInfo").show();
				}
				else
				{
					$("#submitRoster").val("Roster");
					$("#rosterInfo").html("");
					$("#rosterInfo").hide();
				}

				if($("#editEventInfo").is(":hidden"))
				{
					$("#submitEdit").val("Close");
					$("#editEventInfo").show();

					var json = JSON.parse(data);
					$("#eventIdE").val(json.id);
					$("#eventName").val(json.name);
					$("#eventVenue").val(json.venue);
					$("#location").val(json.location);
					$("#datepicker").val(json.dateStart);
					$("#datepicker2").val(json.dateEnd);
					$("#website").val(json.website);
					$("#numberOfAttend").val(json.numberOfAttend);
					$("#requestedNumber").val(json.requestedNumber);
					$("#requestedCharacter").val(json.requestedCharacter);
					$("#secure").val(json.secureChanging);
					$("#blasters").val(json.blasters);
					$("#lightsabers").val(json.lightsabers);
					$("#parking").val(json.parking);
					$("#mobility").val(json.mobility);
					$("#amenities").val(json.amenities);
					$("#comments").val(json.comments);
					$("#label").val(json.label);
					$("#limitedEvent").val(json.limitedEvent);
					$("#era").val(json.limitTo);
					$("#limitRebels").val(json.limitRebels);
					$("#limit501st").val(json.limit501st);
					$("#limitMando").val(json.limitMando);
					$("#limitDroid").val(json.limitDroid);
					$("#referred").val(json.referred);
				}
				else
				{
					$("#submitEdit").val("Edit");
					$("#editEventInfo").hide();
				}
			}
		});
	})

	$("#submitRoster").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&submitRoster=1",
			success: function(data)
			{
				if($("#editEventInfo").is(":hidden"))
				{
					//$("#editEventInfo").show();
					//$("#submitEdit").val("Close");
				}
				else
				{
					$("#editEventInfo").hide();
					$("#submitEdit").val("Edit");
				}

				if($("#rosterInfo").is(":hidden"))
				{
					$("#submitRoster").val("Close");
					$("#rosterInfo").html(data);
					$("#rosterInfo").show();
				}
				else
				{
					$("#submitRoster").val("Roster");
					$("#rosterInfo").html("");
					$("#rosterInfo").hide();
				}
			}
		});
	})

	$("#unsubscribeButton").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#unsubscribeForm");
		var url = form.attr("action");

		$.ajax({
			type: "POST",
			url: url,
			data: form.serialize() + "&unsubscribeButton=1",
			success: function(data)
			{
				if($("#unsubscribeButton").val() == "Unsubscribe")
				{
					alert(data);
					$("#unsubscribeButton").val("Subscribe");
				}
				else
				{
					alert(data);
					$("#unsubscribeButton").val("Unsubscribe");
				}
			}
		});
	})

	$("[id^=deleteComment]").click(function(e)
	{
		e.preventDefault();

		var r = confirm("Are you sure you want to delete this comment?");

		var id = $(this).attr("name");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: "index.php?event=" + $(this).attr("name"),
				data: "comment=" + id + "&deleteComment=1",
				success: function(data)
				{
					// Remove comment
					$("#comment_" + id).remove();

					// Alert to success
			  		alert("The comment was removed successfully!");
				}
			});
		}
	});

	$("#changephoneLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").show();
		$("#changename").hide();
		$("#changeemail").hide();
		$("#unsubscribe").hide();
		$("#changepassword").hide();
	});

	$("#changenameLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").show();
		$("#changeemail").hide();
		$("#unsubscribe").hide();
		$("#changepassword").hide();
	});

	$("#changepasswordLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changepassword").show();
		$("#changename").hide();
		$("#changeemail").hide();
		$("#unsubscribe").hide();
	});

	$("#changeemailLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").hide();
		$("#changeemail").show();
		$("#unsubscribe").hide();
		$("#changepassword").hide();
	});

	$("#unsubscribeLink").click(function(e)
	{
		e.preventDefault();
		$("#changephone").hide();
		$("#changename").hide();
		$("#changeemail").hide();
		$("#unsubscribe").show();
		$("#changepassword").hide();
	});

	$("#submitDelete").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this event?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDelete=1",
				success: function(data)
				{
					// Remove from select option
					$("#eventId").find("option:selected").remove();

					// Alert to success
			  		alert("The event was removed successfully!");
				}
			});
		}
	})

	$("#submitApproveUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#approveTroopers");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to approve this user?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitApproveUser=1",
				success: function(data)
				{
					// Remove from select option
					$("#userID").find("option:selected").remove();

					// Alert to success
			  		alert("The user was approved successfully!");

			  		// Show message if empty
			  		if($("#userID").has("option").length <= 0 )
			  		{
			  			$("#approveTroopers").html("There are no troopers to display.");
			  		}
				}
			});
		}
	})

	$("#submitDenyUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#approveTroopers");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to deny this user?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDenyUser=1",
				success: function(data)
				{
					// Remove from select option
					$("#userID").find("option:selected").remove();

					// Alert to success
			  		alert("The user was denied successfully!");

			  		// Show message if empty
			  		if($("#userID").has("option").length <= 0)
			  		{
			  			$("#approveTroopers").html("There are no troopers to display.");
			  		}
				}
			});
		}
	})

	$("#submitDeleteUser").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this user?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDeleteUser=1",
				success: function(data)
				{
					// Remove from select option
					$("#userID").find("option:selected").remove();

					// Alert to success
			  		alert("The user was removed successfully!");

			  		// Show message if empty
			  		if($("#userID").has("option").length <= 0 )
			  		{
			  			$("#approveTroopers").html("There are no troopers to display.");
			  		}
				}
			});
		}
	})

	$("#submitCancel").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to cancel this event?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitCancel=1",
				success: function(data)
				{
					// Alert to success
			  		alert("The event was canceled successfully!");
				}
			});
		}
	})

	$("body").on("click", "#removetrooper", function(e)
	{
		e.preventDefault();

		var form = $("#troopRosterForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to remove this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&removetrooper=1",
				success: function(data)
				{
					var json = JSON.parse(data);

					$("#roster_" + $("#trooperSelectEdit").val()).remove();

					// Display message
					alert(json.data);
				}
			});
		}
	});

	$("body").on("click", "#edittrooper", function(e)
	{
		e.preventDefault();

		if($("input[name=trooperSelectEdit]").is(":checked"))
		{
			if($("#edittrooper").val() != "Save")
			{
				// Change submit button
				$("#edittrooper").val("Save");

				// Show Inputs for edit
				$("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#status2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).show();
				$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").show();
				$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").show();

				// Hide static values
				$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val()).hide();
				$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val()).hide();
			}
			else
			{
				// Save
				e.preventDefault();

				var form = $("#troopRosterForm");
				var url = form.attr("action");

				var r = confirm("Are you sure you want to edit this roster?");

				if (r == true)
				{
					$.ajax({
						type: "POST",
						url: url,
						data: form.serialize() + "&submitEditRoster=1",
						success: function(data)
						{
							// Change submit button
							$("#edittrooper").val("Edit Trooper");

							// Hide Inputs for edit
							$("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#status2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).hide();
							$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").hide();
							$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val() + "Edit").hide();

							// Set values
							$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#costume2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#backup2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#status2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#reason2" + $("input[name=trooperSelectEdit]:checked").val()).find("input").val());
							$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#attend2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());
							$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).html($("#attendcostume2" + $("input[name=trooperSelectEdit]:checked").val()).find("select :selected").text());

							// Show static values
							$("#costume1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#backup1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#status1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#reason1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#attend1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#attendcostume1" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val()).show();
							$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val()).show();

							var json = JSON.parse(data);
							$("#dateAttending" + $("input[name=trooperSelectEdit]:checked").val()).html(json.data);
							$("#dateAttended" + $("input[name=trooperSelectEdit]:checked").val()).html(json.data2);

							// Alert to success
					  		alert("Roster updated!");
						}
					});
				}
			}
		}
	});

	$("body").on("change", "#status", function(e)
	{
		if($("#status").val() == "4")
		{
			$("#reasonBlock").show();
		}
		else
		{
			$("#reasonBlock").hide();
		}

		if($("#status").val() == "3")
		{
			$("#attendBlock").show();
		}
		else
		{
			$("#attendBlock").hide();
		}
	});

	$("#datepicker").on("change", function()
	{
		var start = Date.parse($("#datepicker").val());
		var end = Date.parse($("#datepicker2").val());
		var diff = new Date(end - start);
		var days = diff/1000/60/60/24;

		if(end != "")
		{
			if(days > 0)
			{
				// Show
				$("#shiftsetup").show();
			}
			else
			{
				// Clear out
				$("#time1a").val("option");
				$("#time1b").val("option");
				$("#time2a").val("option");
				$("#time2b").val("option");
				$("#time3a").val("option");
				$("#time3b").val("option");

				// Hide
				$("#shiftsetup").hide();
			}
		}
	});

	$("#datepicker2").on("change", function()
	{
		var start = Date.parse($("#datepicker").val());
		var end = Date.parse($("#datepicker2").val());
		var diff = new Date(end - start);
		var days = diff/1000/60/60/24;

		if(end != "")
		{
			if(days > 0)
			{
				// Show
				$("#shiftsetup").show();

				// Select first option
				$("#time1a").val($("#time1a option:first").val());
				$("#time1b").val($("#time1b option:first").val());

				$("#time2a").val($("#time2a option:first").val());
				$("#time2b").val($("#time2b option:first").val());

				$("#time3a").val($("#time3a option:first").val());
				$("#time3b").val($("#time3b option:first").val());

				// Add required rules
                $("#time1a").rules("add", 
                {
                    required: true,
					messages:
					{
						time1a: "Please select a time."
					}
                });

                $("#time1b").rules("add", 
                {
                    required: true,
					messages:
					{
						time1a: "Please select a time."
					}
                });
			}
			else
			{
				// Clear out
				$("#time1a").val("");
				$("#time1b").val("");
				$("#time2a").val("");
				$("#time2b").val("");
				$("#time3a").val("");
				$("#time3b").val("");

				// Hide
				$("#shiftsetup").hide();
			}
		}
	});

	$("#time1a").on("change", function()
	{
		if($("#time1a").val() != "" && $("#time1b").val() != "")
		{
			$("#subshift1").show();
		}
		else
		{
			$("#subshift1").hide();
			$("#time2a").val("");
			$("#time2b").val("");
			$("#time3a").val("");
			$("#time3b").val("");
		}
	});

	$("#time1b").on("change", function()
	{
		if($("#time1a").val() != "" && $("#time1b").val() != "")
		{
			$("#subshift1").show();
		}
		else
		{
			$("#subshift1").hide();
			$("#time2a").val("");
			$("#time2b").val("");
			$("#time3a").val("");
			$("#time3b").val("");
		}
	});

	$("#time2a").on("change", function()
	{
		if($("#time2a").val() != "" && $("#time2b").val() != "")
		{
			$("#subshift2").show();
		}
		else
		{
			$("#subshift2").hide();
			$("#time3a").val("");
			$("#time3b").val("");
		}
	});

	$("#time2b").on("change", function()
	{
		if($("#time2a").val() != "" && $("#time2b").val() != "")
		{
			$("#subshift2").show();
		}
		else
		{
			$("#subshift2").hide();
			$("#time3a").val("");
			$("#time3b").val("");
		}
	});

	$("#eventId").on("change", function()
	{
		if($("#editEventInfo").is(":hidden"))
		{
			//$("#editEventInfo").show();
			//$("#submitEdit").val("Close");
		}
		else
		{
			$("#editEventInfo").hide();
			$("#submitEdit").val("Edit");
		}

		if($("#rosterInfo").is(":hidden"))
		{
			//$("#submitRoster").val("Close");
			//$("#rosterInfo").show();
		}
		else
		{
			$("#submitRoster").val("Roster");
			$("#rosterInfo").hide();	
		}
	});

	$("#userID").on("change", function()
	{
		if($("#editEventInfo").is(":hidden"))
		{
			//$("#editUserInfo").show();
			//$("#submitEditUser").val("Close");
		}
		else
		{
			$("#editUserInfo").hide();
			$("#submitEditUser").val("Edit");
		}

		// Only used for approving area
		$.ajax({
			type: "POST",
			url: "process.php?do=getuser",
			data: "id=" + $("#userID").val() + "&getuser=1",
			success: function(data)
			{
				var json = JSON.parse(data);
				$("#nameTable").html(json.name);
				$("#emailTable").html(json.email);
				$("#phoneTable").html(json.phone);
				$("#squadTable").html(json.squad);
				$("#tkTable").html(json.tkid);
			}
		});
	});

	$("#userIDAward").on("change", function()
	{
		$("#awardID").empty();

		// Only used for approving area
		$.ajax({
			type: "POST",
			url: "process.php?do=getawards",
			data: "id=" + $("#userIDAward").val() + "&getawards=1",
			dataType: "json",
			success: function(data)
			{
				$.each(data, function(i, val)
				{
					$("#awardID").append("<option value=" + data[i].id + ">" + data[i].title + "</option>");
				});
				$("#awardUserDelete").show();
			}
		});

		$("#awardUserDelete").show();

  		// Show message if empty
  		if($("#awardID").has("option").length <= 0)
  		{
  			$("#awardUserDelete").hide();
  		}
	});

	$("#award").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#awardUser");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to award this trooper?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitAward=1",
				dataType: "json",
				success: function(data)
				{
					$.each(data, function(i, val)
					{
						$("#awardID").append("<option value=" + data[i].id + ">" + data[i].title + "</option>");
						$("#awardUserDelete").show();
					});
					// Alert to success
			  		alert("The award was awarded successfully!");
				}
			});
		}
	})

	$("#submitDeleteAward").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#awardUserDelete");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to delete this award?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitDeleteAward=1",
				success: function(data)
				{
					// Clear
					$("#awardID").find("option:selected").remove();

					// Alert to success
			  		alert("The award was deleted successfully!");

			  		// Show message if empty
			  		if($("#awardID").has("option").length <= 0)
			  		{
			  			$("#awardUserDelete").hide();
			  		}
				}
			});

	  		// Show message if empty
	  		if($("#awardID").has("option").length <= 0)
	  		{
	  			$("#awardUserDelete").hide();
	  		}
		}
	})

	$("#submitFinish").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#editEvents");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to finish this event?");

		if (r == true)
		{
			$("<form>Charity Money Raised:<br /><br /><input type=\"number\" style=\"z-index:10000\" name=\"charity\" /><br /></form>").dialog({
				modal: true,
				dialogClass: "no-close",
				closeOnEscape: false,

				buttons: {
					"OK": function () {
						var charity = $("input[name=\"charity\"]").val();

						// AJAX
						$.ajax({
							type: "POST",
							url: url,
							data: form.serialize() + "&submitFinish=1&charity=" + charity,
							success: function(data)
							{
								// Alert to success
						  		alert("The event was finished successfully!");
							}
						});
						// END AJAX

						$(this).dialog("close");
					}
				}
			});
		}
	})

	$("#submitConfirmList").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#confirmListForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to confirm these events?");


		if (r == true)
		{
			if($("#costumeChoice").val() != "")
			{
				$.ajax({
					type: "POST",
					url: url,
					data: form.serialize() + "&submitConfirmList=1",
					success: function(data)
					{
			            if($("input:checkbox:checked").length == 0)
			            {
			            	// Select a troop
			            	alert("Please select a troop to confirm.");
			            }
			            else
			            {
				            // If all items gone
				            if($("input:checkbox:checked").length == $("input:checkbox").length)
				            {
				            	// Hide whole area
				            	$("#confirmArea").html("");
				            }
				            else
				            {
				            	// If there is still data
				            	var json = JSON.parse(data);
				            	$("#confirmArea2").html(json.data);
				            }

				            alert("Troops confirmation submitted!");
			        	}
					}
				});
			}
			else
			{
				alert("Please select a costume.");
			}
		}
	})

	$("#submitConfirmListDelete").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#confirmListForm");
		var url = form.attr("action");

		var r = confirm("Are you sure you want to DELETE these events?");

		if (r == true)
		{
			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize() + "&submitConfirmListDelete=1",
				success: function(data)
				{
		            if($("input:checkbox:checked").length == 0)
		            {
		            	// Select a troop
		            	alert("Please select a troop to confirm.");
		            }
		            else
		            {
			            // If all items gone
			            if($("input:checkbox:checked").length == $("input:checkbox").length)
			            {
			            	// Hide whole area
			            	$("#confirmArea").html("");
			            }
			            else
			            {
			            	// If there is still data
			            	var json = JSON.parse(data);
				            $("#confirmArea2").html(json.data);
			            }

			            alert("Troops confirmation submitted!");
			        }
				}
			});
		}
	})

	$("#easyFillButton").button().click(function(e)
	{
		e.preventDefault();

		var form = $("#easyFillTool");
		var url = form.attr("action");

		// Get Text
		var text = $("#easyFill").val();

		// Convert to array
		var textArray = text.split(":");

		// Event Title
		var eventTitle = textArray.indexOf("Event Name");
		eventTitle = textArray[eventTitle + 1].split("\n");
		eventTitle[0] = eventTitle[0].slice(1);
		$("#eventName").val(eventTitle);

		// Event Venue
		var eventVenue = textArray[2].split("Venue address");
		$("#eventVenue").val(eventVenue[0].slice(1));

		// Event Location
		var eventLocation = textArray[3].split("Event Start");
		eventLocation[0] = eventLocation[0].replace(/[\r\n]+/g, " ");
		$("#location").val(eventLocation[0].slice(1));

		// Event Time ISSUE WITH : in time need to fix
		var eventTime = textArray[4].slice(1) + ":" + textArray[5].split("Event End")[0];
		$("#datepicker").val(eventTime);

		// Event Time 2
		var eventTime2 = textArray[6].slice(1) + ":" + textArray[7].split("Event Website")[0];
		$("#datepicker2").val(eventTime2);

		// Website
		var website = textArray[8].slice(1).split("Expected number of attendees")[0];
		$("#website").val(website);

		// Number of Attendees
		var numberOfAttend = textArray[9].slice(1).split("Requested number of characters")[0];
		$("#numberOfAttend").val(parseInt(numberOfAttend));

		// Number of Requested Characters
		var requestedNumber = textArray[10].slice(1).split("Requested character types")[0];
		$("#requestedNumber").val(parseInt(requestedNumber));

		// Requested Characters
		var requestedCharacter = textArray[11].slice(1).split("Secure changing/staging area")[0];
		$("#requestedCharacter").val(requestedCharacter);

		// Secure Changing Area?
		var secure = textArray[12].slice(1).split("Can troopers carry blasters")[0];
		if(secure.includes("No"))
		{
			$("#secure").val(0);
		}
		else
		{
			$("#secure").val(1);
		}

		// Can Troopers Carry Blasters?
		var blasters = textArray[13].slice(1).split("Can troopers carry/bring props like lightsabers and staffs")[0];
		if(blasters.includes("NO") || blasters.includes("no") || blasters.includes("No"))
		{
			$("#blasters").val(0);
		}
		else
		{
			$("#blasters").val(1);
		}

		// Can Troopers Carry Lightsabers?
		var lightsabers = textArray[14].slice(1).split("Is parking available")[0];
		if(lightsabers.includes("NO") || lightsabers.includes("no") || lightsabers.includes("No"))
		{
			$("#lightsabers").val(0);
		}
		else
		{
			$("#lightsabers").val(1);
		}

		// Parking?
		var parking = textArray[15].slice(1).split("Is venue accessible to those with limited mobility")[0];
		if(parking.includes("NO") || parking.includes("no") || parking.includes("No"))
		{
			$("#parking").val(0);
		}
		else
		{
			$("#parking").val(1);
		}

		// Mobility?
		var mobility = textArray[16].slice(1).split("Amenities available at venue")[0];
		if(mobility.includes("NO") || mobility.includes("no") || mobility.includes("No"))
		{
			$("#mobility").val(0);
		}
		else
		{
			$("#mobility").val(1);
		}

		// Amenities?
		var amenities = textArray[17].slice(1).split("Comments")[0];
		$("#amenities").val(amenities);

		// Comments
		var comments = textArray[18].slice(1).split("Referred by")[0];
		$("#comments").val(comments);

		// Referred By
		var referred = textArray[19].slice(1).split("Referred by")[0];
		$("#referred").val(referred);
	})
});
</script>
</body>
</html>';

?>