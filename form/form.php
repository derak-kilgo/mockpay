<?php
/**
 * A form to accept payment details for our mockpay payment method.
 * Example elements don't have any validation except for 'required'.
 */
class My_Mockpay_Form extends Zend_Form
{

    public function init(){
        parent::init();
        
        $this->setAction(basename(__FILE__));
        $this->setMethod('POST');
        
        $elEmail = new Zend_Form_Element_Text('email');
        $elEmail->setLabel('Email Address');
        $elEmail->setRequired(true);
        $this->addElement($elEmail);

        $elName = new Zend_Form_Element_Text('name');
        $elName->setLabel('Name as it appears on your credit card.');
        $elName->setRequired(true);
        $this->addElement($elName);

        $elAddress = new Zend_Form_Element_Text('address');
        $elAddress->setLabel('Billing Address');
        $elAddress->setRequired(true);
        $this->addElement($elAddress);

        $elZipcode = new Zend_Form_Element_Text('zip');
        $elZipcode->setLabel('Zip Code');
        $elZipcode->setRequired(true);
        $this->addElement($elZipcode);

        $elCreditCard = new Zend_Form_Element_Text('creditcardno');
        $elCreditCard->setLabel('Credit Card Number');
        $elCreditCard->setRequired(true);
        $this->addElement($elCreditCard);

        $elCardType = new Zend_Form_Element_Select('cardtype');
        $elCardType->setLabel('Type of Card');
        $elCardType->setMultiOptions(array('Visa'=>'Visa','Master Card'=>'Master Card','American Express'=>'American Express','Discover'=>'Discover'));
        $elCardType->setRequired(true);
        $this->addElement($elCardType);

        $elExpireMonth = new Zend_Form_Element_Select('expiremonth');
        $elExpireMonth->setLabel('Expire Month');
        $elExpireMonth->setMultiOptions(array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'));
        $elExpireMonth->setRequired(true);
        $this->addElement($elExpireMonth);

        $elExpireYear = new Zend_Form_Element_Select('expireyear');
        $elExpireYear->setLabel('Expire Year');
        $elExpireYear->setMultiOptions(array('2012','2013','2014','2015','2016','2017','2018','2019','2020'));
        $elExpireYear->setRequired(true);
        $this->addElement($elExpireYear);
        
        $elCvv2 = new Zend_Form_Element_Text('cv2');
        $elCvv2->setLabel('Card security code');
        $elCvv2->setRequired(true);
        $this->addElement($elCvv2);
        
        $elToken = new Zend_Form_Element_Hidden('token');
        $elToken->setRequired(true);
        $this->addElement($elToken);
        
        $elSubmit = new Zend_Form_Element_Submit('Pay Now');
        $this->addElement($elSubmit);

        $elCancel = new Zend_Form_Element_Submit('Cancel');
        $this->addElement($elCancel);
    }
}