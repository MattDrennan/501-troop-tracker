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
$values = getSheet("1nCywFavUvvWIdUb1Wacuf4xfdf9fZm4lUA34Oz2LE0w", "501st_unit_roster20230315");

// Set up count
$i = 0;

foreach($values as $value)
{
    // If not first
    if($i != 0)
    {
        // Build valid 501st squad ID list
        $validSquadIDs = array_merge([0], array_column($squadArray, 'squadID'));

        // Create IN clause safely
        $inClause = implode(',', array_map('intval', $validSquadIDs)); // static, no user input

        // Clean TKID once
        $tkid = get_numerics(cleanInput($value[6]));

        // Set default values
        $p501 = null;
        $statusText = 'Unknown';

        if ($value[1] == "Active") {
            $p501 = 1;
            $statusText = "Active";
        } elseif ($value[1] == "Reserve") {
            $p501 = 2;
            $statusText = "Reserve";
        } elseif ($value[1] == "Retired") {
            $p501 = 3;
            $statusText = "Retired";
        }

        if ($p501 !== null) {
            $query = "UPDATE troopers SET p501 = $p501 WHERE tkid = '$tkid' AND squad IN ($inClause)";
            $conn->query($query) or die($conn->error);
            echo $tkid . ' - ' . $statusText . '<br /><br />';
        }
    }
    
    // Increment count
    $i++;
}

?>