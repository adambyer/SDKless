<?php
$test_accounts = array(
	'CampaignMonitor' => array(
		'auth' => true,
		'global' => array(
			'merge' => array(
				'API-CLIENT-ID' => '###', // SDKless PHP Test
				'API-CLIENT-SECRET' => '###',
				'REDIRECT-URI' => '---',
				// 'ACCESS-TOKEN' => '###==', // refresh token process takes care of this
				'refresh_token' => '###',
			),
		),
		'endpoint' => array(
			'pull_clients' => array(),
			'pull_lists' => array(
				'merge' => array(
					'client_id' => '###', // crm supports multiple clients; this id needed for list calls
				),
			),
			'pull_list_count' => array(
				'merge' => array(
					'list_id' => '###',
				),
			),
			'pull_list_contacts' => array(
				'merge' => array(
					'list_id' => '###',
				),
				'set' => array(
					'limit' => 11,
				),
			),
			'pull_contact_is_unsubscribed' => array(
				'merge' => array(
					'client_id' => '###',
					'email_address' => '---',
				),
			),
			'pull_list_id_by_name' => array(
				'merge' => array(
					'client_id' => '###',
					'list_name' => '---',
				),
			),
			'push_list' => array(
				'merge' => array(
					'client_id' => '###',
				),
				'set' => array(
					'parameters' => array(
						'list_name' => '---',
					),
				),
			),
			'push_list_contacts' => array(
				'merge' => array(
					'list_id' => '###',
				),
				'array_set' => array(
					'parameters' => array(
						'contacts' => array(
							array(
								'email_address' => '---',
								'first_name' => '---',
								'last_name' => '---',
							),
							array(
								'email_address' => '---',
								'first_name' => '---',
								'last_name' => '---',
							),
							array(
								'email_address' => '---',
								'first_name' => '---',
								'last_name' => '---',
							),
							array(
								'email_address' => '---',
								'first_name' => '---',
								'last_name' => '---',
							),
						),
					),
				),
			),
		),
	),
	'Facebook' => array(
		'auth' => true,
		'global' => array(
			'merge' => array(
				'CLIENT-ID' => '###',
				'CLIENT-SECRET' => '###',
				'REDIRECT-URI' => '---',
				'SCOPE' => 'public_profile,email,user_likes,user_friends,user_posts',
				'ACCESS-TOKEN' => '###',
			),
		),
		'endpoint' => array(
			'pull_user' => array(
				'merge' => array(
					'USER-ID' => 'me',
				),
			),
			'pull_user_likes' => array(
				'merge' => array(
					'USER-ID' => 'me',
				),
			),
			'pull_user_feed' => array(
				'merge' => array(
					'USER-ID' => 'me',
				),
			),
		),
	),
	'MailChimp2' => array(
		'auth' => true,
		'global' => array(
			'merge' => array(
				// 'REDIRECT-URI' => '---', // testing in custom config
				'CLIENT-ID' => '###',
				'CLIENT-SECRET' => '###',
				'DATA-CENTER' => '###',
				'API-KEY' => '###', // comes from combining access_token with data_center (dc) values returned from auth steps
			),
		),
		'endpoint' => array(
			'pull_lists' => array(),
			'pull_contacts_by_email' => array(
				'set' => array(
					'parameters' => array(
						'email_address' => '---',
					),
				),
			),
			'pull_list_contacts' => array(
				'set' => array(
					'parameters' => array(
						'list_id' => '###',
					),
					'limit' => 3,
				),
			),
			'pull_list_segments' => array(
				'set' => array(
					'parameters' => array(
						'list_id' => '###',
					),
				),
			),
			'push_list_segment' => array(
				'set' => array(
					'parameters' => array(
						'list_id' => '###',
						'segment_name' => '---',
					),
				),
			),
			'push_list_contacts' => array(
				'array_set' => array(
					'parameters' => array(
						'contacts' => array(
							array(
								'email_address' => '---',
								'first_name' => '---',
								'last_name' => '---',
							),
							array(
								'email_address' => '---',
								'first_name' => '---',
								'last_name' => '---',
							),
						),
					),
				),
				'set' => array(
					'parameters' => array(
						'list_id' => '---',
					),
				),
			),
			'push_segment_contacts' => array(
				'array_set' => array(
					'parameters' => array(
						'contacts' => array(
							array(
								'email_address' => '---',
							),
							array(
								'email_address' => '---',
							),
						),
					),
				),
				'set' => array(
					'parameters' => array(
						'list_id' => '###',
						'segment_id' => '###',
					),
				),
			),
		),
	),
	'Twitter' => array(
		'auth' => true,
		'global' => array(
			'merge' => array(
				'OAUTH-CONSUMER-KEY' => '###',
				'OAUTH-CONSUMER-SECRET' => '###',
				'OAUTH-CALLBACK' => '---',
				// OAUTH-TOKEN AND OAUTH-TOKEN-SECRET MUST BE INCLUDED IN NON-AUTH-FLOW CALLS (get timeline, etc..) AND MUST BE REMOVED FOR AUTH-FLOW CALLS
				'OAUTH-TOKEN' => '###',
				'OAUTH-TOKEN-SECRET' => '###',
			),
		),
		'endpoint' => array(
			'pull_user_statuses' => array(
				'set' => array(
					'parameters' => array(
						'screen_name' => '---',
					),
				),
			),
			'pull_user_followers' => array(
				'set' => array(
					'parameters' => array(
						'screen_name' => '---',
					),
				),
			),
		),
	),
);