<?php

/**
 * This file is used for e-mailing tentative troopers and removing them from troops
 * 
 * This should be run daily by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Cronjob check
$isCLI = (php_sapi_name() == 'cli');
if(!$isCLI)
{
    die("Sorry! Cannot run in a browser! This script is set to run via cron job.");
}

// Drop troopers
$conn->query("UPDATE event_sign_up LEFT JOIN events ON event_sign_up.troopid = events.id SET event_sign_up.status = 4 WHERE event_sign_up.status = 2 AND NOW() > events.dateStart - INTERVAL 3 DAY");

// Loop through event sign ups that are within 7 days and still have tentative troopers
$query = "SELECT troopers.user_id, troopers.email, troopers.name, events.dateStart, events.id, event_sign_up.trooperid FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid LEFT JOIN troopers ON troopers.id = event_sign_up.trooperid WHERE event_sign_up.status = 2 AND NOW() > events.dateStart - INTERVAL 7 DAY GROUP BY event_sign_up.trooperid";

if ($result = mysqli_query($conn, $query))
{
    while ($db = mysqli_fetch_object($result))
    {        
        // Set up message
        $message = "Hello!\n\nYou are signed up for troop(s) as a tentative trooper that occurs within 7 days. Please set yourself as going or canceled, otherwise you will be dropped from the troop. This is to help other troopers and command staff plan accordingly.\n\n";

        // Send Alert
        createAlert($db->user_id, "You are signed up for troop(s) as a tentative trooper that occurs within 7 days. Please change your status to going or canceled to help other troopers and command staff plan accordingly.");
        
        // Send E-mail
        sendEmail($db->email, readInput($db->name), "Troop Tracker: Please adjust your status!", readInput($message));
    }
}

?>