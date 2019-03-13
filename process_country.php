<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');

	$new_country = R::dispense('countries');
	
	$name = $_POST['name'];
	$language = $_POST['language'];
	$capital = $_POST['capital'];
	$currency = $_POST['currency'];
	$background_url = $_POST['background_url'];

	$new_country->name = $name;
	$new_country->language = $language;
	$new_country->capital = $capital;
	$new_country->currency = $currency;
	$new_country->background_url = $background_url;

	R::store($new_country);

	header('Location: main_menu.php');
?>