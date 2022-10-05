<?php

/**
 * This file is used for processing PayPal IPN data.
 *
 */

namespace Listener;

require('PaypalIPN.php');

use PaypalIPN;

$ipn = new PaypalIPN();

// Include config file
include "../../config.php";

$verified = $ipn->verifyIPN();
if ($verified)
{
	$custom = $_POST["custom"];
	$txn_id = $_POST["txn_id"];
	$mc_gross = $_POST["mc_gross"];
	$txn_type = $_POST["txn_type"];

	// Execute query
	$conn->query("UPDATE troopers SET supporter = '1' WHERE id = '".$custom."'");
	
	// Don't allow duplicates
	$txn_count = $conn->query("SELECT * FROM donations WHERE txn_id = '".$txn_id."'");
	
	// If custom is not set
	if($custom == 0 || $custom == "")
	{
		if(!isset($_GET['trooperid'])) { die(""); }
		
		$custom = cleanInput($_GET['trooperid']);
	}
	
	// Make sure there isn't a duplicate transaction
	if($txn_count->num_rows == 0)
	{
		// Update donations
		$conn->query("INSERT INTO donations (trooperid, amount, txn_id, txn_type) VALUES ('".$custom."', '".$mc_gross."', '".$txn_id."', '".$txn_type."')");
		sendNotification(getName($custom) . " donated $" . $mc_gross . ".", $custom);
	}
}

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");
?>