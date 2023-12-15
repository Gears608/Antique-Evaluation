<?php
    session_start();
    include 'db.php';
    
    // check if all fields are filled out
    if ( !isset($_POST['username'], $_POST['password'], $_POST['confirmpassword'], $_POST['number'], $_POST['email'])) {
    	header('Location: index.php');
    	exit;
    }
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $phoneNo = $_POST['number'];
    $sec1 = $_POST['security1'];
    $sec2 = $_POST['security2'];
    $sec3 = $_POST['security3'];
    
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $secret = '6LctIFYjAAAAAMzYvNKk6i5bS_gG0SC1qx11lhGC';
    $response = $_POST['token_generate'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    
    $request = file_get_contents($url.'?secret='.$secret.'&response='.$response);
    $result = json_decode($request);
    
    if($result->success == true){
        //checks if the username already exists in the database
        $sql = 'SELECT id, password FROM accounts WHERE username = ?';
        if ($stmt = $con->prepare($sql)) 
        {
        	// binds the username to a string
        	$stmt->bind_param('s', $_POST['username']);
        	// executes the sql statement
        	$stmt->execute();
        	// stores the result
        	$stmt->store_result();
        	
        	if ($stmt->num_rows == 0) 
        	{
        	    //checks if the email already exists in the database
        	    $sql = 'SELECT id, password FROM accounts WHERE email = ?';
        	    if ($stmt = $con->prepare($sql)) 
        	    {
                	// binds the username to a string
                	$stmt->bind_param('s', $_POST['email']);
                	// executes the sql statement
                	$stmt->execute();
                	// stores the result
                	$stmt->store_result();
                	if ($stmt->num_rows == 0) 
                	{
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
                	    elseif(preg_match('@[^\w]@', $username)) 
                	    {
                	        exit("Username should not include special characters.");
                	    }
                	    elseif(preg_match('@[^\w]@', $sec1) || preg_match('@[^\w]@', $sec2) || preg_match('@[^\w]@', $sec3)) 
                	    {
                	        exit("Security answers should not include special characters.");
                	    }
                	    else 
                	    {
                	        if($password != $confirmpassword)
                	        {
                	            exit("Passwords do not match.");
                	        }
                	        else 
                	        {
                	            $sql = 'INSERT INTO accounts (username, password, email, telNo, activation_code, activation_expiry, security_q_1, security_q_2, security_q_3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
                	            if($stmt = $con->prepare($sql)){
                    	            $activation_code = uniqid();
                    	            
                    	            //hashes the password and activation code to be stored in the database
                    	            $password = password_hash($password, PASSWORD_DEFAULT);
                    	            $sec1 = password_hash(strtolower($sec1), PASSWORD_DEFAULT);
                    	            $sec2 = password_hash(strtolower($sec2), PASSWORD_DEFAULT);
                    	            $sec3 = password_hash(strtolower($sec3), PASSWORD_DEFAULT);
                                    $activation_code_hashed = password_hash($activation_code, PASSWORD_DEFAULT);
                                    
                                    //sets the expiry date for 1 day after current time
                                    $expiry = date('Y-m-d H:i:s',  time() + (1 * 24 * 60 * 60));
                                    
                    	            $stmt->bind_param('sssssssss', $username, $password, $email, $phoneNo, $activation_code_hashed, $expiry, $sec1, $sec2, $sec3);
                    	            $stmt->execute();
                    	            
                    	            //sends an email to the user
                    	            $from    = 'noreply@lovejoyantiques.com';
                                    $subject = 'Account Activation';
                                    $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                                    $activate_link = 'https://compsecrb2022.000webhostapp.com/activate.php?email=' . $email . '&code=' . $activation_code;
                                    $message = '<p>Please click the following link to activate your account: <a href="' . $activate_link . '">' . $activate_link . '</a><br>This link will expire after 24 hours after which you will have to re-reister your account.<br>If you did not sign up for this service, it is safe to ignore this email and your details will be deleted.</p>';
                                    
                                    mail($email, $subject, $message, $headers);
                	            } else {
                	                exit('SQL Error.');
                	            }
                	        }
                	    }
                	} 
                	else 
            	    {
                	   exit('Email already in use.');
                	}
        	    } else {
        	        exit('SQL Error.');
        	    }
        	} 
        	else 
        	{
        	    exit('Username already taken.'); 
        	}
        }
    } else { 
        exit('ReCaptcha failed.');
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Sign Up</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <div class="login">
    	    <h3>Account created, please check your email for an activation link (May have been put into spam folder). </h3>
    	    <div class="login-footer">
    		    <a href="index.php"> Back to home page.</a>
    		</div>
		</div>
	</body>
</html>