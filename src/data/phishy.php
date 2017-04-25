<?php
$json = json_decode(file_get_contents("/var/www/data/online-valid.json"), true);
$new = [];
foreach($json as $phish) {
	echo $phish['phish_id']."\n";
	$new[$phish['url']] = $phish['phish_id'];
}
file_put_contents("/var/www/data/phishy.json", json_encode($new));
?>