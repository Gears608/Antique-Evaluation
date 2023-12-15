<?php
    session_start();
    
    $session_token = uniqid();
    $_SESSION['token'] = $session_token;
    
    include 'db.php';
    
    // checks that the email and code exist
    if(isset($_GET['email'], $_GET['code'])){
        
        //store the email and code
        $email = $_GET['email'];
        $code = $_GET["code"];
        
        //selects the code and expiry date from the database
        $sql = 'SELECT recovery_code, recovery_expiry FROM accounts WHERE email = ?';
        if($stmt = $con->prepare($sql)){
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            //checks account exists
            if($stmt->num_rows > 0){
                $stmt->bind_result($found_code, $expiry);
                $stmt->fetch();
                //checks code is correct
                if(password_verify($code, $found_code)){
                    //checks if code is expired
                    if($expiry > date('Y-m-d H:i:s')){
                        
                    } else {
                        exit('Link Expired.');
                    }
                } else {
                    exit('Unknown Link');
                }
            } else {
                exit('Account not found.');
            }
        } else {
            exit('SQL Error.');
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
		<title>Password Reset</title>
		<script src="https://www.google.com/recaptcha/api.js?render=6LctIFYjAAAAAAzwY3EVOFvgUtQU5cVf1kV1d6HF"></script>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="login">
			<h1>Reset Password</h1>
			<form action="authenticateNewPassword.php" method="post">
			    <input type="hidden" name="email" value="<?php echo $email;?>">
				<input type="password" name="password" placeholder="Password" id="password" required>
				<input type="password" name="confirmpassword" placeholder="Confirm Password" id="confirmpassword" required>
				<input type="hidden" value="<?php echo $session_token ?>" name="sessionid" id="sessionid">
				<input type="hidden" name="token_generate" id="token_generate">
				<input type="submit" value="Reset Password">
			</form>
			<br>
		</div>
	</body>
</html>

<?php
    include "captchaFunction.php"
?>