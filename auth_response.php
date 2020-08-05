<?php
header('Content-Type: text/html; charset=utf-8');
echo '<h1>Transaction response page</h1>';

require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();
// RSA Private ITSN [Merchant side]:
$po->setRSAKeyDecrypt('RSA Private ITSN [Merchant side]');
//IV ITSN:
$po->setIVITSN('IV ITSN');

// POST response for PTOR, POST_S2S_PO_PAGE, POST_S2S_MT_PAGE of f_relay_method
// PTOR is recommended, a redirect from PO server to response page from merchant website is performed
// POST_S2S_PO_PAGE - POST response Server2Server, PlatiOnline template
// POST_S2S_MT_PAGE - POST response Server2Server, merchant template
// SOAP_PO_PAGE - SOAP response, PlatiOnline template
// SOAP_MT_PAGE - SOAP response, merchant template

$authorization_response = $po->auth_response($_POST['F_Relay_Message'], $_POST['F_Crypt_Message']);
//the other POST fields are the fields sent by the merchant in $f_request['merchants_fields'] field, plain text

/**************************************************************************************************
 **************************************************************************************************
 ***                                                                                            ***
 ***        $authorization_response looks like this:                                            ***
 ***    array(1) {                                                                              ***
 ***             ["PO_AUTH_RESPONSE"]=>                                                         ***
 ***             array(14) {                                                                    ***
 ***                ["F_LOGIN"] => string(15) "PO WWW.DEMO.RO"                                  ***
 ***                ["F_WEBSITE"] => string(0) "WWW.DEMO.RO"                                    ***
 ***                ["F_TEST_REQUEST"] => string(1) "1"                                         ***
 ***                ["F_TIMESTAMP"] => string(38) "Fri , 14 Mar 2014 15:10:56 +0300 (GMT)"      ***
 ***                ["F_ORDER_NUMBER"] => string(12) "Order 1 test"                             ***
 ***                ["F_ACTION"] => string(1) "2"                                               ***
 ***                ["F_AMOUNT"] => string(5) "11.99"                                           ***
 ***                ["F_CURRENCY"] => string(3) "RON"                                           ***
 ***                ["X_RESPONSE_CODE"] => string(1) "2"                                        ***
 ***                ["X_RESPONSE_REASON_CODE"] => string(5) "60036"                             ***
 ***                ["X_RESPONSE_REASON_TEXT"] => string(11) "Autorizată"                       ***
 ***                ["X_AUTH_CODE"] => string(6) "POTEST"                                       ***
 ***                ["X_ACTION_CODE"] => string(2) "NA"                                         ***
 ***                ["X_TRANS_ID"] => string(7) "1175053"                                       ***
 ***            }                                                                               ***
 ***        }                                                                                   ***
 ***                                                                                            ***
 **************************************************************************************************
 **************************************************************************************************/

$X_RESPONSE_CODE = $po->get_xml_tag_content($authorization_response, 'X_RESPONSE_CODE');

// ---------------------------------------------------------------------------------------------------------------------------//

/////////////////////////////////////////
// 									   //
//         raspuns metoda PTOR         //
// 									   //
/////////////////////////////////////////


switch ($X_RESPONSE_CODE) {
    case '2':
        //	authorized
        echo '<h2>The transaction was authorized!</h2>';
        // please update the order status in your system
        break;
    case '13':
        //	on hold
        echo '<h2>The transaction is on hold, additional checking is needed!</h2>';
        // please update the order status in your system
        break;
    case '8':
        //	declined
        echo '<h2>The transaction was declined! Reason: ' . $po->get_xml_tag_content($authorization_response, 'X_RESPONSE_REASON_TEXT') . '</h2>';
        // please update the order status in your system
        break;
    case '10';
        //	error
        echo '<h2>An error was encountered in authorization process</h2>';
        // please update the order status in your system
        break;
}


// ---------------------------------------------------------------------------------------------------------------------------//

/////////////////////////////////////////
// 									   //
//   raspuns metoda POST_S2S_PO_PAGE   //
// 									   //
/////////////////////////////////////////

/*
$raspuns_procesat = true;

switch($X_RESPONSE_CODE) {
    case '2':
        //	authorized
        // please update the order status in your system
        break;
    case '13':
        //	on hold
        // please update the order status in your system
        break;
    case '8':
        //	declined
        // please update the order status in your system
        break;
    case '10';
        //	error
        // please update the order status in your system
        break;
    default:
        $raspuns_procesat = false;
}

// this works for f_relay_handshake = 1 in authorization request. I want HANDSHAKE between merchant server and PO server for POST_S2S_PO_PAGE
// if the response was processed, I send TRUE to PO server for PO_Transaction_Response_Processing
// if the response was not processed and I want the PO server to resend the transaction status, I send RETRY to PO server for PO_Transaction_Response_Processing

header('User-Agent:Mozilla/5.0 (Plati Online Relay Response Service)');

if ($raspuns_procesat) {
    header('PO_Transaction_Response_Processing: true');
}
else {
    header('PO_Transaction_Response_Processing: retry');
}
*/

// ---------------------------------------------------------------------------------------------------------------------------//

/////////////////////////////////////////
// 									   //
//   raspuns metoda POST_S2S_MT_PAGE   //
// 									   //
/////////////////////////////////////////

