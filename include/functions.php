<?php

function init_maps()
{
	$setting_gmaps_api = get_option('setting_gmaps_api');

	mf_enqueue_style('style_maps', plugin_dir_url(__FILE__)."style.css", get_plugin_version(__FILE__));
	wp_enqueue_script('script_gmaps_api', "//maps.googleapis.com/maps/api/js?key=".$setting_gmaps_api."&v=3.exp&libraries=places", array('jquery'), get_plugin_version(__FILE__), true);
	mf_enqueue_script('script_maps', plugin_dir_url(__FILE__)."script.js", array('here_i_am' => __("Here I am", 'lang_maps'), 'plugins_url' => plugins_url()), get_plugin_version(__FILE__));
}

function get_map($data)
{
	if(!isset($data['input'])){		$data['input'] = "";}
	if(!isset($data['coords'])){	$data['coords'] = "";}

	return "<div class='maps_search_container'>"
		.show_textfield(array('name' => 'maps_search_input', 'value' => $data['input'], 'placeholder' => __("Search for your location", 'lang_maps'), 'xtra' => "class='maps_search_input'"))
		."<div class='maps_search_map'></div>"
		.input_hidden(array('name' => 'maps_search_coords', 'value' => $data['coords'], 'xtra' => "class='maps_search_coords'"))
	."</div>";
}

function settings_maps()
{
	$options_area = __FUNCTION__;

	add_settings_section($options_area, "", $options_area."_callback", BASE_OPTIONS_PAGE);

	$arr_settings = array();
	$arr_settings['setting_gmaps_api'] = __("API Key", 'lang_maps');
	$arr_settings['setting_profile_map'] = __("Show Map in Profile", 'lang_maps');

	show_settings_fields(array('area' => $options_area, 'settings' => $arr_settings));
}

function settings_maps_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);

	echo settings_header($setting_key, __("Maps", 'lang_maps'));
}

if(!function_exists('setting_gmaps_api_callback'))
{
	function setting_gmaps_api_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		$description = ($option == '' ? "<a href='//developers.google.com/maps/documentation/javascript/get-api-key' rel='external'>".__("Get yours here", 'lang_maps')."</a>" : "");

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'description' => $description));
	}
}

function setting_profile_map_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, 'no');

	echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
}

function show_search_maps($user)
{
	$profile_search_input = get_the_author_meta('profile_search_input', $user->ID);
	$profile_search_coords = get_the_author_meta('profile_search_coords', $user->ID);

	echo "<table class='form-table mf_form'>
		<tr>
			<th><label for='profile_coords'>".__("City", 'lang_maps')."</label></th>
			<td>"
				.get_map(array('input' => $profile_search_input, 'coords' => $profile_search_coords))
			."</td>
		</tr>
	</table>";
}

function save_search_maps($user_id, $password = "", $meta = array())
{
	$profile_search_input = check_var('maps_search_input');
	$profile_search_coords = check_var('maps_search_coords');

	update_user_meta($user_id, 'profile_search_input', $profile_search_input);
	update_user_meta($user_id, 'profile_search_coords', $profile_search_coords);
}