
<?php

/**
 * This file generates a CSV file from a troop
 *
 * @author  Matthew Drennan
 *
 */

include '../../config.php';

if(!isset($_GET['troopid'])) {
    die("Must include troop ID.");
}

$list = array();

// Query database for event info
$query = "SELECT * FROM events WHERE id = '".cleanInput($_GET['troopid'])."'";
if ($result = mysqli_query($conn, $query))
{
    while ($db = mysqli_fetch_object($result))
    {
        array_push($list, ['', $db->name, '']);
        array_push($list, ['Trooper Name', 'TKID', 'Forum Name', 'Status']);

        // Query database for roster info
        $query2 = "SELECT event_sign_up.id AS signId, event_sign_up.note, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, event_sign_up.status, event_sign_up.signuptime, troopers.id AS trooperId, troopers.name, troopers.tkid, troopers.forum_id, troopers.squad FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".cleanInput($_GET['troopid'])."' ORDER BY event_sign_up.id ASC";

        if ($result2 = mysqli_query($conn, $query2))
        {
            while ($db2 = mysqli_fetch_object($result2))
            {
                array_push($list, [$db2->name, $db2->tkid, $db2->forum_id, getStatus($db2->status)]);
            }
        }
    }
}
   
array_to_csv_download($list);

/**
 * Generate CSV file from array
*/
function array_to_csv_download($array, $filename = "export.csv", $delimiter=",") {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');

    // open the "output" stream
    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
    $f = fopen('php://output', 'w');

    foreach ($array as $line) {
        fputcsv($f, $line, $delimiter);
    }
}

?>