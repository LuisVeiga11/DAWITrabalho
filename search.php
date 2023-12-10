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
	if (! empty($_POST) ){
	
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
		    // Escape user input to prevent SQL injection
		    $searchTerm = mysqli_real_escape_string($myDb, $_POST['searchTerm']);
			
		    // Build the SELECT query
			//$query = "SELECT * FROM products WHERE name = '%$searchTerm%' OR description = '%$searchTerm%'";
			$query = "SELECT * FROM products WHERE name LIKE '%".$searchTerm."%' OR description LIKE '%".$searchTerm."%'";
         	//$result = executeQuery( $myDb, $query);
			//Execute the query
			$result = mysqli_query($myDb, $query);

			// Check for errors
			if (!$result) {
			  echo "Query failed: " . mysqli_error($myDb);
			  exit();
			}		
         			// Save all rows in an array
					$products = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$products[] = $row;
					}
					
					// Save the array in a session variable
					$_SESSION['products'] = $products;
				}
			// Close the connection
			mysqli_close($myDb);
			
		}
?>
<main>
	<div class="product-grid">
	<div class="product">
	<h2>Your search : <?php echo $_POST['searchTerm']; ?></h2>
	<?php
		// Iterate over the array to display the data
		foreach ($_SESSION['products'] as $product) {
			echo '<h2>'.$product['name'].'</h2>';
			echo $product['description'].'<br><br>';
			echo $product['price'].'€<br><br>';
			echo '<a href="index.php?id_prod='.$product["id_products"].'"><img src="images/' . $product['image'] . '"></a><br>';
	}?>
	</div>
	</div>
</body>
</html>