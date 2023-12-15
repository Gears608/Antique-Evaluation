<?php
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
		<title>Home Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body class="loggedin">
		<div class="topbar">
			<div class="title">Lovejoy’s Antique Evaluation Web Application</div>
			<a href="logout.php">Logout</a>
			<a href="profile.php">Profile</a>
			<a href="uploadFile.php">Evaluate</a>
	        <a class="active" href="home.php">Home</a>
		</div>
		<div class="content">
			<h2>Home</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
		</div>
	</body>
</html>