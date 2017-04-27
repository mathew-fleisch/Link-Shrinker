<?php
function valid_url($url) { 
	if(strlen($url)) {
		if(preg_match("/^http/", $url)) {
			if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
				return array(KEYS_IS_VALID=>true);
			} else { return array(KEYS_IS_VALID=>false, KEYS_ERROR=>ERROR_INVALID_URL); }
		} else { return array(KEYS_IS_VALID=>false, KEYS_ERROR=>ERROR_START_HTTP); }
	} else { return array(KEYS_IS_VALID=>false, KEYS_ERROR=>ERROR_EMPTY_URL); }
}

function extract_url($data) {
	if(array_key_exists(KEYS_URL, $data)) {
		if(strlen($data[KEYS_URL])) {
			$url = strip_tags(urldecode(strip_tags($data[KEYS_URL])));
			$valid = valid_url($url);
			if($valid[KEYS_IS_VALID]) {
				return $url;
			} else { return $valid; }
		} else { return array(KEYS_IS_VALID=>false, KEYS_ERROR=>ERROR_EMPTY_URL); }
	} else { return array(KEYS_IS_VALID=>false, KEYS_ERROR=>ERROR_EMPTY_URL); }
}

function redirect_url($url) {
	return '<script type="text/javascript">window.location.href = "'.$url.'";</script>';

}