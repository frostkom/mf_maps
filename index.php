<?php
/*
Plugin Name: MF Maps
Plugin URI: https://github.com/frostkom/mf_maps
Description: 
Version: 2.3.13
Licence: GPLv2 or later
Author: Martin Fors
Author URI: http://martinfors.se

GitHub Plugin URI: frostkom/mf_maps
*/

include_once("include/functions.php");

add_action('cron_base', 'activate_maps', mt_rand(1, 10));

add_action('init', 'init_maps');

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_maps');
	register_uninstall_hook(__FILE__, 'uninstall_maps');

	add_action('admin_init', 'settings_maps');

	if(get_option('setting_profile_map') == 'yes')
	{
		add_action('show_user_profile', 'show_search_maps');
		add_action('edit_user_profile', 'show_search_maps');
		add_action('personal_options_update', 'save_search_maps');
		add_action('edit_user_profile_update', 'save_search_maps');
	}
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