<?php
    session_start();
    
    include 'db.php';

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $secret = '6LctIFYjAAAAAMzYvNKk6i5bS_gG0SC1qx11lhGC';
    $response = $_POST['token_generate'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $request = file_get_contents($url.'?secret='.$secret.'&response='.$response);
    $result = json_decode($request);
    
    if($result->success == true){
        $filepath = $_FILES['file']['tmp_name'];
        $fileSize = filesize($filepath);
        $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
        $filetype = finfo_file($fileinfo, $filepath);
        
        if($fileSize == 0){
            exit ('File Empty.');
        }
        
        if($fileSize > (3*1024*1024)){
            exit('File too large.');
        }
        
        $allowedTypes = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg'
        ];
        
        if (!in_array($filetype, array_keys($allowedTypes))) {
            exit("File not allowed.");
        }
        
        $filename = date('Y_m_d_H_i_s') . '_' . $_SESSION['name'];
        $extension = $allowedTypes[$filetype];
        $targetDirectory = __DIR__ ."/uploads";
        $newFilepath = $targetDirectory . "/" . $filename . "." . $extension;
        if (!copy($filepath, $newFilepath )) { // Copy the file, returns false if failed
           exit("Can't move file.");
        }
        unlink($filepath); // Delete the temp file
        
        $username = $_SESSION['name'];
        $description = $_POST['details'];
        $description = filter_var($description, FILTER_SANITIZE_EMAIL);
        
        if($_POST['contact'] == 'telNo'){
            $sql = 'SELECT telNo FROM accounts WHERE username = ?';
        } else {
            $sql = 'SELECT email FROM accounts WHERE username = ?';
        }
        if($stmt = $con->prepare($sql)){
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($contact);
            $stmt->fetch();
            $stmt->close();
        } else {
            exit('SQL Error 1.');
        }
        
        $sql = 'INSERT INTO images(username, contact, image, description) VALUES (?, ?, ?, ?)';
        if($stmt = $con->prepare($sql)){
            $uploadPath = "/uploads/" . $filename . "." . $extension;
            $stmt->bind_param('ssss', $username, $contact, $uploadPath, $description);
            $stmt->execute();
        } else {
            exit('SQL Error 2.');
        }
        
    } else {
        exit('ReCaptcha Failed.');
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Evaluation Upload</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	    <div class="login">
    	    <h3>Evaluation was sent. </h3>
    	    <div class="login-footer">
    		    <a href="home.php"> Back to home page.</a>
    		</div>
		</div>
	</body>
</html>