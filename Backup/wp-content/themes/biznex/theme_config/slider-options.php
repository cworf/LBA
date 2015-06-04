<?php

return array(
	'biznex_main' => array(
		'name' => 'Main Slider',
		'term' => 'slide',
		'term_plural' => 'slides',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the slide.',
				'title' => 'Image',
				'default' => 'holder.js/1400x820/auto'
			),
			'subtitle' => array(
				'type' => 'line',
				'description' => 'This will be shown under the title.',
				'title' => 'Subtitle'
			),
			'description' => array(
				'type' => 'text',
				'description' => 'Description of the slider.',
				'title' => 'Description'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_main_slider',
				'view' => 'views/main_slider_view',
				'shortcode_defaults' => array(
					'section_id' => '',
					'height' => ''
				)
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_testimonials' => array(
		'name' => 'Testimonials',
		'term' => 'testimonial',
		'term_plural' => 'testimonials',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the author',
				'title' => 'Image',
				'default' => 'holder.js/1400x820/auto'
			),
			'text' => array(
				'type' => 'text',
				'description' => 'The text of thetestimonial.',
				'title' => 'Text'
			),
			'author' => array(
				'type' => 'line',
				'description' => 'Author of the testimonial.',
				'title' => 'Author'
			),
			'company' => array(
				'type' => 'line',
				'description' => 'Auhtor\'s company',
				'title' => 'Company'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_testimonials',
				'view' => 'views/testimonials_view',
				'shortcode_defaults' => array(
					'nr' => 0
				)
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_processes' => array(
		'name' => 'Processes',
		'term' => 'process',
		'term_plural' => 'processes',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the process',
				'title' => 'Image',
				'default' => 'holder.js/180x180/auto'
			),
			'description' => array(
				'type' => 'text',
				'description' => 'Description of the process',
				'title' => 'Description'
			),
			'url' => array(
				'type' => 'line',
				'description' => 'Set the full URL that will be applied to the item\'s link.',
				'title' => 'URL (optional)'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_processes',
				'view' => 'views/processes_view',
				'shortcode_defaults' => array(
					'nr' => 0
				)
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_team' => array(
		'name' => 'Team',
		'term' => 'team member',
		'term_plural' => 'team members',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the team member',
				'title' => 'Image',
				'default' => 'holder.js/180x180/auto'
			),
			'position' => array(
				'type' => 'line',
				'description' => 'Position of the team member',
				'title' => 'Position'
			),
			'description' => array(
				'type' => 'text',
				'description' => 'Description of the team member',
				'title' => 'Description'
			),
			'facebook' => array(
				'type' => 'line',
				'description' => 'Enter the full URL of the Facebook page (it should start with http:// or https://)',
				'title' => 'Facebook'
			),
			'twitter' => array(
				'type' => 'line',
				'description' => 'Enter the full URL of the Twitter page (it should start with http:// or https://)',
				'title' => 'Twitter'
			),
			'dribbble' => array(
				'type' => 'line',
				'description' => 'Enter the full URL of the Dribbble page (it should start with http:// or https://)',
				'title' => 'Dribbble'
			),
			'linkedin' => array(
				'type' => 'line',
				'description' => 'Enter the full URL of the Linkedin page (it should start with http:// or https://)',
				'title' => 'Linkedin'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_team',
				'view' => 'views/team_view',
				'shortcode_defaults' => array(
					'nr' => 0,
					'description' => false
				)
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_skills' => array(
		'name' => 'Skills',
		'term' => 'skill',
		'term_plural' => 'skills',
		'order' => 'ASC',
		'options' => array(
			'value' => array(
				'type' => 'line',
				'description' => 'Value of the skill. Enter a value between 0-100.',
				'title' => 'Value'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_skills',
				'view' => 'views/skills_view',
				'shortcode_defaults' => array(
					'nr' => 0
				)
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_clients' => array(
		'name' => 'Clients',
		'term' => 'client',
		'term_plural' => 'clients',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the client',
				'title' => 'Image',
				'default' => 'holder.js/200x90/auto'
			),
			'url' => array(
				'type' => 'line',
				'description' => '(Optional) Full URL of the client',
				'title' => 'URL'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_clients',
				'view' => 'views/clients_view'
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_services' => array(
		'name' => 'Services',
		'term' => 'service',
		'term_plural' => 'services',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the service.',
				'title' => 'Image',
				'default' => 'holder.js/200x90/auto'
			),
			'description' => array(
				'type' => 'text',
				'description' => 'Description of the service.',
				'title' => 'Description'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_services',
				'view' => 'views/services_view',
				'shortcode_defaults' => array(
					'nr' => 0
				)
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_solutions' => array(
		'name' => 'Solutions',
		'term' => 'solution',
		'term_plural' => 'solutions',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the solution',
				'title' => 'Image',
				'default' => 'holder.js/455x259/auto'
			),
			'description' => array(
				'type' => 'text',
				'description' => 'Description of the solution',
				'title' => 'Description'
			),
			'highlights' => array(
				'type' => array(
					'item' => array(
						'type' => 'line'
					)
				),
				'description' => 'A list of highlights',
				'title' => 'Highlights',
				'multiple' => true
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_solutions',
				'view' => 'views/solutions_view',
				'shortcode_defaults' => array(
					'nr' => 0
				)
			)
		),
		'icon' => '../images/favicon.ico'
	),
	'biznex_portfolio' => array(
		'name' => 'Portfolio',
		'term' => 'portfolio item',
		'term_plural' => 'portfolio items',
		'order' => 'ASC',
		'options' => array(
			'description' => array(
				'type' => 'text',
				'description' => 'Description of the item',
				'title' => 'Description'
			),
			'small_image' => array(
				'type' => 'image',
				'description' => 'This image will be shown in the portfolio section.',
				'title' => 'Small image',
				'default' => 'holder.js/280x200/auto'
			),
			'lightbox' => array(
				'type' => array(
					'image' => array(
						'type' => 'image',
						'description' => 'Select an image.',
						'title' => 'Big image',
						'default' => 'holder.js/455x259/auto'
					),
					'video' => array(
						'type' => 'line',
						'description' => 'Paste the URL of a Youtube or Vimeo video here.',
						'title' => 'Video'
					)
				),
				'title' => 'Fullscreen frame',
				'description' => 'Set the a gallery of images and videos that will appear in a fullscreen frame when you click the zoom icon of the item.',
				'group' => false,
				'multiple' => true
			),
			'url' => array(
				'type' => 'line',
				'description' => 'Full URL of the project. It should start with http:// or https://.',
				'title' => 'URL',
				'default' => ''
			),
			'urltarget' => array(
				'type' => 'checkbox',
				'label' => array('new-window'=>'New window'),
				'description' => 'Check this option to open the item\'s URL in a new window.',
				'title' => 'URL target',
				'default' => ''
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'biznex_portfolio',
				'view' => 'views/portfolio_view',
				'shortcode_defaults' => array(
					'nr' => 0
				)
			)
		),
		'icon' => '../images/favicon.ico'
	)
);