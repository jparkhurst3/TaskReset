<?php

	session_start();	

	require "header.php";

	$currentUserName = $_SESSION["currentUserName"];

	?>

	<div class="content-container">
		<h3 class="pink-highlight">Welcome to TASK RESET <?php echo $currentUserName; ?> !</h3>
		<p> Task Reset is a tool to realign with your priorities, and create a simple task list to focus on. Whenever you're feeling overwhelmed, hit the "RESET" button to figure out exactly what you should be working on next.</p>
		<p> Ready to get started? </p>
		<form method="get" action="/reset_quiz.php?active=reset">
		    <button type="submit">RESET</button>
		</form>
	</div>