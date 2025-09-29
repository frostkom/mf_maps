var map_object,
	markers = [],
	lookup = [],
	zoom_default = 12,
	arr_infowindows = [];

function init_map_object(selector)
{
	var mapType,
		mapStyles = [];

	switch(script_maps.type)
	{
		default:
		case 'roadmap':
			mapType = google.maps.MapTypeId.ROADMAP;
		break;

		case 'satellite':
			mapType = google.maps.MapTypeId.SATELLITE;
		break;

		case 'hybrid':
			mapType = google.maps.MapTypeId.HYBRID;
		break;

		case 'terrain':
			mapType = google.maps.MapTypeId.TERRAIN;
		break;

		case 'custom':
			mapType = google.maps.MapTypeId.ROADMAP;
			mapStyles = [
				{"elementType": "geometry",															"stylers": [{	"color": "#f5f5f5"}]},
				{"elementType": "labels.icon",														"stylers": [{	"visibility": "off"}]},
				{"elementType": "labels.text.fill",													"stylers": [{	"color": "#616161"}]},
				{"elementType": "labels.text.stroke",												"stylers": [{	"color": "#f5f5f5"}]},
				{"featureType": "administrative", "elementType": "geometry",						"stylers": [{	"visibility": "off"}]},
				{"featureType": "administrative.land_parcel", "elementType": "labels.text.fill",	"stylers": [{	"color": "#bdbdbd"}]},
				{"featureType": "landscape", "elementType": "geometry.fill",						"stylers": [{	"color": "#efefef"}]},
				{"featureType": "poi",																"stylers": [{	"visibility": "off"}]},
				{"featureType": "poi", "elementType": "geometry",									"stylers": [{	"color": "#eeeeee"}]},
				{"featureType": "poi", "elementType": "labels.text.fill",							"stylers": [{	"color": "#757575"}]},
				{"featureType": "poi.park", "elementType": "geometry",								"stylers": [{	"color": "#e5e5e5"}]},
				{"featureType": "poi.park", "elementType": "labels.text.fill",						"stylers": [{	"color": "#9e9e9e"}]},
				{"featureType": "road",	"elementType": "geometry",									"stylers": [{	"color": "#ffffff"}]},
				{"featureType": "road", "elementType": "labels.icon",								"stylers": [{	"visibility": "off"}]},
				{"featureType": "road.arterial", "elementType": "labels.text.fill",					"stylers": [{	"color": "#757575"}]},
				{"featureType": "road.highway", "elementType": "geometry",							"stylers": [{	"color": "#dadada"}]},
				{"featureType": "road.highway", "elementType": "labels.text.fill",					"stylers": [{	"color": "#616161"}]},
				{"featureType": "road.local", "elementType": "labels.text.fill",					"stylers": [{	"color": "#9e9e9e"}]},
				{"featureType": "transit",															"stylers": [{	"visibility": "off"}]},
				{"featureType": "transit.line", "elementType": "geometry",							"stylers": [{	"color": "#e5e5e5"}]},
				{"featureType": "transit.station", "elementType": "geometry",						"stylers": [{	"color": "#eeeeee"}]},
				{"featureType": "water", "elementType": "geometry",									"stylers": [{	"color": "#c9c9c9"}]},
				{"featureType": "water", "elementType": "geometry.fill",							"stylers": [{	"color": "#87d9ff"},{	"saturation": -60}, {	"lightness": 30}]},
				{"featureType": "water", "elementType": "labels.text.fill",							"stylers": [{	"color": "#ffffff"}]}
			];
		break;
	}

	var mapCoordinates = get_coordinates_from_string(script_maps.default_position);

	var mapOptions = {
		center: new google.maps.LatLng(mapCoordinates[0], mapCoordinates[1]),
		mapTypeControl: false,
		streetViewControl: script_maps.display_street_view,
		zoomControl: script_maps.display_zoom,
		scaleControl: script_maps.display_scale,
		mapTypeId: mapType,
		styles: mapStyles,
		zoom: zoom_default
	};

	map_object = new google.maps.Map(selector, mapOptions);
}

/*function is_location_free(search)
{
	for(var i = 0, l = lookup.length; i < l; i++)
	{
		if(lookup[i] === search)
		{
			return false;
		}
	}

	return true;
}*/

function add_my_position_marker(position)
{
	var position = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

	add_marker({'pos': position, 'letter': 'I'});

	set_center(position);
}

