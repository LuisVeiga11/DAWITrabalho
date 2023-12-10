<?php
	//this is the App configuration file, where all relevant parameters are defined.
	
	//validation parameters
	$minUsername = 4;
	$maxUsername = 32;
	$minPassword = 6;
	$maxPassword = 48;
	$minname = 5;
	$maxname = 128;
	$mindesc = 5;
	$maxdesc = 128;
	$minprice = 1;
	$maxprice = 10;
	$minimage = 1;
	$maximage = 1000;
	
	//database parameters
	$dbHost = "localhost";
	$dbUsername = "alisha";
	$dbPassword = "cm";
	$dbName = "aulas";
	
	//pages that can be viewed without authentication - add more if needed.
	$pages = array('index',
						'registerForm',
						'loginForm',
						'search',
	              );
?>