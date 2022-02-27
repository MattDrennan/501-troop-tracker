<?php

include "config.php";

print_r($squadArray[0]['eventForum']);

/*$array = array(18, 45, 1396, 1409, 1412);

$test = updateUserForumGroup(15970, 1400, $array);

print_r($test);*/

$test = createThread(8, "test", "test");

print_r($test);

?>