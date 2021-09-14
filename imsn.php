<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

//decript IMSN call sent by PlatiOnline
$po = new PO5();
// RSA Private ITSN [Merchant side]:
$po->setRSAKeyDecrypt('RSA Private ITSN [Merchant side]');
//IV ITSN:
$po->setIVITSN('IV ITSN');

$imsn = $po->imsn($_POST['f_imsn_message'], $_POST['f_crypt_message']);

//set query config
// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');
$po->f_login = 'F_LOGIN from merchant interface';
$f_request['f_partenerid'] = (int)$po->get_xml_tag_content($imsn, 'F_PARTNERID');
$raspuns_query_partener = $po->query_partener($f_request, 40);

if ($po->get_xml_tag_content($raspuns_query_partener, 'PO_ERROR_CODE') == 1) {
	throw new Exception($po->get_xml_tag_content($raspuns_query_partener, 'PO_ERROR_REASON'));
} else {
	$partenerid = (int)$po->get_xml_tag_content($raspuns_query_partener, 'PARTENERID');
	$merchant_info = $po->get_xml_tag($raspuns_query_partener, 'MERCHANT_INFO');
	$f_login = $po->get_xml_tag_content($merchant_info, 'F_LOGIN');
	$active = $po->get_xml_tag_content($merchant_info, 'ACTIVE');
	$demo_account = $po->get_xml_tag_content($merchant_info, 'DEMO_ACCOUNT');
	$keys_info = $po->get_xml_tag($merchant_info, 'KEYS_INFO');
	$f_auth_rsa_public_key = $po->get_xml_tag_content($keys_info, 'F_AUTH_RSA_PUBLIC_KEY');
	$f_auth_init_vector = $po->get_xml_tag_content($keys_info, 'F_AUTH_INIT_VECTOR');
	$f_itsn_rsa_private_key = $po->get_xml_tag_content($keys_info, 'F_ITSN_RSA_PRIVATE_KEY');
	$f_itsn_init_vector = $po->get_xml_tag_content($keys_info, 'F_ITSN_INIT_VECTOR');
	$f_oauth2_rsa_public_key = $po->get_xml_tag_content($keys_info, 'F_OAUTH2_RSA_PUBLIC_KEY'); // login with PO key
	$f_oauth2_init_vector = $po->get_xml_tag_content($keys_info, 'F_OAUTH2_INIT_VECTOR'); // login with PO key

	// now we save the partner f_login, keys and account status

	// if everything is successful, we send status code 200 using http_response_code(200)
	// if the request is not processed, we send status code 422 using http_response_code(422)
}
