<?php
$f_request = array();

$f_request['f_order_number'] = 'order number';
$f_request['f_amount'] = (float)21.59;
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

//$f_request['tracking_script'] = 'tracking script';

$f_request['f_order_cart'] = array();

// PLEASE READ
// $item['itemprice'] - the price WITOUT VAT for 1 piece of the product
// $item['vat']		  - VAT for 1 piece of the product * $item['qty']
// END PLEASE READ

for ($i = 0; $i < 2; $i++) {
    $item = array();

    $item['prodid'] = 1;
    $item['name'] = substr('Produs ' . $i, 0, 250);
    $item['description'] = substr('Descriere ' . $i, 0, 250);
    $item['qty'] = 2;
    $item['itemprice'] = (float)11.05; // price WITOUT VAT for 1 piece of the product
    $item['vat'] = (float)2.22;  // VAT for 1 piece of the product * $item['qty']
    $item['stamp'] = date('Y-m-d');
    $item['prodtype_id'] = 0;

    $f_request['f_order_cart'][] = $item;
}

// ACTIVATE ONLY IF YOU USE COUPONS
/*
//coupon 1
$coupon1 = array();
$coupon1['key'] 		= '0002C';
$coupon1['value'] 		= (float)10.00;
$coupon1['percent'] 	= 1;
$coupon1['workingname']	= 'Cupon reducere';
$coupon1['type'] 		= 0;
$coupon1['scop'] 		= 0;
$coupon1['vat'] 		= (float)1.11;
$f_request['f_order_cart']['coupon1'] = $coupon1;

//coupon 2
$coupon2 = array();
$coupon2['key'] 		= '0002D';
$coupon2['value'] 		= (float)7.50;
$coupon2['percent'] 	= 0;
$coupon2['workingname']	= 'Cupon reducere';
$coupon2['type'] 		= 0;
$coupon2['scop'] 		= 0;
$coupon2['vat'] 		= (float)0.11;
$f_request['f_order_cart']['coupon2'] = $coupon2;

// declare $f_request['f_order_cart']['coupon1'], $f_request['f_order_cart']['coupon2']; we index the field ['coupon'] to have different names in array and to avoid overwriting the values
// the array to xml method takes care of this case by looking for "coupon" substring
$f_request['f_order_cart']['coupon1'] = $coupon1;
$f_request['f_order_cart']['coupon2'] = $coupon2;
*/
// END ACTIVATE ONLY IF YOU USE COUPONS

//shipping
$shipping = array();
$shipping['name'] = substr('Shipping 1', 0, 250);
$shipping['price'] = (float)15.5;
$shipping['pimg'] = 0;
$shipping['vat'] = (float)2.5;

$f_request['f_order_cart']['shipping'] = $shipping;
$f_request['f_order_string'] = 'Order number ' . $f_request['f_order_number'] . ' on website http://domain.com';

// TRA
// $f_request['f_trx_risk_analysis'] = 0; // 0 - false, 1 - true
// Acest tag este luat in considerare doar pentru comerciantii care au semnat anexa TRA si pentru care s-au activat exceptiile 3DS2 - TRA "Transaction Risk Analysis" false - tranzactia se va trimite fara exceptie - default value true - tranzactia se va trimite cu exceptie

//custom merchant fields - they will be returned to you in f_relay_response_url by POST or GET or SOAP, according to where you send them
//$f_request['merchants_fields']['PostQueryString'] = 'postmerchant=posttestmerch'; //PostQueryString
//$f_request['merchants_fields']['GetQueryString'] 	= 'getmerchant=gettestmerch'; //GetQueryString
//$f_request['merchants_fields']['SoapTags'] 		= '<field1>value1</field1><field2>value2</field2>'; //SoapTags

// Acest tag este luat in considerare doar pentru comerciantii care au semnat anexa pentru serviciul de carduri de vacanta true - nu se aplica restrictii la cardul utilizat - default value true - plata nu se poate face cu cardurile de vacanta emise de: Sodexo, UpRomania, EdenRed
//$f_request['f_allow_card_vacanta'] = false;

require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();

//f_login and RSA key will be saved in config
$po->f_login = 'F_LOGIN from merchant interface';

// INFO f_website
// * if you ARE USING the same PlatiOnline account for multiple websites
// - go to https://merchants.plationline.ro, in Settings tab, POS/Website button, click Add a new POS/website and add your websites
// - after we approve your websites, please use Website/POS value for $f_request['f_website']

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
// END INFO f_website

// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');
//end f_login and RSA key will be saved in config

// test mode: 0 - disabled, 1 - enabled
$po->test_mode = 1;

// OPTIONAL - send email to client requesting payment
// days of valability for payment link OR
// stamp2expire - payment link will expire at that given stamp
/*$f_request['paylink'] = array(
    'email2client' => 1,
    'sms2client' => 0,
    'daysofvalability' => 30,
    'stamp2expire' => date('Y-m-d\TH:i:s', strtotime('+1 day'))
);*/
// END OPTIONAL - send email to client requesting payment

// plationline authorization call
// simple payment, no installments / FARA RATE
$auth_response = $po->auth($f_request, 2); // parameter 1 - request content, 2 - f_action (2 simple payment, 12 - ghiseu Posta romana, 13 - ghiseu Raiffeisen Bank)
$redirect_url = $po->get_xml_tag_content($auth_response, 'PO_REDIRECT_URL');
$transid = $po->get_xml_tag_content($auth_response, 'X_TRANS_ID');
if (!empty($redirect_url)) {
    header('Location: ' . $redirect_url);
} else {
    throw new \Exception('ERROR: Serverul nu a intors URL-ul pentru a finaliza tranzactia!');
}

// FOR INSTALLMENTS / PENTRU RATE

// $f_request['f_rate'] = (int)'number of installments chosen by the customer';
// EX: $f_request['f_rate'] = 6;

// AUTH CALL FOR INSTALLMENTS
// $po->auth($f_request, 16);
/*
10 - Raiffeisen Bank installments
16 - Banca Transilvania installments
18 - Alpha Bank Installments
22 - BRD Finance
23 - Garanti Bank
*/

// END FOR INSTALLMENTS / PENTRU RATE

// FOR RECCURENCE

// $f_request['f_recurring_frequency'] 		   = 3;  // frequency: 1 - weekly, 2 - every 2 weeks, 3 - monthly, 4 - quarterly, 5 - semestrial, 8 - annually
// $f_request['f_recurring_expiration_date']   = ''; // reccurence expiry date (format YYYY-mm-dd) - maximum 60 months
// $po->auth($f_request, 20);

// END FOR RECCURENCE
