<?php  
	require('rb.php');

	R::setup('sqlite:travelrate.sqlite');

	$comment_id = $_POST['comment_id'];

	if (is_numeric($comment_id)) {
		$comment = R::load('comments', $comment_id);

		R::trash($comment);	

		header('Location: main_menu.php');	
	}
	
	else {
		echo 'Invalid inputs';
	}	
?>