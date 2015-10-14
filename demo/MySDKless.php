<?php
require_once '../core/SDKless.php';

final class MySDKless extends SDKless\SDKless {

	const MERGE_OPEN = '*|'; // overwrite default if desired
	const MERGE_CLOSE = '|*';

	// local_vars can be used to provide api specific functionality that could not be standardized in SDKless
	public function go($endpoint_name, $endpoint_vars = array(), $local_vars = array()) {
		if (!is_array($local_vars))
			$local_vars = array();

		switch ($this->api_name) {
			case 'ActionNetwork':
				switch ($endpoint_name) {
					// ActionNetwork does not provide an endpoint to get people belonging to a form, w/ name/email
					// - the get_people endpoint includes name/email but can't be filtered by form id
					// - the get_form_submissions is filtered by form id but only returns person id, not name/email
					// - so here we get the person ids first then get name/email for each id
					case 'pull_list_contacts':
						$contacts = parent::go('pull_list_contacts_ids', $endpoint_vars, $local_vars);
						$data = array();

						foreach ($contacts as $c) {
							$endpoint_vars = array(
								'merge' => array(
									'contact_id' => $c['id']
								),
							);

							$contact = parent::go('pull_contact', $endpoint_vars, $local_vars);
							$data[] = $contact;
						}

						break;
					case 'push_list_contacts':
						// ActionNetwork does not support batch creates; call endpoint for each contact
						$contacts = $endpoint_vars['array_set']['parameters']['contacts'];
						$data = array();
						unset($endpoint_vars['array_set']);

						foreach ($contacts as $contact) {
							$endpoint_vars['merge']['FIRST-NAME'] = $contact['FNAME'];
							$endpoint_vars['merge']['LAST-NAME'] = $contact['LNAME'];
							$endpoint_vars['merge']['EMAIL-ADDRESS'] = $contact['EMAIL'];
							// zip is currently required by the API but will not be in next version
							$endpoint_vars['merge']['ZIP-CODE'] = (empty($contact['ZIP'])? 0 : $contact['ZIP']);
							$data[] = parent::go('push_list_contact', $endpoint_vars, $local_vars);
						}

						break;
					default:
						$data = parent::go($endpoint_name, $endpoint_vars, $local_vars);
				}

				// remove ActionNetwork id prefixes
				$this->remove_id_prefix($data, 'action_network:');
				return $data;
				break;
			case 'CampaignMonitor':
				switch ($endpoint_name) {
					case 'pull_list_contacts':
						// Campaign Monitor uses full name only; split into first/last
						$contacts = parent::go($endpoint_name, $endpoint_vars, $local_vars);
						
						foreach ($contacts as &$contact) {
							$name_parts = explode(' ', $contact['full_name']);
							$last_name = array_pop($name_parts);
							$first_name = implode(' ', $name_parts);
							$contact['first_name'] = $first_name;
							$contact['last_name'] = $last_name;
							unset($contact['full_name']);
						}

						return $contacts;
						break;
					case 'push_list_contacts':
						// Campaign Monitor uses full name only; concatenate first/last
						foreach ($endpoint_vars['array_set']['parameters']['contacts'] as &$value) {
							$value['full_name'] = $value['FNAME'] . ' ' . $value['LNAME'];
							unset($value['FNAME']);
							unset($value['LNAME']);
						}

						return parent::go($endpoint_name, $endpoint_vars, $local_vars);
						break;
					default:
						return parent::go($endpoint_name, $endpoint_vars, $local_vars);
				}

				break;
			case 'Salsa':
				// paging in Salsa is done w/ a limit parameter in the format of [offset]/count
				// offset is zero-based and optional
				// this demo app uses page_number/page_size (passed in $local_vars)
				// here we convert them to the Salsa limit format
				if (empty($local_vars['page_size'])) {
					return parent::go($endpoint_name, $endpoint_vars, $local_vars);
				} else {
					$page_size = $local_vars['page_size'];
					$page_number = (empty($local_vars['page_number'])? 1 : $local_vars['page_number']);
					$output = array();

					// paging loop
					do {
						$offset = ($page_size * ($page_number - 1));
						$endpoint_vars['parameters']['limit'] = "$offset,$page_size";
						$response = parent::go($endpoint_name, $endpoint_vars, $local_vars);
						$output = array_merge($output, $response);
						$page_number++;
					} while (!empty($response) && (count($response) == $page_size));

					return $output;
				}

				break;
		}

		// default
		return parent::go($endpoint_name, $endpoint_vars, $local_vars);
	}

	protected function set_uri() {
		parent::set_uri();

		// ConstantContact uses cursor paging but the next cursor value doesn't include the api_key which is required in all calls
		if ($this->api_name == 'ConstantContact') {
			$uri_parts = explode('?', $this->uri);
			$uri = $uri_parts[0];
			$params = $this->get_endpoint_setting('parameters');

			if (!empty($params->api_key) && $this->is_merged($params->api_key)) {
				if (empty($uri_parts[1])) {
					$this->uri .= "?api_key={$params->api_key}";
				} else {
					parse_str($uri_parts[1], $query_params);

					if (empty($query_params['api_key']))
						$this->uri .= "&api_key={$params->api_key}";
				}
			}
		}
	}

	private function remove_id_prefix(&$data, $prefix) {
		if (is_scalar($data))
			$data = str_replace($prefix, '', $data);
		
		if (is_array($data)) {
			foreach ($data as &$record) {
				if (is_array($record) && !empty($record['id']))
					$record['id'] = str_replace($prefix, '', $record['id']);

				if (is_object($record) && !empty($record->id))
					$record->id = str_replace($prefix, '', $record->id);
			}
		}
	}

}