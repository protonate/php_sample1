<?php
/*
Plugin Name: Flavor Finder Admin 
Plugin Name: Flavor Finder Ajax
Plugin URI: #
Description: administrator interface for savoury systems flavor finder
Version: 0.1
Author: Nathan Guisinger/Chris Jordan
Author URI: http://www.seej.net
License: propriety code wholly owned by Savoury Systems International
*/


// add_filter('plugin_row_meta', 'mypageorder_set_plugin_meta', 10, 2 );

// ff_update_results ajax function goes first so add_action can find it
function ff_update_results(){		
	$halal = $_POST["halal"];
	$kosher = $_POST["kosher"];
	$natural = $_POST["natural"];
	$organic = $_POST["organic"];
	$vegetarian = $_POST["vegetarian"];
	$meaty = ($_POST["meaty"] == "%") ? " between 0 and 6" : " between " . ($_POST["meaty"] - 1) . " and " . ($_POST["meaty"] + 1);
	$salty = ($_POST["salty"] == "%") ? " between 0 and 6" : " between " . ($_POST["salty"] - 1) . " and " . ($_POST["salty"] + 1);
	$umami = ($_POST["umami"] == "%") ? " between 0 and 6" : " between " . ($_POST["umami"] - 1) . " and " . ($_POST["umami"] + 1);
	$roasted = ($_POST["roasted"] == "%") ? " between 0 and 6" : " between " . ($_POST["roasted"] - 1) . " and " . ($_POST["roasted"] + 1);
	$brothy = ($_POST["brothy"] == "%") ? " between 0 and 6" : " between " . ($_POST["brothy"] - 1) . " and " . ($_POST["brothy"] + 1);

	global $wpdb;
	$sql = "select productID, productName from ff_flavors where vegan like '$vegetarian' and kosher like '$kosher' and halal like '$halal' and `natural` like '$natural' and organic like '$organic' and meaty $meaty and salty $salty and umami $umami and roasted $roasted and brothy $brothy";

	$flavors = $wpdb->get_results($sql);
	if(count($flavors) > 0){
		foreach($flavors as $flavor){
			$args = array(
				'hierarchical' => 0,
				'meta_key' => 'productID',
				'meta_value' => $flavor->productID
			);
			$pages = get_pages($args);
			foreach($pages as $pagg){
				$link = get_permalink($pagg->ID);
			}
			echo "<p><a href='$link'><strong>#$flavor->productID</strong> $flavor->productName</a></p>";
		}
	}
	else {
		echo "<p>We're sorry.  We can't seem to find an exact match for your request.  Please adjust your flavor settings and try again.</p>";
	}
	exit();
} // ff_update_results
	
add_action('init', 'flavorfinder_init');
add_action('get_header', 'flavorfinder_script');
add_action('admin_menu', 'flavorfinder_menu');
add_action('get_footer', 'flavorfinder_ajax');
add_action('wp_ajax_nopriv_ff_update_results', 'ff_update_results');
add_action('wp_ajax_ff_update_results', 'ff_update_results');

