<?php

include 'config.php';

//deleteSheetRows("1yP4mMluJ1eMpcZ25-4DPnG7K8xzrkHyrfvywcihl_qs", "1724018043", 259, 259);

		
// Get Google Sheet
$values = getSheet("10_w4Fz41iUCYe3G1bQSqHDY6eK4fXP0Ue3pnfA4LoZg", "Roster");


// Set up count
$i = 0;

// Check if we have a match
foreach($values as $value)
{
	echo @$value[6];
}

?>