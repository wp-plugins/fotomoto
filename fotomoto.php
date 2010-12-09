<?php
/*
Plugin Name: Fotomoto
Plugin URI: http://www.fotomoto.com
Description: Fotomoto Plugin
Version: 1.1.0
Author: Fotomoto
Author URI: http://www.fotomoto.com/
*/
define('FOTOMOTO_VERSION', '1.1.0');

if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
      
if (!defined('WP_FOTOMOTO_PLUGIN_URL'))
      define('WP_FOTOMOTO_PLUGIN_URL', WP_CONTENT_URL.'/plugins/fotomoto');      
if (!defined('WP_FOTOMOTO_PLUGIN_DIR'))
      define('WP_FOTOMOTO_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins/fotomoto');

define("FOTOMOTO_ENABLED", "enabled");
define("FOTOMOTO_DISABLED", "disabled");
define("FOTOMOTO_CATEGORY_META", "fotomoto_categorymeta");
define("FOTOMOTO_PRODUCTION_DOMAIN", "fotomoto.com");

global $wpdb;
$wpdb->{FOTOMOTO_CATEGORY_META} = $wpdb->prefix . FOTOMOTO_CATEGORY_META;

$fotomoto_options = get_option('fotomoto_options');
function activate_fotomoto() {  
	global $wpdb;
  add_option('fotomoto_options', fotomoto_default_options());    
  $table_name = $wpdb->prefix . FOTOMOTO_CATEGORY_META;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`fotomoto_category_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`)
		);";
		$wpdb->query($sql);
	}
}

function deactive_fotomoto() {
	global $wpdb;
  delete_option('fotomoto_options');  	
  $table_name = $wpdb->prefix . FOTOMOTO_CATEGORY_META;
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
}

function admin_init_fotomoto() {
  register_setting('fotomoto', 'fotomoto_options');
}

function admin_menu_fotomoto() {
  add_options_page('Fotomoto', 'Fotomoto', 8, 'fotomoto', 'options_page_fotomoto');
}

function fotomoto_update_category_meta($cat_id, $meta_key, $meta_value, $prev_value = '') {
	return update_metadata('fotomoto_category', $cat_id, $meta_key, $meta_value, $prev_value);
}

function fotomoto_get_category_meta($cat_id, $key, $single = false) {
	return get_metadata('fotomoto_category', $cat_id, $key, $single);
}

function fotomoto_get_site_key($email, $first_name, $last_name, $domain) {
	$url = "http://affiliate.".FOTOMOTO_PRODUCTION_DOMAIN."/osignup.json";
  $params = array();
  $params["api_key"] = fotomoto_get_option('api_key');
  $params["affiliate_key"] = fotomoto_get_option('affiliate_key');
  $params["email"] = $email;
  $params["first_name"] = $first_name;
  $params["last_name"] = $last_name;
  $params["domain"] = $domain;
  $params["alternate_sites"] = "";
  
  if (fotomoto_get_option("use_default_pricing") != "") {
  	$params["use_default_pricing"] = "";
  }
  
  if (fotomoto_get_option("setup_auto_pickup") != "") {
  	$params["setup_auto_pickup"] = "";
  }
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  curl_setopt($ch, CURLOPT_HEADER, 0);  // DO NOT RETURN HTTP HEADERS
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // RETURN THE CONTENTS OF THE CALL
  $data = curl_exec($ch);
  
  return json_decode($data, true);
}

