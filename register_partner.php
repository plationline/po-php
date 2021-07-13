<?php
$f_request = array();

$partner_info = array();

//contact
$partner_info['contact']['f_email'] = 'email@domain.com';    // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['contact']['f_phone'] = '0000000000';        // must not be empty, minimum 4 characters. If empty don't send this tag, it will be filled in PO interface

$partner_info['contact']['f_first_name'] = 'first name';        // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['contact']['f_last_name'] = 'last name';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['contact']['f_middle_name'] = '';
$partner_info['contact']['f_job_position'] = 'job';

$partner_info['administrator']['f_email'] = 'email@domain.com';    // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['administrator']['f_phone'] = '0000000000';
$partner_info['administrator']['f_first_name'] = 'first name';        // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['administrator']['f_last_name'] = 'last name';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['administrator']['f_middle_name'] = '';
$partner_info['administrator']['f_job_position'] = 'job';

//invoice
$partner_info['invoice']['f_company'] = 'Test company';        // optional
$partner_info['invoice']['f_cui'] = '111111';            // optional
$partner_info['invoice']['f_reg_com'] = 'J55/99/2000';        // optional
$partner_info['invoice']['f_cnp'] = '9999999999999';        // optional
$partner_info['invoice']['f_zip'] = '999999';            // optional
$partner_info['invoice']['f_country'] = 'Romania';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['invoice']['f_state'] = 'Bucuresti';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['invoice']['f_city'] = 'Bucuresti';            // must not be empty. If empty don't send this tag, it will be filled in PO interface
$partner_info['invoice']['f_address'] = 'Address';            // must not be empty. If empty don't send this tag, it will be filled in PO interface

//mcc_list_csv
$partner_info['mcc_list_csv'] = '1701,1702';
$f_request['partner_info'] = $partner_info;

require_once("lib/po5.php");

$po = new PO5();

//f_login and RSA key will be saved in config
$po->f_login = 'F_LOGIN from merchant interface';

// INFO f_website
// * if you ARE USING the same PlatiOnline account for multiple websites
// - go to https://merchants.plationline.ro, in Settings tab, POS/Website button, click Add a new POS/website and add your websites
// - after we approve your websites, please use Website/POS value for $f_request['f_website']

$f_request['f_website'] = str_replace('www.', '', $_SERVER['SERVER_NAME']);
// END INFO f_website

// RSA Public AUTH [Merchant side]:
$po->setRSAKeyEncrypt('RSA Public AUTH [Merchant side]');
// IV AUTH:
$po->setIV('IV AUTH');

var_dump($po->register_partner($f_request, 23));

?>
