<?php
require_once 'MySDKless.php';
require_once 'test_accounts.php';
require_once 'utils.php';

$do_auth = false;
$params = array();
$api = null;
$step_id = 0;
$done = false;

if (!empty($_GET)) {
	$api = $_GET['api'];
	$global_vars = $test_accounts[$api]['global'];

	try {
		$sdkless = new MySDKless($api, $global_vars);
		$do_auth = true;
	} catch (Exception $e) {
		$error = $e->getMessage();
	}

	# any incoming params other than 'go' and 'api' is assumed to be from a redirect back from the api
	# set step_id to -1 which will cause a lookup of the redirect step in sdkless->authenticate
	foreach ($_GET as $key => $value) {
		if (!in_array($key, array('go', 'api'))) {
			$step_id = -1;
			break;
		}
	}

	if (empty($_GET['go']))
		$params = $_GET;
}

if ($do_auth) {
	do {
		try {
			$output = $sdkless->authenticate($step_id, $params);
			$step_id = $output['step_id'];
			$params = $output['params'];
			$done = $output['done'];
		} catch (Exception $e) {
			$error = $e->getMessage();
			break;
		}

		$step_id++;
	} while (!$done);
}

$config = $sdkless->config->settings;
$custom_config = $sdkless->config->settings_custom;
$global_vars = $sdkless->global_vars;
$endpoint_vars = $sdkless->endpoint_vars;
$curl_opts = $sdkless->response->curl_opts;
$curl_info = $sdkless->response->curl_info;
$responses = $sdkless->response->responses;

// obfuscating potentially sensitive data for demos
if (DEBUG) {
	obfuscate($config);
	obfuscate($custom_config);
	obfuscate($global_vars);
	obfuscate($endpoint_vars);
	obfuscate($curl_opts);
	obfuscate($curl_info);
	obfuscate($responses);
	obfuscate($output);
}

$sdkless_vars = array(
	'CONFIG' => $config,
	'CUSTOM CONFIG' => $custom_config,
	'GLOBAL VARS' => $global_vars,
	'ENDPOINT VARS' => $endpoint_vars,
	'CURL OPTS' => $curl_opts,
	'CURL INFO' => $curl_info,
	'RESPONSES' => $responses,
);
?>
<html>
	<body>
		<div>
			<form>
				<select id="api" name="api">
					<?php 
					foreach ($test_accounts as $key => $account) {
						if (!empty($account['auth'])) {
							$selected = '';
							if ($api && $api == $key)
								$selected = 'selected';
						?>
							<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $key; ?></option>
					<?php 
						}
					}
					?>
				</select>
				<input type="hidden" name="go" value="1">
				<button type="submit">Go</button>
				<a href="auth.php">reset</a>
				<a href="index.php">home</a>
			</form>
		</div>
		<?php if (!empty($error)) { ?>
		<div style="color:red">
			<h3>ERROR</h3>
			<?php echo $error; ?>
		</div>
		<?php } ?>
		<?php 
		if (!empty($sdkless)) {
			foreach ($sdkless_vars as $key => $var) {
		?>
				<div>
					<h4><?php echo $key; ?></h4>
					<pre><?php print_r($var); ?></pre>
				</div>
		<?php
			}
		}
		?>
		<div>
			<h4>OUTPUT</h4>
			<pre><?php
			if (!empty($output)) print_r($output);
			?>
			</pre>
		</div>
	</body>
</html>