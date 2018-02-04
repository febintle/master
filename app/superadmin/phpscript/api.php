<?php
if (!defined('puyuetian'))
	exit('403');

$url = filter_var($_GET['url'], FILTER_VALIDATE_URL);
if ($url && (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://')) {
	$apidata = file_get_contents($url);
	if ($apidata) {
		exit($apidata);
	} else {
		exit('Data Null or APIURL Error:' . $url);
	}
} else {
	exit('URL Error!');
}
