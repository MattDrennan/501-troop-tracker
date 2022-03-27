<?php

/**
 * This file is used for updating forum data such as custom variables and user groups from Troop Tracker data.
 * 
 * This should be run every 30 minutes to an hour by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

/**
 * Used to store all Troop Tracker titles that correspond with Xenforo forum user groups
 * 
 * @var array
*/
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
$query = "SELECT * FROM troopers WHERE user_id != 0 AND approved = 1";
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
		
		// Update full name
		updateUserCustom($db->user_id, "fullname", $db->name);

		/**
		 * Used to store Xenforo forum user group values
		 * 
		 * @var array
		*/
		$groupArray = array();

		/**
		 * Used to store applicable Troop Tracker titles to user
		 * 
		 * @var array
		*/
		$groupArray2 = array();

		// Get user info
		$userInfo = getUserForumID($db->user_id);

		if(!isset($userInfo['errors']))
		{
			// Set group array
			$groupArray = $userInfo['user']['secondary_group_ids'];

			// Add Troop Tracker user group
			array_push($groupArray, $troopTrackerUserGroup);
			array_push($groupArray2, $troopTrackerUserGroup);

			// Check if 501st member with squad
			if($db->p501 > 0 && $db->p501 < 3 && $db->squad > 0 && $db->squad <= count($squadArray))
			{
				if (!in_array($squadArray[($db->squad - 1)]['userGroup'], $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $squadArray[($db->squad - 1)]['userGroup']);
				}

				array_push($groupArray2, $squadArray[($db->squad - 1)]['userGroup']);

				// Check if Florida Garrison member set
				if (!in_array($userGroupGarrison, $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $userGroupGarrison);
				}

				array_push($groupArray2, $userGroupGarrison);
			}
			// 501st member, no squad
			else if($db->p501 > 0 && $db->p501 < 3 && $db->squad == 0)
			{
				if (!in_array($userGroupGarrison, $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $userGroupGarrison);
				}

				array_push($groupArray2, $userGroupGarrison);
			}
			// 501st member, retired
			else if($db->p501 == 3)
			{
				if (!in_array($userGroupRetired, $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $userGroupRetired);
				}

				array_push($groupArray2, $userGroupRetired);
			}
			// 501st member, handler
			else if($db->p501 == 4)
			{
				if (!in_array($handlerUserGroup, $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $handlerUserGroup);
				}

				array_push($groupArray2, $handlerUserGroup);
			}

			// Loop through clubs
			foreach($clubArray as $club => $club_value)
			{
				// Check if club member
				if($db->{$club_value['db']} > 0 && $db->{$club_value['db']} < 3)
				{
					if (!in_array($club_value['userGroup'], $groupArray))
					{
						// Not listed on forum, update
						array_push($groupArray, $club_value['userGroup']);
					}

					array_push($groupArray2, $club_value['userGroup']);	
				}
				// Member, retired
				else if($db->{$club_value['db']} == 3)
				{
					if (!in_array($userGroupRetired, $groupArray))
					{
						// Not listed on forum, update
						array_push($groupArray, $userGroupRetired);
					}

					array_push($groupArray2, $userGroupRetired);	
				}
				// Member, handler
				else if($db->{$club_value['db']} == 4)
				{
					if (!in_array($handlerUserGroup, $groupArray))
					{
						// Not listed on forum, update
						array_push($groupArray, $handlerUserGroup);
					}

					array_push($groupArray2, $handlerUserGroup);
				}

				// Add to check array
				array_push($groupTitles, $club_value['userGroup']);
			}

			// Loop through squads to add to check array
			foreach($squadArray as $squad => $squad_value)
			{
				// Add to check array
				array_push($groupTitles, $squad_value['userGroup']);
			}

			/**
			 * Add $userGroupGarrison, $userGroup501st, $userGroupRetired to the $groupTitles array
			 * so the code knows they exist in Troop Tracker DB.
			*/
			array_push($groupTitles, $userGroupGarrison);
			array_push($groupTitles, $userGroup501st);
			array_push($groupTitles, $userGroupRetired);

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

			/**
			 * Check differences - Intersect titles in Troop Tracker DB ($groupTitles) with forum titles ($groupArray), return values that are the same. Check difference between assigned Troop Tracker DB titles and Forum titles. Will return the values to remove.
			 * 
			 * @var array
			*/
			
			$remove = array_diff(array_intersect($groupArray, $groupTitles), $groupArray2);

			/**
			 * Check difference between forum DB titles and values to remove. Will return forum titles without removed titles.
			*/

			$groupArray = array_diff($groupArray, $remove);

			// Update Xenforo groups
			updateUserForumGroup($db->user_id, $userInfo['user']['user_group_id'], $groupArray);
		}
	}
}

?>