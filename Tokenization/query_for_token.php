<?php
require_once("../PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

// START TRANZACTION QUERY TO OBTAIN CARD INFO AND TOKEN
// YOU MUST OBTAIN A PAYMENT TOKEN EVERY TIME YOU WANT TO AUTHORIZE A NEW PAYMENT (mit_non3DS.php or auth_3DS.php)

$po = new PO5();
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');

// IV AUTH:
$po->setIV('IV AUTH');
$po->f_login = 'F_LOGIN from merchant interface';

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);

$f_request['f_order_number'] = 'order number'; //order number sent at first step
$f_request['x_trans_id'] = 'Tranzaction ID obtained in card verification or auth.php';
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

    // use $token for 3DS or non 3DS payment
}
// END TRANZACTION QUERY TO OBTAIN CARD INFO AND TOKEN
