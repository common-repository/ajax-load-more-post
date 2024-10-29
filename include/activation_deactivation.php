<?php

/**
 * Clean data on activation / deactivation
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
 
register_activation_hook( __FILE__, 'richpostslistandgrid_activation');

function richpostslistandgrid_activation() {

	if( ! current_user_can ( 'activate_plugins' ) ) {
		return;
	} 
	add_option( 'richpostslistandgrid_license_status', 'invalid' );
	add_option( 'richpostslistandgrid_license_key', '' ); 

}

register_uninstall_hook( __FILE__, 'richpostslistandgrid_uninstall');

function richpostslistandgrid_uninstall() {

	delete_option( 'richpostslistandgrid_license_status' );
	delete_option( 'richpostslistandgrid_license_key' ); 
	
}