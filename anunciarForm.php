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
		
		/* Let's include the validation function - BagOfTricks.php - 
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
	   
		$_POST['image'] = $_FILES['image'];
	   
		$validationResult = validateProductsForm($_POST);

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
						//now the new user can be registered					
						// prepare and execute the MySQl statement to insert data for a new user in the 'users' table.
						$query = 'INSERT INTO products (name, description, price, image, id_users) VALUES (?,?,?,?,?)';
						$type = array('s','s','i','s','i');
						$arguments = array($_POST['name'],$_POST['description'], $_POST['price'], $_FILES['image']['name'], $_SESSION['id_users']);
         			$result = executeQuery( $myDb, $query, $type, $arguments);
					
					$upload_file=move_uploaded_file($_FILES["image"]["tmp_name"], "images/".$_FILES['image']['name']);	
					if (! $upload_file){
							echo "Sorry, there was an error uploading your file.";
						  }
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
         				
         				//check if a session is already started to avoid warnings.
							if (session_status() === PHP_SESSION_NONE) {
    							session_start();
   						}
   						
   						//place the success code in the session
         				$_SESSION['code'] = 103;
						
         				//the user is registered. Go to the homepage.
							header('Location:index.php');
							die();       	
         			}
         			// close the active database connection
         			$result = endDbConnection( $myDb );
						die();  
					}	
         		}	  
			}// if the code execution reaches this line, it means that there is at least one error in the form. It will be printed near the respective form field.	
//end main if
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
<form action="" method="POST" enctype="multipart/form-data" class="login-form"> 
  <label for="name">Nome do produto:</label><br>
  <input type="text" id="name" name="name" required value="<?php
  		// check if this field has a reported error. If not, place the value that the user submitted.
  		if ( !empty($validationResult) && isset($validationResult['name']) && !$validationResult['name'][0] ){
  			echo $_POST['name'];
  		}  
  		elseif( !empty($alreadyInUse) && !$alreadyInUse['name'] ){
  			echo $_POST['name'];
  		}
  ?>"><br>
  <?php
  		// check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['name']) && $validationResult['name'][0] ){
  			echo $validationResult['name'][1] . '<br>';
  		}  
  		elseif( !empty($alreadyInUse) && $alreadyInUse['name'] ){
  			echo '<p style="color:blue;font-size:60%;">This username is already taken.</p><br>';
  		}
  ?>
  <label for="price">Preço do produto:</label><br>
  <input type="number" id="price" name="price" min="0.00" max="100000.00" required value="<?php
  		// check if this field has a reported error. If not, place the value that the user submitted.
  		if ( !empty($validationResult) && isset($validationResult['price']) && !$validationResult['price'][0] ){
  			echo $_POST['price'];
  		}  
  		elseif( !empty($alreadyInUse) && !$alreadyInUse['price'] ){
  			echo $_POST['price'];
  		}
  ?>"><br>
  <?php
  		// check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['price']) && $validationResult['price'][0] ){
  			echo $validationResult['price'][1] . '<br>';
  		}  
  ?>
  <label for="description">Descrição do produto:</label><br>
  <textarea name="description" rows="5" cols="10" required value="<?php
  		// check if this field has a reported error. If not, place the value that the user submitted.
  		if ( !empty($validationResult) && isset($validationResult['description']) && !$validationResult['description'][0] ){
  			echo $_POST['description'];
  		}  
  		elseif( !empty($alreadyInUse) && !$alreadyInUse['description'] ){
  			echo $_POST['description'];
  		}
  ?>"></textarea><br>
  <?php
      // check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['description']) && $validationResult['description'][0] ){
  			echo $validationResult['description'][1] . '<br>';
  		}
  ?>
  <label for="image">Imagem do produto: <span style="color:red; font-style: italic; font-size:10px;">(Apenas ficheiros JPG, JPEG ou PNG. Máx: 2MB)</span></label><br>
  <input type="file" id="image" name="image" required><br>
  <?php
      // check if this field has a reported error. If so, show it.
  		if ( !empty( $validationResult) && isset($validationResult['image']) && $validationResult['image'][0] ){
  			echo $validationResult['image'][1] . '<br>';
  		}
  ?><br>
  <input type="submit" value="Submit">
</form> 
</div>

</body>
</html>

