<?php
namespace SDKless;

require_once 'Utilities.php';
require_once 'Exception.php';

class Configuration {
	
	public $json = null;
	public $json_custom = null;
	public $settings = null;
	public $settings_custom = null;
	public $method = null;
	public $custom_endpoint_name = null;
	public $actual_endpoint_name = null;

	private $merge_open = null;
	private $merge_close = null;
	private $settings_unmerged = null;
	private $settings_custom_unmerged = null;

	public function __construct($merge_open, $merge_close) {
		$this->merge_open = $merge_open;
		$this->merge_close = $merge_close;
	}

	public function setup() {
		$settings = json_decode($this->json);

		if (empty($settings))
			throw new SDKlessException("invalid JSON in config file");

		$this->settings = $settings;

		// custom config is optional
		if ($this->json_custom === false)
			return;

		$settings = json_decode($this->json_custom);

		if (empty($settings))
			throw new SDKlessException("invalid JSON in custom config file");

		$this->settings_custom = $settings;
	}

	public function set_actual_endpoint_name() {
		// if passed endpoint_name is mapped in custom config, set to maps_to name 
		if (empty($this->settings_custom->endpoints->{$this->custom_endpoint_name}->maps_to))
			$this->actual_endpoint_name = $this->custom_endpoint_name;
		else
			$this->actual_endpoint_name = $this->settings_custom->endpoints->{$this->custom_endpoint_name}->maps_to;
	}

	public function set_method() {
		$method = 'get';

		if (!empty($this->settings->common_endpoint_settings->all->method))
			$method = $this->settings->common_endpoint_settings->all->method;

		if (!empty($this->settings->endpoints->{$this->actual_endpoint_name}->method))
			$method = $this->settings->endpoints->{$this->actual_endpoint_name}->method;

		$this->method = $method;
		return $method;
	}

	// populate settings with values from settings_custom
	public function apply_custom_global_vars() {
		if (!empty($this->settings_custom->global->merge)) {
			$settings = json_encode($this->settings);

			foreach ($this->settings_custom->global->merge as $key => $value)
				$settings = str_replace("{$this->merge_open}$key{$this->merge_close}", $value, $settings);

			$settings = json_decode($settings);

			if (empty($settings))
				throw new SDKlessException("invalid JSON caused by custom config global merge");

			$this->settings = $settings;
		}

		if (!empty($this->settings_custom->global->set)) {
			foreach ($this->settings_custom->global->set as $key => $value)
				$this->settings->$key = $value;
		}
	}

	public function apply_custom_endpoint_params() {
		if (!empty($this->settings_custom->endpoints->{$this->custom_endpoint_name})) {
			$custom_endpoint = $this->settings_custom->endpoints->{$this->custom_endpoint_name};
			
			if (!empty($custom_endpoint->parameters)) {
				if (empty($this->settings->endpoints->{$this->actual_endpoint_name}->parameters))
					$this->settings->endpoints->{$this->actual_endpoint_name}->parameters = new \stdClass();

				foreach ($custom_endpoint->parameters as $key => $value)
					$this->settings->endpoints->{$this->actual_endpoint_name}->parameters->$key = $value;
			}

			if (!empty($custom_endpoint->output)) {
				if (empty($this->settings->endpoints->{$this->actual_endpoint_name}->output))
					$this->settings->endpoints->{$this->actual_endpoint_name}->output = new \stdClass();

				foreach ($custom_endpoint->output as $key => $value)
					$this->settings->endpoints->{$this->actual_endpoint_name}->output->$key = $value;
			}

			if (isset($custom_endpoint->limit))
				$this->settings->endpoints->{$this->actual_endpoint_name}->limit = $custom_endpoint->limit;

			if (isset($custom_endpoint->paging))
				$this->settings->endpoints->{$this->actual_endpoint_name}->paging = $custom_endpoint->paging;
		}
	}

	// overwrite config_json w/ values from global_vars; merge and set
	public function apply_global_vars($global_vars) {
		if (!empty($global_vars['merge'])) {
			$settings = json_encode($this->settings);

			foreach ($global_vars['merge'] as $key => $value) {
				$this->map_global_parameter($key);
				$value = addslashes($value);
				$settings = str_replace("{$this->merge_open}$key{$this->merge_close}", $value, $settings);
			}

			$settings = json_decode($settings);

			if (empty($settings))
				throw new SDKlessException("invalid JSON caused by global merge vars");

			$this->settings = $settings;
		}

		if (!empty($global_vars['set'])) {
			$this->apply_global_set_vars($global_vars['set']);
		}
	}

	public function set_endpoint_setting($keys, $value) {
		$destination = $this->settings->endpoints->{$this->actual_endpoint_name};
		$this->set_setting($destination, $keys, $value);
	}

