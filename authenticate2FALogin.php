<?php
    session_start();
    
    include 'db.php';

    if (!isset($_SESSION['loggedin'])) {
    	header('Location: index.php');
    	exit;
    }
    
    
    //checks recaptcha is a success
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $secret = '6LctIFYjAAAAAMzYvNKk6i5bS_gG0SC1qx11lhGC';
    $response = $_POST['token_generate'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $request = file_get_contents($url.'?secret='.$secret.'&response='.$response);
    $result = json_decode($request);
    
    if($result->success == true){
        
        $sql = 'SELECT authenticate_code, authenticate_expiry FROM accounts WHERE username = ?';
        if($stmt = $con->prepare($sql)){
            $stmt->bind_param('s', $_SESSION['name']);
            $stmt->execute();
            $stmt->bind_result($code, $expiry);
            $stmt->fetch();
            $stmt->close();
            
            if($expiry > date('Y-m-d H:i:s')){
                if(password_verify($_POST['code'], $code)){
                    
                    $sql = 'UPDATE accounts SET authenticate_code = ? WHERE username = ?';
                    if($stmt = $con->prepare($sql)){
                        $new_code = null;
                        $stmt->bind_param('ss', $new_code, $_SESSION['name']);
                        $stmt->execute();
                    } else {
                        exit('SQL Error.');
                    }
                    
                    if($_SESSION['admin'] == 1){
                	    header('Location:listings.php');
                	} else {
                	    header('Location: home.php');
                	}
                } else {
                    exit('Unknown Code.');
                }
            } else {
                exit('Code Expired.');
            }
        } else {
            exit('SQL Error.');
        }
        
    }
?>