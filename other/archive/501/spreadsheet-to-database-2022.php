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
$values = getSheet("1EEFwnQVvuyXiKtStyur5Gj7Cf_NG0lURCLvAXtj0JH4", "501st_unit_roster20221105");

// Set up count
$i = 0;

foreach($values as $value)
{
    // If not first
    if($i != 0)
    {
        // Build valid 501st squad ID list
        $validSquadIDs = array_merge([0], array_column($squadArray, 'squadID'));

        // Create placeholders for squad IN clause
        $placeholders = implode(',', array_map('intval', $validSquadIDs)); // safe since no user input

        $tkid = get_numerics(cleanInput($value[5]));

        // Determine permission level
        if ($value[1] == "Reserve") {
            $p501 = 3; // Retired
            $statusText = "Retired";
        } elseif ($value[1] == "Active") {
            $p501 = 2; // Reserve
            $statusText = "Reserve";
        } else {
            $p501 = null;
            $statusText = "Unknown Status";
        }

        // Only run the query if a valid status was matched
        if ($p501 !== null) {
            $conn->query("UPDATE troopers SET p501 = $p501 WHERE tkid = '$tkid' AND squad IN ($placeholders)") or die($conn->error);
            echo $tkid . ' - ' . $statusText . '<br /><br />';
        }
    }
    
    // Increment count
    $i++;
}

?>