<?php
	include("include/engine.php");

	$db = db_connect();

	if (isset($_GET['query'])) {
		//print $_GET['query'] . "\n";

		$raw = mysql_query($_GET['query'], $db);
		$results = [];
		while ($result = mysql_fetch_array($raw)) {
			$results[] = $result; 
		}
		print json_encode($results);
	}

	if(function_exists($_GET['f'])) {

		$raw = $_GET['f']($db);
	
   		$results = [];
		while ($result = mysql_fetch_array($raw)) {
			$results[] = $result; 
		}
		print json_encode($results);

	}

	function downloadSections($db) {
		return mysql_query("SELECT name FROM section", $db);
	}

	function downloadSubsections($db) {

		if (!isset($_GET['sectionName'])){
			die("wrong argument list");
		}

		return mysql_query("SELECT `subsection`.`subsection_id`, `subsection`.`name` FROM".
			" `subsection` JOIN `section` ON `section`.`section_id` = `subsection`.`section_id` ".
			"WHERE `section`.`name` = '" . $_GET['sectionName'] . "'", $db);
	}

	function downloadSeries ($db) {
		return mysql_query("SELECT `seria`.`seria_id`, `seria`.`name` FROM `seria` ", $db);
	}

	function downloadItemsInSubsection ($db) {
		if (!isset($_GET['subsectionIdent'])){
			die("wrong argument list");
		}
		return mysql_query("SELECT `item`.`item_id`, `item`.`name`, `item`.`cost`, `item`.`count`, `item`.`about`,".
			" `item`.`picture`, `producer`.`name` AS 'producer', `seria`.`name` AS 'seria' ".
			"FROM `item`, `producer`,`seria` WHERE `producer`.`producer_id` = `item`.`producer_id`".
			" AND `seria`.`seria_id` = `item`.`seria_id` AND `item`.`subsection_id` = ".$_GET['subsectionIdent']."
			", $db);
	}

	function downloadItemsInSeria ($db) {
		if (!isset($_GET['seriaIdent'])){
			die("wrong argument list");
		}
		return mysql_query("SELECT DISTINCT `item`.`item_id`, `item`.`name`, `item`.`cost`, `item`.`count`,".
			" `item`.`about`, `item`.`picture`, `producer`.`name` AS 'producer', `subsection`.`name` AS 'subsection' ".
			"FROM `item`, `producer`,`seria`, `subsection` WHERE `producer`.`producer_id` = `item`.`producer_id` ".
			"AND `subsection`.`subsection_id` = `item`.`subsection_id` AND `item`.`seria_id` = ".$_GET['seriaIdent']."", $db);
	}
	
	function downloadMaterialsForItemIdent ($db) {
		if (!isset($_GET['itemIdent'])){
			die("wrong argument list");
		}
		return mysql_query("SELECT DISTINCT `material`.`name` AS 'material', `color`.`name` AS 'color' ".
			"FROM `material`, `color`, `material_color`, `material_color_item` ".
			"WHERE `material`.`material_id` = `material_color`.`material_id` AND ".
			"`color`.`color_id` = `material_color`.`color_id` AND ".
			"`material_color`.`material_color_id` = `material_color_item`.`material_color_id` AND".
			" `material_color_item`.`item_id` = ".$_GET['itemIdent']."", $db);
	}

	function downloadMeasuresForItemIdent ($db) {
		if (!isset($_GET['itemIdent'])){
			die("wrong argument list");
		}
		return mysql_query("SELECT DISTINCT `measure`.`name` AS 'measure', `unit`.`name` AS".
			" 'unit', `unit_measure_item`.`count` FROM `measure`, `unit`, `unit_measure_item` ".
			"WHERE `measure`.`measure_id` = `unit_measure_item`.`measure_id` ".
			"AND `unit`.`unit_id` = `unit_measure_item`.`unit_id` AND `unit_measure_item`.`item_id` = ".$_GET['itemIdent']."", $db);
	}
	
	function downloadItemByID ($db) {
		if (!isset($_GET['itemIdent'])){
			die("wrong argument list");
		}
		
		return mysql_query("SELECT `item`.`item_id`, `item`.`name`, `item`.`cost`, `item`.`count`,".
			" `item`.`about`, `item`.`picture`, `producer`.`name` AS 'producer', `seria`.`name` ".
			"AS 'seria', `item`.`subsection_id` AS 'subsection_id' FROM `item`, `producer`,`seria` ".
			"WHERE `producer`.`producer_id` = `item`.`producer_id` AND `seria`.`seria_id` = `item`.`seria_id` ".
			"AND `item`.`item_id` = ".$_GET['itemIdent']."", $db);
	}

	function loadUser ($db) {
		if (!isset($_GET['email']) || !isset($_GET['password']) || !isset($_GET['firstName']) ||
			!isset($_GET['lastName']) || !isset($_GET['address']) || !isset($_GET['tel'])){
			die("wrong argument list");
		}
		mysql_query("INSERT INTO `user`(`mail`, `password`, `status`, `user_type_id`) ".
			"VALUES ('".$_GET['email']."','".$_GET['password']."','active',(SELECT `user_type_id` FROM `user_type` ".
				"WHERE `user_type`.`name` = 'user'))", $db);

		mysql_query("INSERT INTO `user_info`(`user_id`, `first_name`, `last_name`, `address`, `tel`) ".
			"VALUES ((SELECT `user_id` FROM `user` WHERE `user`.`mail` = '".$_GET['email']."'),".
				"'".$_GET['firstName']."','".$_GET['lastName']."','".$_GET['address']."','".$_GET['tel']."')", $db);
	}

	function isUserLoginWithMail ($db) {
		if (!isset($_GET['mail']) || !isset($_GET['password'])){
			die("wrong argument list");
		}
		
		return mysql_query("SELECT `user`.`user_id`, `user`.`mail`, `user`.`password`, `user_info`.`first_name`, ".
			"`user_info`.`last_name`, `user_info`.`address`, `user_info`.`tel` FROM `user`, `user_info` ".
			"WHERE `user`.`user_id` = `user_info`.`user_id` AND ".
			"`user`.`mail` = '".$_GET['mail']."' AND `user`.`password` = '".$_GET['password']."'", $db);
	}

	function downloadUserIDByMail ($db) {
		if (!isset($_GET['mail'])){
			die("wrong argument list");
		}
		return mysql_query("SELECT `user_id` FROM `user` WHERE `user`.`mail` = '".$_GET['mail']."'", $db);
	}

	function downloadOrdersForUserIdent ($db) {
		if (!isset($_GET['ident'])){
			die("wrong argument list");
		}
		
		return mysql_query("SELECT `order_id`, `status`, `created`, `comments` FROM `order` ".
			"WHERE `order`.`user_id` = ".$_GET['ident']."", $db);
	}

	function downloadFavouritesForUserIdent ($db) {
		if (!isset($_GET['ident'])){
			die("wrong argument list");
		}
		
		return mysql_query("SELECT `favorite`.`item_id` FROM `favorite` ".
			"WHERE `favorite`.`user_id` = ".$_GET['ident']." ", $db);
	}

	function loadFavourite ($db) {
		if (!isset($_GET['userID']) || !isset($_GET['itemID'])){
			die("wrong argument list");
		}
		
		return mysql_query("INSERT INTO `favorite`(`user_id`, `item_id`) ".
			"VALUES (".$_GET['userID'].",".$_GET['itemID'].")", $db);
	}

	function deleteFavourite ($db) {
		if (!isset($_GET['userID']) || !isset($_GET['itemID'])){
			die("wrong argument list");
		}
		
		return mysql_query("DELETE FROM `favorite` WHERE `favorite`.`user_id` = ".$_GET['userID']." ".
			"AND `favorite`.`item_id` = ".$_GET['itemID']."", $db);
	}

	function searchItemsByName ($db) {
		if (!isset($_GET['name'])){
			die("wrong argument list");
		}
		
		return mysql_query("SELECT `item`.`item_id`, `item`.`name`, `item`.`cost`, `item`.`count`, ".
			"`item`.`about`, `item`.`picture`, `producer`.`name` AS 'producer', `seria`.`name` ".
			"AS 'seria', `subsection`.`name` AS 'subsection' FROM `item`, `producer`,`seria`, `subsection` ".
			"WHERE `producer`.`producer_id` = `item`.`producer_id` AND `seria`.`seria_id` = `item`.`seria_id` ".
			"AND `subsection`.`subsection_id` = `item`.`subsection_id` AND LOWER(`item`.`name`) REGEXP LOWER('^.*".$_GET['name'].".*$') ", $db);
	}

	function searchItemsBySeria ($db) {
		if (!isset($_GET['name'])){
			die("wrong argument list");
		}
		
		return mysql_query("SELECT `item`.`item_id`, `item`.`name`, `item`.`cost`, `item`.`count`,".
			" `item`.`about`, `item`.`picture`, `producer`.`name` AS 'producer', `seria`.`name` ".
			"AS 'seria', `subsection`.`name` AS 'subsection' FROM `item`, `producer`,`seria`,".
			" `subsection` WHERE `producer`.`producer_id` = `item`.`producer_id` ".
			"AND `seria`.`seria_id` = `item`.`seria_id` AND `subsection`.`subsection_id` = `item`.`subsection_id` ".
			"AND LOWER(`seria`.`name`) REGEXP LOWER('^.*".$_GET['name'].".*$') ", $db);
	}
	db_close($db);

?>