function options_page_fotomoto() {  
  if ($_GET['act'] == "edit") {  	
    if (count($_POST) > 0) {
      if (trim($_POST["email"]) == "" || trim($_POST["first_name"]) == "" || trim($_POST["last_name"]) == "" || trim($_POST["domain"]) == "") {
        $message = "Email or First Name or Last Name or Domain cannot be blank.";        
      }
      else {
        $response = fotomoto_get_site_key($_POST["email"], $_POST["first_name"], $_POST["last_name"], $_POST["domain"]);
        if ($response["status"] != 200) {
          $message = $response["error"];
        }
        else {
          update_user_meta($_POST["user_id"], "fotomoto_site_key", $response["site_key"]);
          $message = "User is active now.";
        }
      }
    }
    include(WP_FOTOMOTO_PLUGIN_DIR.'/site_key.php');
    return;
  }
  
  if ($_GET["act"] == "delete") {
  	if (get_user_meta($_GET["user_id"], "fotomoto_site_key", true) != "") update_user_meta($_GET["user_id"], "old_fotomoto_site_key", get_user_meta($_GET["user_id"], "fotomoto_site_key", true)); // backup old key
    delete_user_meta($_GET["user_id"], "fotomoto_site_key"); // remove key
    $message = "User is inactive now.";
    include(WP_FOTOMOTO_PLUGIN_DIR.'/options.php');
    return;
  }
  
  if ($_GET["act"] == "reactivate") {
  	$old_site_key = get_user_meta($_GET["user_id"], "old_fotomoto_site_key", true);
  	if ($old_site_key != "") { // having old site key
  		update_user_meta($_GET["user_id"], "fotomoto_site_key", $old_site_key);
  		$message = "User is active now.";
  		include(WP_FOTOMOTO_PLUGIN_DIR.'/options.php');
  		return;
  	}
  }
  
  if ($_POST["act"] == "update_page_status") {
  	fotomoto_update_page_status($_POST["page_id"], $_POST["status"]);
  	return;
  }
  
  if ($_POST["act"] == "update_category_status") {
  	fotomoto_update_category_meta($_POST["category_id"], "fotomoto_enable_status", $_POST["status"]);
  	return;
  }
 	
 	if ($_POST["act"] == "update_pages_status") {
 		foreach ($_POST as $key=>$value) {
 			if (!preg_match("/^page_status_/", $key)) continue;
 			$page_id = str_replace("page_status_", "", $key);
 			fotomoto_update_page_status($page_id, $value);
 		}
 		$message = "Pages status are updated."; 		
 	};
 	
 	if ($_POST["act"] == "update_categories_status") {
 		foreach ($_POST as $key=>$value) {
 			if (!preg_match("/^category_status_/", $key)) continue;
 			$category_id = str_replace("category_status_", "", $key);
 			fotomoto_update_category_meta($category_id, "fotomoto_enable_status", $value);
 		}
 		$message = "Categories status are updated."; 		
 	};
 	
 	if ($_POST["act"] == "activate_all") {
 		$wp_user_search = new WP_User_Search();
 		foreach ($wp_user_search->get_results() as $user_id) {
 			if (get_user_meta($user_id, "fotomoto_site_key", true) != "") continue; // already activated
 			$old_site_key = get_user_meta($user_id, "old_fotomoto_site_key", true);
			if ($old_site_key != "") { // having old site key
				update_user_meta($user_id, "fotomoto_site_key", $old_site_key);				
			}
			else {
				fotomoto_save_site_key($user_id);
			}
    }
    $message = "All users are activated.";
 	};
  
  if (count($_POST) > 0 && $_POST["act"] == null) {
  	$validated = true;
  	$message = "";
  	if (trim($_POST["store_key"]) != "" && strlen(trim($_POST["store_key"])) != 40) {
			$message .= "Site Key must be 40-character long.<br/>";
			$validated = false;
		}
		
		if (trim($_POST["affiliate_key"]) != "" && strlen(trim($_POST["affiliate_key"])) != 16) {
			$message .= "Affiliate Key must be 16-character long.<br/>";
			$validated = false;
		}
		
		if (trim($_POST["api_key"]) != "" && strlen(trim($_POST["api_key"])) != 40) {
			$message .= "API Key must be 40-character long.<br/>";
			$validated = false;
		}
				
		if (trim($_POST["affiliate_key"]) == "" || trim($_POST["api_key"]) == "") {
			$_POST["enable_multiuser"] = "";
		}

		if ($validated) {
  		fotomoto_save_options();
  		$message = "Settings updated.";
  	}
  }
    
  include(WP_FOTOMOTO_PLUGIN_DIR.'/options.php');
}

