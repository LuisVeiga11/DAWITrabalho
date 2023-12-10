<!DOCTYPE html>
<html>
<head>
  <title>Logout</title>
</head>
<body>
<?php

	//this script did not need any HTML, but it does not hurt.
	
	//check for authentication information and potential navigation menu
	$path = 'goodies/header.php';		
	if (  file_exists($path) ){
	   require_once($path);				
	}
	else{
	   echo 'Internal server error: please try again later (Code: 8).';
	   die();		
	}

	//check if a session is already started to avoid warnings.
	if (session_status() === PHP_SESSION_NONE) {
    session_start();
   }
   
	// the logout operation is truly simple, but it needs to be done right.
	unset( $_SESSION['id_users']);   
   unset( $_SESSION['username']);
	session_destroy();
	$_SESSION = array();
	
	// now send the user to the homepage
	header('Location:index.php');
	die();
?>
</body>
</html>