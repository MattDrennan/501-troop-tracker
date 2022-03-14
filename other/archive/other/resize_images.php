<?php

/**
 * This file is used for converting image files in the upload folder to resized images in the resize folder. This file is for historial purposes only as this is done on photo upload.
 *
 * @author  Matthew Drennan
 *
 */

// Directory string
$directory = "images/uploads/";

// Get uploaded files in directory
$uploadedFiles = scandir($directory);

// Loop through files
foreach ($uploadedFiles as $file => $value)
{
	// Don't allow certain files / directories
	if('.' !== $value && '..' !== $value && 'resize' !== $value && '.DS_Store' !== $value)
	{
		// Get file
		$fileName = $directory . $value;

		// Get path info
		$info = pathinfo($fileName);
		
		// If already exists in resize directory, skip
		if(file_exists($directory . "resize/" . $info['filename'] . ".jpg")) { continue; }

		// Get file type
		$fileType = mime_content_type($fileName);

		if($fileType == "image/png")
		{
			$image = imagecreatefrompng($fileName);
		}
		else if($fileType == "image/jpeg")
		{
			$image = imagecreatefromjpeg($fileName);
		}
		else if($fileType == "image/gif")
		{
			$image = imagecreatefromgif($fileName);
		}

		// Resize image
		$imgResized = imagescale($image , 500, 500);

		// Make new image
		imagejpeg($imgResized, $directory . "resize/" . $info['filename'] . ".jpg");
	}
}

?>