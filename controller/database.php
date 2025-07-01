<?php
	session_start();

	$username="root";
	$password="cmsserverv6";
	$database="1010gps";
	$port = '3311';

	// Opens a connection to a MySQL server
	$connection=mysqli_connect('localhost:3311', $username, $password);
	if (!$connection)
	{
	  die('Not connected : ' . mysqli_error());
	}

	// Set the active MySQL database
	$db_selected = mysqli_select_db($connection,$database);
	if (!$db_selected)
	{
	  die ('Can\'t use db : ' . mysqli_error());
	}
	
	function dbmileage(){
		$username="root";
		$password="cmsserverv6";
		$database="1010gps";
		$port = '3311';

		// Opens a connection to a MySQL server
		$connection=mysqli_connect('localhost:3311', $username, $password,$database);
		return $connection;
	}

	function dbconnection(){
		$username="root";
		$password="cmsserverv6";
		$database="fms";
		$port = '3311';

	// Opens a connection to a MySQL server
	$connection=mysqli_connect('localhost:3311', $username, $password, $database);
	return $connection;
	}

	


?>