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

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
$f_request['f_order_number'] = 'master order number';

$raspuns_query = $po->query($f_request, 0);

if ($po->get_xml_tag_content($raspuns_query, 'PO_ERROR_CODE') == 1) {
    die($po->get_xml_tag_content($raspuns_query, 'PO_ERROR_REASON'));
} else {
    $order = $po->get_xml_tag($raspuns_query, 'ORDER');
    $f_order_number = $po->get_xml_tag_content($order, 'F_ORDER_NUMBER'); // order number (this is the same for master and recurrent payments)

    foreach ($order->children as $trx) {
        if ($trx->name == 'tranzaction') {
            $x_trans_id = $po->get_xml_tag_content($trx, 'x_trans_id'); // transaction ID
            $x_parent_trans_id = $po->get_xml_tag_content($trx, 'x_parent_trans_id'); // master trx ID, false if this is the master TRX
            $f_amount = $po->get_xml_tag_content($trx, 'f_amount'); // amount
            $f_currency = $po->get_xml_tag_content($trx, 'f_currency'); // currency
            $status_fin1 = $po->get_xml_tag_content($po->get_xml_tag($trx, 'status_fin1'), 'code'); // starea tranzactiei, 2-autorizata, 8-refuzata, 10-eroare, 13-onhold
        }
    }
}