function add_marker(data)
{
	if(!data.id){		data.id = '';}
	if(!data.letter){	data.letter = '';}
	if(!data.name){		data.name = '';}
	if(!data.text){		data.text = '';}
	if(!data.pos){		data.pos = new google.maps.LatLng(data.lat, data.long);}

	if(data.icon)
	{
		var marker_data = {
			map: map_object,
			icon: {url: data.icon},
			position: data.pos,
			title: data.name,
			id: data.id,
		};
	}

	else
	{
		var marker_data = {
			map: map_object,
			label: data.letter,
			position: data.pos,
			title: data.name,
			id: data.id,
		};
	}

	/*if(data.id && data.id == '' || is_location_free(data.id))
	{
		if(data.id && data.id != '')
		{
			lookup.push(data.id);
		}*/

		var marker = new google.maps.Marker(marker_data);

		if(data.name != '')
		{
			var infowindow = new google.maps.InfoWindow(
			{
				content: "<div class='marker_content'>"
					+ (data.name != '' ? "<h3>" + data.name + "</h3>" : "")
					+ (data.text != '' ? "<p>" + data.text + "</p>" : "")
				+ "</div>"
			});

			google.maps.event.addListener(marker, 'click', function()
			{
				for(var i = 0; i < arr_infowindows.length; i++)
				{
					arr_infowindows[i].close();
				}

				arr_infowindows.push(infowindow);

				infowindow.open(map_object, marker);
			});

			if(typeof webshop_marker_event === 'function')
			{
				webshop_marker_event(data, map_object, marker);
			}
		}

		markers.push(marker);
	/*}*/
}

function set_center(position)
{
	map_object.setCenter(position);
	map_object.setZoom(zoom_default);
}

function get_coordinates_from_string(string)
{
	return string.replace("(", "").replace(")", "").split(", ");
}

function get_position_from_string(string)
{
	var coordinates = get_coordinates_from_string(string),
		position = new google.maps.LatLng(coordinates[0], coordinates[1]);

	return position;
}

jQuery(function($)
{
	$.fn.gmaps = function(o)
	{
		var dom_obj = this,
			dom_id = dom_obj.attr('id'),
			search_input_class = "maps_search_input",
			search_input_obj = dom_obj.find("." + search_input_class),
			search_map_class = "maps_search_map",
			search_map_obj = dom_obj.find("." + search_map_class),
			search_coordinates_obj = dom_obj.find(".maps_search_coordinates");

		function geocode_address(address)
		{
			var geocoder = new google.maps.Geocoder();

			geocoder.geocode({'address': address}, function(results, status)
			{
				if(status === 'OK')
				{
					var position = results[0].geometry.location;

					add_marker({'pos': position});

					set_center(position);
				}

				else
				{
					console.log('Geocode was not successful for the following reason: ' + status);
				}
			});
		}

		function set_initial_marker()
		{
			var coordinates_temp = search_coordinates_obj.val();

			if(coordinates_temp != '')
			{
				var position = get_position_from_string(coordinates_temp);

				add_marker({'pos': position});

				set_center(position);
			}

			else if(search_input_obj.val() != '')
			{
				geocode_address(search_input_obj.val());
			}

			else if(navigator.geolocation)
			{
				navigator.geolocation.getCurrentPosition(function(position)
				{
					add_my_position_marker(position);
				},
				function(msg)
				{
					console.log(typeof msg == 'string' ? msg : 'Error retrieving GPS coordinates');
				});
			}
		}

		function set_search_marker(places)
		{
			if(places.length > 0)
			{
				for(var i = 0, marker; marker = markers[i]; i++)
				{
					marker.setMap(null);
				}

				var bounds = new google.maps.LatLngBounds();

				for(var i = 0, place; place = places[i]; i++)
				{
					add_marker({'pos': place.geometry.location, 'name': place.name, 'letter': 'S'});

					bounds.extend(place.geometry.location);

					search_coordinates_obj.val(place.geometry.location);
				}

				if(has_maps == true)
				{
					map_object.fitBounds(bounds);
					map_object.setZoom(zoom_default);
				}
			}
		}

		if(search_input_obj.is(":visible") && search_input_obj.length > 0)
		{
			var has_maps = (search_map_obj.length > 0),
				search_input_obj_old = document.getElementById(dom_id).getElementsByClassName(search_input_class)[0],
				search_map_obj_old = document.getElementById(dom_id).getElementsByClassName(search_map_class)[0],
				searchBox = new google.maps.places.SearchBox(search_input_obj_old);

			if(has_maps == true)
			{
				init_map_object(search_map_obj_old);

				map_object.controls[google.maps.ControlPosition.TOP_LEFT].push(search_input_obj_old);

				set_initial_marker();

				google.maps.event.addListener(map_object, 'bounds_changed', function()
				{
					searchBox.setBounds(map_object.getBounds());
				});
			}

			google.maps.event.addListener(searchBox, 'places_changed', function()
			{
				set_search_marker(searchBox.getPlaces());
			});

			dom_obj.addClass('maps_initiated');

			if(script_maps.display_fullscreen == false)
			{
				dom_obj.addClass('hide_fullscreen');
			}

			if(script_maps.display_search == false)
			{
				search_input_obj.addClass('hide');
			}
		}
	};

	var i = 0;

	$(".maps_search_container:not(.maps_initiated):visible").each(function()
	{
		var dom_obj = $(this);

		dom_obj.gmaps();

		i++;
	});

	$(document).on('click', ".settings-nav ul li a", function()
	{
		var dom_href = $(this).attr('href');

		setTimeout(function()
		{
			$(dom_href).next("table").find(".maps_search_container:not(.maps_initiated)").gmaps();
		}, 100); /* It needs this delay because otherwise it will still be invisible when this is initiated */
	});
});