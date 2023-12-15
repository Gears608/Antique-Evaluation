<?php
    session_start();
    
    if(!($_POST['sessionid'] == $_SESSION['token'])){
        exit('Unknown Session ID');
    }
    
    include 'db.php';
    
    // check if username and password fields are filled out
    if ( !isset($_POST['password'], $_POST['confirmpassword']) ) {
    	header('Location: index.php');
    	exit;
    }
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $secret = '6LctIFYjAAAAAMzYvNKk6i5bS_gG0SC1qx11lhGC';
    $response = $_POST['token_generate'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    
    $request = file_get_contents($url.'?secret='.$secret.'&response='.$response);
    $result = json_decode($request);
    
    if($result->success == true){
    
        $password = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];
        $email = $_POST['email'];
        
        if(strlen($password) < 8)
        {
            exit('Password should be at least 8 characters long.');
        } 
        elseif(!preg_match('@[A-Z]@', $password)) 
        {
            exit("Password should include at least 1 uppercase character.");
        } 
        elseif(!preg_match('@[a-z]@', $password)) 
        {
            exit("Password should include at least 1 lowercase character.");
        }
        elseif(!preg_match('@[0-9]@', $password)) 
        {
            exit("Password should include at least 1 number.");
        } 
        elseif(!preg_match('@[^\w]@', $password)) 
        {
            exit("Password should include at least 1 special character.");
        } 
        else 
        {
            if($password != $confirmpassword)
            {
                exit("Passwords do not match.");
            } else {
                //updates the password
                $sql = 'UPDATE accounts SET password = ? WHERE email = ?';
                if($stmt = $con->prepare($sql)){
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->bind_param('ss', $password, $email);
                    $stmt->execute();
                    
                    //removes the old recovery code
                    $sql = 'UPDATE accounts SET recovery_code = ? WHERE email = ?';
                    if($stmt = $con->prepare($sql)){
                        $new_code = null;
                        $stmt->bind_param('ss', $new_code, $email);
                        $stmt->execute();
                    }
                    
                    //unlocks account
                    $sql = 'UPDATE accounts SET incorrect = ? WHERE email = ?';
                    if($stmt = $con->prepare($sql)){
                        $new_attempts = 0;
                        $stmt->bind_param('ss', $new_attempts, $email);
                        $stmt->execute();
                    }
                    
                } else {
                    exit('SQL Error.');
                }
            }
        }
    } else {
        exit('ReCaptcha Failed.');
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Password Reset</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <div class="login">
    	    <h3>Password reset! You may now log in using your new password. </h3>
    	    <div class="login-footer">
    		    <a href="index.php"> Back to home page.</a>
    		</div>
		</div>
	</body>
</html>