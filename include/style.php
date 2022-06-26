<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_maps/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$out = "@media all
{
	.maps_search_input
	{
		margin: .5em;
		opacity: .2;
		padding: .5em;
		text-overflow: ellipsis;
		transition: all .4s ease;
		width: calc(98% - 55px) !important;
	}

		.maps_search_container:hover .maps_search_input
		{
			opacity: 1;
		}

		.maps_initiated.hide_fullscreen .maps_search_input
		{
			width: 98% !important;
		}

	.maps_initiated.hide_fullscreen .gm-fullscreen-control
	{
		display: none;
	}

	.maps_search_map
	{
		height: 50vh;
	}

	.marker_content h3
	{
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
}";

echo $out;