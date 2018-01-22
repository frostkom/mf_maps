<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_maps/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$setting_maps_controls = get_option_or_default('setting_maps_controls', array('search', 'fullscreen', 'zoom'));

if(in_array('fullscreen', $setting_maps_controls))
{
	$input_width = "calc(98% - 45px)";

	$fullscreen_style = "";
}

else
{
	$input_width = "98%";

	$fullscreen_style = ".gm-fullscreen-control
	{
		display: none;
	}";
}

if(in_array('search', $setting_maps_controls))
{
	$input_style = ".maps_search_input
	{
		margin: .5em;
		opacity: .2;
		padding: .5em;
		text-overflow: ellipsis;
		transition: all .4s ease;
		width: ".$input_width." !important;
	}

		.maps_search_container:hover .maps_search_input
		{
			opacity: 1;
		}";
}

else
{
	$input_style = ".maps_search_input
	{
		display: none;
	}";
}

$out = "@media all
{"
	.$input_style
	.$fullscreen_style

	.".maps_search_map
	{
		height: 50vh;
	}
}";

echo $out;