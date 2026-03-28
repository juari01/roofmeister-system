<?php

	$navigation = [
		[
			'name'     => 'Dashboard',
			'function' => 'load_page( "dashboard" ); navbar_display_none();',
			'icon'     => '',
			'security' => ''
		],
		[
			'name'     => 'Customer',
			'function' => 'load_page( "customer", {}, customer.init_actions ); navbar_display_none();',
			'icon'     => '',
			'security' => ''
		],
		[
			'name'     => 'Property',
			'function' => 'load_page( "property", {}, property.init_actions ); navbar_display_none();',
			'icon'     => '',
			'security' => ''
		],
		[
			'name'     => 'Projects',
			'function' => 'load_page( "projects",  {}, project.init_actions ); navbar_display_none();',
			'icon'     => '',
			'security' => ''
		],
		[
			'name'     => 'Appointments',
			'function' => 'load_page( "appointments",  {}, appointment.init_actions ); navbar_display_none();',
			'icon'     => '',
			'security' => ''
		],
		[
			'name'     => 'Files',
			'function' => 'load_page( "files",  {}, files.init_actions ); navbar_display_none();',
			'icon'     => '',
			'security' => ''
		],
		[
			'name'     => 'Administration',
			'function' => '',
			'icon'     => '',
			'security' => 'admin',
			'children' => [
				[
					'name'     => 'Users',
					'function' => 'load_page( "admin/users", {}, users.init_actions ); navbar_display_none();',
					'icon'     => 'image.png',
					'security' => 'admin'
				],
				[
					'name'     => 'Groups',
					'function' => 'load_page( "admin/groups", {}, groups.init_actions ); navbar_display_none();',
					'icon'     => 'image.png',
					'security' => 'admin'
				],
				[
					'name'     => 'Calendar',
				    'function' => 'load_page( "admin/calendars", {}, calendars.init_actions ); navbar_display_none();',
					'icon'     => 'image.png',
					'security' => 'admin'
				],
				[
					'name'     => 'Appointment Types',
				    'function' => 'load_page( "admin/appointmenttypes", {}, appointmenttypes.init_actions ); navbar_display_none();',
					'icon'     => 'image.png',
					'security' => 'admin'
				],
				[
					'name'     => 'Property Types',
				    'function' => 'load_page( "admin/propertytypes", {}, propertytypes.init_actions ); navbar_display_none();',
					'icon'     => 'image.png',
					'security' => 'admin'
				],
				[
					'name'     => 'Paths',
				    'function' => 'load_page( "admin/paths", {}, paths.init_actions); navbar_display_none();',
					'icon'     => 'image.png',
					'security' => 'admin'
				],
			]
		]
	];

?>
