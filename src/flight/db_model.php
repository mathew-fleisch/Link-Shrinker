<?php
require 'constants.php';
/**
 * check_existing() - Url db lookup
 *
 * @param       string  $url    Input url
 * @return      (true ? string : -1)
 */
function check_existing($conn, $url) {
	$stmt = $conn->prepare("SELECT u.alias, COUNT(v.id) AS visits FROM urls AS u, visits AS v WHERE u.url = ? AND u.alias = v.alias;");
	$stmt->execute(array($url));
	return $stmt->fetch();
}


/**
 * get_url() - Returns the original url based on the url id/alias 
 *
 * @param       string  $alias    Input id
 * @return      string
 */
function get_url($conn, $alias) {
	$stmt = $conn->prepare("SELECT url FROM urls WHERE alias = ?");
	$stmt->execute(array($alias));
	return $stmt->fetch();
}


function get_urls($conn, $limit = 20, $offset = 0) {
	$stmt = $conn->prepare("SELECT u.id, IFNULL(COUNT(v.id), 0) AS visit_count, u.alias, u.url, u.ip, u.time, u.active FROM urls AS u LEFT JOIN visits as v on u.alias = v.alias GROUP BY v.alias ORDER BY visit_count DESC;");
	$stmt->execute();
	return $stmt->fetchAll();
}

/**
 * shorten_url() - Inserts user's url into db and 
 *                 returns a random string of about 7 characters
 *
 * @param       string  $url    Input url
 * @param       string  $ip     User's IP
 * @return      string
 */
function shorten_url($conn, $url, $ip) {
	$alias = base_convert(rand(0,10).time(), 10, 36);
	$stmt = $conn->prepare("INSERT INTO urls (url,alias,ip) VALUES (?,?,?)");
	$stmt->bindParam(1, $url);
	$stmt->bindParam(2, $alias);
	$stmt->bindParam(3, $ip);
	$stmt->execute();
	return $alias;
}

/**
 * visit() - Logs visit in db  
 *
 * @param       string  $alias     Random string used in the short url
 * @param       string  $ip        User's IP
 * @param       text    $browser   User's 'User Agent'
 * @param       text    $referrer  User's referring url
 * @return      string
 */
function visit($conn, $alias, $ip, $browser, $referrer) {
	$stmt = $conn->prepare("INSERT INTO visits (alias, ip, browser, referrer) VALUES (?,?,?,?)");
	$stmt->bindParam(1, $alias);
	$stmt->bindParam(2, $ip);
	$stmt->bindParam(3, $browser);
	$stmt->bindParam(4, $referrer);
	if($stmt->execute()) {
		return 1;
	} else {
		return 0;
	}
}





function is_phishy($conn, $url) { 
	$stmt = $conn->prepare("SELECT phish_id FROM phish_tank WHERE url = ?");
	$stmt->execute(array($url));
	$res = $stmt->fetch();
	return (is_object($res) ? $res->phish_id : 0);
}
function put_phish($conn, $phish_id, $url) {
	$stmt = $conn->prepare("INSERT INTO phish_tank (phish_id, url) VALUES (?,?)");
	$stmt->bindParam(1, $phish_id);
	$stmt->bindParam(2, $url);
	return $stmt->execute();
}

function phish_log($conn, $added, $ip, $browser, $referrer) {
	$stmt = $conn->prepare("INSERT INTO phish_log (added, ip, browser, referrer) VALUES (?,?,?,?)");
	$stmt->bindParam(1, $added);
	$stmt->bindParam(2, $ip);
	$stmt->bindParam(3, $browser);
	$stmt->bindParam(4, $referrer);
	return $stmt->execute();
}

function get_phish_log($conn, $limit = 20, $offset = 0) {
	$stmt = $conn->prepare("SELECT * FROM phish_log");
	$stmt->execute();
	return $stmt->fetchAll();
}











/**
 * get_error() - Url db lookup
 *
 * @param       string  $error_code     Unique identifier for errors
 * @param       array   $vars           Array of strings to make error messages dynamic
 * @return      (true ? string : -1)
 */
function get_error($conn, $error_code, $vars) {
	$stmt = $conn->prepare("SELECT message FROM errors WHERE error_code = ?");
	$stmt->execute(array($error_code));
	$res = $stmt->fetch();
	return parse_error($res->message, $vars);
}

/**
 * log_error() - Url db lookup
 *
 * @param       string  $error_code     Unique identifier for errors
 * @param       array   $msg           Array of strings to make error messages dynamic
 * @return      (true ? string : -1)
 */
function log_error($conn, $error_code, $msg, $ip, $browser, $referrer) {
	$stmt = $conn->prepare("INSERT INTO error_log (error_code, message, ip, browser, referrer) VALUES (?,?,?,?,?)");
	$stmt->bindParam(1, $error_code);
	$stmt->bindParam(2, $msg);
	$stmt->bindParam(3, $ip);
	$stmt->bindParam(4, $browser);
	$stmt->bindParam(5, $referrer);
	return $stmt->execute();
}

/**
 * parse_error() - Replaces keywords with elements from an array
 *
 * @param       string  $msg    Raw error message with replace string
 * @param       array   $vars   Array of strings
 * @return      (true ? string : -1)
 */
 function parse_error($msg, $vars) {
	for($i = 0; $i < count($vars); $i++) { 
		$msg = preg_replace("/{".ERROR_REPLACE_STRING."\[".$i."\]}/", $vars[$i], $msg);
	}
	return $msg;
}
