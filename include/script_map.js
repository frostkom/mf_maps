jQuery(function($)
{
	proj4.defs(
		'EPSG:3006',
		'+proj=utm +zone=33 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs'
	);
	ol.proj.proj4.register(proj4);

	var resolutions = [4096, 2048, 1024, 512, 256, 128, 64, 32, 16, 8, 4, 2, 1, 0.5];

	let mapLayers;

	var extent = [-1200000, 4700000, 2600000, 8500000],
		matrixIds = Array.from(Array(resolutions.length).keys());

	mapLayers = [
		new ol.layer.Tile(
		{
			title: 'OpenStreetMap',
			type: 'base',
			visible: true,
			source: new ol.source.OSM()
		})
	];

	const map = new ol.Map(
	{
		target: document.querySelector(".widget.maps_map .map_container"),
		layers: mapLayers,
		view: new ol.View(
		{
			projection: 'EPSG:3006',
			center: script_maps_map.center,
			zoom: script_maps_map.zoom,
			resolutions: resolutions
		}),
		controls: [
			new ol.control.ZoomSlider(),
			/*new LayerSwitcher(
			{
				reverse: false,
			}),*/
			new ol.control.ScaleLine(),
			new ol.control.FullScreen()
		],
		dragging: false
	});

	console.log("Initiated: " , script_maps_map.zoom , script_maps_map.center);

	let mapPoint = new ol.geom.Point(script_maps_map.center);

	var markerPosition = new ol.layer.Vector(
	{
		source: new ol.source.Vector(
		{
			features: [
				new ol.Feature(
				{
					geometry: mapPoint
				})
			]
		}),
		style: new ol.style.Style(
		{
			image: new ol.style.Icon(
			{
				anchor: [0.5, 1],
				src: '/wp-content/plugins/mf_maps/images/map-pin-svgrepo-com.svg',
				scale: 0.03
			})
		})
	});

	map.addLayer(markerPosition);

	console.log("Marker: " , script_maps_map.center);

	/*if(navigator.geolocation)
	{
		navigator.geolocation.getCurrentPosition((position) => {
			const myLat = position.coords.latitude;
			const myLon = position.coords.longitude;
			const transformedCoordinates = proj4('EPSG:4326', 'EPSG:3006', [myLon, myLat]);

			let mapPoint = new ol.geom.Point(transformedCoordinates);

			markerPosition = new ol.layer.Vector(
			{
				source: new ol.source.Vector(
				{
					features: [
						new ol.Feature(
						{
							geometry: mapPoint
						})
					]
				}),
				style: new ol.style.Style(
				{
					image: new ol.style.Icon(
					{
						anchor: [0.5, 1],
						src: '/wp-content/plugins/mf_maps/images/map-pin-svgrepo-com.svg',
						scale: 0.03
					})
				})
			});

			map.addLayer(markerPosition);

			console.log("I am here: " , myLat , myLon , transformedCoordinates);
		});
	}*/

	map.on('moveend', () => {
		var view = map.getView(),
			center = view.getCenter(),
			zoom = view.getZoom();

		/*$.ajax(
		{
			url: script_maps_map.ajax_url,
			type: 'post',
			dataType: 'json',
			data:
			{
				action: "api_maps_save_user_map",
				center: center,
				zoom: zoom,
			},
			success(data){}
		});*/

		console.log("Moved to: " , zoom , center);
	});

	/*var dom_obj_search_result = $(".widget.maps_map .map_search ul");

	$(document).on('focus', ".widget.maps_map .map_search input", function(e)
	{
		dom_obj_search_result.removeClass('hide');
	});

	var search_timeout;

	$(document).on('keyup', ".widget.maps_map .map_search input", function(e)
	{
		clearTimeout(search_timeout);

		var dom_obj = $(e.currentTarget),
			dom_obj_value = dom_obj.val();

		if(dom_obj_value.length >= 3)
		{
			search_timeout = setTimeout(function()
			{
				dom_obj_search_result.html("<li><i class='fa fa-spinner fa-spin fa-2x'></i></li>");

				$.ajax(
				{
					url: script_maps_map.ajax_url,
					type: 'post',
					dataType: 'json',
					data:
					{
						action: "api_maps_map_search",
						value: dom_obj_value,
					},
					success(data)
					{
						dom_obj_search_result.html(data.html).removeClass('hide');
					}
				});
			}, 300);
		}
	});

	var markerSearch;

	$(document).on('click', ".widget.maps_map .map_search ul > li", function(e)
	{
		var dom_obj = $(e.currentTarget),
			dom_obj_fnr = (dom_obj.data('fnr') || 0),
			dom_obj_nkoord = dom_obj.data('nkoord'),
			dom_obj_ekoord = dom_obj.data('ekoord');

		if(markerSearch)
		{
            map.removeLayer(markerSearch);
        }

		if(dom_obj_fnr > 0)
		{
			markerSearch = new ol.layer.Vector(
			{
				source: new ol.source.Vector(
				{
					features:
					[
						new ol.Feature(
						{
							geometry: new ol.geom.Point([dom_obj_ekoord, dom_obj_nkoord])
						})
					]
				}),
				style: new ol.style.Style(
				{
					image: new ol.style.Icon(
					{
						anchor: [0.5, 1],
						src: '/wp-content/plugins/mf_maps/images/map-pin-svgrepo-com.svg',
						scale: 0.03
					})
				})
			});

			map.addLayer(markerSearch);

			map.getView().setCenter([dom_obj_ekoord, dom_obj_nkoord]);
			map.getView().setZoom(12);

			console.log("Added: " , [dom_obj_ekoord, dom_obj_nkoord]);
		}
	});*/
});