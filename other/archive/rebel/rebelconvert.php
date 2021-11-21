<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Unlimited time to execute
ini_set('max_execution_time', '0');
set_time_limit(0);

// Include credential file
require 'cred.php';

// Connect to server - 1
$conn = new mysqli(dbServer, dbUser, dbPassword, dbName);

// Connect to server - 2
$conn2 = new mysqli(dbServer2, dbUser2, dbPassword2, dbName2);

// Check for errors
// Check connection to server - 1
if ($conn->connect_error)
{
	trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

// Check connection to server - 2
if ($conn2->connect_error)
{
	trigger_error('Database connection failed: ' . $conn2->connect_error, E_USER_ERROR);
}

// Event count
$i = 0;

// Event picked
$pickedEvents = array();

if(isset($_POST['submit']))
{
	foreach($_POST as $key => $value)
	{
		// Not submit
		if($key != "submit")
		{
			// 1
			//$conn2->query("UPDATE events SET newid = '".$key."' WHERE id = '".$value."'") or die($conn2->error);
			//$conn->query("UPDATE events SET newid = '1' WHERE id = '".$key."'") or die($conn2->error);

			//2
			$conn2->query("UPDATE events SET newid = '".$value."' WHERE id = '".$key."'") or die($conn2->error);
			$conn->query("UPDATE events SET newid = '1' WHERE id = '".$value."'") or die($conn2->error);

			// Print
			echo $key . ' - ' . $value . ' - DONE!<br />';
		}
	}
}
else
{
	// Loop through events - Exact match
	/*$query = "SELECT * FROM events ORDER BY id DESC";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Loop through events
			$query2 = "SELECT * FROM events WHERE title = '".$db->name."' AND newid == 0";
			if ($result2 = mysqli_query($conn2, $query2))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// Convert dates
					$rD = date("m-d-Y", $db2->date);
					$sD = date("m-d-Y", strtotime($db->dateStart));
					
					// Make sure not already picked, name isn't equal to nothing, and dates match
					if(!in_array($db2->id, $pickedEvents) && $db->name != "" && $rD == $sD)
					{
						//echo $db->name . ' == ' . $db2->title;
						//echo '<br /><br />';
						
						// Increment
						$i++;
						
						// Push to array
						array_push($pickedEvents, $db2->id);

						$conn2->query("UPDATE events SET newid = '".$db->id."' WHERE id = '".$db2->id."'");
					}
				}
			}
		}
	}*/

	// Loop through events - close match (LIKE name & exact date)
	/*$query = "SELECT * FROM events ORDER BY id DESC";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Loop through events
			$query2 = "SELECT * FROM events WHERE title LIKE '%".$db->name."%' AND newid = 0";
			if ($result2 = mysqli_query($conn2, $query2))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// Convert dates
					$rD = date("m-d-Y", $db2->date);
					$sD = date("m-d-Y", strtotime($db->dateStart));
					
					// Make sure not already picked, name isn't equal to nothing, and dates match
					if(!in_array($db2->id, $pickedEvents) && $db->name != "" && $rD == $sD)
					{
						//echo $db->name . ' == ' . $db2->title;
						//echo '<br /><br />';
						
						// Increment
						$i++;
						
						// Push to array
						array_push($pickedEvents, $db2->id);

						$conn2->query("UPDATE events SET newid = '".$db->id."' WHERE id = '".$db2->id."'");
					}
				}
			}
		}
	}*/
	$c = 0;

	// Loop through events (exact date)
	/*
	$query = "SELECT * FROM events WHERE newid = 0 and id > 6825 ORDER BY id ASC";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Setup to see if to show event
			$t = 0;

			if($c == 0)
			{
				echo '
				<form action="" method="POST">';
			}

			// Convert date
			$sD = date("Y-m-d", strtotime($db->dateStart));
			
			// Loop through events
			$query2 = "SELECT id, title, date, title, DATE_FORMAT(from_unixtime(date), '%Y-%m-%d') AS test FROM events WHERE newid = '0' AND DATE_FORMAT(from_unixtime(date), '%Y-%m-%d') = '".$sD."'";
			if ($result2 = mysqli_query($conn2, $query2) or die($conn2->error))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// If first loop
					if($t == 0)
					{
						echo '
						<b>'.$db->id.' - ' . $db->name . ':</b>
						<br />';
					}
					
					echo '<input type="radio" name="'.$db->id.'" value="'.$db2->id.'" />' . $db2->id . ' - ' . $db2->title;
					echo '<br />';
					
					// Increment
					$t++;
					$c++;
				}
			}
		}
	}

	// Don't show if nothing to show
	if($c > 0)
	{
		echo '
		<br />
		<input type="submit" name="submit" value="Submit!" />
		</form>';
	}*/

	/*$query = "SELECT id, title, date, title, DATE_FORMAT(from_unixtime(date), '%Y-%m-%d') AS test FROM events WHERE newid = '0'";
	if ($result = mysqli_query($conn2, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Setup to see if to show event
			$t = 0;

			if($c == 0)
			{
				echo '
				<form action="" method="POST">';
			}
			
			// Loop through events
			$query2 = "SELECT * FROM events WHERE dateStart > from_unixtime(".$db->date.") - INTERVAL 30 DAY AND dateStart < from_unixtime(".$db->date.") + INTERVAL 30 DAY ORDER BY id ASC";
			if ($result2 = mysqli_query($conn, $query2) or die($conn2->error))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// If first loop
					if($t == 0)
					{
						echo '
						<b>'.$db->test.' - '.$db->id.' - ' . $db->title . ':</b>
						<br />';
					}
					
					echo '<input type="radio" name="'.$db->id.'" value="'.$db2->id.'" />' . $db2->id . ' - (' . $db2->dateStart . ') - ' . $db2->name;
					echo '<br />';
					
					// Increment
					$t++;
					$c++;
				}
			}
		}
	}

	// Don't show if nothing to show
	if($c > 0)
	{
		echo '
		<br />
		<input type="submit" name="submit" value="Submit!" />
		</form>';
	}

	echo '<br>Found: '.$c.'<br>';

	echo '
	<b>Total Events Matched:</b> ' . $i .
	'<br /><br />';*/

	$query = "SELECT id, title, date, title, DATE_FORMAT(from_unixtime(date), '%Y-%m-%d') AS test FROM events WHERE newid = '0'";
	if ($result = mysqli_query($conn2, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{		
			// Loop through events
			echo '
			<b>'.$db->test.' - '.$db->id.' - ' . $db->title . ':</b>
			<br />';
		}
	}

	$query = "SELECT * FROM events";
	if ($result = mysqli_query($conn, $query))
	{
		while ($db = mysqli_fetch_object($result))
		{
			// Loop through events
			echo '
			<b>****'.$db->dateStart.' - '.$db->id.' - ' . $db->name . ':</b>
			<br />';
		}
	}

	// Setup count for missing troops
	$j = 0;

	// Loop through events - Missing
	$query2 = "SELECT * FROM events WHERE newid = 0 ORDER BY date";
	if ($result2 = mysqli_query($conn2, $query2))
	{
		while ($db2 = mysqli_fetch_object($result2))
		{
			// Increment
			$j++;
		}
	}

	echo '<b>Events Missing:</b> ' . $j;
}

?>