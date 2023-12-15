<?php
    session_start();
    include 'db.php';
    
    // checks that the email and code exist
    if(isset($_GET['email'], $_GET['code'])){
        
        //store the email and code
        $email = $_GET['email'];
        $code = $_GET["code"];
        
        //checks if an account with the given email exists
        $sql = 'SELECT activation_code,activation_expiry FROM accounts WHERE email = ?';
        if($stmt = $con->prepare($sql)){
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows > 0){
                //checks code matches
                $stmt->bind_result($found_code, $expiry);
                $stmt->fetch();
                if(password_verify($code, $found_code)){
                    //checks code isn't expired
                    if($expiry > date("Y-m-d H:i:s")){
                        // update active state
                        $sql = 'UPDATE accounts SET active = ? WHERE email = ?';
                        if($stmt = $con->prepare($sql)){
                            $active = 1;
                            $stmt->bind_param('is', $active, $email);
                            $stmt->execute();
                        }
                        
                        // clear current activation code
                        $sql = 'UPDATE accounts SET activation_code = ? WHERE email = ?';
                        if($stmt = $con->prepare($sql)){
                            $activation_code = null;
                            $stmt->bind_param('is', $activation_code, $email);
                            $stmt->execute();
                        }
                    } else {
                        //deletes the user if their code is expired
                        $sql = 'DELETE FROM accounts WHERE email = ?';
                        if($stmt = $con->prepare($sql)){
                            $stmt->bind_param('s', $email);
                            $stmt->execute();
                            exit('Code expired, please re-register.');
                        }
                    }
                } else {
                    exit($found_code);
                }
            } else {
                exit('Account not found.');
            }
        }
    } else {
        header('Location: index.php');
    	exit;
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Account Activated</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <div class="login">
    	    <h3>Account acivated! You may now login. </h3>
    	    <div class="login-footer">
    		    <a href="index.php"> Back to home page.</a>
    		</div>
		</div>
	</body>
</html>