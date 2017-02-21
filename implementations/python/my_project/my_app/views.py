import json
import collections
import copy
#
from django.shortcuts import redirect, render
from django.http import Http404
from django.conf import settings
#
from test_accounts import TEST_ACCOUNTS
from sdkless.sdkless import SDKless
from my_app.my_sdkless import MySDKless

def index(request):
	test_accounts = collections.OrderedDict(sorted(TEST_ACCOUNTS.items()))

	if not settings.DEBUG:
		del test_accounts['Facebook']

	endpoints = collections.OrderedDict()

	endpoints['pull_contact_is_unsubscribed'] = 'Pull Contact Is Unsubscribed'
	endpoints['pull_clients'] = 'Pull Clients'

	endpoints['pull_list_count'] = 'Pull List Count'
	endpoints['pull_list_contacts'] = 'Pull List Contacts'
	endpoints['pull_list_id_by_name'] = 'Pull List ID By Name'
	endpoints['pull_list_segments'] = 'Pull List Segments'

	endpoints['pull_lists'] = 'Pull Lists'
	
	endpoints['pull_user'] = 'Pull User'
	endpoints['pull_user_feed'] = 'Pull User Feed'
	endpoints['pull_user_likes'] = 'Pull User Likes'
	endpoints['pull_user_statuses'] = 'Pull User Statuses'
	endpoints['pull_user_followers'] = 'Pull User Followers'

	endpoints['push_list'] = 'Push List'
	endpoints['push_list_contacts'] = 'Push List Contacts'
	endpoints['push_list_segment'] = 'Push List Segment'
	endpoints['push_segment_contacts'] = 'Push Segment Contacts'

	if not settings.DEBUG:
		del endpoints['pull_user_followers']
		del endpoints['push_list']
		del endpoints['push_list_contacts']
		del endpoints['push_list_segment']
		del endpoints['push_segment_contacts']

	config = {}
	custom_config = {}
	global_vars = {}
	endpoint_vars = {}
	responses = {}
	output = {}
	selected_api = request.GET.get('api')
	selected_endpoint = request.GET.get('endpoint')
	error = None

	if selected_endpoint and selected_endpoint not in endpoints:
		raise Http404('Invalid Endpoint')

	if selected_api:
		global_vars = TEST_ACCOUNTS[selected_api].get('global');

		if TEST_ACCOUNTS[selected_api].get('endpoint'):
			endpoint_vars = TEST_ACCOUNTS[selected_api]['endpoint'].get(selected_endpoint)
		else:
			endpoint_vars = None

		local_vars = TEST_ACCOUNTS[selected_api].get('local')

		try:
			sdkless = MySDKless(selected_api, global_vars);
			output = sdkless.go(selected_endpoint, endpoint_vars, local_vars)
			config = sdkless.config.settings
			custom_config = sdkless.config.settings_custom
			responses = sdkless.request.responses
		except Exception as e:
			error = repr(e)
	
	config_output = copy.deepcopy(config)
	custom_config_output = copy.deepcopy(custom_config)
	global_vars_output = copy.deepcopy(global_vars)
	endpoint_vars_output = copy.deepcopy(endpoint_vars)
	responses_output = copy.deepcopy(responses)
	output_output = copy.deepcopy(output)

	if not settings.DEBUG:
		obfuscate(config_output)
		obfuscate(custom_config_output)
		obfuscate(global_vars_output)
		obfuscate(endpoint_vars_output)
		obfuscate(responses_output)
		obfuscate(output_output)

	# using list of tuples to retain order
	sdkless_vars = [
		('CONFIG', json.dumps(config_output)),
		('CUSTOM CONFIG', json.dumps(custom_config_output)),
		('GLOBAL VARS', json.dumps(global_vars_output)),
		('ENDPOINT VARS', json.dumps(endpoint_vars_output)),
		('RESPONSES', json.dumps(responses_output)),
		('OUTPUT', json.dumps(output_output)),
	]

	context = {
		'test_accounts': test_accounts,
		'endpoints': endpoints,
		'selected_api': selected_api,
		'selected_endpoint': selected_endpoint,
		'sdkless_vars': sdkless_vars,
		'error': error,
	}

	return render(request, 'my_app/index.html', context)

