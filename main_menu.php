<!-- The font used for country/city/comment titles is Slabo, as found in Google Fonts -->
<!-- The UI images (trashbin for deleting, picture for editing the picture, plus for adding and stars for rating) are from the ionicons set (ionicons.com)-->
<!-- The font used for the ratings graphs is https://www.fontsquirrel.com/fonts/Aller -->

<!-- Uses the redbean library for database management -->


<!-- Country/City background URL in edit box? -->
<!-- Colour interpolation going through yellow?-->


<!DOCTYPE html>
<html>
<head>
	<title>TravelRate</title>
	<link rel="stylesheet" type="text/css" href="nice_menu_style.css">
	<link href="https://fonts.googleapis.com/css?family=Slabo+27px" rel="stylesheet"> 
</head>
<body>
	
	<!-- Connects to the database and sets up the arrays of countries, cities and comments -->
	<?php
		require('rb.php');
		R::setup('sqlite:travelrate.sqlite');
		date_default_timezone_set("Australia/Adelaide");

		$countries = R::findAll('countries');
		$cities = R::findAll('cities');
		$comments = R::findAll('comments');	
	?>

	<!-- Creates the header block at the top of the page -->
	<div id="headerBlock">
		<h1 id="mainTitle">TravelRate</h1>
		<p id="slogan">Travel great with TravelRate!</p>
	</div>
	<hr>
	
	<!-- Creates the country selector -->
	<?php
		echo '<div class="country_selector">';
		foreach ($countries as $country) {
		 	echo '<div id="' . $country->id . '_country_block" class="country_block"
		 			style="background-image: url(' . $country->background_url . ');">';
		 		echo '<h2 class="country_title">' . $country->name . '</h2>';
		 		echo '<img src="images/android-delete.svg" id="' . $country->id . '_country_delete" class="country_delete">';
		 		echo '<img src="images/android-image.svg" id="' . $country->id . '_country_edit" class="country_edit">';
		 		echo '<hr class="country_rule">';
		 		echo '<div class="country_body">';
			 		echo '<table class="country_table" border="1" cellpadding="5">';
			 			echo '<tr><td class="country_table_block">Language: ' . $country->language . '</td></tr>';
			 			echo '<tr><td class="country_table_block">Capital: ' . $country->capital . '</td></tr>';
			 			echo '<tr><td class="country_table_block">Currency: ' . $country->currency . '</td></tr>';
			 			// echo '<tr><td class="country_table_block">Area: ' . $country->area . '</td></tr>';
			 			// echo '<tr><td class="country_table_block">GDP: ' . $country->gdp . '</td></tr>';
			 		echo '</table>';
			 	echo '</div>';
		 	echo '</div>';
		}
		echo '<button id="add_country_block" class="add_country_block">';
			echo '<div class"add_country_container">';
				echo '<img class="add_country_plus" src="images/android-add.svg">';
				echo '<p class="add_country_text">Add Country</p>';
			echo '</div>';
		echo '</button>';

		echo '</div>'; 
	?>
	
	<!-- Creates all of the city selectors -->
	<?php
		foreach ($countries as $country) {
			$country_cities  = R::find('cities', 'country_id == ' . $country->id);

			echo '<div id="' . $country->id . '_cities" class="city_selector">';
			foreach ($country_cities as $city) {
			 	echo '<div id="' . $city->id . '_city_block" class="city_block" style="background-image: url(' . $city->background_url . ');">';
			 		echo '<h2 class="city_title">' . $city->name . '</h2>';
			 		echo '<img src="images/android-delete.svg" id="' . $city->id . '_city_delete" class="city_delete">';
			 		echo '<img src="images/android-image.svg" id="' . $city->id . '_city_edit" class="city_edit">';
			 		echo '<hr class="city_rule">';
			 		echo '<div class="city_body">';
				 		echo '<table class="city_table" border="1" cellpadding="5">';
				 			
				 			echo '<tr><td class="city_table_block">Population: ' . $city->population . '</td></tr>';
				 			echo '<tr><td class="city_table_block">Avg Temp: ' . $city->temperature . ' </td></tr>';

				 			$all_city_ratings  = R::find('comments', 'city_id == ' . $city->id);
				 			if (count($all_city_ratings) != 0) {
				 				$mean = 0;
					 			foreach ($all_city_ratings as $rating) {
									$mean += $rating->rating / count($all_city_ratings);
								}
								echo '<tr><td class="city_table_block">Avg. Rating: ' . round($mean, 2) . ' </td></tr>';
								//echo '<tr><td class="city_table_block">Avg. Rating: Test</td></tr>';
					 		}
					 		else {
					 			echo '<tr><td class="city_table_block">No Ratings Available</td></tr>';
					 		}
				 			

				 		echo '</table>';
				 	echo '</div>';
			 	echo '</div>';
			}

			echo '<button id="' . $country->id . '_add_city_block" class="add_city_block">';
				echo '<div class"add_city_container">';
					echo '<img class="add_city_plus" src="images/android-add.svg">';
					echo '<p class="add_city_text">Add City</p>';
				echo '</div>';
			echo '</button>';

			echo '</div>';
		}
	?>
	
	<!-- Creates all of the comment selectors -->
	<?php
		foreach ($countries as $country) {
			$country_cities  = R::find('cities', 'country_id == ' . $country->id);
			foreach ($country_cities as $city) {
				$city_comments = R::find('comments', 'city_id == ' . $city->id);

				echo '<div id="' . $city->id . '_info" class="city_info_block">';
					
					echo '<div class="comment_selector">';
						
						echo '<img src="draw_graph.php?city=' . $city->id . '" alt="No graph could be loaded" class="ratings_graph">';

						echo '</img>';

						foreach ($city_comments as $comment) {
							//Linearly interpolates between red (215, 20, 20) and green (20, 215, 20)
							$rating_fraction = $comment->rating / 5.0;
							$color_string = 'rgb(' . (215 + (20 - 215) * $rating_fraction) . ',' . (20 + (215 - 20) * $rating_fraction) . ',' . (20) . ')';

						 	echo '<div class="comment_block" style="background: ' . $color_string , '">';
						 		echo '<h2 class="comment_title">' . $comment->name . '</h2>';
						 		echo '<img src="images/android-delete.svg" id="' . $comment->id . '_commment_delete" class="comment_delete">';
						 		echo '<p class="comment_date">Date: ' . date('d/m/y H:i:s', $comment->time) . '</p>';
						 		echo '<hr class="comment_rule">';
							 	echo '<p class="comment_body">' . $comment->comment . '</p>';
							 	echo '<hr class="comment_rule">';
							 	echo '<div class="comment_rating">';
							 		$rating_int = $comment->rating;
							 		for ($i=0; $i < 5; $i++) { 
							 			if ($rating_int > 0.5) {echo '<img class="rating_star" src="images/android-star.svg">';}
							 			elseif ($rating_int == 0.5) {echo '<img class="rating_star" src="images/android-star-half.svg">';}
							 			else {echo '<img class="rating_star" src="images/android-star-outline.svg">';}
							 			$rating_int -= 1;
							 		}
							 	echo '</div>';
						 	echo '</div>';
						}

						echo '<button id="' . $city->id . '_add_comment_block" class="add_comment_block">';
							echo '<div class"add_comment_container">';
								echo '<img class="add_comment_plus" src="images/android-add.svg">';
								echo '<p class="add_comment_text">Add Comment</p>';
							echo '</div>';
						echo '</button>';

					echo '</div>';
					

				echo '</div>';
			}
		}
	?>
	

	<br>
	

	<!-- Creates the menu for adding a country -->
	<div id="add_country_modal" class="add_country_modal">
		<div class="add_country_modal_block">
			<form method="post" action="process_country.php">
				<div class="add_country_modal_header">
					<h2 class="add_country_modal_title">Add Country</h2>
				</div>
				<div class="add_country_modal_body">
					<table class="add_country_modal_table">
						<tr><td>Country Name:</td><td><input type="text" name="name" class="add_country_modal_input"></td></tr>
						<tr><td>National Language:</td><td><input type="text" name="language" class="add_country_modal_input"></td></tr>
						<tr><td>Capital:</td><td><input type="text" name="capital" class="add_country_modal_input"></td></tr>
						<tr><td>Currency:</td><td><input type="text" name="currency" class="add_country_modal_input"></td></tr>
						<!-- <tr><td>Population:</td><td><input type="number" name="population" min="0" class="add_country_modal_input"></td></tr> -->
						<!-- <tr><td>Area:</td><td><input type="number" name="area" min="0" class="add_country_modal_input"></td></tr> -->
						<!-- <tr><td>GDP:</td><td><input type="number" name="gdp" min="0" class="add_country_modal_input"></td></tr> -->
						<tr><td>Background URL:</td><td><input type="text" name="background_url" class="add_country_modal_input"></td></tr>
					</table>					
				</div>
				<div class="add_country_modal_footer">
					<div class="add_country_modal_buttons">
						<input type="submit" value="Submit" class="add_country_modal_submit">
						<button type="button" id="add_country_modal_cancel" class="add_country_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>



	<!-- Creates the menu for adding a city -->
	<div id="add_city_modal" class="add_city_modal">
		<div class="add_city_modal_block">
			<form method="post" action="process_city.php">
				<div class="add_city_modal_header">
					<h2 class="add_city_modal_title">Add City</h2>
				</div>
				<div class="add_city_modal_body">
					<table class="add_city_modal_table">
						<tr><td>Country:</td><td>
							<select name="country_id" class="add_city_modal_input" id="add_city_modal_country_id">
							<?php
								foreach ($countries as $country) {
									echo '<option value="' . $country->id . '">' . $country->name . '</option>';
								}
							?>
							</select></td></tr>
						<tr><td>City Name:</td><td><input type="text" name="name" class="add_city_modal_input"></td></tr>
						<tr><td>Population:</td><td><input type="number" name="population" min="0" class="add_city_modal_input"></td></tr>
						<tr><td>Average Temperature:</td><td><input type="number" name="temperature" class="add_city_modal_input"></td></tr>
						<tr><td>Background URL:</td><td><input type="text" name="background_url" class="add_city_modal_input"></td></tr>
					</table>					
				</div>
				<div class="add_city_modal_footer">
					<div class="add_city_modal_buttons">
						<input type="submit" value="Submit" class="add_city_modal_submit">
						<button type="button" id="add_city_modal_cancel" class="add_city_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>



	<!-- Creates the menu for adding a comment -->
	<div id="add_comment_modal" class="add_comment_modal">
		<div class="add_comment_modal_block">
			<form method="post" action="process_comment.php">
				<div class="add_comment_modal_header">
					<h2 class="add_comment_modal_title">Add Comment</h2>
				</div>
				<div class="add_comment_modal_body">
					<table class="add_comment_modal_table">
						<tr><td>City:</td><td>
							<select name="city_id" class="add_comment_modal_input" id="add_comment_modal_city_id">
							<?php
								foreach ($cities as $city) {echo '<option value="' . $city->id . '">' . $city->name . '</option>';}
							?>
							</select></td></tr>
						<tr><td>Your Name:</td><td><input type="text" name="name" class="add_comment_modal_input"></td></tr>
						<tr><td>Rating:</td><td><input type="number" name="rating" min="0" max="5" step="0.5" class="add_comment_modal_input"></td></tr>
						<tr><td>Comment:</td><td><input type="text" name="comment" class="add_comment_modal_input"></td></tr>
					</table>					
				</div>
				<div class="add_comment_modal_footer">
					<div class="add_comment_modal_buttons">
						<input type="submit" value="Submit" class="add_comment_modal_submit">
						<button type="button" id="add_comment_modal_cancel" class="add_comment_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>

	
	<!-- Creates the menu for deleting a country -->
	<div id="delete_country_modal" class="delete_country_modal">
		<div class="delete_country_modal_block">
			<form method="post" action="delete_country.php">
				<div class="delete_country_modal_header">
					<h2 class="delete_country_modal_title">Delete Country</h2>
				</div>
				<div class="delete_country_modal_body">
					<table class="delete_country_modal_table">
						<tr><td>Country to Delete:</td><td>
							<select name="country_id" class="delete_country_modal_input" id="delete_country_modal_country_id">
							<?php
								foreach ($countries as $country) {echo '<option value="' . $country->id . '">' . $country->name . '</option>';}
							?>
							</select></td></tr>
					</table>					
				</div>
				<div class="delete_country_modal_footer">
					<div class="delete_country_modal_buttons">
						<input type="submit" value="Submit" class="delete_country_modal_submit">
						<button type="button" id="delete_country_modal_cancel" class="delete_country_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>


	<!-- Creates the menu for deleting a city -->
	<div id="delete_city_modal" class="delete_city_modal">
		<div class="delete_city_modal_block">
			<form method="post" action="delete_city.php">
				<div class="delete_city_modal_header">
					<h2 class="delete_city_modal_title">Delete City</h2>
				</div>
				<div class="delete_city_modal_body">
					<table class="delete_city_modal_table">
						<tr><td>City to Delete:</td><td>
							<select name="city_id" class="delete_city_modal_input" id="delete_city_modal_city_id">
							<?php
								foreach ($cities as $city) {echo '<option value="' . $city->id . '">' . $city->name . '</option>';}
							?>
							</select></td></tr>
					</table>					
				</div>
				<div class="delete_city_modal_footer">
					<div class="delete_city_modal_buttons">
						<input type="submit" value="Submit" class="delete_city_modal_submit">
						<button type="button" id="delete_city_modal_cancel" class="delete_city_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>



	<!-- Creates the menu for deleting a comment -->
	<div id="delete_comment_modal" class="delete_comment_modal">
		<div class="delete_comment_modal_block">
			<form method="post" action="delete_comment.php">
				<div class="delete_comment_modal_header">
					<h2 class="delete_comment_modal_title">Delete Comment</h2>
				</div>
				<div class="delete_comment_modal_body">
					<table class="delete_comment_modal_table">
						<tr><td>Comment to Delete:</td><td>
							<select name="comment_id" class="delete_comment_modal_input" id="delete_comment_modal_comment_id">
							<?php
								foreach ($comments as $comment) {echo '<option value="' . $comment->id . '">' . $comment->name . '\'s comment on ' . date('d/m/y H:i:s', $comment->time) . '</option>';}
							?>
							</select></td></tr>
					</table>					
				</div>
				<div class="delete_comment_modal_footer">
					<div class="delete_comment_modal_buttons">
						<input type="submit" value="Submit" class="delete_comment_modal_submit">
						<button type="button" id="delete_comment_modal_cancel" class="delete_comment_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>


	<!-- Creates the menu for editing the image of a country -->
	<div id="edit_country_modal" class="edit_country_modal">
		<div class="edit_country_modal_block">
			<form method="post" action="edit_country.php">
				<div class="edit_country_modal_header">
					<h2 class="edit_country_modal_title">Edit Country Image</h2>
				</div>
				<div class="edit_country_modal_body">
					<table class="edit_country_modal_table">
						<tr><td>Country to Edit:</td><td>
							<select name="country_id" class="edit_country_modal_input" id="edit_country_modal_country_id">
							<?php
								foreach ($countries as $country) {echo '<option value="' . $country->id . '">' . $country->name . '</option>';}
							?>
							</select></td></tr>
							<tr><td>New Background URL:</td><td><input type="text" name="background_url" class="edit_country_modal_input"></td></tr>
					</table>					
				</div>
				<div class="edit_country_modal_footer">
					<div class="edit_country_modal_buttons">
						<input type="submit" value="Submit" class="edit_country_modal_submit">
						<button type="button" id="edit_country_modal_cancel" class="edit_country_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>


	<!-- Creates the menu for editing the image of a city -->
	<div id="edit_city_modal" class="edit_city_modal">
		<div class="edit_city_modal_block">
			<form method="post" action="edit_city.php">
				<div class="edit_city_modal_header">
					<h2 class="edit_city_modal_title">Edit City Image</h2>
				</div>
				<div class="edit_city_modal_body">
					<table class="edit_city_modal_table">
						<tr><td>City to Edit:</td><td>
							<select name="city_id" class="edit_city_modal_input" id="edit_city_modal_city_id">
							<?php
								foreach ($cities as $city) {echo '<option value="' . $city->id . '">' . $city->name . '</option>';}
							?>
							</select></td></tr>
							<tr><td>New Background URL:</td><td><input type="text" name="background_url" class="edit_city_modal_input"></td></tr>
					</table>					
				</div>
				<div class="edit_city_modal_footer">
					<div class="edit_city_modal_buttons">
						<input type="submit" value="Submit" class="edit_city_modal_submit">
						<button type="button" id="edit_city_modal_cancel" class="edit_city_modal_cancel">Cancel</button>
					</div>
				</div>
			</form>
	  	</div>
	</div>
	




	<script type="text/javascript">
		
		// Sets the action of the clicking a country block
		// It uses the id of the country_block to find the appropriate city_selector
		// and then loops through all of the city_selectors, showing the appropriate
		// one and hiding the rest. It also hides all of the city_info_blocks. 
		var country_blocks = document.getElementsByClassName("country_block");
		var i;

		for (i = 0; i < country_blocks.length; i++) {
		    country_blocks[i].onclick = function(){

		    	var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_')); 
		    	var city_selector_str = id + '_cities';

		    	var k;
				var city_selectors = document.getElementsByClassName("city_selector");
		        for (k = 0; k < city_selectors.length; k++) {
		        	if (city_selectors[k].id == city_selector_str) {city_selectors[k].style.display = "block";}
		        	else {city_selectors[k].style.display = "none";}	
		        }

		        var city_info_blocks = document.getElementsByClassName("city_info_block");
		        for (k = 0; k < city_info_blocks.length; k++) {
		        	city_info_blocks[k].style.display = "none";	
		        }

		    }
		}

		// Goes through all of the city_blocks, and sets the action to be showing the
		// appropriate city_info_block
		var city_blocks = document.getElementsByClassName("city_block");
		var i;

		for (i = 0; i < city_blocks.length; i++) {
		    city_blocks[i].onclick = function(){

		    	var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_')); 
		    	var city_selector_str = id + '_info';

				var city_info_blocks = document.getElementsByClassName("city_info_block");
		        var k;

		        for (k = 0; k < city_info_blocks.length; k++) {
		        	if (city_info_blocks[k].id == city_selector_str) {city_info_blocks[k].style.display = "block";}
		        	else {city_info_blocks[k].style.display = "none";}	
		        }
		    }
		}

		// Controls the add country modal
		var add_country_block = document.getElementById("add_country_block");
		var add_country_modal = document.getElementById("add_country_modal");
		var add_country_modal_cancel = document.getElementById("add_country_modal_cancel");

		add_country_block.onclick = function() {
			add_country_modal.style.display = "block";
		}
		
		add_country_modal_cancel.onclick = function() {
			add_country_modal.style.display = "none";
		}


		// Controls the add city modal
		var add_city_blocks = document.getElementsByClassName("add_city_block");
		var add_city_modal = document.getElementById("add_city_modal");
		var add_city_modal_cancel = document.getElementById("add_city_modal_cancel");
		var add_city_modal_country_id = document.getElementById("add_city_modal_country_id");

		var i;
		for (i = 0; i < add_city_blocks.length; i++) {
			add_city_blocks[i].onclick = function() {

				var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_'));
		    	add_city_modal_country_id.value = id;
				
				add_city_modal.style.display = "block";
			}
		}
		
		add_city_modal_cancel.onclick = function() {
			add_city_modal.style.display = "none";
		}



		// Controls the add comment modal
		var add_comment_blocks = document.getElementsByClassName("add_comment_block");
		var add_comment_modal = document.getElementById("add_comment_modal");
		var add_comment_modal_cancel = document.getElementById("add_comment_modal_cancel");
		var add_comment_modal_city_id = document.getElementById("add_comment_modal_city_id");

		var i;
		for (i = 0; i < add_comment_blocks.length; i++) {
			add_comment_blocks[i].onclick = function() {

				var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_'));
		    	add_comment_modal_city_id.value = id;
				
				add_comment_modal.style.display = "block";
			}
		}
		
		add_comment_modal_cancel.onclick = function() {
			add_comment_modal.style.display = "none";
		}


		// Controls the delete country modal
		var delete_country_blocks = document.getElementsByClassName("country_delete");
		var delete_country_modal = document.getElementById("delete_country_modal");
		var delete_country_modal_cancel = document.getElementById("delete_country_modal_cancel");
		var delete_country_modal_country_id = document.getElementById("delete_country_modal_country_id");

		var i;
		for (i = 0; i < delete_country_blocks.length; i++) {
			delete_country_blocks[i].onclick = function(event) {

				event.stopPropagation();

				var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_'));
		    	delete_country_modal_country_id.value = id;
				
				delete_country_modal.style.display = "block";
			}
		}
		
		delete_country_modal_cancel.onclick = function() {
			delete_country_modal.style.display = "none";
		}



		// Controls the delete city modal
		var delete_city_blocks = document.getElementsByClassName("city_delete");
		var delete_city_modal = document.getElementById("delete_city_modal");
		var delete_city_modal_cancel = document.getElementById("delete_city_modal_cancel");
		var delete_city_modal_city_id = document.getElementById("delete_city_modal_city_id");

		var i;
		for (i = 0; i < delete_city_blocks.length; i++) {
			delete_city_blocks[i].onclick = function(event) {

				event.stopPropagation();

				var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_'));
		    	delete_city_modal_city_id.value = id;
				
				delete_city_modal.style.display = "block";
			}
		}
		
		delete_city_modal_cancel.onclick = function() {
			delete_city_modal.style.display = "none";
		}



		// Controls the delete comment modal
		var delete_comment_blocks = document.getElementsByClassName("comment_delete");
		var delete_comment_modal = document.getElementById("delete_comment_modal");
		var delete_comment_modal_cancel = document.getElementById("delete_comment_modal_cancel");
		var delete_comment_modal_comment_id = document.getElementById("delete_comment_modal_comment_id");

		var i;
		for (i = 0; i < delete_comment_blocks.length; i++) {
			delete_comment_blocks[i].onclick = function(event) {

				event.stopPropagation();

				var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_'));
		    	delete_comment_modal_comment_id.value = id;
				
				delete_comment_modal.style.display = "block";
			}
		}
		
		delete_comment_modal_cancel.onclick = function() {
			delete_comment_modal.style.display = "none";
		}



		// Controls the edit country modal
		var edit_country_blocks = document.getElementsByClassName("country_edit");
		var edit_country_modal = document.getElementById("edit_country_modal");
		var edit_country_modal_cancel = document.getElementById("edit_country_modal_cancel");
		var edit_country_modal_country_id = document.getElementById("edit_country_modal_country_id");

		var i;
		for (i = 0; i < edit_country_blocks.length; i++) {
			edit_country_blocks[i].onclick = function(event) {

				event.stopPropagation();

				var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_'));
		    	edit_country_modal_country_id.value = id;
				
				edit_country_modal.style.display = "block";
			}
		}
		
		edit_country_modal_cancel.onclick = function() {
			edit_country_modal.style.display = "none";
		}


		// Controls the edit city modal
		var edit_city_blocks = document.getElementsByClassName("city_edit");
		var edit_city_modal = document.getElementById("edit_city_modal");
		var edit_city_modal_cancel = document.getElementById("edit_city_modal_cancel");
		var edit_city_modal_city_id = document.getElementById("edit_city_modal_city_id");

		var i;
		for (i = 0; i < edit_city_blocks.length; i++) {
			edit_city_blocks[i].onclick = function(event) {

				event.stopPropagation();

				var id_str = this.id;
		    	var id = id_str.substr(0, id_str.indexOf('_'));
		    	edit_city_modal_city_id.value = id;
				
				edit_city_modal.style.display = "block";
			}
		}
		
		edit_city_modal_cancel.onclick = function() {
			edit_city_modal.style.display = "none";
		}


		// Close out of all modals
		window.onclick = function(event) {
			
		    if (event.target.className == 'add_country_modal') {
		    	var add_country_modal = document.getElementById("add_country_modal");
		    	add_country_modal.style.display = "none";
		    }
		    if (event.target.className == 'add_city_modal') {
		    	var add_city_modal = document.getElementById("add_city_modal");
		    	add_city_modal.style.display = "none";
		    }
		    if (event.target.className == 'add_comment_modal') {
		    	var add_comment_modal = document.getElementById("add_comment_modal");
		    	add_comment_modal.style.display = "none";
		    }
		    if (event.target.className == 'delete_country_modal') {
		    	var delete_country_modal = document.getElementById("delete_country_modal");
		    	delete_country_modal.style.display = "none";
		    }
		    if (event.target.className == 'delete_city_modal') {
		    	var delete_city_modal = document.getElementById("delete_city_modal");
		    	delete_city_modal.style.display = "none";
		    }
		    if (event.target.className == 'delete_comment_modal') {
		    	var delete_comment_modal = document.getElementById("delete_comment_modal");
		    	delete_comment_modal.style.display = "none";
		    }
		    if (event.target.className == 'edit_country_modal') {
		    	var edit_country_modal = document.getElementById("edit_country_modal");
		    	edit_country_modal.style.display = "none";
		    }
		    if (event.target.className == 'edit_city_modal') {
		    	var edit_city_modal = document.getElementById("edit_city_modal");
		    	edit_city_modal.style.display = "none";
		    }


		}

	</script>

</body>
</html>