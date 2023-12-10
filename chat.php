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
			$comments=array();
		if ( is_string( $myDb) ){
				// unable to connect to the database, which constitutes a fatal error.         
         	echo "The web application is unable to function properly at this time. Please try again later.";
         	die();
         }
         else {
		 
		 if (! empty($_POST) ) {
			 // prepare and execute the MySQl statement to insert data for a new user in the 'users' table.
					$query = 'INSERT INTO chat (name, comment, id_users) VALUES (?,?,?)';
					$type = array('s','s','i');
					$arguments = array($_POST['name'], $_POST['comment'],$_SESSION['id_users'] );
         			$result = executeQuery( $myDb, $query, $type, $arguments); 
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
						// Retrieve the comments from the database
					  $query = "SELECT * FROM chat ORDER BY date DESC";
					  $result = mysqli_query($myDb, $query);
					  if (mysqli_num_rows($result) > 0) {
						// output the comments
						while($row = mysqli_fetch_assoc($result)) {
						$comments[]=$row ;
						  }
						}
					}
					}
							
			            //check if a session is already started to avoid warnings.
						if (session_status() === PHP_SESSION_NONE) {
						session_start();
						}

					echo "<div class='chat-grid'><div class='chat'>";
					echo "<h2>Deixe seu comentário</h2>";
					echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
					echo "<label for='name'>Name:</label><br>";
					echo "<input type='text' id='name' name='name'><br>";
					echo "<label for='comment'>Comment:</label><br>";
					echo "<textarea rows='3' cols='40' id='comment' name='comment'></textarea><br><br>";
					echo "<input type='submit' value='Submit'>";
					echo "</form></div></div>";
					foreach($comments as $comm) {
					echo "<b>".$comm['name'],":</b><br>".$comm['comment']."<br/><br/><br/>";
					}
?>
</body>
</html>