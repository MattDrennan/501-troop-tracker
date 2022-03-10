<?php

/**
 * This file is used for processing PayPal IPN data.
 *
 */

// Read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value)
{
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// Post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

// If testing on Sandbox use:
//$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);


// Assign posted variables to local variables
$item_name = $_POST['item_name'];
$business = $_POST['business'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$mc_gross = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$receiver_id = $_POST['receiver_id'];
$quantity = $_POST['quantity'];
$num_cart_items = $_POST['num_cart_items'];
$payment_date = $_POST['payment_date'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$payment_type = $_POST['payment_type'];
$payment_status = $_POST['payment_status'];
$payment_gross = $_POST['payment_gross'];
$payment_fee = $_POST['payment_fee'];
$settle_amount = $_POST['settle_amount'];
$memo = $_POST['memo'];
$payer_email = $_POST['payer_email'];
$txn_type = $_POST['txn_type'];
$payer_status = $_POST['payer_status'];
$address_street = $_POST['address_street'];
$address_city = $_POST['address_city'];
$address_state = $_POST['address_state'];
$address_zip = $_POST['address_zip'];
$address_country = $_POST['address_country'];
$address_status = $_POST['address_status'];
$item_number = $_POST['item_number'];
$tax = $_POST['tax'];
$option_name1 = $_POST['option_name1'];
$option_selection1 = $_POST['option_selection1'];
$option_name2 = $_POST['option_name2'];
$option_selection2 = $_POST['option_selection2'];
$for_auction = $_POST['for_auction'];
$invoice = $_POST['invoice'];
$custom = $_POST['custom'];
$notify_version = $_POST['notify_version'];
$verify_sign = $_POST['verify_sign'];
$payer_business_name = $_POST['payer_business_name'];
$payer_id =$_POST['payer_id'];
$mc_currency = $_POST['mc_currency'];
$mc_fee = $_POST['mc_fee'];
$exchange_rate = $_POST['exchange_rate'];
$settle_currency  = $_POST['settle_currency'];
$parent_txn_id  = $_POST['parent_txn_id'];
$pending_reason = $_POST['pending_reason'];
$reason_code = $_POST['reason_code'];


// Subscription specific vars

$subscr_id = $_POST['subscr_id'];
$subscr_date = $_POST['subscr_date'];
$subscr_effective  = $_POST['subscr_effective'];
$period1 = $_POST['period1'];
$period2 = $_POST['period2'];
$period3 = $_POST['period3'];
$amount1 = $_POST['amount1'];
$amount2 = $_POST['amount2'];
$amount3 = $_POST['amount3'];
$mc_amount1 = $_POST['mc_amount1'];
$mc_amount2 = $_POST['mc_amount2'];
$mc_amount3 = $_POST['mcamount3'];
$recurring = $_POST['recurring'];
$reattempt = $_POST['reattempt'];
$retry_at = $_POST['retry_at'];
$recur_times = $_POST['recur_times'];
$username = $_POST['username'];
$password = $_POST['password'];

// Auction specific vars

$for_auction = $_POST['for_auction'];
$auction_closing_date  = $_POST['auction_closing_date'];
$auction_multi_item  = $_POST['auction_multi_item'];
$auction_buyer_id  = $_POST['auction_buyer_id'];

// Include config file
include "../../config.php";

if (!$fp)
{
	// HTTP ERROR
}
else
{
	fputs ($fp, $header . $req);
	
	while (!feof($fp))
	{
		$res = fgets ($fp, 1024);
		
		if (strpos($res, "VERIFIED") !== 0)
		{
			// Execute query
			$conn->query("UPDATE troopers SET supporter = '1' WHERE id = '".$custom."'");
			
			// Don't allow duplicates
			$txn_count = $conn->query("SELECT * FROM donations WHERE txn_id = '".$txn_id."'");
			
			// If custom is not set
			if($custom == 0 || $custom == "")
			{
				$custom = cleanInput($_GET['trooperid']);
			}
			
			// Make sure there isn't a duplicate transaction
			if($txn_count->num_rows == 0)
			{
				// Make sure this is a payment completed
				if($payment_status == "Completed")
				{
					// Update donations
					$conn->query("INSERT INTO donations (trooperid, amount, txn_id) VALUES ('".$custom."', '".$mc_gross."', '".$txn_id."')");
					sendNotification(getName($custom) . " donated $" . $mc_gross . ".", $custom);
				}
			}

			// Echo
			echo "Verified";
		}
		else if(strpos($res,'INVALID') !== 0)
		{
			// Nothing
		}
	}
	fclose ($fp);
}
?>