function flavorfinder_script(){
	if(is_page('543')){		
		wp_enqueue_script('jquery-cookie', plugin_dir_url( __FILE__ ) . 'jquery.cookie.js', array('jquery'));
		wp_enqueue_script('flavorfinder', plugin_dir_url( __FILE__ ) . 'flavorfinder.js', array('jquery','jquery-ui-core','jquery-ui-draggable','jquery-cookie'));		
		wp_localize_script("jquery-cookie", "JqueryCookie", array());
		wp_localize_script( 'flavorfinder', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));		
	}
}
function flavorfinder_init(){
		wp_deregister_script("jquery");
		wp_enqueue_script("jquery", "http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
		wp_deregister_script("jquery-ui-core");
		wp_deregister_script("jquery-ui-draggable");		
		wp_enqueue_script("jquery-ui-core", "/wp-includes/js/jquery/jquery.ui.core.min.js", array('jquery'));		
		wp_enqueue_script("jquery-ui-widget", "/wp-includes/js/jquery/jquery.ui.widget.min.js", array('jquery','jquery-ui-core'));
		wp_enqueue_script("jquery-ui-mouse", "/wp-includes/js/jquery/jquery.ui.mouse.min.js", array('jquery','jquery-ui-core','jquery-ui-widget'));
		wp_enqueue_script("jquery-ui-draggable", "/wp-includes/js/jquery/jquery.ui.draggable.min.js", array('jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-mouse'));		
		wp_enqueue_script("jquery-ui-slider", "/wp-includes/js/jquery/jquery.ui.slider.min.js", array('jquery','jquery-ui-core','jquery-ui-widget','jquery-ui-mouse', 'jquery-ui-draggable'));
		wp_enqueue_script('jquery-cookie', plugin_dir_url( __FILE__ ) . 'jquery.cookie.js', array('jquery'));
		wp_enqueue_script('flavorfinder', plugin_dir_url( __FILE__ ) . 'flavorfinder.js', array('jquery','jquery-ui-core','jquery-ui-draggable','jquery-cookie'));		
		wp_localize_script("jquery-cookie", "JqueryCookie", array());
		wp_localize_script( 'flavorfinder', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));		
		
		// RUN ONCE ONLY !
		// init for data import during development phase
//		global $wpdb;
//		$dataimport = $wpdb->get_results("select * from ff_data_import");
//		// echo "num_rows: $wpdb->num_rows";
//		echo $wpdb->print_error();
//		foreach($dataimport as $row){
//			$similarIDs = explode(",", $row->similarIDs);
//			foreach($similarIDs as $similarID){
//				$needles = array("#", "?", " ");
//				$trimed = str_replace($needles, "", $similarID);
//				//$sql = "INSERT INTO ff_similar_flavors (productID, similarID) VALUES ('$row->productID', '$trimed');";
//				//echo $sql . "<br />";
//				// $wpdb->query($sql);
//				$wpdb->insert('ff_similar_flavors', array('productID' => $row->productID, 'similarID' => $trimed), array('%s','%s'));
//			}
//		}
		// RUN ONCE ONLY !
		// init for random example data generation
//		global $wpdb;
//		$args = array(
//			'hierarchical' => 0,
//			'meta_key' => 'productID'
//		);
//		$pages = get_pages($args);
//		
//		function get_float(){
//			$min = 0.0;
//			$max = 5.0;
//			$range = $max-$min; 
//			$num = round($min + $range * mt_rand(0, 32767)/32767, 1);
//			return $num;	
//		}
//		
//		function get_int(){
//			$min = 0;
//			$max = 1;
//			$num = mt_rand($min, $max);
//			return $num;
//		}
//		
//		foreach($pages as $pagg){
//			$productID = get_post_meta($pagg->post_id, 'productID', true) ;
//			$ret = $wpdb->update( 'ff_flavors', array( 'vegan' => get_int(), 'kosher' => get_int(), 'halal' => get_int(), 'natural' => get_int(), 'meaty' => get_float(), 'salty' => get_float(), 'umami' => get_float(), 'roasted' => get_float(), 'brothy' => get_float() ), array( 'productID' => $productID ), array( '%d', '%d', '%d', '%d', '%f', '%f', '%f', '%f', '%f' ), array( '%s' ) );
//		}
	}

function flavorfinder_menu()
{   if (function_exists('add_submenu_page')) {
        add_submenu_page(flavorfinder_getTarget(), 'Product Admin', __('Product Admin', 'flavorfinder'), 5,"flavorfinder",'flavorfinder');
    }
}

function flavorfinder_data_meta_box($post){
	echo "flavor product data report goes here";
}

