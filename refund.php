<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();

//set up refund config

// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');

$po->f_login = 'F_LOGIN from merchant interface';

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
$f_request['f_order_number'] = ''; // order number
$f_request['f_amount'] = (float)11.01; // needed amount
$f_request['x_trans_id'] = ''; // transaction ID

// optional
//$f_request['execute_now'] = 0; // 1 - operatia de rambursare se efectua imediat 0 - operatia de rambursare se va trimite cursul zilei
//$f_request['partner_cui'] = ''; // Acest tag este luat in considerare doar pentru comerciantii care au o platforma marketplace


$response_refund = $po->refund($f_request, 1);

if ($po->get_xml_tag_content($response_refund, 'PO_ERROR_CODE') == 1) {
    throw new Exception('<b>ERROR</b>: ' . $po->get_xml_tag_content($response_refund, 'PO_ERROR_REASON'));
} else {
    switch ($po->get_xml_tag_content($response_refund, 'X_RESPONSE_CODE')) {
        case '1':
            echo 'The amount of ' . $po->get_xml_tag_content($response_refund, 'F_AMOUNT') . ' successfully refunded';
            break;
        case '10':
            echo 'Errors occured, transaction NOT REFUNDED';
            break;
    }
}
