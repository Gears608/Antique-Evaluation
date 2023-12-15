<?php 
    session_start();
    $session_token = uniqid();
    $_SESSION['token'] = $session_token;
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Password Recovery</title>
		<script src="https://www.google.com/recaptcha/api.js?render=6LctIFYjAAAAAAzwY3EVOFvgUtQU5cVf1kV1d6HF"></script>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="login">
			<h1>Password Recovery</h1>
			<form action="authenticateRecovery.php" method="post">
				<input type="text" name="username" placeholder="Username" id="username" required>
				<input type="email" name="email" placeholder="Email" id="email" required>
				<input type="text" placeholder="What is your mother's Maiden Name?" name="security1" id="security1" required>
				<input type="text" placeholder="What is the name of the street you grew up on?" name="security2" id="security2" required>
				<input type="text" placeholder="What was your favourite food as a child?" name="security3" id="security3" required>
				<input type="hidden" value="<?php echo $session_token ?>" name="sessionid" id="sessionid">
				<input type="hidden" name="token_generate" id="token_generate">
				
				<input type="submit" value="Send">
			</form>
			<br>
		</div>
	</body>
</html>

<?php
    include "captchaFunction.php"
?>