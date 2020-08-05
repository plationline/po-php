<?php
$f_request = array();

$f_request['f_amount'] = (float)0.50;
$f_request['f_currency'] = 'RON';
//$f_request['f_auth_minutes'] = 20; // 0 - waiting forever, 20 - default (in minutes)
$f_request['f_language'] = 'RO';


$f_request['f_order_string'] = '';
$f_request['f_order_number'] = '';
$f_request['x_trans_id'] = ''; // parent transaction ID from where we get the customer info

//custom merchant fields
//$f_request['merchants_fields']['PostQueryString'] = 'postmerchant=posttestmerch'; //PostQueryString
//$f_request['merchants_fields']['GetQueryString'] 	= 'getmerchant=gettestmerch'; //GetQueryString
//$f_request['merchants_fields']['SoapTags'] 		= '<field1>value1</field1><field2>value2</field2>'; //SoapTags

require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();

//f_login and RSA key will be saved in config
$po->f_login = 'F_LOGIN from merchant interface';
$f_request['f_website'] = $po->f_login;

// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');

$po->test_mode = 0;

$po->paylink($f_request, 21);
