function toMapCoord(lat, lon)
{
    return ol.proj.fromLonLat([lon, lat], 'EPSG:3006');
}

function fromMapCoord(mapCoord)
{
    return ol.proj.toLonLat(mapCoord, 'EPSG:3006');
}

function add_marker(data)
{
    if(!data.pos) data.pos = [];

    if(data.pos[0] != null && typeof data.pos[0] === 'string')
	{
        const lat = parseFloat(data.pos[0]);
        const lon = parseFloat(data.pos[1]);
        data.pos = toMapCoord(lat, lon);
    }

    let mapPoint = new ol.geom.Point(data.pos);
    let feature = new ol.Feature({
        geometry: mapPoint
    });

    feature.set('name', data.name || 'Marker');
    feature.set('text', data.text || 'No description');
    /*feature.set('info', data.info || 'Click for details');*/

    if(typeof markerSource === 'undefined')
	{
        markerSource = new ol.source.Vector();
        markerLayer = new ol.layer.Vector({
            source: markerSource,
            style: new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 1],
                    src: '/wp-content/plugins/mf_maps/images/map_pin_blue.svg',
                    scale: 0.03
                })
            })
        });
        map.addLayer(markerLayer);
    }

    markerSource.addFeature(feature);
}

function zoom_to_markers()
{
	const extent = markerSource.getExtent();

	map.getView().fit(extent,
	{
		duration: 1000,
		maxZoom: 10
	});
}

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

var map = new ol.Map(
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

/*console.log("Initiated: " , script_maps_map.zoom , script_maps_map.center);*/

/*add_marker({
	pos: script_maps_map.center,
	name: 'Map Center',
	text: 'Description',
});*/

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

/*map.on('moveend', () => {
	var view = map.getView(),
		center = view.getCenter(),
		zoom = view.getZoom();

	$.ajax(
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
	});

	console.log("Moved to: " , zoom , center);
});*/

var dom_obj_container = document.querySelector(".widget.maps_map"),
	dom_obj_popup = dom_obj_container.querySelector(".map_marker_dialog"),
	dom_obj_popup_closer = dom_obj_popup.querySelector("a");

const popupOverlay = new ol.Overlay(
{
	element: dom_obj_popup,
	autoPan: true,
	autoPanAnimation: {
		duration: 250
	}
});
map.addOverlay(popupOverlay);

dom_obj_popup_closer.onclick = function()
{
	popupOverlay.setPosition(undefined);
	dom_obj_popup_closer.blur();

	return false;
};

map.on('singleclick', function(evt)
{
	const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature)
	{
		return feature;
	});

	if(feature)
	{
		const coordinates = feature.getGeometry().getCoordinates();
		const name = feature.get('name');
		const text = feature.get('text');

		dom_obj_popup.querySelector("div").innerHTML = "<h4>" + name + "</h4><p>" + text + "</p>"; /*<small>Position: " + coordinates + "</small>*/
		
		popupOverlay.setPosition(coordinates);
	}

	else
	{
		popupOverlay.setPosition(undefined);
	}
});