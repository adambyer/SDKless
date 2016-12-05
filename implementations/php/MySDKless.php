<?php
require_once './sdkless/SDKless.php';

final class MySDKless extends SDKless\SDKless {

	const MERGE_OPEN = '*|'; // overwrite default if desired
	const MERGE_CLOSE = '|*';

	// local_vars can be used to provide api specific functionality that could not be standardized in SDKless
	public function go($endpoint_name, $endpoint_vars = array(), $local_vars = array()) {
		if (!is_array($local_vars))
			$local_vars = array();

		switch ($this->api_name) {
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
							$value['full_name'] = $value['first_name'] . ' ' . $value['last_name'];
							unset($value['first_name']);
							unset($value['last_name']);
						}

						return parent::go($endpoint_name, $endpoint_vars, $local_vars);
						break;
					default:
						return parent::go($endpoint_name, $endpoint_vars, $local_vars);
				}

				break;
		}

		// default handler
		return parent::go($endpoint_name, $endpoint_vars, $local_vars);
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