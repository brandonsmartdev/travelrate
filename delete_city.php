<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');

	$city_id = $_POST['city_id'];

	if (is_numeric($city_id)) {
		$city = R::load('cities', $city_id);

		$city_comments = R::find('comments', 'city_id == ' . $city_id);
		foreach ($city_comments as $comment) {
			R::trash($comment);
		}
		
		R::trash($city);	

		header('Location: main_menu.php');
	}

	else {
		echo 'Invalid inputs';
	}
	
?>