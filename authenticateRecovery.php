<?php
    session_start();
    
    if(!($_POST['sessionid'] == $_SESSION['token'])){
        exit('Unknown Session ID');
    }
    
    if(preg_match('@[^\w]@', $_POST['username'])) 
    {
        exit('Account not found.');
    }
    if(preg_match('@[^\w]@', $_POST['security1']) || preg_match('@[^\w]@', $_POST['security2']) || preg_match('@[^\w]@', $_POST['security3'])) 
    {
        exit('Account not found.');
    }
    
    include 'db.php';
    
    // check if username and password fields are filled out
    if ( !isset($_POST['username'], $_POST['email']) ) {
    	header('Location: index.php');
    	exit;
    }
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
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
        //checks account with given username exists
        $sql = 'SELECT email,active,security_q_1, security_q_2, security_q_3 FROM accounts WHERE username = ?';
        if($stmt = $con->prepare($sql)){
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($found_email, $active, $found_sec1, $found_sec2, $found_sec3);
            $stmt->fetch();
            
            //checks emails match
            if($found_email == $email){
                if($active == 1){
                    if(password_verify(strtolower($sec1), $found_sec1) && password_verify(strtolower($sec2), $found_sec2) && password_verify(strtolower($sec3), $found_sec3)){
                        //generates a unique recovery code
                        $recovery_code = uniqid();
                        //hashes the recovery code to be stored in the database
                        $recovery_code_hashed = password_hash($recovery_code, PASSWORD_DEFAULT);
                        //sets expiry time for 10 mins after current time
                        $expiry = date('Y-m-d H:i:s', time() + (10*60));
                        
                        //updates the recovery code
                        $sql = 'UPDATE accounts SET recovery_code = ? WHERE email = ?';
                        if($stmt = $con->prepare($sql)){
                            $stmt->bind_param('ss', $recovery_code_hashed, $email);
                            $stmt->execute();
                            
                            //updates the expiry time of the recovery code
                            $sql = 'UPDATE accounts SET recovery_expiry = ? WHERE email = ?';
                            if($stmt = $con->prepare($sql)){
                                $stmt->bind_param('ss', $expiry, $email);
                                $stmt->execute();
                                
                                //sends an email to the user
                                $from    = 'noreply@lovejoyantiques.com';
                                $subject = 'Password Recovery';
                                $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                                $recovery_link = 'https://compsecrb2022.000webhostapp.com/resetPassword.php?email=' . $email . '&code=' . $recovery_code;
                                $message = '<p>Please click the following link to reset your password: <a href="' . $recovery_link . '">' . $recovery_link . '</a><br>This link will expire after 10 minutes after which you will have to request a new link.<br>If you did not request a password reset, it is safe to ignore this email.</p>';
                                
                                mail($email, $subject, $message, $headers);
                                        
                            } else {
                                exit('SQL Error.');
                            }
                        } else {
                        exit('SQL Error.');
                        }
                    } else {
                        exit('Account not found.');
                    }
                } else {
                    exit('Account not active.');
                }
            } else {
                exit('Account not found.');
            }
        } else {
            exit('SQL Error.');
        }
    } else {
        exit('ReCaptcha failed.');
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Password Recovery</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <div class="login">
    	    <h3>Password recovey email sent (May have been put into spam folder). </h3>
    	    <div class="login-footer">
    		    <a href="index.php"> Back to home page.</a>
    		</div>
		</div>
	</body>
</html>