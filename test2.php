<?php

include 'config.php';

$values = getSheet("1yP4mMluJ1eMpcZ25-4DPnG7K8xzrkHyrfvywcihl_qs", "Roster");

$new_values = ['1', '2'];
editSheet("1yP4mMluJ1eMpcZ25-4DPnG7K8xzrkHyrfvywcihl_qs", $values, "abcdef", "Roster", "A", "B", $new_values);

$newValues = ['hey', 'there'];
addToSheet("1yP4mMluJ1eMpcZ25-4DPnG7K8xzrkHyrfvywcihl_qs", "Roster", $newValues)

?>