<?php

/**
 * This file is used for auto accepting unconfirmed troops every 6 months to clear up the database
 * 
 * This should be run weekly by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

$conn->query("UPDATE event_sign_up SET status = 3 WHERE status = 0 AND signuptime <= NOW() - INTERVAL 6 MONTH");

?>