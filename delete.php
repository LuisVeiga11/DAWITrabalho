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
	if (! empty($_GET) ){
	
		$path = 'goodies/DatabaseManager.php';		
		if (  file_exists($path) ){
	   	require_once($path);				
		}
		else{
			echo 'Internal server error: please try again later (Code: 11).';
			die();		
		}
		$myDb = establishDbConnection();		
			//check if a fatal error occourred          
         if ( is_string( $myDb) ){
				// unable to connect to the database, which constitutes a fatal error.         
         	echo "The web application is unable to function properly at this time. Please try again later.";
         	die();
         }
         else {
			// Armazena o ID do anúncio que será excluído
			$id = $_GET['id_prod'];

			// Cria a consulta SQL para excluir o anúncio
			$query = "DELETE FROM products WHERE id_products=$id";

			// Executa a consulta e verifica se foi bem-sucedida
			if (mysqli_query($myDb, $query)) {
				echo "Anúncio excluído com sucesso.";
			} else {
				echo "Erro ao excluir anúncio: " . mysqli_error($myDb);
			}
			
			if (session_status() === PHP_SESSION_NONE) {
    							session_start();
   						}
   						
   						//place the success code in the session
         				$_SESSION['code'] = 104;
						
         				//the user is registered. Go to the homepage.
							header('Location:index.php');
							die();       	
         			}
			// Fecha a conexão com o banco de dados
			mysqli_close($myDb);
			
		}
?>