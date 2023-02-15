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

// Loop through all troopers with Xenforo set up
$query = "SELECT * FROM troopers WHERE user_id != 0 AND approved = 1";
if ($result = mysqli_query($conn, $query))
{
	while ($db = mysqli_fetch_object($result))
	{
		// Update TKID on forum
		updateUserCustom($db->user_id, "tkid", getTKNumber($db->id, true));

		// Update Tracker ID on forum
		updateUserCustom($db->user_id, "trackerid", $db->id);
		
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
		
		/**
		 * Used to determine if a user has an active club / squad
		 *
		 * @var int
		*/
		$activeClubs = 0;

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

				$activeClubs++;
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

				$activeClubs++;
				array_push($groupArray2, $userGroupGarrison);
			}
			// 501st member, handler
			else if($db->p501 == 4)
			{
				if (!in_array($handlerUserGroup, $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $handlerUserGroup);
				}

				$activeClubs++;
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

					$activeClubs++;
					array_push($groupArray2, $club_value['userGroup']);	
				}
				// Member, handler
				else if($db->{$club_value['db']} == 4)
				{
					if (!in_array($handlerUserGroup, $groupArray))
					{
						// Not listed on forum, update
						array_push($groupArray, $handlerUserGroup);
					}

					$activeClubs++;
					array_push($groupArray2, $handlerUserGroup);
				}

				// Add to check array
				array_push($groupTitles, $club_value['userGroup']);
			}
			
			// Add retired status if no active clubs
			if($activeClubs == 0)
			{
				if (!in_array($userGroupRetired, $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $userGroupRetired);
				}

				array_push($groupArray2, $userGroupRetired);
			}

			// Check if RIP member
			if($db->permissions == 3)
			{
				if (!in_array($userGroupRIP, $groupArray))
				{
					// Not listed on forum, update
					array_push($groupArray, $userGroupRIP);
				}

				array_push($groupArray2, $userGroupRIP);
			}

			// Loop through squads to add to check array
			foreach($squadArray as $squad => $squad_value)
			{
				// Add to check array
				array_push($groupTitles, $squad_value['userGroup']);
			}

			/**
			 * Add $userGroupGarrison, $userGroup501st, $userGroupRetired, $handlerUserGroup, $userGroupRIP to the $groupTitles array
			 * so the code knows they exist in Troop Tracker DB.
			*/
			array_push($groupTitles, $userGroupGarrison);
			array_push($groupTitles, $userGroup501st);
			array_push($groupTitles, $userGroupRetired);
			array_push($groupTitles, $handlerUserGroup);
			array_push($groupTitles, $userGroupRIP);

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