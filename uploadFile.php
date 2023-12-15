<?php
    // We need to use sessions, so you should always start sessions using the below code.
    session_start();
    // If the user is not logged in redirect to the login page...
    if (!isset($_SESSION['loggedin'])) {
    	header('Location: index.php');
    	exit;
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Upload</title>
		<script src="https://www.google.com/recaptcha/api.js?render=6LctIFYjAAAAAAzwY3EVOFvgUtQU5cVf1kV1d6HF"></script>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <div class="topbar">
			<div class="title">Lovejoy’s Antique Evaluation Web Application</div>
			<a href="logout.php">Logout</a>
			<a href="profile.php">Profile</a>
			<a class="active"href="uploadFile.php">Evaluate</a>
	        <a href="home.php">Home</a>
		</div>
		<div class="login">
			<h1>Upload File</h1>
			<form action="authenticateUpload.php" method="post" enctype="multipart/form-data".>
				<input type="file" name="file" id="file" accept=".jpg, .jpeg, .png" required>
				<input type="hidden" name="token_generate" id="token_generate">
                <textarea name="details" id="details" rows="4" cols="40" placeholder="Details (up to 500 characters)..." maxlength="500" required></textarea>
                <br>
                <label for="contact">Choose a contact method:</label>
                <select name="contact" id="contact">
                    <option value="telNo">Phone</option>
                    <option value="email">Email</option>
                </select>
                <br>
				<input type="submit" value="Upload" name="submit" id="submit">
			</form>
			<div class="login-footer">
			    Files must be less than 3MB.
			</div>
		</div>
	</body>
</html>

<?php
    include "captchaFunction.php"
?>