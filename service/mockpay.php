<?php
/**
* This is model class for our mock payment service.
* Its used in conjunction with the Zend_Soap_Server.
*
* The Zend_Soap_Server class requires that php_doc style comments
* exist for every method in your class. It uses these comments to generate
* the wsdl for your service.
* 
* This class depends on the initCache() method in the zf.php creating a cache object
* and saving it to the zend_registry. It uses cache to store data between requests
* instead of using a database.
* 
* You must allow write access to the mockpay/tmp/ folder by the webserver user.  
*/
class My_Service_Mockpay
{
    const STATE_PAYMENT_PENDING = 'payment-pending';
    const STATE_COMPLETE = 'complete';
    const STATE_CANCEL = 'cancel';
    
	/**
	*
	* Query the payment made via the payment screen.
	*
	* @param string token
	* @return array
	*/
	public function queryPayment($token)
	{
		$filter = new Zend_Filter_Alnum();
		$token = $filter->filter($token);
		if(strlen($token) != 32){
			throw new InvalidArgumentException('Token is not valid');
		}
		$cache = Zend_Registry::get('cache');
		if($cache->test($token)){
			$details = $cache->load($token);
			$details['hasErrors'] = 0;
			return $details;
		}else{
			return array('hasErrors'=>1,'errorMessage'=>'The provided token is invalid.');
		}
	}
	/**
	*
	* Store payment details which will be used by the payment screen.
	*
	* @param string $email
	* @param float  $amount
	* @param string $orderId
        * @param string $desc
        * @param string $uriSuccess
        * @param string $uriFailure
        * @param string $uriCancel
	* @return array
	*/
	public function beginPayment($email,$amount,$orderId,$desc,$uriSuccess,$uriFailure,$uriCancel)
	{
		$email = filter_var($email,FILTER_SANITIZE_EMAIL);
		$float = filter_var($amount,FILTER_SANITIZE_NUMBER_FLOAT);
		
		$desc = filter_var($desc,FILTER_SANITIZE_STRIPPED);
		
		$uriSuccess = filter_var($uriSuccess,FILTER_SANITIZE_URL);
		$uriFailure = filter_var($uriFailure,FILTER_SANITIZE_URL);
		$uriCancel = filter_var($uriCancel,FILTER_SANITIZE_URL);
		
		$filter = new Zend_Filter_Alnum();
		$orderId = $filter->filter($orderId);

		$cache = Zend_Registry::get('cache');
		$token = md5($email . $amount . $orderId);
		if($cache->test($token)){
			return array('hasErrors'=>1,'errorMessage'=>'Duplicate payment','token'=>$token);
		}else{
			$response = array();
			$response['hasErrors'] = 0;
			$response['token'] = $token;
			$response['desc'] = $desc;
			$response['email'] = $email;
			$response['amount'] = $amount;
			$response['orderId'] = $orderId;
			$response['status'] = 'pending-payment';
			$response['successUri'] = $uriSuccess;
			$response['failureUri'] = $uriFailure;
			$response['cancelUri'] = $uriFailure;
			$cache->save($response);
			return $response;
		}
	}
	/**
	*
	* A test method to make sure SOAP is working correctly. Always reponds with 'PONG';
	*
	* @return array
	*/
	public function ping()
	{
		$response = array('Response'=>'pong');
		return $response;
	}
}