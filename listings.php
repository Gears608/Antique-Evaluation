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
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Listings (admin)</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body class="loggedin">
		<div class="topbar">
			<div class="title">Lovejoy’s Antique Evaluation Web Application (admin)</div>
			<a href="logout.php">Logout</a>
			<a href="profileAdmin.php">Profile</a>
		    <a class="active" href="listings.php">Listings</a>
		</div>
		<div class="content">
			<h2>Listings</h2>
				<p>Active Listings:</p>
				<div class="listings">
    				<table>
    					<tr>
    						<td>Username</td>
    						<td>Contact Info</td>
    						<td>Description</td>
    						<td>Image</td>
    					</tr>
    					<?php
        					$sql = 'SELECT * FROM images';
                            if($stmt = $con->query($sql)){
                                while($result = mysqli_fetch_array($stmt)){
                                    echo "
                                        <tr>
                                            <td>".$result["username"]."</td>
                                            <td>".$result["contact"]."</td>
                                            <td>".$result["description"]."</td>
                                            <td><img src=".$result["image"]." width='400' height='500'></td>
                                        </tr>
                                ";
                                }
                                
                            } else {
                                exit('SQL Error.');
                            }
                        ?>
    				</table>
    			</div>
			</div>
		</div>
	</body>
</html>