/*
$raspuns_procesat = true;

switch($X_RESPONSE_CODE) {
    case '2':
        //	authorized
        echo '<h2>The transaction was authorized!</h2>';
        // please update the order status in your system
        break;
    case '13':
        //	on hold
        echo '<h2>The transaction is on hold, additional checking is needed!</h2>';
        // please update the order status in your system
        break;
    case '8':
        //	declined
        echo '<h2>The transaction was declined! Reason: '.$po->get_xml_tag_content($authorization_response,'X_RESPONSE_REASON_TEXT').'</h2>';
        // please update the order status in your system
        break;
    case '10';
        //	error
        echo '<h2>An error was encountered in authorization process</h2>';
        // please update the order status in your system
        break;
    default:
        $raspuns_procesat = false;
}

// instead of sending a <h2> tag using echo, you can send an HTML code, based on X_RESPONSE_CODE

// this works for f_relay_handshake = 1 in authorization request. I want HANDSHAKE between merchant server and PO server for POST_S2S_MT_PAGE
// if the response was processed, I send TRUE to PO server for PO_Transaction_Response_Processing
// if the response was not processed and I want the PO server to resend the transaction status, I send RETRY to PO server for PO_Transaction_Response_Processing

header('User-Agent:Mozilla/5.0 (Plati Online Relay Response Service)');

if ($raspuns_procesat) {
    header('PO_Transaction_Response_Processing: true');
}
else {
    header('PO_Transaction_Response_Processing: retry');
}
*/

// ---------------------------------------------------------------------------------------------------------------------------//

/////////////////////////////////////////
// 									   //
//     raspuns metoda SOAP_PO_PAGE     //
// 									   //
/////////////////////////////////////////

/*
$soap_xml = file_get_contents("php://input");
$soap_parsed = $po->parse_soap_response($soap_xml);
$authorization_response = $po->auth_response($soap_parsed['PO_RELAY_REPONSE']['F_RELAY_MESSAGE'], $soap_parsed['PO_RELAY_REPONSE']['F_CRYPT_MESSAGE']);
$X_RESPONSE_CODE = $authorization_response['PO_AUTH_RESPONSE']['X_RESPONSE_CODE'];

$raspuns_procesat = true;

switch($X_RESPONSE_CODE) {
    case '2':
        //	authorized
        // please update the order status in your system
        break;
    case '13':
        //	on hold
        // please update the order status in your system
        break;
    case '8':
        //	declined
        // please update the order status in your system
        break;
    case '10';
        //	error
        // please update the order status in your system
        break;
    default:
        $raspuns_procesat = false;
}

// this works for f_relay_handshake = 1 in authorization request. I want HANDSHAKE between merchant server and PO server for SOAP_PO_PAGE
// if the response was processed, I send TRUE to PO server for PO_Transaction_Response_Processing
// if the response was not processed and I want the PO server to resend the transaction status, I send RETRY to PO server for PO_Transaction_Response_Processing

header('User-Agent:Mozilla/5.0 (Plati Online Relay Response Service)');

if ($raspuns_procesat) {
    header('PO_Transaction_Response_Processing: true');
}
else {
    header('PO_Transaction_Response_Processing: retry');
}
*/

// ---------------------------------------------------------------------------------------------------------------------------//

/////////////////////////////////////////
// 									   //
//     raspuns metoda SOAP_MT_PAGE     //
// 									   //
/////////////////////////////////////////

/*

$soap_xml = file_get_contents("php://input");
$soap_parsed = $po->parse_soap_response($soap_xml);
$authorization_response = $po->auth_response($soap_parsed['PO_RELAY_REPONSE']['F_RELAY_MESSAGE'], $soap_parsed['PO_RELAY_REPONSE']['F_CRYPT_MESSAGE']);
$X_RESPONSE_CODE = $authorization_response['PO_AUTH_RESPONSE']['X_RESPONSE_CODE'];

$raspuns_procesat = true;

switch($X_RESPONSE_CODE) {
    case '2':
        //	authorized
        echo '<h2>The transaction was authorized!</h2>';
        // please update the order status in your system
        break;
    case '13':
        //	on hold
        echo '<h2>The transaction is on hold, additional checking is needed!</h2>';
        // please update the order status in your system
        break;
    case '8':
        //	declined
        echo '<h2>The transaction was declined! Reason: '.$po->get_xml_tag_content($authorization_response,'X_RESPONSE_REASON_TEXT').'</h2>';
        // please update the order status in your system
        break;
    case '10';
        //	error
        echo '<h2>An error was encountered in authorization process</h2>';
        // please update the order status in your system
        break;
    default:
        $raspuns_procesat = false;
}

// instead of sending a <h2> tag using echo, you can send an HTML code, based on X_RESPONSE_CODE

// this works for f_relay_handshake = 1 in authorization request. I want HANDSHAKE between merchant server and PO server for POST_S2S_MT_PAGE
// if the response was processed, I send TRUE to PO server for PO_Transaction_Response_Processing
// if the response was not processed and I want the PO server to resend the transaction status, I send RETRY to PO server for PO_Transaction_Response_Processing

header('User-Agent:Mozilla/5.0 (Plati Online Relay Response Service)');

if ($raspuns_procesat) {
    header('PO_Transaction_Response_Processing: true');
}
else {
    header('PO_Transaction_Response_Processing: retry');
}
*/