	// populate settings with values from endpoint_vars (passed to SDKless::go), both merge and set
	// set supports 2 array levels
	public function apply_endpoint_vars($endpoint_vars) {
		if (!empty($endpoint_vars['array_set']) && !empty($this->settings_custom->endpoints->{$this->custom_endpoint_name}->array_set_templates)) {
			$array_set = $endpoint_vars['array_set'];
			$array_set_templates = $this->settings_custom->endpoints->{$this->custom_endpoint_name}->array_set_templates;

			// loop through endpoint settings; contains settings such as "parameters"
			foreach ($array_set as $endpoint_setting_key => $endpoint_setting_values) {
				// loop through the endpoint values; contains values such as "contacts"
				foreach ($endpoint_setting_values as $endpoint_setting_subkey => $value_array) {
					if (!is_array($value_array))
						throw new SDKlessException("array_set endpoint parameter must be an array");
						
					if (!empty($array_set_templates->$endpoint_setting_subkey)) {
						$new_endpoint_setting = array();

						// loop through items to set, such as each contact
						foreach ($value_array as $value_key => $values) {
							if (!is_array($values))
								throw new SDKlessException("array_set endpoint parameter must be an array");

							// clone the template, to be updated with incoming values and then will overwrite incoming array
							// cannot use clone method due to nested objects
							$template = unserialize(serialize($array_set_templates->$endpoint_setting_subkey));

							// loop through the actual item values, such as email_address/first_name, to apply to the template
							foreach ($values as $key => $value)
								$this->update_template_value($template, $key, $value);

							$new_endpoint_setting[] = $template;
						}
						
						$this->map_endpoint_parameter($this->custom_endpoint_name, $endpoint_setting_subkey);
						$keys = array($endpoint_setting_key, $endpoint_setting_subkey);
						$this->set_endpoint_setting($keys, $new_endpoint_setting);
					}
				}
			}
		}

		if (!empty($endpoint_vars['set'])) {
			$this->apply_endpoint_parameter_maps($endpoint_vars['set']);
			$this->apply_endpoint_set($endpoint_vars['set']);
		}

		// merge must be done last to allow for resetting merge values only
		$this->store_unmerged();

		if (!empty($endpoint_vars['merge'])) {
			$settings = json_encode($this->settings);

			foreach ($endpoint_vars['merge'] as $key => $value) {
				$this->map_endpoint_parameter($this->custom_endpoint_name, $key);
				$settings = str_replace("{$this->merge_open}$key{$this->merge_close}", $value, $settings);
			}

			$settings = json_decode($settings);

			if (empty($settings))
				throw new SDKlessException("invalid JSON caused by endpoint merge vars");

			$this->settings = $settings;
		}
	}

	public function get_endpoint_setting($setting) {
		// endpoint setting can be in endpoint, common_endpoint_settings->{$this->method}, or common_endpoint_settings->all (in order of precedence)
		// check in order of precedence and immediately return if non-object
		// if object(s), merge them in reverse order since the latter ones overwrite
		$settings = array();
		
		if (!empty($this->settings->endpoints->{$this->actual_endpoint_name}->$setting)) {
			if (is_object($this->settings->endpoints->{$this->actual_endpoint_name}->$setting))
				$settings[] = $this->settings->endpoints->{$this->actual_endpoint_name}->$setting;
			else
				return $this->settings->endpoints->{$this->actual_endpoint_name}->$setting;
		}

		if (!empty($this->settings->common_endpoint_settings->{$this->method}->$setting)) {
			if (is_object($this->settings->common_endpoint_settings->{$this->method}->$setting))
				$settings[] = $this->settings->common_endpoint_settings->{$this->method}->$setting;
			else
				return $this->settings->common_endpoint_settings->{$this->method}->$setting;
		}

		if (!empty($this->settings->common_endpoint_settings->all->$setting)) {
			if (is_object($this->settings->common_endpoint_settings->all->$setting))
				$settings[] = $this->settings->common_endpoint_settings->all->$setting;
			else
				return $this->settings->common_endpoint_settings->all->$setting;
		}

		if (empty($settings))
			return null;

		$return = new \stdClass();

		foreach (array_reverse($settings) as $s) {
			foreach ($s as $key => $value)
				$return->$key = $value;
		}

		$this->clean_endpoint_setting($return);
		return $return;
	}

	public function is_merged($value) {
		return (!is_string($value) || (strpos($value, $this->merge_open) === false) || (strpos($value, $this->merge_close) === false));
	}

	public function reset_to_unmerged() {
		$this->settings = $this->settings_unmerged;
		$this->settings_custom = $this->settings_custom_unmerged;
	}

	public function make_uri(&$uri) {
		$base_uri = $this->settings->base_uri;

		if (empty($uri)) {
			$uri = $base_uri;
			return;
		}

		// if uri is not full, concatenate with base uri
		if ((stripos($uri, 'http://') === false) && (stripos($uri, 'https://') === false))
			$uri = $base_uri . $uri;
	}

