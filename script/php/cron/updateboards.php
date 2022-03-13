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
		$query2 = "SELECT * FROM event_sign_up WHERE troopid = '".$db->id."' ORDER BY signuptime DESC";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				$roster .= '
				-[i]'.getStatus($db2->status).'[/i]: '.getName($db2->trooperid).' ('.getCostume($db2->costume).')
				';
			}
		}

		// Make thread body
		$thread_body = threadTemplate($db->name, $db->venue, $db->location, $db->dateStart, $db->dateEnd, $db->website, $db->numberOfAttend, $db->requestedNumber, $db->requestedCharacter, $db->secureChanging, $db->blasters, $db->lightsabers, $db->parking, $db->mobility, $db->amenities, $db->comments, $db->referred, $db->id, $db->label, $roster);

		// Update thread
		editPost($db->post_id, $thread_body);
	}
}

?>