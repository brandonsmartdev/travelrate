<?php 
	
	function create_centered_text($im, $x, $y, $font_size, $rotation, $font, $text) {
    	$text_box = imagettfbbox($font_size, $rotation, $font, $text);
		
		$text_width = $text_box[2]-$text_box[0];
		$text_height = $text_box[7]-$text_box[1];
		$aligned_x = $x - ($text_width/2);
		$aligned_y = $y - ($text_height/2);
		
		imagettftext($im, $font_size, $rotation, $aligned_x, $aligned_y, imagecolorallocate($im, 0, 0, 0), $font, $text);
	}


	header("Content-type: image/png");
	
	require("rb.php");
	R::setup('sqlite:travelrate.sqlite');

	$city_id = $_GET['city'];
	$city = R::load('cities', $city_id);

	$all_ratings  = R::find('comments', 'city_id == ' . $city->id);





	if (count($all_ratings) != 0) {

		// Creates the peach coloured image (from a plain, peach coloured template image)
		$im = imagecreatefrompng("images/graph_background.png");

		// Creates some of the basic elements of the image
		create_centered_text($im, imagesx($im) / 2.0, 40, 35, 0, 'fonts/Slabo27px-Regular.ttf', 'Reviews for ' . $city->name);
		create_centered_text($im, 460, 460, 30, 0, 'fonts/Slabo27px-Regular.ttf', 'Rating');
		create_centered_text($im, 50, 330, 30, 90, 'fonts/Slabo27px-Regular.ttf', 'Frequency');
		imageline($im, 120, 410, 780, 410, imagecolorallocate($im, 0, 0, 0));
		imageline($im, 120, 410, 120, 90, imagecolorallocate($im, 0, 0, 0));

		// Gets the maximum fraction (from 0 to 1, rounded up to the nearest 0.1)
		$percentages = array();
		for ($i=0; $i <= 5.0; $i += 0.5) { 
			$comments_per_rating  = R::find('comments', 'city_id == ' . $city->id . ' AND rating == ' . $i);
			array_push($percentages, count($comments_per_rating) / count($all_ratings));
		}
		$max_fraction = ceil(max($percentages) * 10) / 10; // Gets the maximum value in percentages, and rounds it up to the nearest 0.1

		// Creates all of the vertical bars, and the rating value texts
		for ($i=0; $i <= 5.0; $i += 0.5) { 
			$comments_per_rating  = R::find('comments', 'city_id == ' . $city->id . ' AND rating == ' . $i);
			$rating_fraction = $i / 5.0; // The rating expressed as a number between 0 and 1 (eg 2.5/5 is a 0.5/1)
			imagefilledrectangle(
				$im,
				740 - 120 * (5 - $i),
				409 - 320 * (count($comments_per_rating) / count($all_ratings)) / $max_fraction,
				780 - 120 * (5 - $i),
				409,
				imagecolorallocate($im, 215 + (20 - 215) * $rating_fraction, 20 + (215 - 20) * $rating_fraction, 20)
			);
			create_centered_text($im, 760 - 120 * (5 - $i), 430, 15, 0, 'fonts/Slabo27px-Regular.ttf', $i);
		}

		// Creates all of the percentage texts
		for ($i=0; $i <= $max_fraction * 100; $i += 10) { 
			create_centered_text($im, 90, 410 - 320 * ($i / 100) / $max_fraction, 12, 0, 'fonts/Slabo27px-Regular.ttf', $i . '%');
		}

		// Calculates the mean rating, and draws a dotted line
		// echo $percentages;
		$mean = 0;
		foreach ($all_ratings as $rating) {
			$mean += $rating->rating / count($all_ratings);
		}

		imageline(
			$im,
			160 + (760 - 160) * $mean * 0.2,
			410,
			160 + (760 - 160) * $mean * 0.2,
			90, imagecolorallocate($im, 0, 0, 255));

		create_centered_text($im, 100, 453, 12, 0, 'fonts/Slabo27px-Regular.ttf', 'The blue line represents');
		create_centered_text($im, 100, 473, 12, 0, 'fonts/Slabo27px-Regular.ttf', 'the average (mean) rating');


		imagepng($im);
		imagedestroy($im);


	}
	




	else {

		$im = imagecreatefrompng("images/graph_background.png");

		create_centered_text($im, imagesx($im) / 2.0, imagesy($im) / 2.0, 50, 0, 'fonts/Slabo27px-Regular.ttf', 'There are no reviews');

		// $image_width = imagesx($im);  
		// $image_height = imagesy($im);
		// $text_box = imagettfbbox(20, 0, 'fonts/Slabo27px-Regular.ttf', 'There are no reviews');
		
		// $text_width = $text_box[2]-$text_box[0];
		// $text_height = $text_box[7]-$text_box[1];
		// $x = ($image_width/2) - ($text_width/2);
		// $y = ($image_height/2) - ($text_height/2);
		
		// imagettftext($im, 20, 0, $x, $y, imagecolorallocate($im, 0, 0, 0), 'fonts/Slabo27px-Regular.ttf', 'There are no reviews');

		imagepng($im);
		imagedestroy($im);
	}

	
	
	
	


	

	

?>