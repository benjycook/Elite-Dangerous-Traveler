<?php
	class dao {

/*
   while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      echo "Username: " . $row["username";
   }
   */
		
            public static function query($query) {
		   $dbLink = mysql_connect("localhost", "root", "");
                mysql_select_db("eddb");
	        $result = mysql_query($query);
	        if (!$result) {
	            //print_r(new Exception("mysql error " . mysql_errno() . ": " . mysql_error() . "\nOn query: " . $query));
	        }
	        return $result;
	    }
	}

	
	
	function addPossibleCategory($category) {
        $query = 'insert into commodities_category set ' .
                 'id = "'.mysql_real_escape_string($category["id"]).'", ' .
                'name = "'.mysql_real_escape_string($category["name"]).'"';
        $res = dao::query($query);
	}
	
	function addCommodity($commodity) {
	 $query = 'insert into commodities set ' .
                 'id = "'.mysql_real_escape_string($commodity["id"]).'", ' .
                'name = "'.mysql_real_escape_string($commodity["name"]).'", ' .
                'category_id = "'.mysql_real_escape_string($commodity["category_id"]).'", ' .
                'average_price = "'.mysql_real_escape_string($commodity["average_price"]).'"';
        $res = dao::query($query);
	}
	
	
	function addSystem($system) {
	 $query = 'insert into systems set ' .
                 'id = "'.mysql_real_escape_string($system["id"]).'", ' .
                'name = "'.mysql_real_escape_string($system["name"]).'", ' .
                'x = "'.mysql_real_escape_string($system["x"]).'", ' .
                'y = "'.mysql_real_escape_string($system["y"]).'", ' .
                'z = "'.mysql_real_escape_string($system["z"]).'"';
        $res = dao::query($query);
	}
        
        function addStation($station) {
            $query = 'insert into stations set ' .
                   'id = "'.mysql_real_escape_string($station["id"]).'", ' .
                   'name = "'.mysql_real_escape_string($station["name"]).'", ' .
                   'system_id = "'.mysql_real_escape_string($station["system_id"]).'", ' .
                   'has_blackmarket = "'.mysql_real_escape_string($station["has_blackmarket"]).'", ' .
                   'max_landing_pad_size = "'.mysql_real_escape_string($station["max_landing_pad_size"]).'", ' .
                   'distance_to_star = "'.mysql_real_escape_string($station["distance_to_star"]).'"';
            $res = dao::query($query);
	}
        
        function addListing($listing) {
            $query = 'insert into stations_listings set ' .
                   'id = "'.mysql_real_escape_string($listing["id"]).'", ' .
                   'station_id = "'.mysql_real_escape_string($listing["name"]).'", ' .
                   'commodity_id = "'.mysql_real_escape_string($listing["system_id"]).'", ' .
                   'supply = "'.mysql_real_escape_string($listing["has_blackmarket"]).'", ' .
                   'buy_price = "'.mysql_real_escape_string($listing["max_landing_pad_size"]).'", ' .
                   'sell_price = "'.mysql_real_escape_string($listing["max_landing_pad_size"]).'", ' .   
                   'collected_at = "'.mysql_real_escape_string($listing["distance_to_star"]).'"';
            $res = dao::query($query);
	}
	
	function importCommodities() {
		$commodities = json_decode(file_get_contents("commodities.json"),true);
		$commodityCategories = array();
		foreach($commodities as $commodity) {
			addPossibleCategory($commodity["category"]);
			addCommodity($commodity);
		}
	}
	
	//print_r($commoditiesRaw);
	
	
	
	function importSystems() {
		$systems = json_decode(file_get_contents("systems.json"),true);
		foreach($systems as $system) {
			addSystem($system);
		}
		//print_r($systems);
	}
	
	
	
	
	function importStations() {
		$stationsStr = file_get_contents("stations.json");
	//	echo $stationsStr; exit();
		$stations = json_decode($stationsStr,true);
                foreach($stations as $station) {
                    addStation($station);
                    foreach($stations["listings"] as $listing) {
                        addListing($listing);
                    }
                }
		//print_r($stations[0]);
	}
	
	ini_set('memory_limit', '2048M');
	
        importCommodities();
        //importSystems();
	//importStations();
?>
