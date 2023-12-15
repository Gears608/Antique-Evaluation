<?php 
    session_start();
    $session_token = uniqid();
    $_SESSION['token'] = $session_token;
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<script src="https://www.google.com/recaptcha/api.js?render=6LctIFYjAAAAAAzwY3EVOFvgUtQU5cVf1kV1d6HF"></script>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="login">
			<h1>Login</h1>
			<form action="authenticate.php" method="post">
				<input type="text" name="username" placeholder="Username" id="username" required>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<input type="hidden" name="token_generate" id="token_generate">
				<input type="hidden" value="<?php echo $session_token ?>" name="sessionid" id="sessionid">
				<input type="submit" value="Login" name="submit" id="submit">
			</form>
			<div class="login-footer">
    			<a href="recovery.php">Forgot Password?</a> <br> <br>
    			<a href="signup.php">Don't have an account?</a>
			</div>
		</div>
	</body>
</html>

<?php
    include "captchaFunction.php"
?>