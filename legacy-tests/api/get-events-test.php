<?php

require_once(dirname(__FILE__) . '/../assertions.php');

ob_start();

$project_root = dirname(__FILE__, 3);

$_GET['events'] = '1';

include $project_root . '/src/api.php';

$actual_output = trim(ob_get_clean());

$expected_output = getExpectedContents(__FILE__, 'json');

assertEquals($expected_output, $actual_output);

echo "OK: The actual output matches the expected output.\n";
