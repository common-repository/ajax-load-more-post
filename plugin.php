<?php 
/*
  Plugin Name: Ajax Load More - WordPress infinite scroll
  Description: Posts list and grid view with infinite scroll load more button.
  Author: iKhodal Web Solution
  Plugin URI: https://www.ikhodal.com/ajax-post-list-widget-and-shortcode-wordpress-plugin
  Author URI: https://www.ikhodal.com
  Version: 2.1 
  Text Domain: richpostslistandgrid
*/ 
  
  
//////////////////////////////////////////////////////
// Defines the constants for use within the plugin. //
////////////////////////////////////////////////////// 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  


/**
*  Assets of the plugin
*/
$rplg_plugins_url = plugins_url( "/assets/", __FILE__ );

define( 'rplg_media', $rplg_plugins_url ); 

/**
*  Plugin DIR
*/
$rplg_plugin_dir = plugin_basename(dirname(__FILE__));

define( 'rplg_plugin_dir', $rplg_plugin_dir );  

/**
 *  Set Plugin Information
 */
if(!function_exists('get_plugin_data'))  
  require_once(ABSPATH.'wp-admin/includes/plugin.php');

$plugin_info = get_plugin_data(__FILE__);  
define( 'rplg_plugin_url', "https://wordpress.org/plugins/ajax-load-more-post/" ); 
define( 'rplg_plugin_support', $plugin_info["AuthorURI"] ); 
define( 'rplg_plugin_name', $plugin_info["Name"] ); 



/**
 * Include abstract class for common methods
 */
require_once 'include/abstract.php';


///////////////////////////////////////////////////////
// Include files for widget and shortcode management //
///////////////////////////////////////////////////////

/**
 * Register custom post type for shortcode
 */ 
require_once 'include/shortcode.php';

/**
 * Admin panel widget configuration
 */ 
require_once 'include/admin.php';

/**
 * Load Category and Post View on frontent pages
 */
require_once 'include/richpostslistandgrid.php'; 

/**
 * Clean data on activation / deactivation
 */
require_once 'include/activation_deactivation.php';  
 