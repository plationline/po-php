<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();

//set query config
// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');

$po->f_login = 'F_LOGIN from merchant interface';

$f_request['f_website'] = $po->f_login;
$f_request['f_date'] = ''; //YYYY-MM-DD

$raspuns_query_by_date = $po->query_by_date($f_request, 0);

if ($po->get_xml_tag_content($raspuns_query_by_date, 'PO_ERROR_CODE') == 1) {
    throw new Exception($po->get_xml_tag_content($raspuns_query_by_date, 'PO_ERROR_REASON'));
} else {
    $payments = $po->get_xml_tag($raspuns_query_by_date, 'payments');
    foreach ($payments->children as $order) {
        $f_order_number = $po->get_xml_tag_content($order, 'f_order_number'); // order number
        $tranzaction = $po->get_xml_tag($order, 'tranzaction');
        $f_amount = $po->get_xml_tag_content($tranzaction, 'f_amount'); // amount authorized
        $x_trans_id = $po->get_xml_tag_content($tranzaction, 'x_trans_id'); // transaction ID
        $status_fin1 = $po->get_xml_tag($tranzaction, 'status_fin1');
        $code = $po->get_xml_tag_content($status_fin1, 'code'); // transaction status, 2-authorized, 8-declined, 10,16,17-error, 13-onhold
        echo $x_trans_id . " : " . $code . "<br>";
    }
}
