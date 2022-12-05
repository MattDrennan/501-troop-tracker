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

// Drop troopers
$conn->query("UPDATE event_sign_up LEFT JOIN events ON event_sign_up.troopid = events.id SET event_sign_up.status = 4 WHERE event_sign_up.status = 2 AND NOW() > events.dateStart - INTERVAL 1 DAY");

// Loop through event sign ups that are within 7 days and still have tentative troopers
$query = "SELECT events.dateStart, events.id, event_sign_up.trooperid FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE status = 2 AND NOW() > events.dateStart - INTERVAL 7 DAY GROUP BY event_sign_up.trooperid";

if ($result = mysqli_query($conn, $query))
{
    while ($db = mysqli_fetch_object($result))
    {        
        // Set up message
        $message = "Hello!\n\nYou are signed up for troop(s) as a tentative trooper and the troop(s) are within 7 days time from now. Please set yourself as going or canceled, otherwise you will be dropped from the troop. This is to help other troopers and command staff plan accordingly.\n\n";
        
        // Send E-mail
        sendEmail($db->email, readInput($db->name), "Troop Tracker: Please adjust your status!", readInput($message));
    }
}

$conn->query("UPDATE events.dateStart, events.id, event_sign_up.trooperid FROM event_sign_up LEFT JOIN events ON events.id = event_sign_up.troopid WHERE status = 2 AND NOW() > events.dateStart - INTERVAL 7 DAY");

?>