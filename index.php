<?php
header('Content-Type: text/html, charset=utf-8');
require 'flight-master/flight/Flight.php';
// include 'controllers.php';
include 'functions.php';
include 'ChromePhp.php';
// include 'oo.php';

//Home
Flight::route('/', function(){
  Flight::render('hello.php');
	ChromePhp::log('I used ChromePhp for troubleshooting.');
});

// List all states
Flight::route('/v1/states/', function(){
  Flight::render('states.php');
	
});

// List cities from a certain state
Flight::route('GET /v1/states/@state/cities', function($state){
	$state = strtoupper($state);
    $json_states = get_states('cities.csv', $state);
    Flight::json($json_states);
});

// Lists a single city, can receive radius query data
Flight::route('GET /v1/states/@state/cities/@city', function($state, $city){
	$state = strtoupper($state);
	if (is_numeric($city)) {
    	$current_city = find_city_by_id('cities.csv', $city);
    } else {
    	$current_city = find_city_by_name('cities.csv', $city, $state);
    }
	if (isset($current_city)) {
		$cityname = $current_city->name;
	    $radius = Flight::request()->query->radius;
	    $nearby = find_distance($state, $cityname, $radius, 'cities.csv');
	    Flight::json($nearby);
	}
});

// Allow users to mark visits to a city
Flight::route('POST /v1/users/@user/visits', function($user){
	$city = Flight::request()->data->name;
	$state = Flight::request()->data->state;
	$current_city = find_city_by_name('cities.csv', $city, $state);
	if (isset($current_city)) {
		$city_id = $current_city->id;
		write_to_visits($user,$city_id);
	}
});

// Allows users to see visited cities and enter new visited cities as a query
Flight::route('GET /v1/users/@user/visits', function($user){
    $current_user = find_user('users.csv', $user);

	if (isset($current_user)) {
		$user_id = $current_user->id;

		// While I was developing, I used a form for the ease of UI in order to send to the POST. 
		// echo '<form action="/flight_challenge/v1/users/'.$user_id.'/visits" method="POST">
		// 	City: <input type="text" name="name"><br>
		// 	State: <input type="text" name="state"><br>
		// 	<input type="submit" value="Submit">
		// </form>';

		//The below accepts query strings and adds them to the database
    	$city = Flight::request()->query->name;
    	$state = Flight::request()->query->state;
		if (isset($current_user) && isset($city) && isset($state)) {
			$curl = curl_post_json($user_id, $city, $state);
			ChromePhp::log($curl);
		} else {
			echo '<strong>Please enter a valid city and state in this format: <br>?name=Chicago&state=IL.</strong><hr>';
		}

		$visited_cities = visited_cities('visits.csv', 'cities.csv', $user_id);
    	if (isset($visited_cities)) {
    		print_r(json_encode($visited_cities));
    	}
	} 
});
Flight::start();