function on_load_maps()
{
	var i = 0;

	jQuery('.maps_search_container').each(function()
	{
		var dom_obj = jQuery(this);

		dom_obj.gmaps();

		i++;
	});
}

jQuery.fn.gmaps = function(o)
{
	var map,
		zoom_default = 12;

	var dom_obj = this,
		dom_id = dom_obj.attr('id'),
		search_input_class = "maps_search_input",
		search_input_obj = dom_obj.find('.' + search_input_class),
		search_map_class = "maps_search_map",
		search_map_obj = dom_obj.find('.' + search_map_class),
		search_coords_obj = dom_obj.find('.maps_search_coords');

	function add_marker(data)
	{
		if(!data.name){		data.name = "";}
		if(!data.text){		data.text = "";}
		if(!data.pos){		data.pos = new google.maps.LatLng(data.lat, data.long);}

		var markers = [];

		if(data.icon)
		{
			var marker = new google.maps.Marker(
			{
				map: map,
				icon: {
					url: data.icon
				},
				position: data.pos,
				title: data.name
			});
		}

		if(data.text != '')
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
				infowindow.open(map, marker);
			});
		}

		markers.push(marker);
	}

	function get_coords_from_string(string)
	{
		return string.replace("(", "").replace(")", "").split(", ");
	}

	function get_position_from_string(string)
	{
		var coords = get_coords_from_string(string),
			position = new google.maps.LatLng(coords[0], coords[1]);

		return position;
	}

	function geocode_address(address)
	{
		var geocoder = new google.maps.Geocoder();

		geocoder.geocode({'address': address}, function(results, status)
		{
			if(status === 'OK')
			{
				var position = results[0].geometry.location;

				add_marker({'pos': position, 'icon': script_maps.plugins_url + '/mf_maps/images/star.png', 'text': "<a href='//google.com/maps/search/" + address + "' rel='external'><i class='fa fa-external-link fa-2x'></i></a>"});

				map.setCenter(position);
				map.setZoom(zoom_default);
			}

			else
			{
				console.log('Geocode was not successful for the following reason: ' + status);
			}
		});
	}

	function set_initial_marker()
	{
		var coords_temp = search_coords_obj.val();

		if(coords_temp != '')
		{
			var position = get_position_from_string(coords_temp);

			add_marker({'pos': position, 'icon': script_maps.plugins_url + '/mf_maps/images/star.png'});

			map.setCenter(position);
			map.setZoom(zoom_default);
		}

		else if(search_input_obj.val() != '')
		{
			geocode_address(search_input_obj.val());
		}

		else if(navigator.geolocation)
		{
			navigator.geolocation.getCurrentPosition(function(position)
			{
				var position = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

				add_marker({'pos': position, 'icon': script_maps.plugins_url + '/mf_maps/images/regroup.png'}); /*, 'name': script_maps.here_i_am */

				map.setCenter(position);
				map.setZoom(zoom_default);
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

			/* For each place, get the icon, place name, and location */
			var bounds = new google.maps.LatLngBounds();

			for(var i = 0, place; place = places[i]; i++)
			{
				add_marker({'pos': place.geometry.location, 'name': place.name, 'icon': script_maps.plugins_url + '/mf_maps/images/star.png'});

				bounds.extend(place.geometry.location);

				search_coords_obj.val(place.geometry.location);
			}

			if(has_maps == true)
			{
				map.fitBounds(bounds);
				map.setZoom(zoom_default);
			}
		}
	}

	if(search_input_obj.is(':visible') && search_input_obj.length > 0) /* && map_initiated == false */
	{
		var has_maps = (search_map_obj.length > 0 ? true : false),
			markers = [],
			search_input_obj_old = document.getElementById(dom_id).getElementsByClassName(search_input_class)[0],
			search_map_obj_old = document.getElementById(dom_id).getElementsByClassName(search_map_class)[0],
			searchBox = new google.maps.places.SearchBox(search_input_obj_old);

		if(has_maps == true)
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

			var mapCoords = get_coords_from_string(script_maps.default_position),
				mapOptions = {
				center: new google.maps.LatLng(mapCoords[0], mapCoords[1]),
				mapTypeControl: false,
				streetViewControl: script_maps.display_street_view,
				zoomControl: script_maps.display_zoom,
				scaleControl: script_maps.display_scale,
				mapTypeId: mapType,
				styles: mapStyles,
				zoom: zoom_default
			};

			map = new google.maps.Map(search_map_obj_old, mapOptions);

			map.controls[google.maps.ControlPosition.TOP_LEFT].push(search_input_obj_old);

			set_initial_marker();

			/* Bias the search results towards places that are within the bounds of the current map's viewport */
			google.maps.event.addListener(map, 'bounds_changed', function()
			{
				var bounds = map.getBounds();

				searchBox.setBounds(bounds);
			});
		}

		google.maps.event.addListener(searchBox, 'places_changed', function()
		{
			set_search_marker(searchBox.getPlaces());
		});
	}
};

jQuery(function($)
{
	on_load_maps();

	if(typeof collect_on_load == 'function')
	{
		collect_on_load('on_load_maps');
	}

	$(document).on('click', '.toggler', function(e)
	{
		var toggler_rel = $(this).attr('rel'),
			toggle_container = $('.toggle_container[rel=' + toggler_rel + ']'),
			is_toggle_container = $(e.target).parents(".toggle_container").length > 0;

		if(toggle_container.length > 0 && is_toggle_container == false)
		{
			toggle_container.find('.maps_search_container').gmaps();
		}
	});
});