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
$query = "SELECT * FROM events WHERE closed = '0'";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Make thread body
		$thread_body = '
		[b]Event Name:[/b] '.$db->name.'
		[b]Venue:[/b] '.$db->venue.'
		[b]Venue address:[/b] '.$db->location.'
		[b]Event Start:[/b] '.date("m/d/y h:i A", strtotime($db->dateStart)).'
		[b]Event End:[/b] '.date("m/d/y h:i A", strtotime($db->dateEnd)).'
		[b]Event Website:[/b] '.$db->website.'
		[b]Expected number of attendees:[/b] '.number_format($db->numberOfAttend).'
		[b]Requested number of characters:[/b] '.number_format($db->requestedNumber).'
		[b]Requested character types:[/b] '.$db->requestedCharacter.'
		[b]Secure changing/staging area:[/b] '.yesNo($db->secureChanging).'
		[b]Can troopers carry blasters:[/b] '.yesNo($db->blasters).'
		[b]Can troopers carry/bring props like lightsabers and staffs:[/b] '.yesNo($db->lightsabers).'
		[b]Is parking available:[/b] '.yesNo($db->parking).'
		[b]Is venue accessible to those with limited mobility:[/b] '.yesNo($db->mobility).'
		[b]Amenities available at venue:[/b] '.ifEmpty($db->amenities, "No amenities for this event.").'
		[b]Comments:[/b] '.ifEmpty($db->comments, "No comments for this event.").'
		[b]Referred by:[/b] '.ifEmpty($db->referred, "Not available").'

		[b]Roster:[/b]';

		// Loop through all events to update threads
		$query2 = "SELECT * FROM event_sign_up WHERE troopid = '".$db->id."' ORDER BY signuptime DESC";
		if ($result2 = mysqli_query($conn, $query2))
		{
			while ($db2 = mysqli_fetch_object($result2))
			{
				$thread_body .= '
				-[i]'.getStatus($db2->status).'[/i]: '.getName($db2->trooperid).' ('.getCostume($db2->costume).')
				';
			}
		}

		$thread_body .= '
		[b][u]Sign Up / Event Roster:[/u][/b]

		[url]https://fl501st.com/troop-tracker/index.php?event=' . $db->id . '[/url]';

		// Update thread
		editPost($db->post_id, $thread_body);
	}
}

?>