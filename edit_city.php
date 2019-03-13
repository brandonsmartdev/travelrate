<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');

	$city_id = $_POST['city_id'];
	$background_url = $_POST['background_url'];



	if (is_numeric($city_id)) {
		
		$city = R::load('cities', $city_id);
		$city->background_url = $background_url;

		if ($city->id != 0) {
			R::store($city);
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