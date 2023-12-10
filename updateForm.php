<?php
   //check for authentication information and potential navigation menu
	$path = 'goodies/header.php';		
	if (  file_exists($path) ){
	   require_once($path);				
	}
	else{
	   echo 'Internal server error: please try again later (Code: 8).';
	   die();		
	}

	/* It is the first time displaying this form? If so, fields must be completed with data from the database, with the exception of the password. 
	 * In this example, the user will be able to change the email address and the password.
   */
	if ( empty($_POST) ){
	
		$path = 'goodies/DatabaseManager.php';		
		if (  file_exists($path) ){
	   	require_once($path);				
		}
		else{
			echo 'Internal server error: please try again later (Code: 11).';
			die();		
		}
			
		// establish a connection to the database by calling the proper function that exists in the DatabaseManager.php file			
		$myDb = establishDbConnection();		
         
		//check if a fatal error occourred          
      if ( is_string( $myDb) ){
			// unable to connect to the database, which constitutes a fatal error.         
         echo "The web application is unable to function properly at this time. Please try again later.";
         die();
      }
      else {
         // prepare and execute the MySQl statement to obtain the authenticated user's data (in this example, only the email address is needed).
			$query = 'SELECT email FROM users WHERE id_users=?';
			$type = array('i');
			$arguments = array($_SESSION['id_users']);
         $result = executeQuery( $myDb, $query, $type, $arguments);
         	
         // close the active database connection
         endDbConnection( $myDb );
         	
         //check if an error has occurred (result is a string)
         if ( is_string($result) ){
				echo $result;	
			   die(); 
         }
         elseif( !$result ){
         	echo "This operation is unavailable right now. Please try again later (Code: 20)";
			   die(); 
         }
         else{
         	// yet another security check: was only one row returned from the table, as it should (only one user with that username/password data)?         		
				if ( mysqli_num_rows($result) != 1 ){
					echo "Fatal error! There is something inherently wrong with the system. Please try again later.";
					die();
				}         		
         	else{
         		// get the user's data obtained from the database table	
         		$row = mysqli_fetch_assoc($result);
					
					//save the data to a variable to be used in the form. This code is ready to be expanded, if needed, to other fields.
					$originalData = array('email' => $row['email']);
				}
			}
		}
	}//end if
	elseif ( !empty($_POST) ){ #Code execution only enters the if after a first form submission (with or without data in the form fields).
		
		/* Let's include the validation function - BagOfTtricks.php - 
		 * so that it can be used in this process.	
	   */
	   # this file is in a folder - "goodies". Therefore, it needs to be included in the path.
	   $path = 'goodies/BagOfTricks.php';		
		if (  file_exists($path) ){
		   require_once($path);				
		}
		else{
		   echo 'Internal server error: please try again later (Code: 8).';
		   die();		
		}
		 	
		/* Form' fields validation is dealt with by a function designed for each different form and implemented in the BagOfTricks.php file.
		 * That function will return an array if any field has content that is not compliant with the established rules or a true value if 
		 * no errors are detected.
		 * It accepts, as a parameter, all the data that the user submitted via form, which is nicely packaged in $_POST.
	   */
	   
		$validationResult = validateUpdateForm ($_POST);
		
		//check if there were errors in filling out the form by checking the value in the $validationResult variable. 
		if ( ! is_array($validationResult) && ! is_string($validationResult) ){
			/* no errors were present in the form. Proceed to update the existing user in the "users" table from "aulas" database. The latter
			 * can be accessed via browser, using http://localhost/adminer. Table "users" will be created in class. All the database
			 * related processes are implemented in the DatabaseManager.php file. As such, it must be included.
		    */ 			
			$path = 'goodies/DatabaseManager.php';		
		   if (  file_exists($path) ){
				 require_once($path);				
		   }
		   else{
			   echo 'Internal server error: please try again later (Code: 11).';
			   die();		
		   }
			
			// establish a connection to the database by calling the proper function that exists in the DatabaseManager.php file			
			$myDb = establishDbConnection();		
         
			//check if a fatal error occourred          
         if ( is_string( $myDb) ){
				// unable to connect to the database, which constitutes a fatal error.         
         	echo "The web application is unable to function properly at this time. Please try again later.";
         	die();
         }
         else {
         	
				//does the session have the required data to proceed (user id)?          	
         	if ( !array_key_exists('id_users', $_SESSION) || !isset($_SESSION['id_users']) ){
         		//if not, end here.
         		echo "A fatal error has occurred. Please try again later. (code: 22)";
         		die();
         	}
         	
    			// prepare and execute a MySQl statement updating the user data in the 'users' table in the database.
				$query = 'UPDATE users SET email=?, password=? WHERE id_users=?';
				$type = array('s','s','i');
				$arguments = array($_POST['email'], md5($_POST['password']), $_SESSION['id_users']);
         	$result = executeQuery( $myDb, $query, $type, $arguments);
         	       		
         	//check if an error has occurred (result is a string)
         	if (!empty ($result) && is_string($result) ){
					echo $result;	
         	}
         	elseif( !empty($result) && !$result ){
         		echo "This operation is unavailable right now. Please try again later (Code: 20)";
         		die();
         	}
         	else{
					// all went well. Return the user to the homepage with a success message
					$_SESSION['code'] = 102;
					$result = endDbConnection( $myDb );
					header('Location:index.php');
					die();
         	}
         	
         	// close the active database connection
         	$result = endDbConnection( $myDb );
				die();         
			}      
		}
		// if the code execution reaches this line, it means that there is at least one error in the form. It will be printed near the respective form field.	
	} //end main if
?>
<!DOCTYPE html>
<html>
<head>
  <title>DAW DOI</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
	<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php
	/* print an error message if the form validation function is being incorrectly used. If that happens, $validationResult
	 * will have a string with the error.
    */
    if ( !empty($validationResult) && is_string($validationResult) ){
    	echo $validationResult;
    }
?>
<div class="login-container">
<form action="" method="POST" class="login-form">
  <label for="email">Email:</label><br>
  <input type="text" id="email" name="email" value="<?php
  		// check if this field has a reported error. If not, place the value that the user submitted.
  		if ( (!empty($validationResult) && isset($validationResult['email']) && !$validationResult['email'][0]) ){
  			echo $_POST['email'];
  		}
  		elseif( !empty($originalData) && array_key_exists('email', $originalData) && isset($originalData['email']) ){
  			echo $originalData['email'];
  			unset ($originalData['email']);
  		} 
  ?>"><br>
  <?php
      // check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['email']) && $validationResult['email'][0] ){
  			echo $validationResult['email'][1] . '<br>';
  		}
  ?>
  <label for="password">Password:</label><br>
  <input type="password" id="password" name="password"><br>
  <?php
      // check if this field has a reported error. If so, show it.
  		if ( !empty($validationResult) && isset($validationResult['password']) && $validationResult['password'][0] ){
  			echo $validationResult['password'][1] . '<br>';
  		}
  ?>
  <label for="rpassword">Repeat Password:</label><br>
  <input type="password" id="rpassword" name="rpassword"><br>
  <?php
      // check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['rpassword']) && $validationResult['rpassword'][0] ){
  			echo $validationResult['rpassword'][1] . '<br>';
  		}
  ?>
  <input type="submit" value="Submit">
</form>
</div> 
</body>
</html>