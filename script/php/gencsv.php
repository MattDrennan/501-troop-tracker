
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
$statement = $conn->prepare("SELECT * FROM events WHERE id = ?");
$statement->bind_param("i", $_GET['troopid']);
$statement->execute();

if ($result = $statement->get_result())
{
    while ($db = mysqli_fetch_object($result))
    {
        array_push($list, ['', '', $db->name, '']);
        array_push($list, ['Trooper Name', 'TKID', 'Forum Name', 'Costume', 'Status']);

        // Query database for roster info
        $statement2 = $conn->prepare("SELECT event_sign_up.id AS signId, event_sign_up.note, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, event_sign_up.status, event_sign_up.signuptime, troopers.id AS trooperId, troopers.name, troopers.tkid, troopers.forum_id, troopers.squad FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = ? ORDER BY event_sign_up.id ASC");
        $statement2->bind_param("i", $_GET['troopid']);
        $statement2->execute();

        if ($result2 = $statement2->get_result())
        {
            while ($db2 = mysqli_fetch_object($result2))
            {
                array_push($list, [$db2->name, $db2->tkid, $db2->forum_id, getCostume($db2->costume), getStatus($db2->status)]);
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

    $f = fopen('php://output', 'w');

    foreach ($array as $line) {
        fputcsv($f, $line, $delimiter);
    }
}

?>