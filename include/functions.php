<?php

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