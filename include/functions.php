<?php

function get_coordinates_from_location($location)
{
	$out = "";

	$setting_gmaps_api = get_option('setting_gmaps_api');

	if($location != '' && $setting_gmaps_api != '')
	{
		list($content, $headers) = get_url_content(array(
			'url' => "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($location)."&key=".$setting_gmaps_api,
			'catch_head' => true,
		));

		switch($headers['http_code'])
		{
			case 200:
				$json = json_decode($content, true);

				if($json['results'][0]['geometry'])
				{
					$lat = str_replace(',', '.', $json['results'][0]['geometry']['location']['lat']);
					$lng = str_replace(',', '.',$json['results'][0]['geometry']['location']['lng']);

					$out = "(".$lat.", ".$lng.")";
				}
			break;

			default:
				do_log(__("I could not connect to gMaps", 'lang_maps').": ".$headers['http_code']." (".var_export($headers, true).", ".$content.")");
			break;
		}
	}

	return $out;
}

function get_map($data)
{
	if(!isset($data['id'])){			$data['id'] = mt_rand(1, 100);}
	if(!isset($data['input_name'])){	$data['input_name'] = 'maps_search_input';}
	if(!isset($data['input'])){			$data['input'] = "";}
	if(!isset($data['coords_name'])){	$data['coords_name'] = 'maps_search_coords';}
	if(!isset($data['coords'])){		$data['coords'] = "";}

	return "<div id='maps_search_container_".$data['id']."' class='maps_search_container'>"
		.show_textfield(array('name' => 'maps_search_input', 'value' => $data['input'], 'placeholder' => __("Search for an address and find its position", 'lang_maps'), 'xtra' => "class='maps_search_input'"))
		."<div class='maps_search_map'></div>"
		.input_hidden(array('name' => $data['coords_name'], 'value' => $data['coords'], 'xtra' => "class='maps_search_coords'"))
	."</div>";
}