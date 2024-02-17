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
	$statement = $conn->prepare("UPDATE troopers SET supporter = '1' WHERE id = ?");
	$statement->bind_param("i", $custom);
	$statement->execute();
	
	// Don't allow duplicates
	$statement = $conn->prepare("SELECT * FROM donations WHERE txn_id = ?");
	$statement->bind_param("s", $txn_id);
	$statement->execute();
	$statement->store_result();
	$txn_count = $statement->num_rows;
	
	// If custom is not set
	if($custom == 0 || $custom == "")
	{
		if(!isset($_GET['trooperid'])) { die(""); }
		
		$custom = $_GET['trooperid'];
	}
	
	// Make sure there isn't a duplicate transaction
	if($txn_count == 0)
	{
		// Update donations
		$statement = $conn->prepare("INSERT INTO donations (trooperid, amount, txn_id, txn_type) VALUES (?, ?, ?, ?)");
		$statement->bind_param("idss", $custom, $mc_gross, $txn_id, $txn_type);
		$statement->execute();

		sendNotification(getName($custom) . " donated $" . $mc_gross . "", $custom);
	}
}

// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");
?>