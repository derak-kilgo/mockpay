<?php
/*
 * This is the soap endpoint. It will handel the soap requests and respond with the
 * proper wsdl for our web service. 
 */

//Load zend framework and initalize.
ob_start();
require '../zf.php';
initZendFramework();

//Load the 'model' class for our api.
require 'mockpay.php';


$uri = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; //'php_self == /mockpay/service/endpoint.php';
$wsdl = $uri . '?wsdl';
if(isset($_GET['wsdl'])) {
    //This will auto generate our wsdl for us. All it needs is the class which will act as the model for our service.
    $autodiscover = new Zend_Soap_AutoDiscover();
    $autodiscover->setUri($uri);
    $autodiscover->setClass('My_Service_MockPay');
    $autodiscover->handle();
} else {
    //Make a new soap server and attach our model class to it.
    $options = array('cache_wsdl'=>WSDL_CACHE_NONE,'location'=>$uri);
    $server = new Zend_Soap_Server($wsdl,$options);
    $server->setClass('My_Service_MockPay');
    $server->handle();
}