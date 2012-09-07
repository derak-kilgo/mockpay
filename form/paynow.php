<?php
/*
 *
 * This is the payment page of the MockPay Payment Gateway.
 * Navigate here with a token on the url to begin your payment session.
 * 
 *  
 */
ob_start();
session_start();

//Configure and load Zend Framework
require '../zf.php';
initZendFramework();

//Load the payment form
require 'form.php';
$form = new My_Mockpay_Form();
$form->setAction(basename(__FILE__));

//Form requires a token to look up an order.
if(!isset($_REQUEST['token'])){
    echo 'Token was not provided. Cannot begin payment';
    die();
}

/* @var Zend_Cache_Abstract $cache */
$cache = Zend_Registry::get('cache');
if ((!empty($_POST)) && $form->isValid($_POST)){
    $token = $form->getElement('token')->getValue();
    $orderDetails = $cache->load($token);
    if($orderDetails['status'] != 'pending-payment'){
        //Order exists, but its status indicates its been canceled or processed already.
        $uri = "{$orderDetails['failureUri']}=failure&token=$token&msg=duplicate";
        header("Location: $uri");
        die('duplicate');
    }elseif(isset($_POST['Cancel'])){
        //Cancel the order.
       $orderDetails['status'] = 'cancel';
       $uri = "{$orderDetails['cancelUri']}=cancel&token=$token&msg=cancel";
       $cache->save($orderDetails,$token);
       header("Location: $uri");
       die('cancel');
    }elseif(isset($_POST['PayNow'])){
        //mark the order as processed successfully.
       $uri = "{$orderDetails['successUri']}=success&token=$token&msg=complete";
       $orderDetails['status'] = 'complete';
       $cache->save($orderDetails,$token);
       header("Location: $uri");
       die('complete');
    }else{
        echo "<p>Invalid operation. Select either Cancel or PayNow.</p>";
    }
}else{
    
    //You were just redirected here via your cart. Populate the payment option form with data
    //from the transaction.
    $token = $_REQUEST['token'];
    $orderDetails = $cache->load($token);
    $form->getElement('email')->setValue($orderDetails['email']);
    $form->getElement('token')->setValue($token);
    //If the token has already been used, send user to the duplicate screen.
    if($orderDetails['status'] != 'pending-payment'){
        $uri = "{$orderDetails['failureUri']}=failure&token=$token&msg=duplicate";
        header("Location: $uri");
        die('duplicate');
    }
}

?>
<html>
<head>
<title>MockPay Payment System</title>
</head>
<body>
<h1>Summary of Payment</h1>
<p>
Order Summary: <br>
 Amount: <b><?php echo $orderDetails['amount']?></b>        <br>
 Item:   <b><?php echo $orderDetails['desc']?></b>          <br>
 Order#  <b>order# <?php echo $orderDetails['orderId']?></b><br>
</p>
<h4>Enter your payment information below.</h4>
<div style='padding: 10px; margin:auto;'>
<?php 
$view = new Zend_View(); //zf form requires a view to render.
echo $form->render($view);
?>
</div>
<p>Once your payment is completed, you will be redirected back to the store.</p>
</body>
</html>