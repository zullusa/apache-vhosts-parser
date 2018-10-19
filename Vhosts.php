<?php

$domains = explode(',', $argv[1]);

if (empty($domains)){
	$domains = file('./deleted.txt', FILE_IGNORE_NEW_LINES);
}


//Specify your vhosts file
$vhosts = file('c:/www/apache/conf/extra/httpd-vhosts.conf');
$v = array();
$isvh = false;
$vhbody = null;
$text = null;

foreach ($vhosts as $line) {
	// process comments outside the virtual host record
	if (preg_match('/^[\s]{0,}#/', $line) && !$isvh){
		$v[] = array('c', $line);
	}
	if (preg_match('/^[\s]{0,}<VirtualHost.*>/i', $line) && !$isvh){
		$isvh = true;
	}
	if ($isvh){
		$vhbody .= $line;
	}
	if (preg_match('/^[\s]{0,}<\/VirtualHost>/i', $line) && $isvh){
		$v[] = array('v', $vhbody);
		$vhbody = null;
		$isvh = false;
	}
	//echo $vhbody;
}
$matches = null;
foreach ($v as $row){
	if ($row[0] == 'c'){
		$text .= $row[1];
	}
	if ($row[0] == 'v') {
		//use your servername mask
		preg_match('/ServerName[\s]{0,}(.*)\.secondlevel\.com/i', $row[1], $matches, PREG_OFFSET_CAPTURE);
		$domain = $matches[1][0];
		
		//it excludes specified domains from vhosts
		if (!in_array($domain, $domains)){
			$text .=  $row[1];
		}
	}
}

file_put_contents('c:/www/apache/conf/extra/httpd-vhosts.conf', $text);
