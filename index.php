<?php 

/**
 * An API to find users based within 50 miles of London from data provided by bpdts-test-app.herokuapp.com
 *
*/
	header('Content-Type: application/json');
	
	//Json files from api
	$json_string = "https://bpdts-test-app.herokuapp.com/users";
	$json_stringLondon = "https://bpdts-test-app.herokuapp.com/city/London/users";

	//lat long of the center of London & a rough estimate on the radius of London.
	$londonLat = 51.6082208;
	$londonLong = -0.4597294; 
	$londonRad = 25; //average radius in miles from the center of Greater London to the edge in miles

	//removing Json encoding to use api as arrays
	$jsondata = file_get_contents($json_string);
	$obj = json_decode($jsondata);
	$jsondataLondon = file_get_contents($json_stringLondon);
	$objLondon = json_decode($jsondataLondon);

	//new array to store people in or around London
	$peopleArray = array();

	//Loop through all users to find ones located within 50 miles of London
	foreach ($obj as $key => $value) {

		//removes any potentially invalid characters e.g. " or '
		$thisLat = floatval($value->latitude);
		$thisLong = floatval($value->longitude);

		if(distanceBetweenTwoPoints($londonLat,$londonLong,$thisLat,$thisLong) <= 50 + $londonRad){
 
 			//add the user to the array if the user is within the requested distance of London
			array_push($peopleArray, $value);
		}
	}
	//loop through all users listed as living in London
	foreach ($objLondon as $key => $value) {
		 
		array_push($peopleArray, $value);
	}

	//encode the array to Json and pass to the user
	echo json_encode($peopleArray, JSON_FORCE_OBJECT);

	//function to find the distance in miles between two sets of lat longs using Haversine's formula
	function distanceBetweenTwoPoints($latFrom,$longFrom,$latTo,$longTo,$earthRadius = 3958.8){

		//convert latitudes to radians to use with trigonometry 
		$latFromRad = deg2rad($latFrom);
		$latToRad = deg2rad($latTo); 

		//convert distance between the lat longs in radians
		$latDelta = deg2rad($latTo - $latFrom);
		$longDelta = deg2rad($longTo - $longFrom);

		//Haversine's formula
		$a = sin($latDelta / 2) * sin($latDelta / 2) + 
			 cos($latFromRad) * cos($latToRad) * 
			 sin($longDelta / 2) * sin($longDelta / 2);
		$b = 2 * atan2(sqrt($a), sqrt(1 - $a));

		return $earthRadius * $b;
	}

?>