<?php
$cities = json_decode(add_headers_to_json('cities.csv'));
?>

<h1>States</h1>
<?php
$states = [];
foreach ($cities as $city) {
	$states[] = $city->state;
}
$unique_states = array_unique($states);
foreach ($unique_states as $state) {
	echo '<a href="/flight_challenge/v1/states/'.$state.'/cities/">'.$state.'</a><br>';
}	