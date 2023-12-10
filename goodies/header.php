<?php
	/* This page is to be included in all pages. It may contain a navigation menu. The user must be checked to be authenticated or not and proceed accordingly.
	 */
	echo '<link rel="stylesheet" type="text/css" href="css/styles.css">';
	echo '<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1"></head>';

	//check if a session is already started to avoid warnings.
	if (session_status() === PHP_SESSION_NONE) {
    session_start();
   }
	
	if ( empty($_SESSION) || !array_key_exists('username', $_SESSION) || !isset($_SESSION['username']) ){
		//the user is not authenticated. This can be an error or a direct access. Send it back to the login page or see if this page can be seen without authorization.
		
		$path = 'goodies/ConfigApp.php';		
		if (  file_exists($path) ){
		   require_once($path);				
		}
		else{
		   echo 'Internal server error: please try again later (Code: 8).';
		   die();		
		}
		
		//which is the script where the header is being included? Get the last position of the URL, remove the "." and the "php".
	   $scriptName = explode('/', $_SERVER['PHP_SELF']);
	   $scriptName = (explode('.', $scriptName[sizeof($scriptName)-1]))[0];
	   
	   // is it on the no authentication required list available on ConfigApp.php?
	   if( in_array( $scriptName, $pages ) ){
	   	//allow the user to navigate here - present the simple menu
	   	echo '<header>
				<img src="images/logo_original_autogalaxy_resize.png">
				<form method="post" action="search.php">
				  <input type="text" name="searchTerm" id="searchTerm" placeholder="Search...">
				  <button type="submit">Procurar</button>
				</form>
			  </header>
			  <nav>
				<ul>
				  <li><a href="index.php">Home</a></li>
				  <li><a href="loginForm.php">Login</a></li>
				  <li><a href="registerForm.php">Register</a></li>
				</ul>
			  </nav>' ;
	   }
	   else{	
			//set the error to display in the login form
			$_SESSION['code'] = 101;
		
			//send the user away
			header('Location:loginForm.php');
			die();
		}
	}//end main if
	else{
		//the user is authenticated. Show the full menu. We can later differentiate between user types and show different menus
		//allow the user to navigate here - present the full menu
		echo '<header>
				<img src="images/logo_original_autogalaxy_resize.png">';
		if($_SESSION['type_user'] == "admin"){
			echo '<h1>Hello ' . $_SESSION['username'] . ' (' . $_SESSION['type_user'] . ')! </h1>';
		}
		else{
			echo '<h1>Hello ' . $_SESSION['username'] . '!</h1>';
		}
		echo '<form method="post" action="search.php">
				  <input type="text" name="searchTerm" id="searchTerm" placeholder="Search...">
				  <button type="submit">Procurar</button>
				</form>
			  </header>
			  <nav>
				<ul>
				  <li><a href="index.php">Home</a></li>
				  <li><a href="anunciarForm.php">Announce</a></li>
				  <li><a href="updateForm.php">Update</a></li>
				  <li><a href="logout.php">Logout</a></li>
				</ul>
			  </nav>';
	}	
?>