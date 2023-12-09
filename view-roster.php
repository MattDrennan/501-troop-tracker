
<?php

/**
 * This file generates a roster for POC to view
 *
 * @author  Matthew Drennan
 *
 */

include 'config.php';

if(!isset($_GET['troopid'])) {
    die("Must include troop ID.");
}

echo '
<style>
table {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #ddd;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2;}

tr:hover {background-color: #ddd;}

td {
    position: relative;
    padding: 5px 10px;
}

th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #323f4e;
    color: white;
}

tr.strikeout td:before {
    content: " ";
    position: absolute;
    top: 50%;
    left: 0;
    border-bottom: 1px solid red;
    width: 100%;
}
</style>';

$list = array();

// Query database for event info
$query = "SELECT * FROM events WHERE id = '".cleanInput($_GET['troopid'])."'";
if ($result = mysqli_query($conn, $query))
{
    while ($db = mysqli_fetch_object($result))
    {
        echo '
        <table>
        <tr>
            <th>Trooper Name</th>   <th>TKID</th>   <th>Costume</th>    <th>Status</th>
        </tr>';

        // Query database for roster info
        $query2 = "SELECT event_sign_up.id AS signId, event_sign_up.note, event_sign_up.costume_backup, event_sign_up.costume, event_sign_up.status, event_sign_up.troopid, event_sign_up.addedby, event_sign_up.status, event_sign_up.signuptime, troopers.id AS trooperId, troopers.name, troopers.tkid, troopers.forum_id, troopers.squad FROM event_sign_up JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE troopid = '".cleanInput($_GET['troopid'])."' ORDER BY event_sign_up.id ASC";

        if ($result2 = mysqli_query($conn, $query2))
        {
            while ($db2 = mysqli_fetch_object($result2))
            {
                echo '
                <tr '.(getStatus($db2->status) == "Canceled" ? 'class="strikeout"' : '').'>
                    <td>'.$db2->name.'</td>   <td>'.$db2->tkid.'</td>   <td>'.getCostume($db2->costume).'</td>   <td>'.getStatus($db2->status).'</td>
                </tr>';
            }
        }
    }
}

echo '</table>';

?>