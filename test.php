<?php

include "config.php";

$test = createThread(5, "Test", "Test");

print_r($test['thread']['thread_id']);

?>