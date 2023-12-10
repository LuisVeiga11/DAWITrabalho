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
	
	if ( !empty($_POST) ){ #Code execution only enters the if after a first form submission (with or without data in the form fields).
		
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
	   
		$validationResult = validateRegisterForm ($_POST);
		
		//check if there were errors in filling out the form by checking the value in the $validationResult variable. 
		if ( ! is_array($validationResult) && ! is_string($validationResult) ){
			/* no errors were present in the form. Proceed to insert the new user in the "users" table from "aulas" database. The latter
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
         	
         	//first things first: let's check if there is already a username or an email like the ones the user is trying to register.
				
				//declare a simple error structure to better inform the user about what the change needs to be in the register data
				$alreadyInUse = array( 'username' => false, 'email' => false);         	
         	
         	//now, the query
         	$query = "SELECT username,email FROM users WHERE username=? OR email=?";
         	$type = array('s','s');
				$arguments = array($_POST['username'], $_POST['email']);
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
					
					//query successful. Are there results?					         		
					if ( mysqli_num_rows($result) > 0 ){
						
						//no one know how many lines are returned. We can have a maximum of two lines, assuming that both the email and the username are in different registered users records.
						while ($row = mysqli_fetch_assoc($result) ){
						
							//is the email or the username that are the same in this record?
							if ( $_POST['username'] == $row['username']){
								$alreadyInUse['username'] = true;
							}						
							else {
								$alreadyInUse['email'] = true;
							}
						}						
					}         		
         		else{
						//now the new user can be registered					
					
						// prepare and execute the MySQl statement to insert data for a new user in the 'users' table.
						$query = 'INSERT INTO users (username, email, password) VALUES(?,?,?)';
						$type = array('s','s','s');
						$arguments = array($_POST['username'], $_POST['email'], md5($_POST['password']) );
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
         				/* Going to send a message via session. This does not mean that the user is authenticated: only that the session is being used as a covert communication channel.
         				 * Please see the codes.php file to have the meaning of each message. 
         				 */
         				$query = 'INSERT INTO users_type_users (id_users) VALUES(?)';
						$type = array('i');
						$arguments = array(mysqli_insert_id($myDb));
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
         				//check if a session is already started to avoid warnings.
							if (session_status() === PHP_SESSION_NONE) {
    							session_start();
   						}
   						
   						//place the success code in the session
         				$_SESSION['code'] = 100;
						
         				//the user is registered. Go to the homepage.
							header('Location:index.php');
							die();       	
         			}
					//Busca de id= mysqli_insert_id($myDb)
         			// close the active database connection
         			$result = endDbConnection( $myDb );
						die();  
					}	
         		}	  
			}      
		}
		// if the code execution reaches this line, it means that there is at least one error in the form. It will be printed near the respective form field.	
	}
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
  <label for="username">Username:</label><br>
  <input type="text" id="username" name="username" value="<?php
  		// check if this field has a reported error. If not, place the value that the user submitted.
  		if ( !empty($validationResult) && isset($validationResult['username']) && !$validationResult['username'][0] ){
  			echo $_POST['username'];
  		}  
  		elseif( !empty($alreadyInUse) && !$alreadyInUse['username'] ){
  			echo $_POST['username'];
  		}
  ?>"><br>
  <?php
  		// check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['username']) && $validationResult['username'][0] ){
  			echo $validationResult['username'][1] . '<br>';
  		}  
  		elseif( !empty($alreadyInUse) && $alreadyInUse['username'] ){
  			echo '<p style="color:blue;font-size:60%;">This username is already taken.</p><br>';
  		}
  ?>
  <label for="email">Email:</label><br>
  <input type="text" id="email" name="email" value="<?php
  		// check if this field has a reported error. If not, place the value that the user submitted.
  		if ( !empty($validationResult) && isset($validationResult['email']) && !$validationResult['email'][0] ){
  			echo $_POST['email'];
  		}  
  		elseif( !empty($alreadyInUse) && !$alreadyInUse['email'] ){
  			echo $_POST['email'];
  		}
  ?>"><br>
  <?php
      // check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['email']) && $validationResult['email'][0] ){
  			echo $validationResult['email'][1] . '<br>';
  		}
  		elseif( !empty($alreadyInUse) && $alreadyInUse['email'] ){
  			echo '<p style="color:blue;font-size:60%;">This email address is already taken.</p><br>';
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