<?php
    // database information
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'id19927510_root';
    $DATABASE_PASS = 'Q-+a9{@o9xxJzFno';
    $DATABASE_NAME = 'id19927510_compseclogin';
    
    // connect to given database
    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
    if ( mysqli_connect_errno() ) {
    	// error if the database cannot be accessed
    	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
    }
?>