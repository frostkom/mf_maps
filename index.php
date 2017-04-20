<?php
/*
Plugin Name: MF Maps
Plugin URI: https://github.com/frostkom/mf_maps
Description: 
Version: 2.2.6
Author: Martin Fors
Author URI: http://martinfors.se

GitHub Plugin URI: frostkom/mf_maps
*/

include_once("include/functions.php");

add_action('init', 'init_maps');

if(is_admin())
{
	register_uninstall_hook(__FILE__, 'uninstall_maps');

	add_action('admin_init', 'settings_maps');

	if(get_option('setting_profile_map') == 'yes')
	{
		add_action('show_user_profile', 'show_search_maps');
		add_action('edit_user_profile', 'show_search_maps');
		add_action('personal_options_update', 'save_search_maps');
		add_action('edit_user_profile_update', 'save_search_maps');
	}

	function uninstall_maps()
	{
		mf_uninstall_plugin(array(
			'options' => array('setting_gmaps_api', 'setting_profile_map'),
		));
	}
}

load_plugin_textdomain('lang_maps', false, dirname(plugin_basename(__FILE__)).'/lang/');