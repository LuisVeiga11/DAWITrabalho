<?php

	/* This file has functions that deal with database management processes, such as 
	 * connect, disconnect and perform prepared statements.
	 */

	function establishDbConnection(){
		
		/* the (four) required parameters to establish a connection to a database are available in 
 	    * the app configuration file (ConfigApp.php). As such, this file must be include to proceed.
	    */
	   
	   // let's first check if the configuration file exists in this path
		$path = 'goodies/ConfigApp.php';		
		if (  file_exists($path) ){		
			require ($path);				
		}
		else{
			return('Internal server error: please try again later (Code: 10).');		
			die();
		}
		
		// now proceed with establishing a connection to the database using the specified parameters. Before check if they exist.
		if ( isset($dbHost) && isset($dbUsername) && isset($dbPassword) && isset($dbName) ){
			$myDb = mysqli_connect($dbHost,$dbUsername,$dbPassword,$dbName);		
		}
		else{
			// the required parameters are not defined. Fatal error.
			return ("Fatal error: the application cannot function properly right now (Code: 5). Please try again later.");		
		}

		/* lastly, verify if the connection was established or an error has occurred. Indeed, without a
		 * database connection, this app (and so many others today) cannot proceed as it is a fatal error.
		 */

		if ( mysqli_connect_errno() ){
  			return ("Fatal error: the application cannot function properly right now (Code: 4). Please try again later.");
  			die();
  		}
  		else{
			//connection was successful: return the connection handler and proceed.
			return($myDb);   		
  		}	
	} //end function

	// this function will receive a query, prepare it with the MySQL engine and also bind the arguments. Then, it will execute it and return results on success.
	function executeQuery( $myDb, $query, $type, $arguments){
		
		/* A prepared statement is both safer and quicker when considering multiple executions. However, it can be slower when it is only
		 * done once. It is, indeed, a trade of. As best practice, it is advisable to use prepared statements always, to put yet
		 * another layer of protection against SQL Injection attacks.
		 */
		 
		// in a prepared statement, the first step is to prepare the query in the MySQL engine. Both structure and resources will be allocated to the query
		$preparedQuery = mysqli_prepare($myDb, $query);
		
		// if an error occurred, the return value will be false.
		if ( ! $preparedQuery ){
			return("Fatal error: this operation is currently unavailable. Please try again later.(Code: 1)");		
			die();
		}
		
		//now the parameters - $arguments - must be linked to the placeholders or tags	
		if ( ! mysqli_stmt_bind_param($preparedQuery, implode($type), ...$arguments) ){
			return("Fatal error: this operation is currently unavailable. Please try again later (Code: 2).");		
			die();
		}
      
      //all went well. The query may now be executed.
	   if ( mysqli_stmt_execute($preparedQuery) ){
	   	//get result and store it
	   	$result = mysqli_stmt_get_result($preparedQuery);	
			
			// free allocated resources
         mysqli_stmt_close($preparedQuery);
			return ($result);
		}
		else{
			// free allocated resources
         mysqli_stmt_close($preparedQuery);
			return (false);
		}
	}

	//this function will terminate an active database connection upon user's request.
   function endDbConnection( $myDb ) {
		/* first let's check if the sent connection is active. Please note that if it is not active and the global option mysqli.reconnect is active,
		 * a new connection is going to be tried.
		 */
		 
		if ( mysqli_ping($myDb) ){
			// it is. Let's terminate the connection.
			if ( mysqli_close( $myDb ) ){		
				return(true);
			}
			else {
				return (false); // this means that the connection was not closed. However, in short scripts it is really not important because when the script ends it will terminate automatically.  
			}
		}
		else{
			return ("No active connection available.");		
		}  				 	
   }
?>