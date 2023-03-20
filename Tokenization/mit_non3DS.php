<?php
require_once("../PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

// START TRANZACTION AUTH BY TOKEN
$f_request = array();

$f_request['f_amount'] = (float)0.50;
$f_request['f_currency'] = 'RON';
//$f_request['f_auth_minutes'] = 20; // 0 - waiting forever, 20 - default (in minutes)
$f_request['f_language'] = 'RO';

$f_request['f_order_string'] = 'Sale by token';
$f_request['f_order_number'] = 'order number'; //order number sent at card verification
$f_request['x_trans_id'] = 'master transaction ID'; // initial tranzaction number obtained in card_verification.php or auth.php
$f_request['x_payment_token'] = 'payment token'; // payment token obtained by query_for_token.php

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

// END TRANZACTION AUTH BY TOKEN
