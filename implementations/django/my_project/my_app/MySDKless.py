from sdkless.SDKless import SDKless

class MySDKless(SDKless):

	MERGE_OPEN = '*|' # overwrite default if desired
	MERGE_CLOSE = '|*'

	def __init__(self, api_name = None, global_vars = {}):
		super(MySDKless, self).__init__(api_name, global_vars)

	# local_vars can be used to provide api specific functionality that could not be standardized in SDKless
	def go(self, endpoint_name, endpoint_vars = {}, local_vars = {}):
		if not isinstance(local_vars, dict):
			local_vars = {}

		if self.api_name == 'CampaignMonitor':
			if endpoint_name == 'pull_list_contacts':
				# Campaign Monitor uses full name only; split into first/last
				contacts = super(MySDKless, self).go(endpoint_name, endpoint_vars, local_vars)
				new_contacts = []

				for contact in contacts:
					name_parts = contact['full_name'].split()
					last_name = name_parts.pop(-1)
					first_name = " ".join(name_parts)
					contact['first_name'] = first_name
					contact['last_name'] = last_name
					del contact['full_name']
					new_contacts.append(contact)

				return new_contacts
			elif endpoint_name == 'push_list_contacts':
				# Campaign Monitor uses full name only; concatenate first/last
				for value in endpoint_vars['array_set']['parameters']['contacts']:
					value['full_name'] = "{} {}".format(value['first_name'], value['last_name'])
					del value['first_name']
					del value['last_name']

		# default handler
		return super(MySDKless, self).go(endpoint_name, endpoint_vars, local_vars)

	def _remove_id_prefix(self, data, prefix):
		new_data = data

		if isinstance(data, str):
			new_data = data.replace(prefix, '')
		
		if isinstance(data, list):
			for record in data:
				if isinstance(record, dict) and record.get('id'):
					record['id'] = record['id'].replace(prefix, '')

				new_data.append(record)

		return new_data