	// $keys can be an array or string (string will be converted to array)
	// - should contain heirarchy of keys in the destination array
	// - ex. array('a', 'b', 'c') would update $destination->a->b->c
	private function set_setting($destination, $keys, $value) {
		if (!is_array($keys))
			$keys = array($keys);

		foreach ($keys as $k => $key) {
			if (is_array($destination)) {
				// other than final destination, destination should be structure
				if (($k != (count($keys) - 1)) && !empty($destination[$key]) && !Utilities::is_structure($destination[$key])) {
					throw new SDKlessException("set parameter '$key' expects a sctructure but config contains scalar");
				}

				if (empty($destination[$key]))
					$destination[$key] = array();

				$destination =& $destination[$key];
			} else {
				// other than final destination, destination should be structure
				if (($k != (count($keys) - 1)) && !empty($destination->$key) && !Utilities::is_structure($destination->$key)) {
					throw new SDKlessException("set parameter '$key' expects a sctructure but config contains scalar");
				}

				if (empty($destination->$key))
					$destination->$key = array();

				$destination =& $destination->$key;
			}
		}

		$destination = $value;
	}

	private function apply_global_set_vars($set, $keys = array()) {
		foreach ($set as $key => $value) {
			$this->map_global_parameter($key);
			$keys[] = $key;

			if (is_array($value)) {
				$this->apply_global_set_vars($value, $keys);
			} else {
				$destination = $this->settings;
				$this->set_setting($destination, $keys, $value);
			}
		}
	}

	// get actual endpoint parameter name from custom one
	private function map_endpoint_parameter($endpoint, &$param) {
		if (!empty($this->settings_custom->endpoints->$endpoint->parameter_maps->$param)) {
			$param = $this->settings_custom->endpoints->$endpoint->parameter_maps->$param;
			return;
		}

		$this->map_global_parameter($param);
	}

	private function map_global_parameter(&$param) {
		if (!empty($this->settings_custom->global->parameter_maps->$param))
			$param = $this->settings_custom->global->parameter_maps->$param;
	}

	// template values are set to the incoming key
	// replace the template value with the incoming value
	// recursive
	private function update_template_value(&$template, $key, $value) {
		if (!Utilities::is_structure($template))
			throw new SDKlessException("template must be a structure");

		foreach ($template as $template_key => $template_value) {
			if (is_scalar($template_value)) {
				if ($template_value === $key) {
					if (is_array($template))
						$template[$template_key] = $value;
					else
						$template->$template_key = $value;
				}
			}

			if (Utilities::is_structure($template_value))
				$this->update_template_value($template_value, $key, $value);
		}
	}

	// update incoming keys to mapped keys
	// recursive
	private function apply_endpoint_parameter_maps(&$structure) {
		$count = count($structure);
		$counter = 0;

		if (!Utilities::is_structure($structure))
			throw new SDKlessException("apply_endpoint_parameter_maps requires a structure");

		foreach ($structure as $key => $value) {
			// changing array mid-loop can cause endless loop
			if ($counter == $count)
				break;

			if (is_string($key)) {
				if (is_array($structure)) {
					unset($structure[$key]);
					$this->map_endpoint_parameter($this->custom_endpoint_name, $key);
					$structure[$key] = $value;
				} else {
					unset($structure->$key);
					$this->map_endpoint_parameter($this->custom_endpoint_name, $key);
					$structure->$key = $value;
				}
			}

			if (is_array($structure)) {
				if (Utilities::is_structure($structure[$key]))
					$this->apply_endpoint_parameter_maps($structure[$key]);
			} else {
				if (Utilities::is_structure($structure->$key))
					$this->apply_endpoint_parameter_maps($structure->$key);
			}

			$counter++;
		}
	}

	// recursively go through the endpoint set array
	private function apply_endpoint_set($set, $keys = array()) {
		foreach ($set as $key => $value) {
			$keys[] = $key;

			if (is_array($value)) {
				$this->apply_endpoint_set($value, $keys);
			} else {
				$this->set_endpoint_setting($keys, $value);
			}
		}
	}

	// call before merging to allow reset
	private function store_unmerged() {
		$this->settings_unmerged = $this->settings;
		$this->settings_custom_unmerged = $this->settings_custom;
	}

	// recursive
	private function clean_endpoint_setting(&$setting) {
		if (!Utilities::is_structure($setting))
			return;

		foreach ($setting as $key => &$value) {
			if (Utilities::is_structure($value)) {
				$this->clean_endpoint_setting($value);
			} else {
				$do_unset = false;

				if (is_null($value))
					$do_unset = true;

				// skip params containing unmerged vars; allows for optional params
				if (!$this->is_merged($value))
					$do_unset = true;

				if ($do_unset) {
					if (is_array($setting))
						unset($setting[$key]);
					else
						unset($setting->key);
				}
			}
		}
	}

}