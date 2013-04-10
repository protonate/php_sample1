<?php 
require("/home/content/16/5633716/html/wp-includes/plugin.php");
require("/home/content/16/5633716/html/wp-includes/post.php");
 
define('DB_NAME', 'sav1003910114601');
define('DB_USER', 'sav1003910114601');
define('DB_PASSWORD', 'Sp1ce.N1ce2010');
define('DB_HOST', 'sav1003910114601.db.5633716.hostedresource.com');


$halal = $_POST["halal"];
$kosher = $_POST["kosher"];
$natural = $_POST["natural"];
$organic = $_POST["organic"];
$vegetarian = $_POST["vegetarian"];
$meaty = $_POST["meaty"];
$salty = $_POST["salty"];
$umami = $_POST["umami"];
$roasted = $_POST["roasted"];
$brothy = $_POST["brothy"];

/*
	global $wpdb;
	echo "\$wpdb: " + $wpdb;
	$similars = $wpdb->get_results("select * from ff_similar_flavors where productID = '$productID'");
	foreach($similars as $similar){
		$product = $wpdb->get_row("select * from ff_flavors where productID = '$similar->similarID'");
		$args = array(
			'hierarchical' => 0,
			'meta_key' => 'productID',
			'meta_value' => $similar->similarID
		);
		$pages = get_pages($args);
		foreach($pages as $pagg){
			$link = get_permalink($pagg->ID);
		}
		echo "<a href='$link'><strong>#$product->productID</strong> $product->productName</a><br />";
	}
*/


$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die ('error connection to database');
mysql_select_db(DB_NAME, $link) or die ("error selecting database");

$sql = "select productID, productName from ff_flavors where (vegan=$vegetarian or kosher=$kosher or halal=$halal or `natural`=$natural or organic=$organic) and meaty like '$meaty' and salty like '$salty' and umami like '$umami' and roasted like '$roasted' and brothy like '$brothy';";

//$sql = "select productID from ff_flavors";
$result = mysql_query($sql, $link);
if(mysql_num_rows($result) > 0 ) {
	while($row = mysql_fetch_object($result)) {
		$args = array(
			'hierarchical' => 0,
			'meta_key' => 'productID',
			'meta_value' => $row->productID
			);
		$pages = get_pages($args);
		foreach($pages as $pagg){
			$link = get_permalink($pagg->ID);
		}
		echo "<a href='$link'><strong>#$row->productID</strong> $row->productName</a><br />";	
		//echo "<p>" . $row->productID . "</p>";
	}
}
else {
	echo "<p>We're sorry.  We can't seem to find an exact match for your request.  Please adjust your flavor settings and try again.</p>";
}

?>

<p><?php  echo($sql); ?></p>

