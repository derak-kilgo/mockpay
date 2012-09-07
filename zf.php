<?php
/*
 * A utility method to setup the zend framework and the php environment so mockpay will function
 * properly.
 */
function initZendFramework(){
	ob_start();
	error_reporting(E_ALL & E_STRICT);

	ini_set('display_errors',1);
        //while developing, disable the php SoapClient cache.
	ini_set('soap.wsdl_cache_enabled', '0');
        //you will get a soap error if the page is gzipped by php.
	ini_set('zlib.output_compression', '0');
	ini_set('zlib.output_compression_level', '-1');
        //In some cases, the X-Powered-By header can cause problems with the soap client.
	if(function_exists('header_remove')){
		header_remove('X-Powered-By');
	}
        
	define('APP_ROOT', dirname(__FILE__));
        $doc_root = getenv('DOCUMENT_ROOT');
        
        /*
         * If the zend framework isn't in your include path, you'll need to configure and uncomment the lines below
         * for mockpay to function properly.
         */
        
        /*
        set_include_path('.' . PATH_SEPARATOR . $doc_root . '/ZendFramework-1.11.9/library/' 
                                                . PATH_SEPARATOR . $doc_root . '/ZendFramework-1.11.9/extras/library/' 
                                . PATH_SEPARATOR . '../application/models');
        //*/
        
        //Configure the zend autoloader.
	require_once "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);
        
        //A file based cache is our persistant storage for this project.
	$cache = initCache();
        //Save it to memory so our mockpay object can use it.
	Zend_Registry::set('cache',$cache);
}

/**
* A utility method to create a disk based cache object.
* For this project we'll be using a cache object to store data between requests instead of a database.
* 
* Make sure the mockpay/tmp folder is writeable by the webserver user. (chmod u+rwX ./mockpay/tmp)
*/
function initCache()
{
	//Without a database, A file based 'cache' is an ok option for percisting data between requests.
	$frontendOptions = array(
	   'lifetime' => 7200, // cache lifetime of 2 hours
	   'automatic_serialization' => true
	);
	$backendOptions = array(
	    'cache_dir' => APP_ROOT . '/tmp/' // Directory where to put the cache files
	);
	// getting a Zend_Cache_Core object
	$cache = Zend_Cache::factory('Core',
	                             'File',
	                             $frontendOptions,
	                             $backendOptions);
	return $cache;
}