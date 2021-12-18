<?php

// Include config file
include "../../config.php";

// Set directory seperator
$ds = DIRECTORY_SEPARATOR;

// Set directory for storing files
$storeFolder = '../../images/uploads/';

// If file exists
if (!empty($_FILES))
{
	// Set up file and move to folder
	$fileName = date("Y-m-d-H-i-s-") . $_FILES['file']['name'];
	$tempFile = $_FILES['file']['tmp_name'];         
	$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
	$targetFile =  $targetPath . $fileName;
	move_uploaded_file($tempFile, $targetFile);
	
	// Check if admin (command staff) checkbox checked
	if($_POST['admin'] == 0)
	{
		// Insert file into database
		$conn->query("INSERT INTO uploads (troopid, trooperid, filename) VALUES ('".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['trooperid'])."', '".addslashes($fileName)."')");
	}
	else
	{
		// Insert file into database - admin
		$conn->query("INSERT INTO uploads (troopid, trooperid, filename, admin) VALUES ('".cleanInput($_POST['troopid'])."', '".cleanInput($_POST['trooperid'])."', '".addslashes($fileName)."', '1')");
	}
}

?> 