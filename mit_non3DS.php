<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

// 1. START CARD VERIFICATION - INITIAL TRANZACTION
$f_request = array();

$f_request['f_order_number'] = 'order number';
$f_request['f_amount'] = 0;
$f_request['f_currency'] = 'RON/EUR/USD'; // choose one currency
//$f_request['f_auth_minutes'] = 20; // 0 - waiting forever, 20 - default (in minutes)
$f_request['f_language'] = 'RO'; // RO / EN / HU / IT / FR / DE / ES

$customer_info = array();

//contact
$customer_info['contact']['f_email'] = 'email@domain.com';    // must not be empty. If empty don't send this tag, it will be filled in PO interface
$customer_info['contact']['f_phone'] = '0231100100';        // must not be empty, minimum 4 characters. If empty don't send this tag, it will be filled in PO interface
$customer_info['contact']['f_mobile_number'] = '0799999999';
$customer_info['contact']['f_send_sms'] = 1;                   // 1 - sms client notification 0 - no notification
$customer_info['contact']['f_first_name'] = 'first name';        // must not be empty. If empty don't send this tag, it will be filled in PO interface
$customer_info['contact']['f_last_name'] = 'last name';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
//$customer_info['contact']['f_middle_name'] 	 = '';

//invoice
$customer_info['invoice']['f_company'] = 'Test company';        // optional
$customer_info['invoice']['f_cui'] = '111111';            // optional
$customer_info['invoice']['f_reg_com'] = 'J55/99/2000';        // optional
$customer_info['invoice']['f_cnp'] = '9999999999999';        // optional
$customer_info['invoice']['f_zip'] = '999999';            // optional
$customer_info['invoice']['f_country'] = 'Romania';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$customer_info['invoice']['f_state'] = 'Bucuresti';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$customer_info['invoice']['f_city'] = 'Bucuresti';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$customer_info['invoice']['f_address'] = 'Address';            // must not be empty. If empty don't send this tag, it will be filled in PO interface

$f_request['customer_info'] = $customer_info;

$shipping_info = array();

$shipping_info['same_info_as'] = 0; // 0 - different info, 1- same info as customer_info

//contact
$shipping_info['contact']['f_email'] = 'email@domain.com';
$shipping_info['contact']['f_phone'] = '0231999999';
$shipping_info['contact']['f_mobile_number'] = '0749999999';
$shipping_info['contact']['f_send_sms'] = 1;                   // 1 - sms client notification 0 - no notification
$shipping_info['contact']['f_first_name'] = 'first name';
$shipping_info['contact']['f_last_name'] = 'last name';
//$shipping_info['contact']['f_middle_name'] 	 = '';

//address
$shipping_info['address']['f_company'] = 'test company';
$shipping_info['address']['f_zip'] = '999999';
$shipping_info['address']['f_country'] = 'Romania';
$shipping_info['address']['f_state'] = 'Bucuresti';
$shipping_info['address']['f_city'] = 'Bucuresti';
$shipping_info['address']['f_address'] = substr('Address', 0, 100);

// shipping info may not be sent if no shipping is necessary (virtual products)
$f_request['shipping_info'] = $shipping_info;

$transaction_relay_response = array();

$transaction_relay_response['f_relay_response_url'] = 'http://domain.com/auth_response.php';

// INFO f_relay_method
$transaction_relay_response['f_relay_method'] = 'PTOR'; // PTOR, POST_S2S_PO_PAGE, POST_S2S_MT_PAGE, SOAP_PO_PAGE, SOAP_MT_PAGE
// * if your website has SSL enabled, use PTOR relay method. It will redirect the client to merchant website at f_relay_response_url
// * if your website DOES NOT USE SSL, use POST_S2S_PO_PAGE. It will show the PlatiOnline response page and we will send the tranzaction response SERVER-to-SERVER to f_relay_response_url so you can update the order status
// END INFO f_relay_method

$transaction_relay_response['f_post_declined'] = 1; // Valoarea = 1	(default value; sistemul PO trimite rezultatul la f_relay_response_url prin metoda f_relay_method)	Valoarea = 0	(systemul PO trimite rezultatul doar pentru tranzactiile "Autorizate" si "In curs de verificare" la <f_relay_response_url> prin metoda <f_relay_method>)
$transaction_relay_response['f_relay_handshake'] = 1; // default 1
$f_request['transaction_relay_response'] = $transaction_relay_response;
//$f_request['tracking_script'] = "";

$f_request['f_order_cart'] = array();

// PLEASE READ
// $item['itemprice'] - the price WITOUT VAT for 1 piece of the product
// $item['vat']		  - VAT for 1 piece of the product * $item['qty']
// END PLEASE READ


// DO THIS FOR ALL CART ITEMS
$item = array();

$item['prodid'] = 1;
$item['name'] = substr('Card verification', 0, 250);
$item['description'] = substr('Card verification', 0, 250);
$item['qty'] = 1;
$item['itemprice'] = (float)1; // price WITOUT VAT for 1 piece of the product
$item['vat'] = (float)0;  // VAT for 1 piece of the product * $item['qty']
$item['stamp'] = date('Y-m-d');
$item['prodtype_id'] = 0;

$f_request['f_order_cart'][] = $item;

//shipping
$shipping = array();
$shipping['name'] = substr('Shipping', 0, 250);
$shipping['price'] = (float)0;
$shipping['pimg'] = 0;
$shipping['vat'] = (float)0;

$f_request['f_order_cart']['shipping'] = $shipping;
$f_request['f_order_string'] = 'Order number ' . $f_request['f_order_number'] . ' on website http://domain.com';

