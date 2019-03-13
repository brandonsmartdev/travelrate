<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');

	$country_id = $_POST['country_id'];
	$background_url = $_POST['background_url'];

	

	if (is_numeric($country_id)) {
	
		$country = R::load('countries', $country_id);
		$country->background_url = $background_url;

		if ($country->id != 0) {
			R::store($country);
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