<?php

// Include config
include "../../config.php";

// Get data from Paypal Webhook POST
$data = json_decode(file_get_contents("php://input"));

// Subscription activated
if ($data->event_type == 'BILLING.SUBSCRIPTION.ACTIVATED')
{
    // User ID
    $userid = $data->resource->custom_id;

	// If user ID is not empty
    if (!empty($userid))
    {
		// Query
		$conn->query("UPDATE troopers SET supporter = '1' WHERE id = '".$userid."'");
    }
}

// Subscription CREATED
if ($data->event_type == 'BILLING.SUBSCRIPTION.CREATED')
{
    // User ID
    $userid = $data->resource->custom_id;

	// If user ID is not empty
    if (!empty($userid))
    {
		// Query
		$conn->query("UPDATE troopers SET supporter = '1' WHERE id = '".$userid."'");
    }
}

// Subscription EXPIRED
if ($data->event_type == 'BILLING.SUBSCRIPTION.EXPIRED')
{
    // User ID
    $userid = $data->resource->custom_id;

	// If user ID is not empty
    if (!empty($userid))
    {
		// Query
		$conn->query("UPDATE troopers SET supporter = '0' WHERE id = '".$userid."'");
    }
}

// Subscription CANCELLED
if ($data->event_type == 'BILLING.SUBSCRIPTION.CANCELLED')
{
    // User ID
    $userid = $data->resource->custom_id;

	// If user ID is not empty
    if (!empty($userid))
    {
		// Query
		$conn->query("UPDATE troopers SET supporter = '0' WHERE id = '".$userid."'");
    }
}

// Subscription SUSPENDED
if ($data->event_type == 'BILLING.SUBSCRIPTION.SUSPENDED')
{
    // User ID
    $userid = $data->resource->custom_id;

	// If user ID is not empty
    if (!empty($userid))
    {
		// Query
		$conn->query("UPDATE troopers SET supporter = '0' WHERE id = '".$userid."'");
    }
}

// Subscription FAILED
if ($data->event_type == 'BILLING.SUBSCRIPTION.PAYMENT.FAILED')
{
    // User ID
    $userid = $data->resource->custom_id;

	// If user ID is not empty
    if (!empty($userid))
    {
		// Query
		$conn->query("UPDATE troopers SET supporter = '0' WHERE id = '".$userid."'");
    }
}

?>