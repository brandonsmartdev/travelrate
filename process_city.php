<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');
	
	$name = $_POST['name'];
	$country_id = $_POST['country_id'];
	$population = $_POST['population'];
	$temperature = $_POST['temperature'];
	$background_url = $_POST['background_url'];

	if (is_numeric($country_id) && is_numeric($temperature) && is_numeric($population)) {
		if (R::load('countries', $country_id)->id != 0) {
			$new_city = R::dispense('cities');

			$new_city->name = $name;
			$new_city->country_id = $country_id;
			$new_city->population = $population;
			$new_city->temperature = $temperature;
			$new_city->background_url = $background_url;

			R::store($new_city);

			header('Location: main_menu.php');	
		}
		else {
			echo 'Invalid inputs';
		}
	}

	else {
		echo 'Invalid inputs';
	}

	
	
?>