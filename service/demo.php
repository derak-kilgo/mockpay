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
 * Step 1 with mockpay gatway is to connect to the api via soap and send the payment details.
 * The soap response with have a token which we'll need in step2.
 */
$wsdl = 'http://127.0.0.1/mockpay/service/endpoint.php?wsdl';
$options = array(
	'trace' => 1, 
        'exceptions' => true, 
        'cache_wsdl' => WSDL_CACHE_NONE, 
        'features' => SOAP_SINGLE_ELEMENT_ARRAYS);
$client = new SoapClient($wsdl,$options);
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
$amount = '12.34';
$orderId = '000005';
$desc = 'A sample order.';
$uriSuccess = $_SERVER['PHP_SELF'] . '?success';
$uriFailure = $_SERVER['PHP_SELF'] . '?success';
$uriCancel = $_SERVER['PHP_SELF'] . '?success';
$response = $client->beginPayment($email, $amount, $orderId, $desc, $uriSuccess, $uriFailure, $uriCancel);
echo '<pre>' . print_r($response,true) . '</pre><hr />';
echo '<a target="_blank" href="/mockpay/form/paynow.php?token=' . $response['token'] . '">' . $response['token'] . '</a>';