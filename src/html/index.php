<?php
require '../flight/Flight.php';
require '../flight/db_model.php';
require '../flight/constants.php';


Flight::register('db', 'PDO', array('mysql:host=localhost;port=3306;dbname=shrink', 'root', ''), function($db) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
});

Flight::route('/', function(){
    Flight::render('main_page.php', array());
});

Flight::route('GET /api/url/@alias', function($alias){
	$conn    = Flight::db();
	$request = Flight::request();

	$res = get_url($conn, $alias);
	if(is_object($res)) {
		Flight::json(array(KEYS_URL=>$res->url));
	} else {
		Flight::json(array(KEYS_ERROR=>ERROR_NOT_FOUND));
	}
});
Flight::route('POST /api/url', function(){
	$conn = Flight::db();
	$request = Flight::request();

	$success = false;
	$logged  = false;
	$message = '';
	$alias   = '';

	$data = (array)Flight::request()->data;
	$data = array_pop($data);

	$url = extract_url($data);

	if(!is_object($url)) {
		$phishy = is_phishy($conn, $url);
		if($phishy) {
			$message = get_error($conn, SOMETHING_SMELLS_PHISHY, array($phishy));
			$logged  = log_error($conn, SOMETHING_SMELLS_PHISHY, $message, $request->ip, $request->user_agent, $request->referrer);
			Flight::json(
				array(
					KEYS_SUCCESS=>$success,
					KEYS_LOGGED=>$logged,
					KEYS_MESSAGE=>$message
				)
			);
		} else {
			$success = true;
			$existing = check_existing($conn, $url);
			if(strlen($existing->alias)) {
				$alias = $existing->alias;
				$message = MESSAGE_SUCCESSFUL;
			} else {
				$alias = shorten_url($conn, $url, $request->ip);
				$message = MESSAGE_SHORT_URL_CREATED;
			}
			Flight::json(
				array(
					KEYS_SUCCESS=>$success,
					KEYS_MESSAGE=>$message,
					KEYS_URL=>$url,
					KEYS_EXISTING=>$existing->visits,
					KEYS_ALIAS=>$alias
				)
			);
		}
	} else { Flight:json($url[KEYS_ERROR]); }


});


Flight::route('GET /api/phish/update', function() {
	$conn = Flight::db();
	$count = 0;
	$total = 0;
	$json = json_decode(file_get_contents(PHISH_PATH), true);
	foreach($json as $url=>$phish_id) {
		if(!is_phishy($conn, $url)) {
			if(!put_phish($conn, $phish_id, $url)) { 
				Flight::json(array('error'=>"Could not insert: $phish_id: $url"));;
				break;
			} else { $count++; }
		} else { $total++; }
	}
	Flight::json(array('added'=>$count, 'total'=>$total));
});


Flight::route('POST /api/phish/url', function(){
	$conn = Flight::db();

	$data = (array)Flight::request()->data;
	$data = array_pop($data);

	$url = extract_url($data);

	if(!is_object($url)) {
		Flight::json(array(is_phishy($conn, $url)));
	} else { Flight:json($url[KEYS_ERROR]); }
});

Flight::start();



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
?>
