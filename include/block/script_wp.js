(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl,
		InspectorControls = wp.blockEditor.InspectorControls;

	registerBlockType('mf/maps',
	{
		title: script_maps_block_wp.block_title,
		description: script_maps_block_wp.block_description,
		icon: 'admin-site',
		category: 'widgets',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'maps_latitude':
			{
                'type': 'string',
                'default': ''
            },
			'maps_longitude':
			{
                'type': 'string',
                'default': ''
            },
			'maps_zoom':
			{
                'type': 'string',
                'default': ''
            }
		},
		'supports':
		{
			'html': false,
			'multiple': false,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			}
		},
		edit: function(props)
		{
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						el(
							TextControl,
							{
								label: script_maps_block_wp.maps_latitude_label,
								type: 'text',
								value: props.attributes.maps_latitude,
								onChange: function(value)
								{
									props.setAttributes({maps_latitude: value});
								}
							}
						),
						el(
							TextControl,
							{
								label: script_maps_block_wp.maps_longitude_label,
								type: 'text',
								value: props.attributes.maps_longitude,
								onChange: function(value)
								{
									props.setAttributes({maps_longitude: value});
								}
							}
						),
						el(
							TextControl,
							{
								label: script_maps_block_wp.maps_zoom_label,
								type: 'text',
								value: props.attributes.maps_zoom,
								onChange: function(value)
								{
									props.setAttributes({maps_zoom: value});
								}
							}
						)
					),
					el(
						'strong',
						{className: props.className},
						script_maps_block_wp.block_title
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});
})();