function fotomoto_update_page_status($page_id, $value) {
	if ($page_id == "home") {
		fotomoto_set_option("home_enable", trim($value));
	}
	else {
		update_post_meta($page_id, "fotomoto_enable_status", $value);
	}
}
function options_page_fotomoto_site_key() {
  if (count($_POST) > 0) {
  	fotomoto_save_options();
  	$message = "Settings updated.";
  }
  
  include(WP_FOTOMOTO_PLUGIN_DIR.'/site_key.php');
}

function fotomoto_page_enabled($page_id) {
	if ($page_id == "home") return (fotomoto_get_option("home_enable") != FOTOMOTO_DISABLED);
	return (get_post_meta($page_id, "fotomoto_enable_status", true) != FOTOMOTO_DISABLED);
}

function fotomoto_category_enabled($category_id) {
	return (fotomoto_get_category_meta($category_id, "fotomoto_enable_status", true) != FOTOMOTO_DISABLED);
}

function fotomoto_default_options() {
	$default_options = array();
	$default_options["store_key"] = "";
	$default_options["enable_multiuser"] = "";
	$default_options["affiliate_key"] = "";	
  $default_options["api_key"] = "";
  $default_options["home_enabled"] = "";
  $default_options["use_default_pricing"] = "";
  $default_options["setup_auto_pickup"] = "";
  $default_options["activate_new_user"] = "";
  $default_options["exclusive_list"] = array();
	return $default_options;
}

function fotomoto_set_option($option_name, $option_value) {
	$fotomoto_options = get_option('fotomoto_options');
	if (!$fotomoto_options || !array_key_exists($option_name, $fotomoto_options)) {
    	$fotomoto_options = fotomoto_default_options();
    }
	$fotomoto_options[$option_name] = $option_value;
	update_option('fotomoto_options', $fotomoto_options);
}

function fotomoto_get_option($option_name) {
	$fotomoto_options = get_option('fotomoto_options');
	if (!$fotomoto_options || !array_key_exists($option_name, $fotomoto_options)) {
    	$fotomoto_options = fotomoto_default_options();
    }
	return $fotomoto_options[$option_name];
}

function fotomoto_save_options() {
  fotomoto_set_option("store_key", trim($_POST["store_key"]));
  fotomoto_set_option("enable_multiuser", trim($_POST["enable_multiuser"]));
	fotomoto_set_option("affiliate_key", trim($_POST["affiliate_key"]));
	fotomoto_set_option("api_key", trim($_POST["api_key"]));
	fotomoto_set_option("use_default_pricing", trim($_POST["use_default_pricing"]));
	fotomoto_set_option("setup_auto_pickup", trim($_POST["setup_auto_pickup"]));
	fotomoto_set_option("activate_new_user", trim($_POST["activate_new_user"]));
	$list = explode(",", trim($_POST["exclude_url"]));
	fotomoto_set_option("exclusive_list", $list);
}

function fotomoto_curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function fotomoto_script($key, $ext="") {
	$url = "http://widget.".FOTOMOTO_PRODUCTION_DOMAIN."/stores/script/" . $key . ".js";
	if ($ext != "") {
		$url .= "?ext=" . $ext;
	}
	return $url;
}

function fotomoto_is_scripted() {
	global $post;
	wp_reset_query();
	if (fotomoto_get_option("store_key") == "") return false;
	/*$list = fotomoto_get_option("exclusive_list");
	$page_path = str_replace(site_url(), "", fotomoto_curPageURL());
	foreach ($list as $item) {	
		if ($item == "") continue;
		if ($item == "/") {
			if (is_front_page()) return false;
			else continue;
		}

		if (strpos($page_path, $item) !== FALSE) return false;		
	}*/
	
	if (is_front_page() && !fotomoto_page_enabled("home")) return false;
	if (get_post_meta($post->ID, "fotomoto_enable_status", true) == "") { // page enabled is not set, check category enabled		
		foreach((get_the_category($post->ID)) as $category) { 
			if (!fotomoto_category_enabled($category->cat_ID)) return false; // if there is a category disabled
		}
	}
	else {
		return fotomoto_page_enabled($post->ID);
	}
	
	return true;
}


