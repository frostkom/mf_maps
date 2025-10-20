<?php
/*
Plugin Name: MF Maps
Plugin URI: https://github.com/frostkom/mf_maps
Description:
Version: 2.7.5
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_maps
Domain Path: /lang
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_maps = new mf_maps();

	add_action('enqueue_block_editor_assets', array($obj_maps, 'enqueue_block_editor_assets'));
	add_action('init', array($obj_maps, 'init'));

	if(is_admin())
	{
		register_uninstall_hook(__FILE__, 'uninstall_maps');

		add_action('admin_init', array($obj_maps, 'settings_maps'));

		add_action('show_user_profile', array($obj_maps, 'edit_user_profile'));
		add_action('edit_user_profile', array($obj_maps, 'edit_user_profile'));
		add_action('profile_update', array($obj_maps, 'profile_update'));
	}

	add_filter('get_map', array($obj_maps, 'get_map'), 10, 2);
	add_filter('get_coordinates_from_location', array($obj_maps, 'get_coordinates_from_location'));

	function uninstall_maps()
	{
		mf_uninstall_plugin(array(
			'options' => array('setting_gmaps_api', 'setting_maps_type', 'setting_maps_controls', 'setting_maps_default_position', 'setting_profile_map'),
			'user_meta' => array('meta_search_input', 'meta_search_coords'),
		));
	}
}