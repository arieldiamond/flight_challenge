<?php
//converts CSV to JSON and adds headers as keys
function add_headers_to_json($filename) {
	$file = fopen($filename, 'r');

	$headers = fgetcsv($file, ',');
	$csv_array = array();

	while ($row = fgetcsv($file, ',')) {
	    $csv_array[] = array_combine($headers, $row);
	}
	fclose($file);

	$json = json_encode($csv_array);
	return $json;
}

//Locates user in the csv file
function find_user($userfile, $user) {
	$user_sought = $user;
	$users = json_decode(add_headers_to_json($userfile));

	foreach ($users as $user) {
		$userid = $user->id;
		if ($user_sought == $userid) {
			$current_user = $user;
		}
	}
	if (isset($current_user)) {
		return $current_user;
	} else {
		echo 'This user does not exist in our system, please try again.';
	}
}

// Locates city by name and state in the csv file
function find_city_by_name($cityfile, $city_sought, $state_sought) {
	$city_sought = str_replace("-", " ", $city_sought);
	$cities = json_decode(add_headers_to_json($cityfile));

	foreach ($cities as $city) {
		$cityname = $city->name;
		$statename = $city->state;
		if (strtolower($city_sought) == strtolower($cityname) && strtoupper($state_sought) == $statename) {
			$current_city = $city;
		}
	}
	if (isset($current_city)) {
		return $current_city;
	} else {
		echo '<strong>This city does not exist in our system, please try again.<br></strong>';
	}
}

// Locates city by id
function find_city_by_id($cityfile, $city_sought_id) {
	$cities = json_decode(add_headers_to_json($cityfile));

	foreach ($cities as $city) {
		$city_id = $city->id;
		if ($city_sought_id == $city_id) {
			$current_city = $city;
		}
	}
	if (isset($current_city)) {
		return $current_city;
	} else {
		echo '<strong>That city does not exist in our system, please try again. <br><a href="/flight_challenge/v1/users/'.$user.'/visits">Back</a></strong>';
	}
}

// Locates all cities within a certain state
function get_states($file, $state_sought) {
	if (isset($state_sought)) {
		$cities = json_decode(add_headers_to_json($file));
		
		$cities_in_states = array();

		foreach($cities as $city) {
			$state = $city->state;
			if( strtoupper($state) == strtoupper($state_sought) ) {
				array_push($cities_in_states, $city);
			} 
		}
		if (count($cities_in_states) > 0) {
			//// For UI
			// echo '<h1>Cities in '.$state_sought.':</h1>';
			// foreach ($cities_in_states as $city) {
			// 	echo '<h3>'.$city->id.'. '.$city->name.', '.$city->state.'</h3>';
			// 	echo '<p>Location: '.$city->latitude.', '.$city->longitude.'</p><hr>';
			// }
			return $cities_in_states;
		} else {
			echo 'Please try again. Did you enter a US state abbreviation?';
		}
	} else {
		echo 'Please try again. Did you enter a US state abbreviation?';
	}
}

//finds cities within a certain distance of a start city
function find_distance($start_state, $start_city, $radius, $cityfile) {
	$cities = json_decode(add_headers_to_json($cityfile));
	$start_city = str_replace("-", " ", $start_city);
	$start_city = find_city_by_name($cityfile, $start_city, $start_state);
	$nearby_cities = array();

	$lon1 = deg2rad($start_city->longitude); // convert from degrees to radians
	$lat1 = deg2rad($start_city->latitude);
	$earth_radius = 3959; //in miles
	
	foreach ($cities as $city) {
		$lon2 = deg2rad($city->longitude);
		$lat2 = deg2rad($city->latitude);

		//determine difference
		$latDelta = $lat1 - $lat2;
		$lonDelta = $lon1 - $lon2;

		//Vicenty formula
		$lonDelta = $lon1 - $lon2;
		$a = pow(cos($lat1) * sin($lonDelta), 2) +
		    pow(cos($lat2) * sin($lat1) - sin($lat2) * cos($lat1) * cos($lonDelta), 2);
		$b = sin($lat2) * sin($lat1) + cos($lat2) * cos($lat1) * cos($lonDelta);

		$angle = atan2(sqrt($a), $b);
		$distance= $angle * $earth_radius;

	  	if ($distance <= $radius && $distance != 0) {
	  		// for UI
	  		// echo $city->name.", ".$city->state.': '.$distance.' miles away<br>';
	  		$nearby_cities[] = $city;
	  	}
	}
	return $nearby_cities;
}

// CURL to POST as JSON
function curl_post_json($user, $city, $state){
	$city = str_replace("-", " ", $city);
	if (isset($user) && isset($city) && isset($state)) {
		$city_data = array(
		    'name' => $city,
		    'state' => strtoupper($state)
		);
		$ch = curl_init('http://localhost:8888/flight_challenge/v1/users/'.$user.'/visits');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($city_data));
		$result = curl_exec($ch);

		if($result === FALSE){
		    die(curl_error($curl_setup));
		}

		$response_city_data = json_decode($result, TRUE);

		return $response_city_data;
	} 

}

//Adds new visit to CSV file
function write_to_visits($city_id, $user_id) {
	//TODO: filter duplicate visits
	$visit_id = count(file("visits.csv"));
	$visited = $visit_id.",".$city_id.",".$user_id;
	
	$file = fopen("visits.csv","a");
	fputcsv($file, explode(',', $visited));
	fclose($file);
}

// Reports back which cities the user has visited
function visited_cities($visitfile, $cityfile, $user_id) {
	$visits = json_decode(add_headers_to_json($visitfile));
	$visited_cities = array();

	foreach ($visits as $visit) {
		$visit_user_id = $visit->user_id;
		if ($visit_user_id == $user_id) {
			$visited_city_id = $visit->city_id;
			$visited_city = find_city_by_id($cityfile, $visited_city_id);
			$visited_cities[] = $visited_city;
		}
	}

	if(count($visited_cities) > 0) {
		echo '<h3>Your Visited Cities:</h3>';
		foreach ($visited_cities as $visited_city) {
			echo $visited_city->name.", ".$visited_city->state."<br>";
		}
		return $visited_cities;
	} else {
		echo 'It looks like you haven\'t visited any cities yet!';
	}
}
