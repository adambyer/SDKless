<?php
$test_accounts = array(
	'ActionNetwork' => array(
		'global' => array(
			'merge' => array(
				'API-KEY' => '****',
			),
		),
		'endpoint' => array(
			'pull_export_lists' => array(),
			'pull_list_count' => array(),
			'pull_list_contacts' => array(
				'merge' => array(
					'list_id' => '****',
				),
			),
			'pull_list_id_by_name' => array(
				'merge' => array(
					'list_name' => '****',
				),
			),
			'push_list' => array(
				'set' => array(
					'parameters' => array(
						'list_name' => '****',
					),
				),
			),
			'push_list_contacts' => array(
				'merge' => array(
					'list_id' => '****',
				),
				'array_set' => array(
					'parameters' => array(
						'contacts' => array(
							array(
								'EMAIL' => 'test-one@test.com',
								'FNAME' => 'test',
								'LNAME' => 'one',
								'ZIP' => 0, // zip is currently required by the API but will not be in next version
							),
							array(
								'EMAIL' => 'test-two@test.com',
								'FNAME' => 'test',
								'LNAME' => 'two',
								'ZIP' => 12345,
							),
						),
					),
				),
			),
		),
	),
	'ActiveCampaign' => array(
		'global' => array(
			'merge' => array(
				'DOMAIN' => '****',
				'API-KEY' => '****',
			),
		),
		'endpoint' => array(
			'pull_import_lists' => array(),
			'pull_list_contacts' => array(
				'set' => array(
					'parameters' => array(
						'list_id' => '****',
					),
				),
			),
		),
	),
	'CampaignMonitor' => array(
		'global' => array(
			'merge' => array(
				'API-CLIENT-ID' => '****',
				'API-CLIENT-SECRET' => '****',
				'REDIRECT-URI' => '****',
				'refresh_token' => '****',
			),
		),
		'endpoint' => array(
			'pull_clients' => array(),
			'pull_import_lists' => array(
				'merge' => array(
					'client_id' => '****',
				),
			),
			'pull_list_count' => array(
				'merge' => array(
					'list_id' => '****',
				),
			),
			'pull_list_contacts' => array(
				'merge' => array(
					'list_id' => '****',
				),
				'set' => array(
					'limit' => 15,
				),
			),
			'pull_contact_is_unsubscribed' => array(
				'merge' => array(
					'client_id' => '****', // crm supports multiple clients; this id needed for list calls
					'email_address' => '****',
				),
			),
			'pull_list_id_by_name' => array(
				'merge' => array(
					'client_id' => '****',
					'list_name' => '****',
				),
			),
			'push_list' => array(
				'merge' => array(
					'client_id' => '****',
				),
				'set' => array(
					'parameters' => array(
						'list_name' => '****',
					),
				),
			),
			'push_list_contacts' => array(
				'merge' => array(
					'list_id' => '****',
				),
				'array_set' => array(
					'parameters' => array(
						'contacts' => array(
							array(
								'EMAIL' => 'test-one@test.com',
								'FNAME' => 'test',
								'LNAME' => 'one',
							),
							array(
								'EMAIL' => 'test-two@test.com',
								'FNAME' => 'test',
								'LNAME' => 'two',
							),
						),
					),
				),
			),
		),
	),
	'ConstantContact' => array(
		'global' => array(
			'merge' => array(
				'CLIENT-ID' => '****',
				'CLIENT-SECRET' => '****',
				'REDIRECT-URI' => '****',
				'ACCESS-TOKEN' => '****',
			),
		),
		'endpoint' => array(
			'pull_list_count' => array(
				'merge' => array(
					'list_id' => '****',
				),
			),
			'pull_list_contacts' => array(
				'merge' => array(
					'LIST-ID' => '****',
				),
				'set' => array(
					'limit' => 10,
				),
			),
			'pull_list_id_by_name' => array(
				'merge' => array(
					'list_name' => '****',
				),
			),
			'push_list' => array(
				'set' => array(
					'parameters' => array(
						'list_name' => '****',
					),
				),
			),
			'pull_contact_is_unsubscribed' => array(
				'set' => array(
					'parameters' => array(
						'email_address' => '****',
					),
				),
			),
		),
		'local' => array(
			'list_name' => '****',
		),
	),
	'Facebook' => array(
		'global' => array(
			'merge' => array(
				'CLIENT-ID' => '****',
				'CLIENT-SECRET' => '****',
				'REDIRECT-URI' => '****',
				'SCOPE' => 'public_profile,email,user_likes,user_friends,read_stream',
				'ACCESS-TOKEN' => '****',
			),
		),
		'endpoint' => array(
			'pull_user' => array(
				'merge' => array(
					'USER-ID' => '****',
				),
			),
			'pull_user_friends' => array(
				'merge' => array(
					'USER-ID' => '****',
				),
			),
			'pull_user_feed' => array(
				'merge' => array(
					'USER-ID' => '****',
				),
			),
		),
	),
	'MailChimp' => array(
		'global' => array(
			'merge' => array(
			  //'REDIRECT-URI' => '****', // can also be in custom config
			  'CLIENT-ID' => '****',
			  'CLIENT-SECRET' => '****',
				'DATA-CENTER' => '****',
				'API-KEY' => '****', // comes from combining access_token with data_center (dc) values returned from auth steps
			),
		),
		'endpoint' => array(
			'pull_import_lists' => array(),
			'pull_contacts_by_email' => array(
				'set' => array(
					'parameters' => array(
						'email_address' => '****',
					),
				),
			),
			'pull_list_contacts' => array(
				'set' => array(
					'parameters' => array(
						'list_id' => '****',
					),
					'limit' => 3,
				),
			),
			'pull_list_segments' => array(
				'set' => array(
					'parameters' => array(
						'list_id' => '****',
					),
				),
			),
			'push_list_segment' => array(
				'set' => array(
					'parameters' => array(
						'list_id' => '****',
						'segment_name' => '****',
					),
				),
			),
			'push_list_contacts' => array(
				'array_set' => array(
					'parameters' => array(
						'contacts' => array(
							array(
								'email_address' => 'test-one@test.com',
								'first_name' => 'test',
								'last_name' => 'one',
							),
							array(
								'email_address' => 'test-two@test.com',
								'first_name' => 'test',
								'last_name' => 'two',
							),
						),
					),
				),
				'set' => array(
					'parameters' => array(
						'list_id' => '****',
					),
				),
			),
			'push_segment_contacts' => array(
				'array_set' => array(
					'parameters' => array(
						'contacts' => array(
							array(
								'email_address' => 'test-one@test.com',
							),
							array(
								'email_address' => 'test-two@test.com',
							),
						),
					),
				),
				'set' => array(
					'parameters' => array(
						'list_id' => '****',
						'segment_id' => '****',
					),
				),
			),
		),
	),
	'Salsa' => array(
		'global' => array(
			'merge' => array(
				'DOMAIN' => '****',
				'EMAIL' => '****',
				'PASSWORD' => '****',
			),
		),
		'endpoint' => array(
			'pull_list_contacts' => array(
				'merge' => array(
					'LIST-ID' => '****',
				),
				'set' => array(
					'parameters' => array(
						'limit' => 30,
					),
				),
			),
		),
		'local' => array(
			'page_number' => 1,
			'page_size' => 50,
			'cookie_id' => '****', // this would probably be the client-id; used to create an auth cookie name specific to this client
		),
	),
	'Twitter' => array(
		'global' => array(
			'merge' => array(
				'OAUTH-CONSUMER-KEY' => '****',
				'OAUTH-CONSUMER-SECRET' => '****',
				'OAUTH-CALLBACK' => '****',
				//'OAUTH-TOKEN' => '****', // non-auth calls
				//'OAUTH-TOKEN-SECRET' => '****', // non-auth calls
			),
		),
		'endpoint' => array(
			'pull_user_statuses' => array(
				'set' => array(
					'parameters' => array(
						'screen_name' => '****',
					),
				),
			),
		),
	),
);