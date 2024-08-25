<?php

$fileDir = '../';  // Relative path from this script to the Xenforo root
require('src/XF.php');
XF::start($fileDir);
$app = XF::setupApp('XF\Pub\App');
$app->start();

// Now you can query the database
$db = \XF::db();

$results = $db->fetchAll("SELECT * FROM xf_user_group");

$group_array = array();

foreach ($results as $row) {
    $group_array[$row['user_group_id']] = array(
        "groupID" => $row['user_group_id'],
        "bannerText" => $row['banner_text'],
        "title" => $row['title'],
        "order" => $row['display_style_priority']
    );
}

// Output the sorted array as JSON
echo json_encode($group_array);
