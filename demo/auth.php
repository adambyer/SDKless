<?php
session_start();
require_once 'MySDKless.php';
require_once 'test_accounts.php';

$api = 'Twitter'; // enter account id here (Facebook, Twitter, CampaignMonitor)
$global_vars = $test_accounts[$api]['global'];
$do_auth = false;
$done = false;
$params = array();

try {
	$sdkless = new MySDKless($api, $global_vars);
} catch (Exception $e) {
	$error = $e->getMessage();
}

if (!empty($_GET)) {
	$do_auth = true;

	if (empty($_GET['go']))
		$params = $_GET;
}

if ($do_auth) {
	do {
		$_SESSION['SDKlessAuthStepId']++; // can't increment AFTER authenticate call because it may be a redirect

		try {
			$output = $sdkless->authenticate($_SESSION['SDKlessAuthStepId'], $params, $done);
			$params = $output;
		} catch (Exception $e) {
			$error = $e->getMessage();
			break;
		}
	} while (!$done);
} else {
	$_SESSION['SDKlessAuthStepId'] = -1;
}

$sdkless_vars = array(
	'CONFIG' => 'config->settings',
	'CUSTOM CONFIG' => 'config->settings_custom',
	'GLOBAL VARS' => 'global_vars',
	'ENDPOINT VARS' => 'endpoint_vars',
	'CURL OPTS' => 'response->curl_opts',
	'CURL INFO' => 'response->curl_info',
	'RESPONSES' => 'response->responses',
);
?>
<html>
	<body>
		<a href="?go=1">go</a>
		<a href="auth.php">reset</a>
		<a href="index.php">home</a>
		<?php if (!empty($error)) { ?>
		<div style="color:red">
			<h3>ERROR</h3>
			<?php echo $error; ?>
		</div>
		<?php } ?>
		<?php 
		if (!empty($sdkless)) {
			foreach ($sdkless_vars as $key => $var) {
				$subvar = null;

				if (strpos($var, '->') !== false) {
					$vars = explode('->', $var);
					$var = $vars[0];
					$subvar = $vars[1];
				}

		?>
				<div>
					<h4><?php echo $key; ?></h4>
					<pre><?php
					if (empty($subvar))
						print_r($sdkless->$var);
					else
						print_r($sdkless->$var->$subvar)
					?>
					</pre>
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