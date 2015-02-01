<?php

error_reporting(E_ALL);
ini_set('display_errors','On');
	class dao {

/*
   while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      echo "Username: " . $row["username";
   }
   */
		
    public static function query($query) {
		   
                mysql_select_db("eddb");
	        $result = mysql_query($query);
	        if (!$result) {
	            //print_r(new Exception("mysql error " . mysql_errno() . ": " . mysql_error() . "\nOn query: " . $query));
	        }
	       // print_r($result."  ".$query."\n");
	        return $result;
	    }
	}
	
	function returnResponse($object) {
		header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Credentials: true');

header('Content-Type: application/json');

		echo json_encode($object);
	}
	$dbLink = mysql_connect("localhost", "root", "");
	
	$systemName = $_GET['name'];
	
	$query = "select * from systems where name = '".mysql_real_escape_string($systemName)."'";
	$result = dao::query($query);
	$system = mysql_fetch_array($result, MYSQL_ASSOC);
	
	$stations = array();
	$query = "select * from stations where system_id = '".mysql_real_escape_string($system['id'])."'";
	$result = dao::query($query);

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$stations[] = $row;
	}
	
	$system["stations"] = $stations;

	$neighboringSystems = array();
	$query = "select * from systems where id!='".mysql_real_escape_string($system['id'])."' order by (pow(x-".$system['x'].",2)+pow(y-".$system['y'].",2)+pow(".$system['z'].",2)) asc limit 3";
	$result = dao::query($query);

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$neighboringSystems[] = $row;
	}
	
	
	
	$system["neighboringSystems"] = $neighboringSystems;
	
	returnResponse($system);
?>