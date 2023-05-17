<?php

require_once(dirname(__DIR__).'/IyzipayBootstrap.php');

IyzipayBootstrap::init();
$url = 'https://sandbox-api.iyzipay.com';
if ($fl['config']['iyzipay_mode'] == '0') {
	$url = 'https://sandbox-api.iyzipay.com';
}

class Config
{
    public static function options()
    {
    	global $fl,$url;
        $options = new \Iyzipay\Options();
        $options->setApiKey($fl['config']['iyzipay_key']);
        $options->setSecretKey($fl['config']['iyzipay_secret_key']);
        $options->setBaseUrl($url);

        return $options;
    }
}
$ConversationId = rand(11111111,99999999);
$request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
$request->setLocale(\Iyzipay\Model\Locale::TR);
$request->setConversationId($ConversationId);
$request->setCurrency(\Iyzipay\Model\Currency::TL);
$request->setBasketId("B".rand(11111111,99999999));
$request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
$request->setEnabledInstallments(array(2, 3, 6, 9));



$buyer = new \Iyzipay\Model\Buyer();
$buyer->setId($fl['config']['iyzipay_buyer_id']);
$buyer->setName($fl['config']['iyzipay_buyer_name']);
$buyer->setSurname($fl['config']['iyzipay_buyer_surname']);
$buyer->setGsmNumber($fl['config']['iyzipay_buyer_gsm_number']);
$buyer->setEmail($fl['config']['iyzipay_buyer_email']);
$buyer->setIdentityNumber($fl['config']['iyzipay_identity_number']);
$buyer->setRegistrationAddress($fl['config']['iyzipay_address']);
$buyer->setCity($fl['config']['iyzipay_city']);
$buyer->setCountry($fl['config']['iyzipay_country']);
$buyer->setZipCode($fl['config']['iyzipay_zip']);
$request->setBuyer($buyer);



$shippingAddress = new \Iyzipay\Model\Address();
$shippingAddress->setContactName($fl['config']['iyzipay_buyer_name'].' '.$fl['config']['iyzipay_buyer_surname']);
$shippingAddress->setCity($fl['config']['iyzipay_city']);
$shippingAddress->setCountry($fl['config']['iyzipay_country']);
$shippingAddress->setAddress($fl['config']['iyzipay_address']);
$shippingAddress->setZipCode($fl['config']['iyzipay_zip']);
$request->setShippingAddress($shippingAddress);

$billingAddress = new \Iyzipay\Model\Address();
$billingAddress->setContactName($fl['config']['iyzipay_buyer_name'].' '.$fl['config']['iyzipay_buyer_surname']);
$billingAddress->setCity($fl['config']['iyzipay_city']);
$billingAddress->setCountry($fl['config']['iyzipay_country']);
$billingAddress->setAddress($fl['config']['iyzipay_address']);
$billingAddress->setZipCode($fl['config']['iyzipay_zip']);
$request->setBillingAddress($billingAddress);