//custom merchant fields - they will be returned to you in f_relay_response_url by POST or GET or SOAP, according to where you send them
//$f_request['merchants_fields']['PostQueryString'] = 'postmerchant=posttestmerch'; //PostQueryString
//$f_request['merchants_fields']['GetQueryString'] 	= 'getmerchant=gettestmerch'; //GetQueryString
//$f_request['merchants_fields']['SoapTags'] 		= [ 'root' => ['field1' => 'value1', 'field2' => 'value2'] ]; //SoapTags

$po = new PO5();

//f_login and RSA key will be saved in config
$po->f_login = 'F_LOGIN from merchant interface';

// INFO f_website
// * if you ARE USING the same PlatiOnline account for multiple websites
// - go to https://merchants.plationline.ro, in Settings tab, POS/Website button, click Add a new POS/website and add your websites
// - after we approve your websites, please use Website/POS value for $f_request['f_website']

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);

// maximum trx amount for future payment using f_action = 25
//$f_request['f_mit_trx_max_amount'] = 200;

// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');

// IV AUTH:
$po->setIV('IV AUTH');
//end f_login and RSA key will be saved in config

// test mode: 0 - disabled, 1 - enabled
$po->test_mode = 1;

// plationline request for token payment
$auth_response = $po->auth($f_request, 24); // parameter 1 - request content, 2 - f_action (request for token payments.)
$redirect_url = $po->get_xml_tag_content($auth_response, 'PO_REDIRECT_URL');
$transid = $po->get_xml_tag_content($auth_response, 'X_TRANS_ID');
if (!empty($redirect_url)) {
    header('Location: ' . $redirect_url);
} else {
    throw new \Exception('ERROR: Serverul nu a intors URL-ul pentru a finaliza tranzactia!');
}
// this will redirect the customer to PlatiOnline page
die();

// Transaction response: Auth - card verified - X_RESPONSE_CODE = 2, transitions automatically to 20
// ITSN: Auth - card verified - STATUS_FIN1 = 20

// 1. END CARD VERIFICATION - INITIAL TRANZACTION

// 2. START TRANZACTION QUERY TO OBTAIN CARD INFO AND TOKEN
// YOU MUST OBTAIN A PAYMENT TOKEN EVERY TIME YOU WANT TO AUTHORIZE A MIT PAYMENT
$po = new PO5();
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');

// IV AUTH:
$po->setIV('IV AUTH');
$po->f_login = 'F_LOGIN from merchant interface';

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);

$f_request['f_order_number'] = 'order number'; //order number sent at first step
$f_request['x_trans_id'] = 'Tranzaction ID obtained in first step'; //initial tranzaction number obtained at first step ($transid)
$f_request['x_request_payment_token'] = 1;
$raspuns_query = $po->query($f_request, 0);

if ($po->get_xml_tag_content($raspuns_query, 'PO_ERROR_CODE') == 1) {
    throw new Exception($po->get_xml_tag_content($raspuns_query, 'PO_ERROR_REASON'));
} else {
    $order = $po->get_xml_tag($raspuns_query, 'ORDER');
    $order_number = $po->get_xml_tag_content($order, 'F_ORDER_NUMBER');
    $tranzaction = $po->get_xml_tag($order, 'TRANZACTION');
    $X_TRANS_ID = $po->get_xml_tag_content($tranzaction, 'X_TRANS_ID');
    $starefin1 = $po->get_xml_tag_content($po->get_xml_tag($tranzaction, 'STATUS_FIN1'), 'CODE');
    $starefin2 = $po->get_xml_tag_content($po->get_xml_tag($tranzaction, 'STATUS_FIN2'), 'CODE');

    $po_payment_token = $po->get_xml_tag($raspuns_query, 'PO_PAYMENT_TOKEN');
    $token = $po->get_xml_tag_content($po_payment_token, 'TOKEN');
    $token_expire_timestamp = $po->get_xml_tag_content($po_payment_token, 'TOKEN_EXPIRE_TIMESTAMP');
    $cc_6x4 = $po->get_xml_tag_content($po_payment_token, 'CC_6X4');
    $cc_issuer_name = $po->get_xml_tag_content($po_payment_token, 'CC_ISSUER_NAME');
    $cc_issuer_country = $po->get_xml_tag_content($po_payment_token, 'CC_ISSUER_COUNTRY');
    $cc_expire_timestamp = $po->get_xml_tag_content($po_payment_token, 'CC_EXPIRE_TIMESTAMP');
}
// 2. END TRANZACTION QUERY TO OBTAIN CARD INFO AND TOKEN


// 3. START TRANZACTION AUTH BY TOKEN
$f_request = array();

$f_request['f_amount'] = (float)0.50;
$f_request['f_currency'] = 'RON';
//$f_request['f_auth_minutes'] = 20; // 0 - waiting forever, 20 - default (in minutes)
$f_request['f_language'] = 'RO';

$f_request['f_order_string'] = 'Sale with token';
$f_request['f_order_number'] = $order_number; //order number sent at first step
$f_request['x_trans_id'] = $X_TRANS_ID; // initial tranzaction number obtained at first step
$f_request['x_payment_token'] = $token; // payment token obtained by query in step 2

$po = new PO5();
$po->f_login = 'F_LOGIN from merchant interface';
$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);

// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');

// IV AUTH:
$po->setIV('IV AUTH');
$po->test_mode = 1;

$sale_response = $po->sale_by_token($f_request, 25);

$X_RESPONSE_CODE = $po->get_xml_tag_content($sale_response, 'X_RESPONSE_CODE');
$X_TRANS_ID = $po->get_xml_tag_content($sale_response, 'X_TRANS_ID');

// $X_RESPONSE_CODE
// 2 - auth
// 8 - declined

// 3. END TRANZACTION AUTH BY TOKEN
