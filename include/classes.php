<?php

class mf_maps
{
	var $location_temp;

	function __construct(){}

	function block_render_map_callback($attributes)
	{
		if(!isset($attributes['maps_latitude'])){		$attributes['maps_latitude'] = 411964;}
		if(!isset($attributes['maps_longitude'])){		$attributes['maps_longitude'] = 6175590;}
		if(!isset($attributes['maps_zoom'])){			$attributes['maps_zoom'] = 4;}

		$plugin_include_url = plugin_dir_url(__FILE__);

		wp_enqueue_style('style_openlayers', "//cdn.jsdelivr.net/npm/ol@v10.1.0/ol.css");
		//wp_enqueue_style('style_openlayers_switcher', "//cdn.jsdelivr.net/npm/ol-layerswitcher@latest/dist/ol-layerswitcher.css");
		mf_enqueue_style('style_maps_map', $plugin_include_url."style_map.css");

		wp_enqueue_script('script_openlayers', "//cdn.jsdelivr.net/npm/ol@v10.1.0/dist/ol.js");
		//wp_enqueue_script('script_openlayers_switcher', "//cdn.jsdelivr.net/npm/ol-layerswitcher@latest/dist/ol-layerswitcher.js");
		wp_enqueue_script('script_proj4js', "//cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.0/proj4.js");
		mf_enqueue_script('script_maps_map', $plugin_include_url."script_map.js", array(
			'center' => array((int)$attributes['maps_latitude'], (int)$attributes['maps_longitude']),
			'zoom' => $attributes['maps_zoom'],
		));

		$out = "<div".parse_block_attributes(array('class' => "widget maps_map", 'attributes' => $attributes)).">
			<div class='map_container' tabindex='0'></div>";

			/*$out .= "<div class='map_search mf_form'>"
				.show_textfield(array('placeholder' => __("Search for property or address", 'lang_maps')))
				."<ul class='striped hide'>
					<li>".__("Search in the field above and the result will show up here", 'lang_maps')."</li>
				</ul>"
			."</div>";*/

			$out .= "<div class='map_info mf_form'></div>
		</div>";

		return $out;
	}

