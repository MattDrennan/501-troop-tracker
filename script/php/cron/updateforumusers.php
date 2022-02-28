<?php

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Set up all titles into an array
$groupTitles = array();

// Loop through all titles with Xenforo set up
$query = "SELECT * FROM titles WHERE forum_id != 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		array_push($groupTitles, $db->forum_id);
	}
}

// Loop through all troopers with Xenforo set up
$query = "SELECT * FROM troopers WHERE user_id != 0";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Check if 501 member
		if($db->p501 > 0)
		{
			// Update TKID on forum
			updateUserCustom($db->user_id, "tkid", $db->tkid);
		}

		// Set up group array
		$groupArray = array();

		// Set up another array to check differences later
		$groupArray2 = array();

		// Get user info
		$userInfo = getUserForumID($db->user_id);

		if(!isset($userInfo['errors']))
		{
			// Set group array
			$groupArray = $userInfo['user']['secondary_group_ids'];

			// Update user groups so they match troop tracker
			$query2 = "SELECT titles.forum_id, title_troopers.trooperid FROM titles LEFT JOIN title_troopers ON titles.id = title_troopers.titleid WHERE titles.forum_id != 0 AND title_troopers.trooperid = ".$db->id." GROUP BY titles.id";
			if ($result2 = mysqli_query($conn, $query2))
			{
				while ($db2 = mysqli_fetch_object($result2))
				{
					// Check if user group is already on profile
					if (!in_array($db2->forum_id, $groupArray))
					{
						// Not listed on forum, update
						array_push($groupArray, $db2->forum_id);
					}

					array_push($groupArray2, $db2->forum_id);
				}
			}

			// Check differences - Intersect titles in database with all titles with users, check difference with titles from database assigned to trooper
			$remove = array_diff(array_intersect($groupArray, $groupTitles), $groupArray2);

			$groupArray = array_diff($groupArray, $remove);

			// Update Xenforo groups
			updateUserForumGroup($db->user_id, $userInfo['user']['user_group_id'], $groupArray);
		}
	}
}

?>