def auth(request):
	test_accounts = collections.OrderedDict(sorted({key:value for key, value in TEST_ACCOUNTS.items() if value.get('auth')}.items()))
	do_auth = False
	done = False
	step_id = 0
	global_vars = {}
	config = {}
	custom_config = {}
	params = {}
	step_responses = {}
	step_params = {}
	step_outputs = {}
	selected_api = None
	error = None

	if request.GET:
		selected_api = request.GET['api']
		global_vars = test_accounts[selected_api]['global'];

		try:
			sdkless = MySDKless(selected_api, global_vars)
			do_auth = True
		except Exception as e:
			error = str(e)

		# any incoming params other than 'go' and 'api' is assumed to be from a redirect back from the api
		# set step_id to -1 which will cause a lookup of the redirect step in sdkless.authenticate
		for key, value in request.GET.items():
			if key not in ('go', 'api'):
				step_id = -1
				break

		if not request.GET.get('go'):
			params = request.GET

	if do_auth:
		while not done:
			try:
				output = sdkless.authenticate(step_id, params)
				step_id = output.get('step_id')
				done = output.get('done')
				params = output.get('params')
				redirect_uri = output.get('redirect')

				if done:
					break

				step_params[str(step_id)] = params
				step_outputs[str(step_id)] = output
				step_responses[str(step_id)] = sdkless.request.responses

				if redirect_uri:
					done = True
					return redirect(redirect_uri)
					break
			except Exception as e:
				error = str(e)
				break

			step_id += 1

		config = sdkless.config.settings
		custom_config = sdkless.config.settings_custom

	config_output = copy.deepcopy(config)
	custom_config_output = copy.deepcopy(custom_config)
	global_vars_output = copy.deepcopy(global_vars)
	step_params_output = copy.deepcopy(step_params)
	step_responses_output = copy.deepcopy(step_responses)
	step_outputs_output = copy.deepcopy(step_outputs)

	if not settings.DEBUG:
		obfuscate(config_output)
		obfuscate(custom_config_output)
		obfuscate(global_vars_output)
		obfuscate(step_params_output)
		obfuscate(step_responses_output)
		obfuscate(step_outputs_output)

	# using list of tuples to retain order
	sdkless_vars = [
		('CONFIG', json.dumps(config_output)),
		('CUSTOM CONFIG', json.dumps(custom_config_output)),
		('GLOBAL VARS', json.dumps(global_vars_output)),
		('STEP PARAMS', json.dumps(step_params_output)),
		('STEP RESPONSES', json.dumps(step_responses_output)),
		('STEP OUTPUTS', json.dumps(step_outputs_output)),
	]
	context = {
		'test_accounts': test_accounts,
		'selected_api': selected_api,
		'sdkless_vars': sdkless_vars,
		'error': error,
	}

	return render(request, 'my_app/auth.html', context)

def obfuscate(struct):
	if type(struct) is list:
		for key, value in enumerate(struct):
			if type(value) is list or type(value) is dict:
				obfuscate(value)
			elif value and (type(value) is str or type(value) is unicode) and value.lower()[0:4] == 'http':
				struct[key] = '####'

	if type(struct) is dict:
		for key, value in struct.items():
			if (
					key.lower()[-2:] == 'id'
					or key.lower()[-3:] == 'uri'
					or key.lower() == 'user'
					or 'accountname' in key.lower()
					or 'email' in key.lower()
					or 'login_name' in key.lower()
					or 'screen_name' in key.lower()
					or 'auth' in key.lower()
					or 'code' in key.lower()
					or 'token' in key.lower()
					or 'secret' in key.lower()
					or 'key' in key.lower()):
				struct[key] = '####'
			elif type(value) is list or type(value) is dict:
				obfuscate(value)
			elif value and (type(value) is str or type(value) is unicode) and value.lower()[0:4] == 'http':
				struct[key] = '####'
