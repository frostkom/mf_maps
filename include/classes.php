<?php

class mf_maps
{
	function __construct()
	{

	}

	function admin_init()
	{
		$this->wp_head();
	}

	function wp_head()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		$setting_gmaps_api = get_option('setting_gmaps_api');

		if($setting_gmaps_api != '')
		{
			$setting_maps_type = get_option_or_default('setting_maps_type', 'roadmap');
			$setting_maps_controls = get_option_or_default('setting_maps_controls', array('search', 'fullscreen', 'zoom'));
			$setting_maps_default_position = get_option_or_default('setting_maps_default_position', "(55.6133308, 12.976285800000028)");

			mf_enqueue_style('style_maps', $plugin_include_url."style.php", $plugin_version);
			wp_enqueue_script('script_gmaps_api', "//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=".$setting_gmaps_api, array(), $plugin_version);
			mf_enqueue_script('script_maps', $plugin_include_url."script.js", array(
				'type' => $setting_maps_type,
				'display_street_view' => in_array('street_view', $setting_maps_controls),
				'display_zoom' => in_array('zoom', $setting_maps_controls),
				'display_scale' => in_array('scale', $setting_maps_controls),
				'default_position' => $setting_maps_default_position,
				'here_i_am' => __("Here I am", 'lang_maps'),
				'plugins_url' => plugins_url()
			), $plugin_version);
		}
	}
}

if(class_exists('RWMB_Field'))
{
	class RWMB_Gps_Field extends RWMB_Field
	{
		static public function html($meta, $field)
		{
			/*return "<div class='map_wrapper'>"
				.show_textfield(array('name' => "webshop_map_input", 'placeholder' => __("Search for an address and find its position", 'lang_maps')))
				."<div id='webshop_map'></div>"
				.show_textfield(array('name' => $field['field_name'], 'value' => $meta, 'placeholder' => __("Coordinates will be displayed here", 'lang_maps'), 'id' => "webshop_map_coords", 'xtra' => "class='rwmb-text'"))
			."</div>";*/

			return get_map(array('input_name' => 'webshop_map_input', 'coords_name' => $field['field_name'], 'coords' => $meta));
		}
	}
}