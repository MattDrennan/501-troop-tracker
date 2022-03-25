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
		$thread_body = threadTemplate(html_entity_decode($db->name), html_entity_decode($db->venue), html_entity_decode($db->location), $db->dateStart, $db->dateEnd, $db->website, $db->numberOfAttend, $db->requestedNumber, $db->requestedCharacter, $db->secureChanging, $db->blasters, $db->lightsabers, $db->parking, $db->mobility, html_entity_decode($db->amenities), html_entity_decode($db->comments), html_entity_decode($db->referred), $db->id, $db->label, $roster);

		// Update thread
		editPost($db->post_id, $thread_body);
	}
}

?>