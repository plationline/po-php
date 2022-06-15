<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();

//set up void config
// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('');
// IV AUTH:
$po->setIV('');

$po->f_login = '';

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
$f_request['f_order_number'] = 'order number';
$f_request['f_sms_message'] = 'poSMS test message';
$f_request['f_phone'] = 'phone number for sms';
$f_request['f_stamp_to_send'] = date("m/d/Y h:i:s a");

$response_void = $po->sms($f_request, 11);

if ($po->get_xml_tag_content($response_void, 'PO_ERROR_CODE') == 1) {
    throw new Exception('<b>ERROR</b>: ' . $po->get_xml_tag_content($response_void, 'PO_ERROR_REASON'));
} else {
    switch ($po->get_xml_tag_content($response_void, 'X_RESPONSE_CODE')) {
        case '11':
            echo 'SMS sent';
            break;
        case '10':
            echo 'SMS not sent';
            break;
    }
}
