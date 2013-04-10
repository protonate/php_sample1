<?php 
define('DB_NAME', 'sav1003910114601');
define('DB_USER', 'sav1003910114601');
define('DB_PASSWORD', 'Sp1ce.N1ce2010');
define('DB_HOST', 'sav1003910114601.db.5633716.hostedresource.com');

$productID = $_POST["productID"];
$vegan = ($_POST["vegan"] == "true") ? 1 : 0;
$kosher = ($_POST["kosher"] == "true") ? 1 : 0;
$halal = ($_POST["halal"] == "true") ? 1 : 0;
$natural = ($_POST["natural"] == "true") ? 1 : 0;
$organic = ($_POST["organic"] == "true") ? 1 : 0;
$similarIDs = $_POST["similarIDs"];
$meaty = $_POST["meaty"];
$salty = $_POST["salty"];
$umami = $_POST["umami"];
$roasted = $_POST["roasted"];
$brothy = $_POST["brothy"]; 

$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die ('error connection to database');
mysql_select_db(DB_NAME, $link) or die ("error selecting database");
$sql = "update ff_flavors set vegan=$vegan, kosher=$kosher, halal=$halal, `natural`=$natural, organic=$organic, meaty=$meaty, salty=$salty, umami=$umami, roasted=$roasted, brothy=$brothy where productID = '$productID';";
mysql_query($sql, $link);
$result = mysql_affected_rows($link);
$ret = "none";
if($result == 1 || $result == 0){
	$ret = "ok";	
}
else{
	$ret = "error on ff_flavors: " . mysql_error($link);
}

$discards = false;

if($ret == "ok"){
	$sql = "delete from ff_similar_flavors where productID='$productID'";
	mysql_query($sql,$link);
	$similars = explode(",", $similarIDs);
	foreach($similars as $similar){
		$similarID = trim($similar);
		$sql = "select ID from ff_flavors where productID = '$similarID'";
		$result = mysql_query($sql, $link);		
		if(mysql_num_rows($result) > 0){
			$sql = "insert into ff_similar_flavors (productID, similarID) values ('$productID','$similarID');";
			mysql_query($sql,$link);
			$result = mysql_affected_rows($link);
//			$ret .= $similarID . ": " . $result . "\r\n";
//			$ret .= $sql;
			if($result == 1 || $result == 0){
				// REMOVE CONCAT
				$ret = "ok";	
			}
			else{
				// REMOVE CONCAT
				$ret = "error on ff_similar_flavors: " . mysql_error($link);
			}
		}
		else {$discards = true;}
	}	
}
if($discards && $ret == "ok"){
	$ret = "ok discards";
}

echo $ret;
?>