<?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
    	header('Location: index.php');
    	exit;
    }
    
    if($_SESSION['admin'] == 0){
        header('Location: logout.php');
    	exit;
    }
    
    include 'db.php';
    
    $sql = 'SELECT password, email, telNo FROM accounts WHERE username = ?';
    if($stmt = $con->prepare($sql)){
        $stmt->bind_param('s', $_SESSION['name']);
        $stmt->execute();
        $stmt->bind_result($password, $email, $telNo);
        $stmt->fetch();
        $stmt->close();
    } else {
        exit('SQL Error.');
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body class="loggedin">
		<div class="topbar">
			<div class="title">Lovejoy’s Antique Evaluation Web Application (admin)</div>
			<a href="logout.php">Logout</a>
			<a class="active" href="profileAdmin.php">Profile</a>
		    <a href="listings.php">Listings</a>
		</div>
		<div class="content">
			<h2>Profile</h2>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?=$_SESSION['name']?></td>
					</tr>
					<tr>
						<td>Password (encrypted):</td>
						<td><?=$password?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>Phone Number:</td>
						<td><?=$telNo?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>