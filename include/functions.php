<?php

function get_map($data)
{
	if(!isset($data['id'])){			$data['id'] = mt_rand(1, 100);}
	if(!isset($data['input'])){			$data['input'] = "";}
	if(!isset($data['coords_name'])){	$data['coords_name'] = 'maps_search_coords';}
	if(!isset($data['coords'])){		$data['coords'] = "";}

	return "<div id='maps_search_container_".$data['id']."' class='maps_search_container'>"
		.show_textfield(array('name' => 'maps_search_input', 'value' => $data['input'], 'placeholder' => __("Search for an address and find its position", 'lang_maps'), 'xtra' => "class='maps_search_input'"))
		."<div class='maps_search_map'></div>"
		.input_hidden(array('name' => $data['coords_name'], 'value' => $data['coords'], 'xtra' => "class='maps_search_coords'"))
	."</div>";
}

function settings_maps()
{
	$options_area = __FUNCTION__;

	add_settings_section($options_area, "", $options_area."_callback", BASE_OPTIONS_PAGE);

	$arr_settings = array();
	$arr_settings['setting_gmaps_api'] = __("API Key", 'lang_maps');

	if(get_option('setting_gmaps_api') != '')
	{
		$arr_settings['setting_maps_type'] = __("Design", 'lang_maps');
		$arr_settings['setting_maps_controls'] = __("Display Controls", 'lang_maps');
		$arr_settings['setting_maps_default_position'] = __("Default Position", 'lang_maps');
		$arr_settings['setting_profile_map'] = __("Display Map in Profile", 'lang_maps');
	}

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

		$suffix = ($option == '' ? "<a href='//developers.google.com/maps/documentation/javascript/get-api-key'>".__("Get yours here", 'lang_maps')."</a>" : "");

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'suffix' => $suffix));
	}
}

function setting_maps_type_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	$arr_data = array(
		'' => "-- ".__("Choose here", 'lang_maps')." --",
		'roadmap' => __("Roadmap", 'lang_maps'),
		'satellite' => __("Satellite", 'lang_maps'),
		'hybrid' => __("Hybrid", 'lang_maps'),
		'terrain' => __("Terrain", 'lang_maps'),
		'custom' => __("Custom", 'lang_maps'),
	);

	echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option));
}

function setting_maps_controls_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	$arr_data = array(
		'search' => __("Search", 'lang_maps'),
		'fullscreen' => __("Fullscreen", 'lang_maps'),
		'street_view' => __("Street View", 'lang_maps'),
		'zoom' => __("Zoom", 'lang_maps'),
		'scale' => __("Scale", 'lang_maps'),
	);

	echo show_select(array('data' => $arr_data, 'name' => $setting_key."[]", 'value' => $option));
}

function setting_maps_default_position_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, '(55.6133308, 12.976285800000028)');

	echo get_map(array('input' => '', 'coords_name' => $setting_key, 'coords' => $option));
}

function setting_profile_map_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key, 'no');

	echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
}

function show_search_maps($user)
{
	$profile_search_input = get_the_author_meta('meta_search_input', $user->ID);
	$profile_search_coords = get_the_author_meta('meta_search_coords', $user->ID);

	echo "<table class='form-table mf_form'>
		<tr>
			<th><label for='profile_coords'>".__("City", 'lang_maps')."</label></th>
			<td>"
				.get_map(array('id' => $user->ID, 'input' => $profile_search_input, 'coords' => $profile_search_coords))
			."</td>
		</tr>
	</table>";
}

function save_search_maps($user_id, $password = "", $meta = array())
{
	$profile_search_input = check_var('maps_search_input');
	$profile_search_coords = check_var('maps_search_coords');

	update_user_meta($user_id, 'meta_search_input', $profile_search_input);
	update_user_meta($user_id, 'meta_search_coords', $profile_search_coords);
}