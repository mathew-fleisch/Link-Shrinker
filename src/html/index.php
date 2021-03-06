<?php
require '../flight/Flight.php';
require '../flight/db_model.php';
require '../flight/constants.php';
require '../flight/helpers.php';


Flight::register('db', 'PDO', array('mysql:host=localhost;port=3306;dbname=shrink', 'root', ''), function($db) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
});

Flight::route('/', function(){
    Flight::render('main_page.php', array());
});

Flight::route('/admin', function(){
	$conn    = Flight::db();
	$request = Flight::request();

	$urls = get_urls($conn);
	$phish_logs = get_phish_log($conn);

    Flight::render('admin_page.php', array('urls'=>$urls,'phish_logs'=>$phish_logs));
});

Flight::route('/a/@alias', function($alias){
	$conn    = Flight::db();
	$request = Flight::request();
	if(visit($conn, $alias, $request->ip, $request->user_agent, $request->referrer)) {
		$res = get_url($conn, $alias);
		if(is_object($res)) {
			echo redirect_url($res->url);
		} else { echo $alias.ERROR_NOT_FOUND; exit(); }
	} else { 
		echo ERROR_REDIRECT_LOG_FAIL;
		exit();
	}
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
	$conn    = Flight::db();
	$request = Flight::request();
	$count   = 0;
	$total   = 0;
	$json = json_decode(file_get_contents(PHISH_PATH), true);
	foreach($json as $url=>$phish_id) {
		if(!is_phishy($conn, $url)) {
			if(!put_phish($conn, $phish_id, $url)) { 
				Flight::json(array('error'=>"Could not insert: $phish_id: $url"));
				break;
			} else { $count++; }
		} else { $total++; }
	}
	if(phish_log($conn, $count, $request->ip, $request->user_agent, $request->referrer)) { 
		Flight::json(array('added'=>$count, 'total'=>$total));
	} else { 
		Flight::json(array('error'=>'phish_log failed.', 'added'=>$count, 'total'=>$total));
	}
});


Flight::route('POST /api/phish/url', function(){
	$conn    = Flight::db();

	$data = (array)Flight::request()->data;
	$data = array_pop($data);

	$url = extract_url($data);

	if(!is_object($url)) {
		Flight::json(array(is_phishy($conn, $url)));
	} else { Flight:json($url[KEYS_ERROR]); }
});

Flight::start();

?>
