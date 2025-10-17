<?php

/**
 * This file is used for organizing custom functions
 *
 * @author  Matthew Drennan
 *
 */

/**
 * Gets squad by location using the Google API
 * 
 * @param string $address The address of the event
 * @return int Returns the ID of the squad based on location
*/
function getSquad($address)
{
	// Squad code
	$squad = 0;

	// Request
	$geocode = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&key=".googleKey."");
    $output = json_decode($geocode);

    // Get Data
    if(isset($output->results[0]->address_components[4]->long_name))
    {
    	$county = $output->results[0]->address_components[4]->long_name;

	    // Parjai
	    if($county == "Escambia County" || $county == "Santa Rosa" || $county == "Okaloosa County" || $county == "Walton County" || $county == "Holmes County" || $county == "Washington County" || $county == "Jackson County" || $county == "Calhoun County" || $county == "Bay County" || $county == "Gulf County" || $county == "Gadsen County" || $county == "Liberty County" || $county == "Leon County" || $county == "Wakulla County" || $county == "Franklin County")
	    {
	    	$squad = 3;
	    }

	    // Squad 7
	    else if($county == "Jefferson County" || $county == "Madison County" || $county == "Taylor County" || $county == "Hamilton County" || $county == "Suwannee County" || $county == "Lafayette County" || $county == "Dixie County" || $county == "Columbia County" || $county == "Gilchrist County" || $county == "Baker County" || $county == "Union County" || $county == "Bradford County" || $county == "Alachua County" || $county == "Levy County" || $county == "Nassau County" || $county == "Duval County" || $county == "Clay County" || $county == "St. Johns County" || $county == "Putnam County" || $county == "Flagler County" || $county == "Marion County")
	    {
	    	$squad = 4;
	    }

	    // Makaze
	    else if($county == "Volusia County" || $county == "Citrus County" || $county == "Lake County" || $county == "Seminole County" || $county == "Orange County" || $county == "Brevard County" || $county == "Osceola County" || $county == "Highlands County" || $county == "Okeechobee County" || $county == "Indian River County")
	    {
	    	$squad = 2;
	    }

	    // Tampa Bay
	    else if($county == "Charlotte County" || $county == "Lee County" || $county == "Desolo County" || $county == "Hardee County" || $county == "Sarasota County" || $county == "Manatee County" || $county == "Hillsborough County" || $county == "Polk County" || $county == "Pasco County" || $county == "Pinellas County" || $county == "Sumter County" || $county == "Hernando County")
	    {
	    	$squad = 5;
	    }

	    // Everglades
	    else if($county == "Hendry County" || $county == "Palm Beach County" || $county == "Broward County" || $county == "Collier County" || $county == "Monroe County" || $county == "Dade County" || $county == "Glades County" || $county == "Martin County" || $county == "St. Lucie County")
	    {
	    	$squad = 1;
	    }
	    else
	    {
	    	$squad = 2;
	    }
	}

    return $squad;
}

?>