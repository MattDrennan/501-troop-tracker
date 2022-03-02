<?php

/**
 * This file was used for scraping costume data for the troop tracker. This file is stored for historical purposes.
 * 
 * The HTML file was downloaded locally, and this PHP script was run.
 *
 * @author  Matthew Drennan
 *
 */

include "../../config.php";

include_once('simple_html_dom.php');


function html_get($url, $search) {
	global $conn;
	// Didn't find it yet.
	$return = false;
	
    // create HTML DOM
    $html = file_get_html($url);

    // get article block
    foreach($html->find($search) as $found) {
		// Found at least one.
		$return - true;
		$string = substr(strip_tags($found->innertext), 4);
		echo $string;
		echo '<br />';
		
		if(strlen($string) > 3)
		{
			$conn->query("INSERT INTO costumes (costume, era, club) VALUES ('".trim(addslashes($string))."', 4, 0)");
		}
    }
    
    // clean up memory
    $html->clear();
    unset($html);

    return $return;
}

print_r(html_get("costumes.html", "a"));
?>