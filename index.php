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
			if(!empty($_GET['id_prod'])){
				$query = "SELECT * FROM products WHERE id_products= " .$_GET['id_prod'];
			} else{
				$query = "SELECT * FROM products";
			}
         	
			$result = mysqli_query($myDb, $query);
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

         			// Save all rows in an array
					$products = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$products[] = $row;
					}
					
					// Save the array in a session variable
					$_SESSION['products'] = $products;
				}
		}
		
	//check if a session is already started to avoid warnings.
	if (session_status() === PHP_SESSION_NONE) {
    session_start();
   }

	//check if there is any message to present the user with.
	if ( !empty($_SESSION) && array_key_exists('code', $_SESSION) && isset($_SESSION['code']) ){
		//we need the code book	
		require_once('goodies/codes.php');
		
		//is this code valid?
		if ( isset($codes[$_SESSION['code']] ) ){
			echo '<br>' . $codes[$_SESSION['code']] . '<br>';
			
			//clean the variable and respective code so not to have repeated messages.
			unset($_SESSION['code']);
		}
	}
?>
  <main>
	<div class="product-grid">
	<div class="product">
	<?php
		// Iterate over the array to display the data
		foreach ($_SESSION['products'] as $product) {
			echo '<h2>'.$product['name'].'</h2>';
			echo $product['description'].'<br><br>';
			echo $product['price'].'€<br><br>';
			echo '<a href="?id_prod='.$product["id_products"].'"><img src="images/' . $product['image'] . '"></a><br>';
			echo '<button>Add to cart</button></td><br>';
			if($_SESSION['type_user'] == "admin"){
			echo '<a href="delete.php?id_prod='.$product["id_products"].'"><button>Delete</button></a>';
			}
			else{
				echo '';
			}
			echo '<hr><br>';
		}
		?>
	</div>
	</div>
</body>
</html>