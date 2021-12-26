<?php

// Include config
include(aPath . 'config.php');

// Get Simple PHP DOM Tool - just a note, for this code to work, $stripRN must be false in tool
include(aPath . 'tool/dom/simple_html_dom.php');

// Purge rebel troopers
$conn->query("DELETE FROM rebel_troopers") or die($conn->error);

// Purge rebel costumes
$conn->query("DELETE FROM rebel_costumes") or die($conn->error);

// Costume image array (duplicate check)
$costumeImagesG = array();

// Loop through all records
for($i = 0; $i <= 1000; $i += 10)
{
	// get DOM from URL or file
	$html = file_get_html('https://www.forum.rebellegion.com/baza2.php?b=10&start=' . $i);
	
	// Did we find an array
	$isArrayContained = false;

	// Loop through comments
	foreach($html->find('comment') as $e)
	{
		// If comment contains array
		if(contains($e, "Array"))
		{
			// Find where it says array
			$arrayI = strpos($e, "Array");
			
			// Find where array ends
			$arrayI2 = strrpos($e, ")");
			
			// If there is a start and end of an array
			if($arrayI != 0 && $arrayI2 != 0)
			{
				// Set up array string
				$stringArray = str_replace("-->", "", trim(substr($e, $arrayI, $arrayI2)));
				
				// Convert string (print_r) to array
				$array = print_r_reverse($stringArray);
				
				// Loop through arrays
				foreach($array as $innerRow => $innerArray)
				{
					// Rebel ID - Setup
					$rebelID = 0;
					
					// Rebel Name - Setup
					$rebelName = "";
					
					// Rebel Forum - Setup
					$rebelForum = "";
					
					// Loop through inner arrays
					foreach($innerArray as $key => $a)
					{
						// If first value (ID)
						if($key == 0)
						{
							// Set Rebel ID
							$rebelID = $innerArray[0];
							
							// Search ID for profile
							$html2 = file_get_html('https://www.forum.rebellegion.com/forum/profile.php?mode=viewprofile&u=' . $innerArray[0]);
							
							// Costume name array
							$costumeNames = array();
							
							// Costume image array
							$costumeImages = array();
							
							// Set should add to prevent duplicates
							$addTo = true;
							
							// Loop through costume images on profile
							foreach($html2->find('img[height=125]') as $r)
							{
								//echo $r->src;
								
								// Check to see if exists in duplicate array
								if(in_array(str_replace("sm", "-A", $r->src), $costumeImagesG))
								{
									$addTo = false;
								}
								
								// Check to see if we can add (duplicates)
								if($addTo)
								{
									// Push to array
									array_push($costumeImages, str_replace("sm", "-A", $r->src));
									
									// Push to array (to check for duplicates)
									array_push($costumeImagesG, str_replace("sm", "-A", $r->src));
								}
							}
							
							// Loop through costume names
							foreach($html2->find('span[class=gen]') as $s)
							{
								// Get bolds
								foreach($s->find('b') as $b)
								{
									// Get links
									foreach($b->find('a') as $a)
									{
										//echo $a->innertext . '<br />';
										
										// Prevent an issue where it inserts a 1
										if($a->innertext != 1 && !contains($a->innertext, "http"))
										{
											// Check to see if we can add (duplicates)
											if($addTo)
											{
												// Push to array
												array_push($costumeNames, $a->innertext);
											}
										}
									}
								}
							}
							
							// Start i count
							$cc = 0;

							// Loop through created arrays
							foreach($costumeNames as $c)
							{
								// Query
								$conn->query("INSERT INTO rebel_costumes (rebelid, costumename, costumeimage) VALUES ('".cleanInput($innerArray[0])."', '".cleanInput($costumeNames[$cc])."', '".cleanInput($costumeImages[$cc])."')") or die($conn->error);
								
								echo $innerArray[0] . ' - ' . $costumeNames[$cc] . ' - ' . $costumeImages[$cc] . ' <br />';
								
								// Increment
								$cc++;
							}
						}
						// Rebel Forum
						else if($key == 1)
						{
							// Set Rebel Forum
							$rebelForum = $a;
						}
						else if($key == 2)
						{
							// Set Rebel Name
							$rebelName = $a;
						}
					}
					
					// Query
					$conn->query("INSERT INTO rebel_troopers (rebelid, name, rebelforum) VALUES ('".cleanInput($rebelID)."', '".cleanInput($rebelName)."', '".cleanInput($rebelForum)."')") or die($conn->error);
				}
			}
			
			// Found an array
			$isArrayContained = true;
		}
	}
	
	// An array was not contained
	if(!$isArrayContained)
	{
		// Stop loop
		break;
	}
}

// print_r_reverse: Convert a string (print_r) back to a value
function print_r_reverse($input)
{
    $lines = preg_split('#\r?\n#', trim($input));
    if (trim($lines[0]) != 'Array' && trim($lines[0] != 'stdClass Object'))
    {
        // bottomed out to something that isn't an array or object
        if ($input === '')
        {
            return null;
        }
        return $input;
    }
    else
    {
        // this is an array or object, lets parse it
        $match = array();
        if (preg_match("/(\s{5,})\(/", $lines[1], $match))
        {
            // this is a tested array/recursive call to this function
            // take a set of spaces off the beginning
            $spaces = $match[1];
            $spaces_length = strlen($spaces);
            $lines_total = count($lines);
            for ($i = 0; $i < $lines_total; $i++)
            {
                if (substr($lines[$i], 0, $spaces_length) == $spaces)
                {
                    $lines[$i] = substr($lines[$i], $spaces_length);
                }
            }
        }
        $is_object = trim($lines[0]) == 'stdClass Object';
        array_shift($lines); // Array
        array_shift($lines); // (
        array_pop($lines); // )
        $input = implode("\n", $lines);
        $matches = array();
        // make sure we only match stuff with 4 preceding spaces (stuff for this array and not a nested one)
        preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $input, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $pos = array();
        $previous_key = '';
        $in_length = strlen($input);
        // store the following in $pos:
        // array with key = key of the parsed array's item
        // value = array(start position in $in, $end position in $in)
        foreach($matches as $match)
        {
            $key = $match[1][0];
            $start = $match[0][1] + strlen($match[0][0]);
            $pos[$key] = array($start, $in_length);
            if ($previous_key != '')
            {
                $pos[$previous_key][1] = $match[0][1] - 1;
            }
            $previous_key = $key;
        }
        $ret = array();
        foreach($pos as $key => $where)
        {
            // recursively see if the parsed out value is an array too
            $ret[$key] = print_r_reverse(substr($input, $where[0], $where[1] - $where[0]));
        }

        return $is_object ? (object) $ret : $ret;
    }
}

// For early PHP version
function contains($haystack, $needle)
{
	return $needle !== '' && mb_strpos($haystack, $needle) !== false;
}

?>