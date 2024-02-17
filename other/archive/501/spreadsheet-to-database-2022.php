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
        // Query
        if($value[1] == "Reserve")
        {
            $conn->query("UPDATE troopers SET p501 = 3 WHERE tkid = '".get_numerics(cleanInput($value[5]))."' AND squad <= ".count($squadArray)."") or die($conn->error);
            echo get_numerics(cleanInput($value[5])) . ' - Retired<br /><br />';
        }
        else if($value[1] == "Active")
        {
            $conn->query("UPDATE troopers SET p501 = 2 WHERE tkid = '".get_numerics(cleanInput($value[5]))."' AND squad <= ".count($squadArray)."") or die($conn->error);
            echo get_numerics(cleanInput($value[5])) . ' - Reserve<br /><br />';
        }
    }
    
    // Increment count
    $i++;
}

?>