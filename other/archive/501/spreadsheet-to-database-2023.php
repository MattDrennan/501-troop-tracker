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
        // Query
        if($value[1] == "Reserve")
        {
            $conn->query("UPDATE troopers SET p501 = 2 WHERE tkid = '".get_numerics(cleanInput($value[6]))."' AND squad <= ".count($squadArray)."") or die($conn->error);
            echo get_numerics(cleanInput($value[6])) . ' - Retired<br /><br />';
        }
        else if($value[1] == "Retired")
        {
            $conn->query("UPDATE troopers SET p501 = 3 WHERE tkid = '".get_numerics(cleanInput($value[6]))."' AND squad <= ".count($squadArray)."") or die($conn->error);
            echo get_numerics(cleanInput($value[6])) . ' - Retired<br /><br />';
        }
        else if($value[1] == "Active")
        {
            $conn->query("UPDATE troopers SET p501 = 1 WHERE tkid = '".get_numerics(cleanInput($value[6]))."' AND squad <= ".count($squadArray)."") or die($conn->error);
            echo get_numerics(cleanInput($value[6])) . ' - Reserve<br /><br />';
        }
    }
    
    // Increment count
    $i++;
}

?>