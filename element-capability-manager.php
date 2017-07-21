<?php 
/*
Plugin Name: Element Capability Manager
Plugin URI: http://wordpress.org/plugins/element-capability-manager/
Description: This plugin prevents you from modifing any element of a specific post type, leaving all the others editable.
Version: 1.1
Author: Milena Gennara
License: GPL2
Copyright: Milena Gennara

Copyright 2013  Milena Gennara  (email : ecm@synapse-lab.it)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

define('EC_PLUGIN_PATH', dirname(__FILE__));
define('EC_PLUGIN_FOLDER', basename(EC_PLUGIN_PATH));
define('EC_PLUGIN_URL', plugins_url() . '/' . EC_PLUGIN_FOLDER );
define('EC_PLUGIN_NAME', __("Element Capability Manager",'ecm'));
define('EC_PLUGIN_SLUG', __("element-capability-manager",'ecm'));

include_once('ecm_functions.php');
load_plugin_textdomain('ecm', false, EC_PLUGIN_FOLDER . '/languages'); // load languages

register_activation_hook(__FILE__, 'ecm_init_fn');			// save default options on plugin activation

if ( is_admin() ){ // admin actions
	add_action( 'admin_menu', 'ecm_admin_menu' );  			// add admin page menu
	add_action( 'admin_init', 'ecm_register_settings' ); 	// register settings for saving options in settings page
	
	//add_action( 'admin_notices', 'ecm_settings_error' );
	add_action( 'add_meta_boxes', 'ecm_add_box_element' );	// add metaboxes inside the element
	add_action( 'save_post', 'ecm_save_post_meta', 10, 2 );	// save metabox data
}
add_filter('parse_query','ecm_filter_pages'); 				// filter pages for user
add_filter('user_has_cap', 'ecm_filter_admin_page', 0, 3 ); // filter in single element page in admin panel
?>