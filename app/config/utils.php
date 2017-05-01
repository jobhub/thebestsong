<?php

// Utils
function print_r2($val) {
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}

function error($message) {
	print $message;
	return false;	
}

function generateHash($length = 24) {
	return preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes($length)));
}

?>
