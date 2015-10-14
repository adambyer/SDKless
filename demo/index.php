<?php
require_once 'MySDKless.php';
require_once 'test_accounts.php';

$endpoints = array(
	'pull_contact_is_unsubscribed' => 'Pull Contact Is Unsubscribed',
	'pull_clients' => 'Pull Clients',

	'pull_list_count' => 'Pull List Count',
	'pull_list_contacts' => 'Pull List Contacts',
	'pull_list_id_by_name' => 'Pull List ID By Name',
	'pull_list_segments' => 'Pull List Segments',

	'pull_import_lists' => 'Pull Import Lists',
	'pull_export_lists' => 'Pull Export Lists',
	
	'pull_user' => 'Pull User',
	'pull_user_feed' => 'Pull User Feed',
	'pull_user_friends' => 'Pull User Friends',
	'pull_user_statuses' => 'Pull User Statuses',

	'push_list' => 'Push List',
	'push_list_contacts' => 'Push List Contacts',
	'push_list_segment' => 'Push List Segment',
	'push_segment_contacts' => 'Push Segment Contacts',
);
$selected_endpoint = '';

if (!empty($_GET['api'])) {
	$selected_api = $_GET['api'];
	$selected_endpoint = $_GET['endpoint'];
	$global_vars = $test_accounts[$selected_api]['global'];
	$endpoint_vars = (empty($test_accounts[$selected_api]['endpoint'][$selected_endpoint])? null : $test_accounts[$selected_api]['endpoint'][$selected_endpoint]);
	$local_vars = (empty($test_accounts[$selected_api]['local'])? null : $test_accounts[$selected_api]['local']);

	try {
		$sdkless = new MySDKless($selected_api, $global_vars);
		$output = $sdkless->go($selected_endpoint, $endpoint_vars, $local_vars);
	} catch (Exception $e) {
		$error = $e->getMessage();
	}
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
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
$(function() {
	var selected_endpoint = '<?php echo $selected_endpoint; ?>';
	setup_endpoints($('#api').val());

	if (selected_endpoint == '') {
		$("#endpoint").val($("#endpoint option:not([disabled])").first().val());
	}
	
  $('#api').change(function() {
  	setup_endpoints($(this).val());
  	$("#endpoint").val($("#endpoint option:not([disabled])").first().val());
  });

  function setup_endpoints(api) {
  	$('#endpoint option').each(function() {
  		if ($(this).hasClass(api)) {
  			$(this).removeAttr('disabled');
  		} else {
  			$(this).attr('disabled', 'disabled');
  		}
  	});
  }
});
</script>
<body>
	<div>
		<form>
			<select id="api" name="api">
				<?php
				foreach ($test_accounts as $api => $account) {
					$selected = (!empty($selected_api) && ($selected_api == $api)? 'selected' : '');
				?>
					<option value="<?php echo $api; ?>" <?php echo $selected; ?>><?php echo $api; ?></option>
				<?php } ?>
			</select>
			<select id="endpoint" name="endpoint">
				<?php
				foreach ($endpoints as $endpoint_id => $endpoint_name) {
					$class = '';

					foreach ($test_accounts as $api => $account) {
						if (array_key_exists($endpoint_id, $account['endpoint']))
							$class .= "$api ";
					}

					$selected = (!empty($selected_endpoint) && ($selected_endpoint == $endpoint_id)? 'selected' : '');
				?>
					<option class="<?php echo $class; ?>" value="<?php echo $endpoint_id; ?>" <?php echo $selected; ?>><?php echo $endpoint_name; ?></option>
				<?php } ?>
			</select>
			<button type="submit">Go</button>
			<a href="index.php">reset</a>
			<a href="auth.php">auth</a>
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