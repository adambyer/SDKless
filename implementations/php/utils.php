<?php
function obfuscate(&$struct) {
	if (!$struct)
		return;

	foreach ($struct as $key => &$value) {
		if (gettype($value) == 'string' && key_test($key)) {
			if (gettype($struct) == 'array')
				$struct[$key] = '####';
			if (gettype($struct) == 'object')
				$struct->$key = '####';
		} else if (gettype($value) == 'string' && (strtolower(substr($value, 0, 4)) == 'http' || stripos($value, 'token') !== false || stripos($value, 'auth') !== false)) {
			if (gettype($struct) == 'array')
				$struct[$key] = '####';
			if (gettype($struct) == 'object')
				$struct->$key = '####';
		} else if (gettype($value) == 'array' || gettype($value) == 'object') {
			obfuscate($value);
		}
	}
}

function key_test($key) {
	return
		strtolower(substr($key, -2)) == 'id'
		or strtolower(substr($key, -3)) == 'uri'
		or $key == 'user'
		or stripos($key, 'accountname') !== false
		or stripos($key, 'email') !== false
		or stripos($key, 'login_name') !== false
		or stripos($key, 'auth') !== false
		or stripos($key, 'code') !== false
		or stripos($key, 'token') !== false
		or stripos($key, 'secret') !== false
		or stripos($key, 'key') !== false;
}