function flavorfinder()
{
	nocache_headers();
	global $wpdb;
	$mode = "";
	$mode = $_GET['mode'];
	$parentID = 0;
	
	if (isset($_GET['parentID']))
		$parentID = $_GET['parentID'];
		
	$success = "";

?>

	<h2>Product Flavor Finder Administration</h2>
	<?php $form_script = plugins_url('process-form.php', __FILE__); ?>
	
	<form id='ff-form' name='ff-form' action='' method='post'>
	<input id="formscript" name="formscript" type="hidden" value='<?php echo $form_script; ?>' />
	<table width="100" border="0">
	    <tr>
	        <th nowrap="nowrap" bgcolor="#CCCCCC">Product ID</th>
	        <th nowrap="nowrap" bgcolor="#CCCCCC">Product Name</th>
	        <th nowrap="nowrap" bgcolor="#CCCCCC">Certifications *<br />
	            <table width="100%" border="0">
	                <tr>
	                    <td align="center">V</td>
	                    <td align="center">K</td>
	                    <td align="center">H</td>
	                    <td align="center">N</td>
	                    <td align="center">O</td>
	                </tr>
	        </table></th>
	        <th nowrap="nowrap" bgcolor="#CCCCCC">Similar Flavors <br />
	        (comma separated)</th>
	        <th nowrap="nowrap" bgcolor="#CCCCCC">Characteristics **<br />
	            <table width="100%" border="0">
	                <tr>
	                    <td align="center">M</td>
	                    <td align="center">S</td>
	                    <td align="center">U</td>
	                    <td align="center">R</td>
	                    <td align="center">B</td>
	                </tr>
	        </table></th>
	       <td>&nbsp;</td>
	    </tr>
	 <?php 
		$args = array(
			'hierarchical' => 0,
			'meta_key' => 'productID'
		);
		$pages = get_pages($args);
		foreach($pages as $pagg){
			$productID = get_post_meta($pagg->post_id, 'productID', true) ;			
			// update product names with page titles in case they have been changed
			$wpdb->update('ff_flavors', array('productName' => $pagg->post_title), array('productID' => $productID), array('%s'), array('%s'));
			// if productID not in ff_flavors table
			$ff_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->ff_flavors WHERE productID = '$productID';"));
			if ( $ff_count == 0 ) {
				// productID not found, insert new row
				$wpdb->insert( 'ff_flavors', array( 'productID' => $productID, 'productName' => $pagg->post_title ), array( '%s', '%s' ) );
			}
		}
		
		// get pages again in case of any new inserts
		$pages = get_pages($args);
		foreach($pages as $pagg){
			$productID = get_post_meta($pagg->post_id, 'productID', true) ;
			$post_title = $pagg->post_title;
			$ff_data = $wpdb->get_row("SELECT * FROM ff_flavors WHERE productID = '$productID';");
			$vegan = ($ff_data->vegan == 1) ? "checked='checked'" : "";
			$kosher = ($ff_data->kosher == 1) ? "checked='checked'" : "";
			$halal = ($ff_data->halal == 1) ? "checked='checked'" : "";
			$natural = ($ff_data->natural == 1) ? "checked='checked'" : "";
			$organic = ($ff_data->organic == 1) ? "checked='checked'" : "";
			$meaty = $ff_data->meaty;
			$salty = $ff_data->salty;
			$umami = $ff_data->umami;
			$roasted = $ff_data->roasted;
			$brothy	 = $ff_data->brothy;	

			// get similar flavors
			$ff_similars = $wpdb->get_results("SELECT similarID FROM ff_similar_flavors WHERE productID = '$productID' ORDER BY similarID;");
			$similars = "";
			foreach($ff_similars as $ff_similar){
				$similars .= $ff_similar->similarID . ", ";
			}
			$similars = rtrim($similars, " ,");
		?> 
		<tr>
	        <td nowrap="nowrap">#
	           <input name="productID" type="text" id="productID" value="<?php echo $productID; ?>" size="6" maxlength="4" disabled="disabled" /></td>
	        <td><input name='productName' type='text' id='productName' value='<?php echo $post_title; ?>' size='40' disabled="disabled" /></td>
	        <td><table width="100%" border="0">
	                <tr>
	                    <td align="center"><span class="vegan"><input name="vegan" type="checkbox" id="vegan" <?php echo $vegan; ?> disabled="disabled" /></span></td>
	                    <td align="center"><input name="kosher" type="checkbox" id="kosher" <?php echo $kosher; ?> disabled="disabled" /></td>
	                    <td align="center"><input type="checkbox" name="halal" id="halal" <?php echo $halal; ?> disabled="disabled" /></td>
	                    <td align="center"><input type="checkbox" name="natural" id="natural" <?php echo $natural; ?> disabled="disabled" /></td>
	                    <td align="center"><input type="checkbox" name="organic" id="organic" <?php echo $organic; ?> disabled="disabled" /></td>
	                </tr>
	        </table></td>
	        <td><input name="similarIDs" type="text" id="similarIDs" value="<?php echo $similars; ?>" size="40" disabled="disabled" /></td>
	        <td><table width="100%" border="0">
	            <tr>
	                <td align="center"><input name="meaty" type="text" id="meaty" value="<?php echo $meaty; ?>" size="3" maxlength="3" disabled="disabled" /></td>
	                <td align="center"><input name="salty" type="text" id="salty" value="<?php echo $salty; ?>" size="3" maxlength="3" disabled="disabled" /></td>
	                <td align="center"><input name="umami" type="text" id="umami" value="<?php echo $umami; ?>" size="3" maxlength="3" disabled="disabled" /></td>
	                <td align="center"><input name="roasted" type="text" id="roasted" value="<?php echo $roasted; ?>" size="3" maxlength="3" disabled="disabled" /></td>
	                <td align="center"><input name="brothy" type="text" id="brothy" value="<?php echo $brothy; ?>" size="3" maxlength="3" disabled="disabled" /></td>
	            </tr>
	        </table></td>
	        <td class="form-controls">
		        <a class="edit-anchor" href="#">edit</a>
		       <a class="save-anchor" href="#" style="display: none;">save</a>
		       <a class="cancel-anchor" href="#" style="display: none;">cancel</a> 
	       </td>
	    </tr>
		<?php 
		}
	?>
	    
	</table>
	</form>
	<p>
	   
	</p>
	<p><strong>* Certifications:</strong> Vegetarian, Kosher, Halal, Natural, Organic<br />
	<strong>** Characteristics:</strong> Meaty, Salty, Umami, Roasted, Brothy; with values of 0.0 - 5.0</p>
<?php
}

