<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();

//set up void config
// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');

$po->f_login = 'F_LOGIN from merchant interface';

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
$f_request['f_order_number'] = '';
$f_request['x_trans_id'] = ''; // transaction ID

$response_void = $po->void($f_request, 7);

if ($po->get_xml_tag_content($response_void, 'PO_ERROR_CODE') == 1) {
    throw new Exception('<b>ERROR</b>: ' . $po->get_xml_tag_content($response_void, 'PO_ERROR_REASON'));
} else {
    switch ($po->get_xml_tag_content($response_void, 'X_RESPONSE_CODE')) {
        case '7':
            echo 'Transaction successfully voided';
            break;
        case '10':
            echo 'Errors occured, transaction NOT VOIDED';
            break;
    }
}