	function enqueue_block_editor_assets()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_maps_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version);

		wp_localize_script('script_maps_block_wp', 'script_maps_block_wp', array(
			'block_title' => __("Map", 'lang_maps'),
			'block_description' => __("Display a Map", 'lang_maps'),
			'maps_latitude_label' => __("Latitude", 'lang_maps'),
			'maps_longitude_label' => __("Longitude", 'lang_maps'),
			'maps_zoom_label' => __("Zoom", 'lang_maps'),
		));
	}

	function init()
	{
		load_plugin_textdomain('lang_maps', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		register_block_type('mf/maps', array(
			'editor_script' => 'script_maps_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_map_callback'),
		));
	}

	function settings_maps()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

		$arr_settings = array();
		$arr_settings['setting_gmaps_api'] = __("API Key", 'lang_maps');

		if(get_option('setting_gmaps_api') != '')
		{
			$arr_settings['setting_maps_type'] = __("Design", 'lang_maps');
			$arr_settings['setting_maps_controls'] = __("Display Controls", 'lang_maps');
			$arr_settings['setting_maps_default_position'] = __("Default Position", 'lang_maps');
			$arr_settings['setting_profile_map'] = __("Display Map in Profile", 'lang_maps');
		}

		show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
	}

	function settings_maps_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Maps", 'lang_maps'));
	}

	function setting_gmaps_api_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		$suffix = ($option == '' ? "<a href='//developers.google.com/maps/documentation/javascript/get-api-key'>".__("Get yours here", 'lang_maps')."</a>" : "");

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'suffix' => $suffix));
	}

	function setting_maps_type_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'roadmap');

		$arr_data = array(
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

		echo apply_filters('get_map', '', array('input' => '', 'coordinates_name' => $setting_key, 'coordinates' => $option));
	}

	function setting_profile_map_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
	}

	function edit_user_profile($user)
	{
		if(get_option('setting_profile_map') == 'yes')
		{
			$profile_search_input = get_the_author_meta('meta_search_input', $user->ID);
			$profile_search_coordinates = get_the_author_meta('meta_search_coords', $user->ID);

			echo "<table class='form-table mf_form'>
				<tr>
					<th><label>".__("City", 'lang_maps')."</label></th>" // for='profile_coords'
					."<td>"
						.apply_filters('get_map', '', array('id' => $user->ID, 'input' => $profile_search_input, 'coordinates' => $profile_search_coordinates))
					."</td>
				</tr>
			</table>";
		}
	}

	function profile_update($user_id)
	{
		if(get_option('setting_profile_map') == 'yes')
		{
			$profile_search_input = check_var('maps_search_input');
			$profile_search_coordinates = check_var('maps_search_coordinates');

			update_user_meta($user_id, 'meta_search_input', $profile_search_input);
			update_user_meta($user_id, 'meta_search_coords', $profile_search_coordinates);
		}
	}

	function init_maps()
	{
		$setting_gmaps_api = get_option('setting_gmaps_api');

		if($setting_gmaps_api != '')
		{
			$setting_maps_type = get_option_or_default('setting_maps_type', 'roadmap');
			$setting_maps_controls = get_option_or_default('setting_maps_controls', array('search', 'fullscreen', 'zoom'));
			$setting_maps_default_position = get_option_or_default('setting_maps_default_position', "(55.6133308, 12.976285800000028)");

			$plugin_include_url = plugin_dir_url(__FILE__);
			$plugin_version = get_plugin_version(__FILE__);

			mf_enqueue_style('style_maps', $plugin_include_url."style.php");
			wp_enqueue_script('script_gmaps_api', "//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=".$setting_gmaps_api, array(), $plugin_version);
			mf_enqueue_script('script_maps', $plugin_include_url."script.js", array(
				'plugins_url' => plugins_url(),
				'type' => $setting_maps_type,
				'display_fullscreen' => in_array('fullscreen', $setting_maps_controls),
				'display_scale' => in_array('scale', $setting_maps_controls),
				'display_search' => in_array('search', $setting_maps_controls),
				'display_street_view' => in_array('street_view', $setting_maps_controls),
				'display_zoom' => in_array('zoom', $setting_maps_controls),
				'default_position' => $setting_maps_default_position,
			));
		}
	}

	function get_map($out, $data)
	{
		if(!isset($data['id'])){				$data['id'] = mt_rand(1, 100);}
		if(!isset($data['input_name'])){		$data['input_name'] = 'maps_search_input';}
		if(!isset($data['input'])){				$data['input'] = "";}
		if(!isset($data['coordinates_name'])){	$data['coordinates_name'] = 'maps_search_coordinates';}
		if(!isset($data['coordinates'])){		$data['coordinates'] = "";}

		$this->init_maps();

		$out = "<div id='maps_search_container_".$data['id']."' class='maps_search_container'>"
			.show_textfield(array('name' => 'maps_search_input', 'value' => $data['input'], 'placeholder' => __("Search for an address and find its position", 'lang_maps'), 'xtra' => "class='maps_search_input'"))
			."<div class='maps_search_map'></div>"
			.input_hidden(array('name' => $data['coordinates_name'], 'value' => $data['coordinates'], 'xtra' => "class='maps_search_coordinates'"))
		."</div>";

		return $out;
	}

	function get_transient_coordinates_from_location()
	{
		$out = "";

		$setting_gmaps_api = get_option('setting_gmaps_api');

		if($setting_gmaps_api != '')
		{
			list($content, $headers) = get_url_content(array(
				'url' => "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($this->location_temp)."&key=".$setting_gmaps_api,
				'catch_head' => true,
			));

			switch($headers['http_code'])
			{
				case 200:
					$json = json_decode($content, true);

					if(isset($json['results'][0]['geometry']) && $json['results'][0]['geometry'])
					{
						$lat = str_replace(',', '.', $json['results'][0]['geometry']['location']['lat']);
						$lng = str_replace(',', '.',$json['results'][0]['geometry']['location']['lng']);

						$out = "(".$lat.", ".$lng.")";
					}
				break;

				default:
					do_log("I could not connect to gMaps: ".$headers['http_code']." (".var_export($headers, true).", ".$content.")");
				break;
			}
		}

		return $out;
	}

	function get_or_set_transient($data)
	{
		if(!isset($data['key'])){			$data['key'] = '';}
		if(!isset($data['callback'])){		$data['callback'] = '';}

		$out = "";

		if($data['key'] != '')
		{
			$out = get_transient($data['key']);

			if($out == "")
			{
				if(is_callable($data['callback']))
				{
					$out = call_user_func($data['callback']);

					if($out != '')
					{
						set_transient($data['key'], $out, DAY_IN_SECONDS);
					}
				}
			}
		}

		return $out;
	}

	function get_coordinates_from_location($location)
	{
		$out = "";

		if($location != '')
		{
			$this->location_temp = $location;

			$out = $this->get_or_set_transient(array('key' => 'coordinates_from_location_'.$location, 'callback' => array($this, 'get_transient_coordinates_from_location')));
		}

		return $out;
	}
}

if(class_exists('RWMB_Field'))
{
	class RWMB_Gps_Field extends RWMB_Field
	{
		static public function html($meta, $field)
		{
			return apply_filters('get_map', '', array('input_name' => 'webshop_map_input', 'coordinates_name' => $field['field_name'], 'coordinates' => $meta));
		}
	}
}