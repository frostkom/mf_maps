<?php
/*
Plugin Name: MF Maps
Plugin URI: https://github.com/frostkom/mf_maps
Description: 
Version: 2.5.5
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_maps
Domain Path: /lang

Depends: MF Base
GitHub Plugin URI: frostkom/mf_maps
*/

include_once("include/classes.php");
include_once("include/functions.php");

$obj_maps = new mf_maps();

add_action('cron_base', 'activate_maps', mt_rand(1, 10));

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_maps');
	register_uninstall_hook(__FILE__, 'uninstall_maps');

	add_action('admin_init', array($obj_maps, 'settings_maps'));
	add_action('admin_init', array($obj_maps, 'admin_init'), 0);

	add_action('show_user_profile', array($obj_maps, 'edit_user_profile'));
	add_action('edit_user_profile', array($obj_maps, 'edit_user_profile'));
	add_action('personal_options_update', array($obj_maps, 'edit_user_profile_update'));
	add_action('edit_user_profile_update', array($obj_maps, 'edit_user_profile_update'));
}

else
{
	add_action('wp_head', array($obj_maps, 'wp_head'), 0);

	//add_filter('filter_profile_fields', array($obj_maps, 'filter_profile_fields'));
}

function activate_maps()
{
	mf_uninstall_plugin(array(
		'options' => array('setting_maps_height'),
	));

	replace_user_meta(array('old' => 'profile_search_input', 'new' => 'meta_search_input'));
	replace_user_meta(array('old' => 'profile_search_coords', 'new' => 'meta_search_coords'));
}

function uninstall_maps()
{
	mf_uninstall_plugin(array(
		'options' => array('setting_gmaps_api', 'setting_maps_type', 'setting_maps_controls', 'setting_maps_default_position', 'setting_profile_map'),
		'meta' => array('meta_search_input', 'meta_search_coords'),
	));
}

load_plugin_textdomain('lang_maps', false, dirname(plugin_basename(__FILE__)).'/lang/');