function fotomoto() { 	
	if (isset($_GET["fotomoto_debug"])) {
?>
<!--
VERSION: 0.0.1

REQUEST URI: <?= $_SERVER["REQUEST_URI"] ?>

STORE KEY: <?= fotomoto_get_option('store_key') ?>

EXCLUSIVE LIST: <?= print_r(fotomoto_get_option("exclusive_list")) ?>

IS_HOME: <?= is_home() ?>

IS_FRONT_PAGE: <?= is_front_page() ?>

-->
<?php
	}
	if (!fotomoto_is_scripted()) return;
?>
<script type="text/javascript" src="<?php echo fotomoto_script(fotomoto_get_option('store_key'), $ext) ?>"></script>
<noscript>If Javascript is disabled browser, to place orders please visit the page where I <a href="http://www.fotomoto.com/store/<?php echo fotomoto_get_option('store_key') ?>" target="_blank">sell my photos</a>, powered by <a href="http://www.fotomoto.com" target="_blank">Fotomoto</a>.</noscript>
<?php 
}

function fotomoto_classes() {
  global $post;
  if ($post) {
    $site_key = get_user_meta($post->post_author, "fotomoto_site_key", true);
    if (trim($site_key) != "") return "ftmt_id_$site_key";
  }
  return "ftmt_idna nofotomoto";
}

register_activation_hook(__FILE__, 'activate_fotomoto');
register_deactivation_hook(__FILE__, 'deactive_fotomoto');

if (is_admin()) {
  add_action('admin_init', 'admin_init_fotomoto');
  add_action('admin_menu', 'admin_menu_fotomoto');
  add_action('admin_print_styles', 'fotomoto_stylesheet', 1);
  add_action('admin_print_scripts', 'fotomoto_headscripts');
  
}
 
function fotomoto_headscripts(){
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-tabs');
	wp_register_script("fotomoto_admin_script", WP_FOTOMOTO_PLUGIN_URL.'/js/admin.js');
	wp_enqueue_script('fotomoto_admin_script');  
}

function fotomoto_stylesheet() {		
	wp_register_style("jquery_css", "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css");
	wp_enqueue_style("jquery_css");	
	
	wp_register_style("fotomoto_admin_css", WP_FOTOMOTO_PLUGIN_URL.'/css/admin.css');
	wp_enqueue_style("fotomoto_admin_css");		
}

function fotomoto_user_domain($user_login) {
	$domain = get_site_url();
	$domain = str_replace("http://www.", "http://", $domain);
	$domain = str_replace("http://", "http://{$user_login}.", $domain);
	return $domain;
}

function fotomoto_save_site_key($user_id) {
	$user_object = new WP_User($user_id);
	if ($user_object->user_level >= 2) { // author level+
		$email = $user_object->user_email;
		$first_name = ($user_object->first_name == "" ? $user_object->user_login : $user_object->first_name);
		$last_name = ($user_object->last_name == "" ? $user_object->user_login : $user_object->last_name);
		$domain = fotomoto_user_domain($user_object->user_login);
		$response = fotomoto_get_site_key($email, $first_name, $last_name, $domain);
		if ($response["status"] == 200) {
			update_user_meta($user_id, "fotomoto_site_key", $response["site_key"]);      
		}
	}	
}

function fotomoto_user_register($user_id) {	
	if (fotomoto_get_option("activate_new_user") != "") { // automatically activate new user
		fotomoto_save_site_key($user_id);
	}
}

function fotomoto_image_tag_class($class){
  $class .=' [ftmt_id]';
  return $class;
}
add_filter('get_image_tag_class','fotomoto_image_tag_class');
add_shortcode('ftmt_id', 'fotomoto_classes');	
if (!is_admin()) {
	add_action('wp_footer', 'fotomoto');
}
add_action('user_register', 'fotomoto_user_register');
?>