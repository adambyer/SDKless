TEST_ACCOUNTS = {
	'CampaignMonitor': {
		'auth': True,
		'global': {
			'merge': {
				'API-CLIENT-ID': '###',
				'API-CLIENT-SECRET': '###',
				'REDIRECT-URI': '---',
				# 'ACCESS-TOKEN': '###==', # refresh token process takes care of this
				'REFRESH-TOKEN': '###',
			},
		},
		'endpoint': {
			'pull_clients': {},
			'pull_lists': {
				'merge': {
					'client_id': '###', # crm supports multiple clients; this id needed for list calls
				},
			},
			'pull_list_count': {
				'merge': {
					'list_id': '###',
				},
			},
			'pull_list_contacts': {
				'merge': {
					'list_id': '###',
				},
				'set': {
					'limit': 11,
				},
			},
			'pull_contact_is_unsubscribed': {
				'merge': {
					'client_id': '###',
					'email_address': '---',
				},
			},
			'pull_list_id_by_name': {
				'merge': {
					'client_id': '###',
					'list_name': '---',
				},
			},
			'push_list': {
				'merge': {
					'client_id': '###',
				},
				'set': {
					'parameters': {
						'list_name': '---',
					},
				},
			},
			'push_list_contacts': {
				'merge': {
					'list_id': '###',
				},
				'array_set': {
					'parameters': {
						'contacts': [
							{
								'email_address': '---',
								'first_name': '---',
								'last_name': '---',
							},
							{
								'email_address': '---',
								'first_name': '---',
								'last_name': '---',
							},
						],
					},
				},
			},
		},
	},
	'Facebook': {
		'auth': True,
		'global': {
			'merge': {
				'CLIENT-ID': '###',
				'CLIENT-SECRET': '###',
				'REDIRECT-URI': '---', # Facebook doesn't allow 127.0.0.1
				'SCOPE': 'public_profile,email,user_likes,user_friends,user_posts',
				'ACCESS-TOKEN': '###',
			},
		},
		'endpoint': {
			'pull_user': {
				'merge': {
					'USER-ID': 'me',
				},
			},
			'pull_user_likes': {
				'merge': {
					'USER-ID': 'me',
				},
				'limit': 15,
			},
			'pull_user_feed': {
				'merge': {
					'USER-ID': 'me',
				},
				'set': {
					'limit': 12,
				},
			},
		},
	},
	'MailChimp2': {
		'auth': True,
		'global': {
			'merge': {
				# 'REDIRECT-URI' => '---', // testing in custom config
				'CLIENT-ID': '###',
				'CLIENT-SECRET': '###',
				'DATA-CENTER': '###',
				'API-KEY': '###', # comes from combining access_token with data_center (dc) values returned from auth steps
			},
		},
		'endpoint': {
			'pull_lists': {},
			'pull_contacts_by_email': {
				'set': {
					'parameters': {
						'email_address': '---',
					},
				},
			},
			'pull_list_contacts': {
				'set': {
					'parameters': {
						'list_id': '###',
					},
					# 'limit': 3,
				},
			},
			'pull_list_segments': {
				'set': {
					'parameters': {
						'list_id': '###',
					},
				},
			},
			'push_list_segment': {
				'set': {
					'parameters': {
						'list_id': '###',
						'segment_name': '---',
					},
				},
			},
			'push_list_contacts': {
				'array_set': {
					'parameters': {
						'contacts': [
							{
								'email_address': '---',
								'first_name': '---',
								'last_name': '---',
							},
							{
								'email_address': '---',
								'first_name': '---',
								'last_name': '---',
							},
						],
					},
				},
				'set': {
					'parameters': {
						'list_id': '###',
					},
				},
			},
			'push_segment_contacts': {
				'array_set': {
					'parameters': {
						'contacts': [
							{
								'email_address': '---',
							},
							{
								'email_address': '---',
							},
						],
					},
				},
				'set': {
					'parameters': {
						'list_id': '###',
						'segment_id': '###',
					},
				},
			},
		},
	},
	'MailChimp3': {
		'auth': True,
		'global': {
			'merge': {
				# 'REDIRECT-URI' => '---', // testing in custom config
				'CLIENT-ID': '###',
				'CLIENT-SECRET': '###',
				'DATA-CENTER': '###',
				'API-KEY': '###', # comes from combining access_token with data_center (dc) values returned from auth steps
				'USERNAME': 'any name',
			},
		},
		'endpoint': {
			'pull_lists': {},
			'pull_list_contacts': {
				'merge': {
					'LIST-ID': '###',
				},
				'set': {
					'parameters': {
						'count': 4,
					},
				},
			},
			'pull_list_segments': {
				'merge': {
					'LIST-ID': '###',
				},
			},
			'push_list_segment': {
				'merge': {
					'LIST-ID': '###',
				},
				'set': {
					'parameters': {
						'segment_name': '---',
					},
				},
			},
			'push_list_contacts': {
				'array_set': {
					'parameters': {
						'contacts': [
							{
								'email_address': '---',
								'first_name': '---',
								'last_name': '---',
							},
							{
								'email_address': '---',
								'first_name': '---',
								'last_name': '---',
							},
						],
					},
				},
				'merge': {
					'LIST-ID': 'b8aad53e3a',
				},
			},
			'push_segment_contacts': {
				'array_set': {
					'parameters': {
						'contacts': [
							{
								'email_address': '---',
							},
							{
								'email_address': '---',
							},
						],
					},
				},
				'merge': {
					'LIST-ID': '###',
					'SEGMENT-ID': '###',
				},
			},
		},
	},
	'Twitter': {
		'auth': True,
		'global': {
			'merge': {
				'OAUTH-CONSUMER-KEY': '---',
				'OAUTH-CONSUMER-SECRET': '---',
				'OAUTH-CALLBACK': '---',
				# OAUTH-TOKEN AND OAUTH-TOKEN-SECRET MUST BE INCLUDED IN NON-AUTH-FLOW CALLS (get timeline, etc..) AND MUST BE REMOVED FOR AUTH-FLOW CALLS
				# 'OAUTH-TOKEN': '###',
				# 'OAUTH-TOKEN-SECRET': '###',
			},
		},
		'endpoint': {
			'pull_user_statuses': {
				'set': {
					'parameters': {
						'screen_name': '---',
					},
				},
			},
			'pull_user_followers': {
				'set': {
					'parameters': {
						'screen_name': '---',
					},
				},
			},
		},
	},
}