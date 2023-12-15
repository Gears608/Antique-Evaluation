<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Signup</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<script src="https://www.google.com/recaptcha/api.js?render=6LctIFYjAAAAAAzwY3EVOFvgUtQU5cVf1kV1d6HF"></script>
	</head>
	<body>
		<div class="login">
			<h1>Sign Up</h1>
			<form action="authenticateSignUp.php" method="post">
				<input type="text" name="username" placeholder="Username" id="username" required>
				<input type="email" name="email" placeholder="Email" id="email" required>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<input type="password" name="confirmpassword" placeholder="Confirm Password" id="confirmpassword" required>
				<input type="tel" name="number" placeholder="Phone No. Format = 1234567890" id="number" pattern="[0-9]{10}" required> <br>
				Security Questions
				<input type="text" placeholder="What is your mother's Maiden Name?" name="security1" id="security1" required>
				<input type="text" placeholder="What is the name of the street you grew up on?" name="security2" id="security2" required>
				<input type="text" placeholder="What was your favourite food as a child?" name="security3" id="security3" required>
				<input type="hidden" name="token_generate" id="token_generate">
				<input type="submit" value="Sign Up">
			</form>
			<div class="login-footer">
    			<a href="index.php">Already have an account?</a>
			</div>
		</div>
	</body>
</html>

<?php
    include "captchaFunction.php"
?>