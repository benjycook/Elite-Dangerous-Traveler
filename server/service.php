<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
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
		
		echo json_encode($object);
	}
	$dbLink = mysql_connect("localhost", "root", "");
	if($_GET["command"]=="get_commodities_by_categories") {
			$query = "select * from commodities_category";
			$result = dao::query($query);
			$categories = array();

			while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$categories[] = $row;
			}
			
			foreach($categories as $i=>$category) {
				$categories[$i]["commodities"] = array();
				$query = "select id,name from commodities where category_id='".mysql_real_escape_string($category['id'])."'";
				
				$result = dao::query($query);
				while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$categories[$i]["commodities"][] = $row;
				}
			}
			
			returnResponse($categories);
	};
	
	if($_GET["command"]=="get_systems") {
			$query = "select id,name from systems";
			$result = dao::query($query);
			$categories = array();

			while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$categories[] = $row;
			}
			
			
			returnResponse($categories);
	};
	
	
	if($_GET["command"]=="price_query") {
		
		$buySell = $_GET["buy_sell"]; // see_prices, buy, sell
		$commodityName = $_GET["commodity"];
		$systemName = $_GET["system"];
		
	//	select c.name, sl.supply, sl.buy_price, sl.sell_price, sl.demand, unix_timestamp()-sl.collected_at as age,st.name as station_name,st.system_id,st.max_landing_pad_size,st.has_blackmarket,sy.name as system_name,(sqrt(pow(100-sy.x,2)+pow(100-sy.y,2)+pow(100-sy.z,2))) as distance from stations_listings as sl, stations as st, systems as sy, commodities as c where sl.commodity_id=c.id and st.id = sl.station_id and sy.id = st.system_id and c.name = "Hydrogen Fuel" order by age asc, sell_price desc, distance asc limit 100 offset 0
		$query = "select c.name, sl.supply, sl.buy_price, sl.sell_price, sl.demand, unix_timestamp()-sl.collected_at as age, st.name as station_name, sy.name as system_name from commodities as c, stations_listings as sl, stations as st, systems as sy where c.name='".$commodityName."' and sl.commodity_id = c.id and st.system_id = sy.id and sy.name='".$systemName."' and st.id = sl.station_id limit 100;";
		$result = dao::query($query);
		$results = array();

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$results[] = $row;
		}
		
		$response = array();
		// age, station_name, price, supply,demand  
		// if selling, sell_price, if buying, buy_price
		foreach($results as $listing) {
			$listForDisplay = array();
			$listForDisplay["Age"] = gmdate("H", $listing["age"])." Hours ago";
			$listForDisplay["Station Name"]=$listing["station_name"];
			if($buySell=="sell") {
				$listForDisplay["Price"] = $listing["sell_price"];
			} else {
				$listForDisplay["Price"] = $listing["buy_price"];
			}
			if($listForDisplay["Price"]==0) continue;
			$listForDisplay["Supply"] = $listing["supply"];
			$listForDisplay["Demand"] = $listing["demand"];
			$response[] = $listForDisplay;
		}
		
		returnResponse($response);
		
	}
	
?>