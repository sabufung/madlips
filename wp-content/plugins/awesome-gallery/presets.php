<?php

function asg_get_default_preset(){
	$preset = asg_get_gallery_defaults();
	unset($preset['source']);
	unset($preset['sources']);
	return $preset;
}

function asg_get_presets(){
	return array(
		array('name' => __('None', 'asg')),
		array(
			'name' => __('Default', 'asg'),
			'data' => asg_get_default_preset()
		),
		array(
			'name' => __('Flickr style', 'asg'),
			'data' => asg_parse_args(asg_get_default_preset(), array(
				'layout' => array('gap' => 7, 'height' => 320),
				'caption' => array(
					'mode' => 'on-hover'
				),
				'image' => array(
					'blur' => 'off'
				),
				'overlay' => array(
					'mode' => 'off'
				)
			))
		),
		array(
			'name' => __('500px style', 'asg'),
			'data' => asg_parse_args(asg_get_default_preset(), array(
					'layout' => array('mode' => 'usual', 'width' => 330, 'height' => 330, 'gap' => 20),
					'caption' => array(
						'mode' => 'on',
						'opacity' => 0.6,
						'font1' => array('family' => '', 'style' => '200', 'size' => 20),
						'font2' => array('family' => '', 'style' => '200', 'size' => 14)),
					'overlay' => array('mode' => 'off'),
					'shadow' => array('radius' => 3, 'color' => '#666666', 'opacity' => 0.3, 'mode' => 'on')

				)
			)
		),
		array(
			'name' => __('B&W', 'asg'),
			'data' => asg_parse_args(asg_get_default_preset(), array(
				'layout' => array('gap' => 2, 'height' => 270),
				'overlay' => array(
					'mode' => 'off-hover',
					'opacity' => 0.5
				),
				'image' => array(
					'blur' => 'off',
					'bw' => 'off-hover'
				),
			)),
		),
		array(
			'name' => __('B&W Faded', 'asg'),
			'data' => asg_parse_args(asg_get_default_preset(), array(
				'layout' => array('height' => 300),
				'caption' => array('mode' => 'on-hover'),
				'image' => array('bw' => 'on', 'blur' => 'off'),
				'overlay' => array(
					'mode' => 'off-hover'
				)
			))
		),
		array(
			'name' => __('Hover lighten', 'asg'),
			'data' => array(
				'layout' => array('gap' => 15, 'height' => 300),
				'caption' => array('mode' => 'off-hover'),
				'overlay' => array('mode' => 'off-hover', 'opacity' => 0.2),
				'border' => array(
					'color1' => '#E0E0E0',
					'width1' => 1,
					'color2' => '#F3F3F3',
					'width2' => 10
				)
			)
		),
		array(
			'name' => __('Hover blur', 'asg'),
			'data' => array(
				'layout' => array('gap' => 15),
				'caption' => array('mode' => 'on-hover'),
				'overlay' => array('mode' => 'on-hover', 'opacity' => 0.3, 'image' => asg_get_plus_image()),
				'image' => array('blur' => 'on-hover'),
				'border' => array(
					'color1' => '#E0E0E0',
					'width1' => 1,
					'color2' => '#F7F7F7',
					'width2' => 10
				)
			)
		),
		array(
			'name' => __('Vertical minimalism', 'asg'),
			'data' => array(
				'layout' => array('gap' => 1, 'mode' => 'vertical-flow'),
				'caption' => array('mode' => 'on-hover', 'opacity' => 0.6, 'position' => 'fill', 'align' => 'center',
					'font1' => array('style' => 200, 'size' => 20)),
				'image' => array('blur' => 'on-hover')
			)
		),
		array(
			'name' => __('Vertical minimalism 2', 'asg'),
			'data' => array(
				'layout' => array('gap' => 1, 'mode' => 'vertical-flow', 'width' => 300),
				'caption' => array('mode' => 'on-hover', 'opacity' => 0.6, 'position' => 'center', 'align' => 'center',
					'font1' => array('style' => 200, 'size' => 20)),
				'image' => array('blur' => 'on-hover')
			)
		)
	);
}
