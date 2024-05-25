<?php

/**
 * This file is used for updating event posts with the most up-to-date information from Troop Tracker.
 * 
 * This should be run every two minutes by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Loop through all events to update threads
$query = "SELECT * FROM events WHERE closed = '0' AND thread_id != 0 AND post_id != 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Make roster
		$roster = '[b]Roster:[/b]';

		// Loop through all events to update threads
		$query2 = "SELECT * FROM event_sign_up WHERE troopid = '".$db->id."' ORDER BY signuptime ASC";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				// Set trooper name
				$name = getName($db2->trooperid);

				// Check if placeholder
				if($db2->note != "")
				{
					$name = $db2->note;
				}

				$roster .= '
				-[i]'.getStatus($db2->status).'[/i]: '.$name.' ('.getCostume($db2->costume).')
				';
			}
		}

		// Make thread body
		$thread_body = threadTemplate($db->name, $db->venue, $db->location, $db->dateStart, $db->dateEnd, $db->website, $db->numberOfAttend, $db->requestedNumber, $db->requestedCharacter, $db->secureChanging, $db->blasters, $db->lightsabers, $db->parking, $db->mobility, $db->amenities, $db->comments, $db->referred, $db->id, $db->label, $roster);

		// Get dates
		$date1 = date('Y-m-d H:i:s', strtotime($db->dateStart));
		$date2 = date('Y-m-d H:i:s', strtotime($db->dateEnd));
					
		// Update thread
		// If a shift
		if(isLink($db->id) > 0)
		{
			editThread($db->thread_id, date("m/d/y h:i A", strtotime($date1)) . " - " . date("h:i A", strtotime($date2)) . " " . readInput($db->name));
		}
		else
		{
			// Not a shift
			editThread($db->thread_id, date("m/d/y", strtotime($date1)) . " - " . readInput($db->name));
		}
		
		// Update post
		editPost($db->post_id, $thread_body);
	}
}

// Check for events that need to be closed
$date = date('Y-m-d H:i:s', strtotime('-1 HOUR'));
$statement = $conn->prepare("SELECT * FROM events WHERE dateEnd < ? AND closed != '2' AND closed != '1'");
$statement->bind_param("s", $date);
$statement->execute();

if ($result = $statement->get_result())
{
	while ($db = mysqli_fetch_object($result))
	{
		// Close them
		$statement2 = $conn->prepare("UPDATE events SET closed = '1' WHERE id = ?");
		$statement2->bind_param("i", $db->id);
		$statement2->execute();

		// Move thread
		moveThread($db->thread_id, labelToForumCategoryArchive($db->label, $db->squad));
	}
}

?>