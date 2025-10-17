<?php

$fileDir = '../';  // Relative path from this script to the Xenforo root
require('src/XF.php');
XF::start($fileDir);
$app = XF::setupApp('XF\Pub\App');
$app->start();

// Now you can query the database
$db = \XF::db();

// Now query the required tables
try {
    // Fetch data from xf_user_upgrade_active
    $userUpgradeActive = $db->fetchAll("SELECT * FROM xf_user_upgrade_active");

    // Fetch data from xf_user_upgrade_expired
    $userUpgradeExpired = $db->fetchAll("SELECT * FROM xf_user_upgrade_expired");

    // Get the start and end timestamps for the current month
    $startOfMonth = strtotime(date('Y-m-01 00:00:00'));  // First day of the current month
    $endOfMonth = strtotime(date('Y-m-t 23:59:59'));     // Last day of the current month

    // Query to select data from xf_user_upgrade_active within the current month
    $activeResults = $db->fetchAll("
        SELECT 'active' AS status, user_upgrade_record_id, user_id, user_upgrade_id, start_date, end_date
        FROM xf_user_upgrade_active
        WHERE start_date >= ? AND start_date <= ?
    ", [$startOfMonth, $endOfMonth]);

    // Query to select data from xf_user_upgrade_expired within the current month
    $expiredResults = $db->fetchAll("
        SELECT 'expired' AS status, user_upgrade_record_id, user_id, user_upgrade_id, start_date, end_date
        FROM xf_user_upgrade_expired
        WHERE start_date >= ? AND start_date <= ?
    ", [$startOfMonth, $endOfMonth]);

    // Combine the results into one array
    $combinedResults = array_merge($userUpgradeActive, $userUpgradeExpired);

    // Fetch data from xf_user_upgrade
    $userUpgrades = $db->fetchAll("SELECT * FROM xf_user_upgrade");

    // Fetch payment log
    $paymentLog = $db->fetchAll("SELECT * FROM xf_payment_provider_log WHERE log_message = 'Payment received, upgraded\/extended.' ORDER BY provider_log_id DESC");
} catch (\Exception $e) {
    http_response_code(500);  // Internal Server Error
    echo json_encode(['error' => 'Database query failed', 'details' => $e->getMessage()]);
    exit();
}

// Combine the results into a structured array
$response = [
    'userUpgradeActive' => $userUpgradeActive,
    'userUpgradeExpired' => $userUpgradeExpired,
    'userUpgrades' => $userUpgrades,
    'combinedResults' => $combinedResults,
    'paymentLog' => $paymentLog
];

// Output the combined array as JSON
header('Content-Type: application/json');
echo json_encode($response);
