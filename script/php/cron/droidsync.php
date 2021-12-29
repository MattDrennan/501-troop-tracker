<?php

// Include config
include(realpath("../../../") . '/config.php');

// Purge Droids
$conn->query("DELETE FROM droid_troopers") or die($conn->error);

// Pull extra data from spreadsheet
$url = 'https://sheets.googleapis.com/v4/spreadsheets/195NT1crFYL_ECVyzoaD2F1QXGW5WxlnBDfDaLVtM87Y/values/Sheet1?key=' . googleSheets;
$json = json_decode(file_get_contents($url));
$rows = $json->values;
$i = 0;

foreach($rows as $row)
{
	// If not first
	if($i != 0)
	{
		// Insert into database
		$conn->query("INSERT INTO droid_troopers (forum_id, droidname, imageurl) VALUES ('".$row[0]."', '".$row[1]."', '".$row[2]."')") or die($conn->error);
	}

	// Increment
	$i++;
}

?>