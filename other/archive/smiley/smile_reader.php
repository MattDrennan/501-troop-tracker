<?php

$handle = fopen("smilies.pak", "r");

if ($handle)
{
	while (($line = fgets($handle)) !== false)
	{
	    $array = explode(",", $line);
	    echo '<br />';
	    //echo str_replace("'", "", $array[0]);
	    //echo str_replace("'", "", $array[5]);
	    $url = '&#60;img src="https://www.fl501st.com/boards/images/smilies/'.str_replace("'", "", $array[0]).'" code="'.trim(str_replace("'", "", $array[5])).'" />';
	    echo "'".trim(str_replace("'", "", $array[5]))."' => '".$url."',";
	}

	fclose($handle);
}

?>