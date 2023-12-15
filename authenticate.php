<?php
    session_start();
    
    if(!($_POST['sessionid'] == $_SESSION['token'])){
        exit('Unknown Session ID');
    }
    
    // check if username and password fields are filled out
    if ( !isset($_POST['username'], $_POST['password']) ) {
    	header('Location: index.php');
    	exit;
    }
    
    if(preg_match('@[^\w]@', $_POST['username'])) 
    {
        exit('Incorrect username and/or password!');
    }
    
    include 'db.php';
    
    //checks recaptcha is a success
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $secret = '6LctIFYjAAAAAMzYvNKk6i5bS_gG0SC1qx11lhGC';
    $response = $_POST['token_generate'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $request = file_get_contents($url.'?secret='.$secret.'&response='.$response);
    $result = json_decode($request);
    
    if($result->success == true){
        // beigning creation of an sql statement which will retrieve the id and password from accounts using a given username
        if ($stmt = $con->prepare('SELECT id, email, password, active, admin, incorrect FROM accounts WHERE username = ?')) {
        	// binds the username to a string
        	$stmt->bind_param('s', $_POST['username']);
        	// executes the sql statement
        	$stmt->execute();
        	// stores the result
        	$stmt->store_result();
        	
        	if ($stmt->num_rows > 0) {
            	$stmt->bind_result($id, $email, $password, $active, $admin, $incorrect);
            	$stmt->fetch();
            	if($active == 1){
            	    if($incorrect < 3){
                    	// checks if password is correct
                    	if (password_verify($_POST['password'], $password)) 
                    	{
                        		// logs the user into a session
                        		session_regenerate_id();
                        		$_SESSION['loggedin'] = TRUE;
                        		$_SESSION['name'] = $_POST['username'];
                        		$_SESSION['id'] = $id;
                        		$_SESSION['admin'] = $admin;
                        		
                        		//generates a unique authenticate code
                                $authenticate_code = uniqid();
                                //hashes the authenticate code to be stored in the database
                                $authenticate_code_hashed = password_hash($authenticate_code, PASSWORD_DEFAULT);
                        		//sets expiry time for 10 mins after current time
                                $expiry = date('Y-m-d H:i:s', time() + (10*60));
                        		
                        		//updates the authenticate code
                                $sql = 'UPDATE accounts SET authenticate_code = ? WHERE email = ?';
                                if($stmt = $con->prepare($sql)){
                                    $stmt->bind_param('ss', $authenticate_code_hashed, $email);
                                    $stmt->execute();
                                    
                                    //updates the expiry time of the recovery code
                                    $sql = 'UPDATE accounts SET authenticate_expiry = ? WHERE email = ?';
                                    if($stmt = $con->prepare($sql)){
                                        $stmt->bind_param('ss', $expiry, $email);
                                        $stmt->execute();
                                        
                                        //sends an email to the user
                                        $from    = 'noreply@lovejoyantiques.com';
                                        $subject = 'Authentication Code';
                                        $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                                        $message = '<p>Please use the following code to authenticate your log in: ' .  $authenticate_code . '<br>This link will expire after 10 minutes after which you will have to request a new link.<br>If you did not request an authentication code, it is safe to ignore this email.</p>';
                                        
                                        mail($email, $subject, $message, $headers);
                                                
                                    } else {
                                        exit('SQL Error.');
                                    }
                                } else {
                                exit('SQL Error.');
                                }
                        		
                    	} else {
                    		$sql = 'UPDATE accounts SET incorrect = ? WHERE email = ?';
                    		if($stmt = $con->prepare($sql)){
                    		    $incorrect = $incorrect + 1;
                    		    $stmt->bind_param('ss', $incorrect, $email);
                    		    $stmt->execute();
                    		} else {
                    		    exit('SQL Error.');
                    		}
                    		
                    		exit('Incorrect username and/or password!');
                    	}
            	    } else {
            	        exit('Account locked. Please reset your password.');
            	    }
            	} else {
            	        exit('Account not activated.');
            	    }
            } else {
        	    // error if the username could not be found in the database
        	    exit('Incorrect username and/or password!');
            }
            
            $stmt->close();
        }
    } else {
        exit('ReCaptcha Failed.');
    }
    
    
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Authenticate Sign In</title>
		<script src="https://www.google.com/recaptcha/api.js?render=6LctIFYjAAAAAAzwY3EVOFvgUtQU5cVf1kV1d6HF"></script>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="login">
			<h1>Authenticate</h1>
			<form action="authenticate2FALogin.php" method="post">
				<input type="text" name="code" placeholder="Authentication Code" id="code" required>
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