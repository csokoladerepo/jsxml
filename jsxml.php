<?php
/**
************************
JSXML
ver: 0.1.1publicbeta
date: 2012.06.28 - 23:30
repo: https://github.com/csokoladerepo/jsxml
git: https://github.com/csokoladerepo/jsxml.git
author: @csokoladelabs https://csokolade.eu
************************
**/

/**
*
SESSION
*
**/
session_start();
$APPSESSION = session_id();
DEFINE("GLOBAL_APP_SESSION", "$APPSESSION", true); //TRUE OR FALSE

header('Content-type: text/html; charset=UTF-8') ;	

/**
*
ERROR REPORTING
*
**/
define('ENVIRONMENT', 'bug search');
if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'bug search':
		    ini_set('display_errors', 'On');
		    error_reporting (63);
			error_reporting(E_ALL);
		break;
	
		case 'testing':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('App error. The core not set correctly.');
	}
}

/**
*
DB API
*
**/
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_SERVER', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/**
*
APP SETUP - APP API
*
**/                    
define('engine', 'jsxml#engine', true);
define('ERROR_APP_URL', '#BADREQUEST', true);
define('METHODFORMAT', 'json', true);
define('PUBLIC_FORMAT', 'xml', true);
define('REQUEST', 'api', true);

/**
*
BAD API REQUEST
*
**/

if ($_SERVER['REQUEST_METHOD'] == 'GET' && (!(isset($_GET['format'])
&& $_GET['format'] == 'xml' && isset($_GET['request'])
&& $_GET['request'] == 'api'))) 
header("Location:".ERROR_APP_URL);

else {
 
/**
*
CONNECT TO DATABASE
*
**/
$connect = mysql_connect(DB_SERVER,DB_USER,DB_PASS) or die("app core connect error". ' ' .  mysql_error());
mysql_query('SET NAMES utf8'); 

$db = mysql_select_db(DB_NAME,$connect) or die("app core connect error". ' ' . mysql_error());
mysql_query('SET NAMES utf8');

if (!$db){
	echo "app core connect error";
}    
    /**
    *
    SQL QUERY
    *
    **/  
	$querylimit = mysql_real_escape_string($_POST['limit']); //FROM FORM INPUT
	$sql = "SELECT DATA FROM `TABLE` LIMIT $querylimit"; //DATA FOR EXAMPLE = "cars,buses"	
	$result = mysql_query($sql);	

	/**
    *
    GET DATA
    *
    **/ 
	$products = array(); 
	if(mysql_num_rows($result)) {
		while($item = mysql_fetch_assoc($result)) {
			$products[] = array('item'=>$item);
		}
	}
	
    /**
    *
    METHOD API FORMAT
    *
    **/ 	
	if(METHODFORMAT == 'json') {
		header('Content-type: application/json');
		echo json_encode(array('products'=>$products));
	}
	
	/**
    *
    PRINT XML
    *
    **/ 
	else {
		header('Content-type: text/xml','encoding="UTF-8"');
		//echo '<![CDATA['."\n";
		echo '<?xml version="1.0" encoding="utf-8"?>'."\n";		
		echo '<products>'."\n";		
		foreach($products as $index => $item) {
			if(is_array($item)) {
				foreach($item as $key => $value) {
					echo '<',$key,'>'."\n";	
					if(is_array($value)) {
						foreach($value as $tag => $val) {
							echo '<',$tag,'>',$val,'</',$tag,'>'."\n";	
						}
					}
					echo '</',$key,'>'."\n";						
				}
			}
		}
		echo '</products>'."\n";
		//echo ']]>';
	}
}
?>


	

    




							  
	
    
