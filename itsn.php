<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

//decript ITSN call sent by PlatiOnline
$po = new PO5();
// RSA Private ITSN [Merchant side]:
$po->setRSAKeyDecrypt('RSA Private ITSN [Merchant side]');
//IV ITSN:
$po->setIVITSN('IV ITSN');

$call_itsn = $po->itsn($_POST['f_itsn_message'], $_POST['f_crypt_message']);

//set query config
// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');

$po->f_login = 'F_LOGIN from merchant interface';

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
$f_request['f_order_number'] = $po->get_xml_tag_content($call_itsn, 'F_ORDER_NUMBER');
$f_request['x_trans_id'] = $po->get_xml_tag_content($call_itsn, 'X_TRANS_ID');
$raspuns_itsn = $po->query($f_request, 0);

// in case of error:

//  <po_query_response>
//		<po_error_code>1</po_error_code>
//		<po_error_reason><![CDATA[Invalid request]]></po_error_reason>
//	</po_query_response>

if ($po->get_xml_tag_content($raspuns_itsn, 'PO_ERROR_CODE') == 1) {
    throw new Exception($po->get_xml_tag_content($raspuns_itsn, 'PO_ERROR_REASON'));
} else {
    $order = $po->get_xml_tag($raspuns_itsn, 'ORDER');
    $tranzaction = $po->get_xml_tag($order, 'TRANZACTION');

    $F_ORDER_NUMBER = $po->get_xml_tag_content($order, 'F_ORDER_NUMBER');
    $X_TRANS_ID = $po->get_xml_tag_content($tranzaction, 'X_TRANS_ID');
    $starefin1 = $po->get_xml_tag_content($po->get_xml_tag($tranzaction, 'STATUS_FIN1'), 'CODE');
    $starefin2 = $po->get_xml_tag_content($po->get_xml_tag($tranzaction, 'STATUS_FIN2'), 'CODE');

    $stare1 = '<f_response_code>1</f_response_code>';

    switch ($starefin1) {
        case '13':
            //$starefin = 'on-hold';
            // please update the order status in your system
            break;
        case '2':
            //$starefin = 'authorized';
            // please update the order status in your system
            break;
        case '8':
            //$starefin = 'declined';
            // please update the order status in your system
            break;
        case '3':
            //$starefin = 'pending settle';
            // please update the order status in your system
            break;
        case '5':
            /* Verify starefin2 status*/
            switch ($starefin2) {
                case '1':
                    //$starefin='pending refund';
                    // please update the order status in your system
                    break;
                case '2':
                    //$starefin='refund';
                    // please update the order status in your system
                    break;
                case '3':
                    //$starefin='payment refused';
                    // please update the order status in your system
                    break;
                case '4':
                    //$starefin='settled';
                    // please update the order status in your system
                    break;
            }
            break;
        case '6':
            //$starefin= 'pending void';
            // please update the order status in your system
            break;
        case '7':
            //$starefin='voided';
            // please update the order status in your system
            break;
        case '9':
            //$starefin='expired';
            // please update the order status in your system
            break;
        case '10':
        case '16':
        case '17':
            //$starefin='error';
            // please update the order status in your system
            break;
        case '1':
            //$starefin='pending authorization';
            // please update the order status in your system
            break;
        default:
            $stare1 = '<f_response_code>0</f_response_code>';
    }

    /* send ITSN response */
    $raspuns_xml = '<?xml version="1.0" encoding="UTF-8" ?>';
    $raspuns_xml .= '<itsn>';
    $raspuns_xml .= '<x_trans_id>' . $X_TRANS_ID . '</x_trans_id>';
    $raspuns_xml .= '<merchServerStamp>' . date("Y-m-d\TH:i:s") . '</merchServerStamp>';
    $raspuns_xml .= $stare1;
    $raspuns_xml .= '</itsn>';

    echo $raspuns_xml;
}
