<?php
/*
 *
 * Use this demo page to verify that your instance
 * of the MockPay payment gateway is functioning properly
 *  
 */

//Load zend framework and initalize.
require '../zf.php';
initZendFramework();

error_reporting(E_ALL);
ini_set('display_errors',1);

/*
 * Step 1 with mockpay gatway is to connect to the api via soap and send the payment details.
 * The soap response with have a token which we'll need in step2.
 */
$wsdl = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/endpoint.php?wsdl'; //'php_self == /mockpay/service/endpoint.php';
//$wsdl = 'http://a1ikuznugm2rjfqr.my.phpcloud.com/mockpay/service/endpoint.php?wsdl';

$options = array(
	'trace' => 1, 
        'exceptions' => true, 
        'cache_wsdl' => WSDL_CACHE_NONE, 
        'features' => SOAP_SINGLE_ELEMENT_ARRAYS);
$client = new SoapClient($wsdl,$options);


/*If there are any args on the url, stop here and display them.
 * We'll set this address as the success,cancel and failure urls.
 */
if(count($_GET) > 0){
	echo '<pre>' . print_r($_GET,true) . '</pre><hr />';
        if(isset($_GET['token'])){
            /*
            * Query payment on an invalid token.
            */
            echo '<p>Checking token in response:</p>';
            $response = $client->queryPayment($_GET['token']);
            echo '<pre>' . print_r($response,true) . '</pre><hr />';
        }
	die();
}

/*
 * Display the methods in the mockpay soap api.
 */
$response = $client->__getFunctions();
echo '<pre>' . print_r($response,true) . '</pre><hr />';

/*
 * Confirm that the soap api is working by calling the ping() method.
 */
echo '<p>Service Check:</p>';
$response = $client->ping();
echo '<pre>' . print_r($response,true) . '</pre><hr />';
/*
 * Query payment on an invalid token.
 */
echo '<p>Check for an invalid token:</p>';
$token = '51a9982df291574b946c41aa61c6f2ae';
$response = $client->queryPayment($token);
echo '<pre>' . print_r($response,true) . '</pre><hr />';

/*
 * Create a new order via the soap api and print the token and link to the payment form.
 */
$email = 'a@b.com';
$amount = mt_rand(1,100) . '.' . str_pad(mt_rand(1,99),2,STR_PAD_LEFT);
$orderId = str_pad(mt_rand(1,9999),5,STR_PAD_LEFT);
$desc = 'A sample order.';
$responseUriBase = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/demo.php';
$uriSuccess = $responseUriBase . '?success';
$uriFailure = $responseUriBase . '?failure';
$uriCancel =  $responseUriBase . '?cancel';
$response = $client->beginPayment($email, $amount, $orderId, $desc, $uriSuccess, $uriFailure, $uriCancel);
echo '<pre>' . print_r($response,true) . '</pre><hr />';

$target = dirname($wsdl) . '/../form/paynow.php';
echo '<a target="_blank" href="' . $target . '?token=' . $response['token'] . '">' . $response['token'] . '</a>';