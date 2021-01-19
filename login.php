<?php
	require "includes/shortheader.php";
?>

	<main>
		
		<div class="content-container">
			<h2>Welcome!</h2>
			<div class="login-form-container">
				<form action="includes/loginHandling.php" method="POST">
					<input type="text" name="userName" placeholder="Username">
					<br><br>
					<input type="password" name="userPwd" placeholder="Password...">
					<br><br>
					<button class="big-button" type="submit" name="login-submit">Login</button>
				</form>
			</div>
		</div>	

	</main>

<?php
	require "footer.php";
?>