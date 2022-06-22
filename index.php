<?php
/*
Plugin Name: MF Maps
Plugin URI: https://github.com/frostkom/mf_maps
Description:
Version: 2.6.1
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_maps
Domain Path: /lang

Depends: MF Base
GitHub Plugin URI: frostkom/mf_maps
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_maps = new mf_maps();

	if(is_admin())
	{
		register_uninstall_hook(__FILE__, 'uninstall_maps');

		add_action('admin_init', array($obj_maps, 'settings_maps'));
		add_action('admin_init', array($obj_maps, 'admin_init'), 0);

		add_action('show_user_profile', array($obj_maps, 'edit_user_profile'));
		add_action('edit_user_profile', array($obj_maps, 'edit_user_profile'));
		add_action('profile_update', array($obj_maps, 'profile_update'));
	}

	else
	{
		add_action('wp_head', array($obj_maps, 'wp_head'), 0);

		//add_filter('filter_profile_fields', array($obj_maps, 'filter_profile_fields'));
	}

	add_filter('get_map', array($obj_maps, 'get_map'), 10, 2);
	add_filter('get_coordinates_from_location', array($obj_maps, 'get_coordinates_from_location'));

	load_plugin_textdomain('lang_maps', false, dirname(plugin_basename(__FILE__))."/lang/");

	function uninstall_maps()
	{
		mf_uninstall_plugin(array(
			'options' => array('setting_gmaps_api', 'setting_maps_type', 'setting_maps_controls', 'setting_maps_default_position', 'setting_profile_map'),
			'meta' => array('meta_search_input', 'meta_search_coords'),
		));
	}
}