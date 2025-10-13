<?php

/**
 * This file is used for converting 501st spreadsheets to Troop Tracker data.
 * 
 * This file is stored for archiving purposes.
 *
 * @author  Matthew Drennan
 *
 */

include 'config.php';

// Pull extra data from spreadsheet
$values = getSheet("10_w4Fz41iUCYe3G1bQSqHDY6eK4fXP0Ue3pnfA4LoZg", "Roster");

// Set up count
$i = 0;

foreach($values as $value)
{
    // If not first
    if($i != 0)
    {
        // Build valid 501st squad ID list
        $validSquadIDs = array_merge([0], array_column($squadArray, 'squadID'));
        $inClause = implode(',', array_map('intval', $validSquadIDs)); // Safe, static values

        // Clean TKID once
        $tkid = get_numerics(cleanInput($value[6]));

        // Determine p501 status based on member type
        $statusMap = [
            "Active"  => 1,
            "Reserve" => 2,
            "Retired" => 3
        ];

        if (isset($statusMap[$value[1]])) {
            $p501 = $statusMap[$value[1]];
            $statusText = $value[1];

            $query = "UPDATE troopers SET p501 = $p501 WHERE tkid = '$tkid' AND squad IN ($inClause)";
            $conn->query($query) or die($conn->error);

            echo $tkid . ' - ' . $statusText . '<br /><br />';
        }
    }
    
    // Increment count
    $i++;
}

?>