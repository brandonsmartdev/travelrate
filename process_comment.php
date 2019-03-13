<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');
	
	$name = $_POST['name'];
	$city_id = $_POST['city_id'];
	$comment = $_POST['comment'];
	$rating = $_POST['rating'];
	$time = time();

	if (is_numeric($city_id) && is_numeric($rating)) {
		if (R::load('cities', $city_id)->id != 0) {
			$new_comment = R::dispense('comments');

			$new_comment->name = $name;
			$new_comment->city_id = $city_id;
			$new_comment->comment = $comment;
			$new_comment->rating = $rating;
			$new_comment->time = $time;

			R::store($new_comment);

			// echo $city_id;

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