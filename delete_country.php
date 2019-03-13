<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');

	$country_id = $_POST['country_id'];

	// The country, cities that belong to the country, and the comments that belong to the cities all need to be deleted

	if (is_numeric($country_id)) {
		// Delete the country
		$country = R::load('countries', $country_id);

		R::trash($country);

		// Delete the cities that belong to the country, and the comments of those cities
		$country_cities = R::find('cities', 'country_id == ' . $country_id);
		foreach ($country_cities as $city) {
			$city_comments = R::find('comments', 'city_id == ' . $city->id);
			foreach ($city_comments as $comment) {
				R::trash($comment);
			}
			R::trash($city);
		}
			
		header('Location: main_menu.php');
	}

	else {
		echo 'Invalid inputs';
	}
	
?>