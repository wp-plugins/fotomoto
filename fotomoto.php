<?php
/*
Plugin Name: Fotomoto
Plugin URI: http://www.fotomoto.com
Description: Enables Fotomoto on all pages.
Version: 0.1.8
Author: Fotomoto
Author URI: http://www.fotomoto.com/
*/

define('FOTOMOTO_VERSION', '0.1.8');

if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

$fotomoto_options = get_option('fotomoto_options');

function activate_fotomoto() {
  add_option('fotomoto_options', fotomoto_default_options());
}

function deactive_fotomoto() {
  delete_option('fotomoto_options');
}

function admin_init_fotomoto() {
  register_setting('fotomoto', 'fotomoto_options');
}

function admin_menu_fotomoto() {
  add_options_page('Fotomoto', 'Fotomoto', 8, 'fotomoto', 'options_page_fotomoto');
}

function options_page_fotomoto() {
  if (count($_POST) > 0) {
  	fotomoto_save_options();
  	$message = "Settings updated.";
  }
  
  include(WP_PLUGIN_DIR.'/fotomoto/options.php');
}

function fotomoto_default_options() {
	$default_options = array();
	$default_options["store_key"] = "";
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
	$url = "http://widget.fotomoto.com/stores/script/" . $key . ".js";
	if ($ext != "") {
		$url .= "?ext=" . $ext;
	}
	return $url;
}

function fotomoto_is_scripted() {
	wp_reset_query();
	if (fotomoto_get_option("store_key") == "") return false;
	$list = fotomoto_get_option("exclusive_list");
	$page_path = str_replace(site_url(), "", fotomoto_curPageURL());
	foreach ($list as $item) {	
		if ($item == "") continue;
		if ($item == "/") {
			if (is_front_page()) return false;
			else continue;
		}

		if (strpos($page_path, $item) !== FALSE) return false;		
	}
	return true;
}

function fotomoto() { 	
	global $ngg;
	$ngg_thumbEffect = "";
	if ($ngg != null) { //&& $ngg->options["useFotomoto"] == "1") {
		$ngg_thumbEffect = $ngg->options["thumbEffect"];
		if ($ngg_thumbEffect == "lightbox" && class_exists('wp_lightboxplus')) $ngg_thumbEffect = "lightboxplus";
	}
	$ext = $ngg_thumbEffect;
	
	if (isset($_GET["fotomoto_debug"])) {
?>
<!--
VERSION: 0.1.8

REQUEST URI: <?= $_SERVER["REQUEST_URI"] ?>

EXCLUSIVE LIST: <?= print_r(fotomoto_get_option("exclusive_list")) ?>

STORE KEY: <?= fotomoto_get_option('store_key') ?>

IS_HOME: <?= is_home() ?>

IS_FRONT_PAGE: <?= is_front_page() ?>

EXT: <?= $ext ?>

-->
<?php
	}
	if (!fotomoto_is_scripted()) return;
?>
<script type="text/javascript" src="<?php echo fotomoto_script(fotomoto_get_option('store_key'), $ext) ?>"></script>
<noscript>If Javascript is disabled browser, to place orders please visit the page where I <a href="http://www.fotomoto.com/store/<?php echo fotomoto_get_option('store_key') ?>" target="_blank">sell my photos</a>, powered by <a href="http://www.fotomoto.com" target="_blank">Fotomoto</a>.</noscript>
<?php 
}

register_activation_hook(__FILE__, 'activate_fotomoto');
register_deactivation_hook(__FILE__, 'deactive_fotomoto');

if (is_admin()) {
  add_action('admin_init', 'admin_init_fotomoto');
  add_action('admin_menu', 'admin_menu_fotomoto');
}

if (!is_admin()) {
	add_action('wp_footer', 'fotomoto');
}

?>