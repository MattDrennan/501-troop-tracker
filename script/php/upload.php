<?php

/**
 * This file is used for processing file uploads and storing the information in the database.
 * 
 * This should be every two minutes by a cronjob.
 *
 * @author  Matthew Drennan
 *
 */

// Include config file
include "../../config.php";

// Set directory seperator
$ds = DIRECTORY_SEPARATOR;

// Set directory for storing files
$storeFolder = '../../images/uploads/';

// If file exists
if (!empty($_FILES))
{
	/**
	 * Verify this is an allowed image
	 * 
	 * https://www.geeksforgeeks.org/php-exif_imagetype-function/
	 * 
	 * @var array $filesAllowed
	*/
	$filesAllowed = array(1, 2, 3);

	/**
	 * @var int $uploadedType Returns an int based on the file type
	*/
	$uploadedType = exif_imagetype($_FILES['file']['tmp_name']);

	// Check if this is an allowed image
	$error = !in_array($uploadedType, $filesAllowed);

	// This file is not allowed, stop the script
	if($error)
	{
		die("Uploaded File is not allowed!");
	}

	// Set up file and move to folder
	$fileName = date("Y-m-d-H-i-s-") . $_FILES['file']['name'];
	$tempFile = $_FILES['file']['tmp_name'];         
	$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
	$targetFile =  $targetPath . $fileName;
	move_uploaded_file($tempFile, $targetFile);

	/**
	 * Add a smaller image for faster page loads
	 */

	// Get path info
	$info = pathinfo($targetFile);

	// Get image size
	list($width, $height) = getimagesize($targetFile);

	// Limit size
	if($width > 500 || $height > 500) { $width = 500; $height = 500; }

	// Get file type
	$fileType = mime_content_type($targetFile);

	// Check file type to convert image
	if($fileType == "image/png")
	{
		$image = imagecreatefrompng($targetFile);
	}
	else if($fileType == "image/jpeg")
	{
		$image = imagecreatefromjpeg($targetFile);
	}
	else if($fileType == "image/gif")
	{
		$image = imagecreatefromgif($targetFile);
	}

	// Resize image
	$imgResized = imagescale($image , $width, $height);

	// Make new image
	imagejpeg($imgResized, $storeFolder . "resize/" . $info['filename'] . ".jpg");
	
	// Check if troop instruction image
	try {
	    // Check if troop instruction image
	    if ($_POST['admin'] == 0) {
	        // Prepare the SQL statement
	        $stmt = $conn->prepare("INSERT INTO uploads (troopid, trooperid, filename) VALUES (?, ?, ?)");
	    } else {
	        // Prepare the SQL statement for admin
	        $stmt = $conn->prepare("INSERT INTO uploads (troopid, trooperid, filename, admin) VALUES (?, ?, ?, '1')");
	    }

	    // Check if the SQL statement was prepared successfully
	    if (!$stmt) {
	        throw new Exception("SQL Preparation Error: " . $conn->error);
	    }

	    // Bind the parameters
	    $troopid = (int) $_POST['troopid'];
	    $trooperid = (int) $_POST['trooperid'];

	    $stmt->bind_param("iis", $troopid, $trooperid, $fileName);

	    // Execute the SQL statement
	    if (!$stmt->execute()) {
	        throw new Exception("SQL Execution Error: " . $stmt->error);
	    }

	    // If successful, send a success response
	    echo json_encode(['success' => true, 'message' => 'File uploaded successfully.']);
	} catch (Exception $e) {
	    // Log the MySQL error to a log file
	    //error_log("[" . date("Y-m-d H:i:s") . "] MySQL Error: " . $e->getMessage() . "\n");

	    // Send a JSON error response to Dropzone
	    echo json_encode(['success' => false, 'message' => 'Database error: Please contact the administrator.']);
	    http_response_code(500); // Internal Server Error
	}
}

?> 