// get certifications
function flavorfinder_certifications($productID){
	global $wpdb;
	$ff_data = $wpdb->get_row("select * from ff_flavors where productID = '$productID'");
	$output = "<style type='text/css'>";
	if($ff_data->vegan != 1) { $output .= "#cert_vegetarian {display: none;}"; }
	if($ff_data->kosher != 1) { $output .= "#cert_kosher {display: none;}"; }
	if($ff_data->halal != 1) { $output .= "#cert_halal {display: none;}"; }
	if($ff_data->natural != 1) { $output .= "#cert_natural {display: none;}"; }
	if($ff_data->organic != 1) { $output .= "#cert_organic {display: none;}"; }
	$output .= "</style>";	
	echo $output;
}

function flavorfinder_similar($productID){
	global $wpdb;

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
}

function flavorfinder_graph($productID){
	global $wpdb;
	$ff_data = $wpdb->get_row("select * from ff_flavors where productID = '$productID'");
	$output = "<style type='text/css'>";
	function getHeight($val){
		$max = 5.0;
		$diff = $max - $val;
		$percent = $diff/$max;
		$maxheight = 114;
		$height = $maxheight * $percent;
		$height = ($height < 1) ? 1 : $height;
		$heightpx = $height . "px";
		return $heightpx;
	}
 	$meaty = getHeight($ff_data->meaty);
 	$salty = getHeight($ff_data->salty);
 	$umami = getHeight($ff_data->umami);
 	$roasted = getHeight($ff_data->roasted);
 	$brothy = getHeight($ff_data->brothy);
 	$output .= "#graph #c1 {height: $meaty;}";
	$output .= "#graph #c2 {height: $salty;}";
	$output .= "#graph #c3 {height: $umami;}";
	$output .= "#graph #c4 {height: $roasted;}";
	$output .= "#graph #c5 {height: $brothy;}";
	$output .= "</style>";
	echo $output;
}
//Switch page target depending on version
function flavorfinder_getTarget() {
	return "edit-pages.php";
}

function flavorfinder_ajax() {	

	global $post;
	if(is_page('543')) {
		// echo "flavor finder ajax goes here";
	?>
        <div id='ff-container'>
        <form>
        <!-- start certifications -->
            <?php $ui_css = plugins_url('ui-css/ui-lightness/jquery-ui-1.8.4.custom.css', __FILE__); ?>
            <link type="text/css" href="<?php echo $ui_css; ?>" rel="stylesheet">
            <?php $ff_css = plugins_url('flavor-finder.css', __FILE__); ?>
            <link type="text/css" href="<?php echo $ff_css; ?>" rel="stylesheet">
        
            <div id='ff-certifications'>
                <div class='ff-cert-title'>Certifications</div>
                <div class='ff-cert'>
                    <div id='cert_halal' class='ff-cert-checkbox' ff-filter='%'></div>
                    <div class='ff-cert-label'>Halal</div>
                </div>           
                <div class='ff-cert'>
                    <div id='cert_kosher' class='ff-cert-checkbox' ff-filter='%'></div>
                    <div class='ff-cert-label'>Kosher</div>
                </div>           
                <div class='ff-cert'>
                    <div id='cert_natural' class='ff-cert-checkbox' ff-filter='%'></div>
                    <div class='ff-cert-label'>Natural</div>
                </div>           
                <div class='ff-cert'>
                    <div id='cert_organic' class='ff-cert-checkbox' ff-filter='%'></div>
                    <div class='ff-cert-label'>Organic</div>
                </div>           
                <div class='ff-cert'>
                    <div id='cert_vegetarian' class='ff-cert-checkbox' ff-filter='%'></div>
                    <div class='ff-cert-label'>Vegetarian</div>
                </div>            
            </div>
            <!-- end certifications -->
            
             <!-- start criteria -->
            <div class='ff-criteria'>
                <div id="ff-slider-container" class='ff-sliders'>
                    <span class="slider" id="criteria1">0</span>
                    <span class="slider" id="criteria2">0</span>
                    <span class="slider" id="criteria3">0</span>
                    <span class="slider" id="criteria4">0</span>
                    <span class="slider" id="criteria5">0</span>
                </div>
            </div>
            <!-- end criteria -->
            
                <!-- start results -->
            <div class='ff-results'>
                <div class='ff-title-result'>Flavor Results</div>
                <div id="ff-results-panel" class='ff-result-list'>
                    <p>finding flavors, one moment</p>
                </div>
                <div id='results-status'>fetching results...</div>
            </div>
            <!-- end results -->
        </form>
        </div>    

    <? } // flavorfinder_ajax
}
function flavorfinder_backlink() {
	if($_COOKIE["flavorfinder"] != null) {
		echo "<br /><a href='/?page_id=543'><< Go Back to the Flavor Finder</a>"; 
	}
}
?>