<?php
require_once("PlatiOnline/PO5.php");

use PlatiOnline\PO5 as PO5;

$po = new PO5();

$f_request = array();

$partner_info = array();

//contact
$partner_info['contact_customer_support']['f_email'] = 'email@domain.com';       // must not be empty.
$partner_info['contact_customer_support']['f_phone'] = '0000000000';             // must not be empty. Minimum 4 characters, maximum 20
$partner_info['contact_customer_support']['f_first_name'] = 'first name';        // must not be empty. Maximum 50 characters
$partner_info['contact_customer_support']['f_last_name'] = 'last name';          // must not be empty. Maximum 50 characters
$partner_info['contact_customer_support']['f_middle_name'] = '';                 // optional. Maximum 50 characters
$partner_info['contact_customer_support']['f_job_position'] = 'job';             // must not be empty. Maximum 50 characters

$partner_info['administrator']['f_email'] = 'email@domain.com'; // must not be empty.
$partner_info['administrator']['f_phone'] = '0000000000';       // must not be empty. Minimum 4 characters, maximum 20.
$partner_info['administrator']['f_first_name'] = 'first name';  // must not be empty. Maximum 50 characters
$partner_info['administrator']['f_last_name'] = 'last name';    // must not be empty. Maximum 50 characters
$partner_info['administrator']['f_middle_name'] = '';           // optional
$partner_info['administrator']['f_job_position'] = 'job';       // optional. Maximum 50 characters

//invoice
$partner_info['invoice']['f_company'] = 'Test company';         // must not be empty. Maximum 50 characters
$partner_info['invoice']['f_cui'] = '111111';                   // must not be empty. alphanumeric
$partner_info['invoice']['f_reg_com'] = 'J55/99/2000';          // must not be empty. Maximum 50 characters
$partner_info['invoice']['f_country'] = 'Romania';              // optional, maximum 50 characters
$partner_info['invoice']['f_state'] = 'Bucuresti';              // must not be empty. Maximum 50 characters
$partner_info['invoice']['f_city'] = 'Bucuresti';               // must not be empty. Maximum 50 characters
$partner_info['invoice']['f_address'] = 'Address';              // must not be empty. Maximum 250 characters
$partner_info['invoice']['f_zip'] = '999999';                   // optional . Maximum 50 characters

//mcc_list_csv
$partner_info['mcc_list_csv'] = '1701,1702';                    // must not be empty. Maximum 250 characters

//bank info
// $partner_info['bank_info'] is optional, if sent the following rules apply
$partner_info['bank_info']['f_currency'] = 'RON';               // must not be empty, RON/EUR/USD, default RON
$partner_info['bank_info']['f_bank_name'] = 'Bank name';        // must not be empty. Maximum 250 characters
$partner_info['bank_info']['f_bank_iban'] = 'IBAN';             //  must not be empty, no whitespaces, minimum 16 characters
$partner_info['bank_info']['f_bank_country'] = 'RO';            //  must not be empty, RO for now
$partner_info['bank_info']['f_bank_address'] = 'address';       // optional. Maximum 250 characters
$partner_info['bank_info']['f_bank_swift'] = '-';               // optional. Maximum 250 characters

// $partner_info['pos_info'] is optional, if sent the following rules apply
$partner_info['pos_info']['f_website'] = 'domain name, no http/s or www';   // must not be empty. Maximum 250 characters
$partner_info['pos_info']['f_itsn_url'] = 'itsn url';                       // must not be empty.
$partner_info['pos_info']['f_country'] = 'RO';                              // optional. RO. Delivery country, if different from $partner_info['invoice']['f_country']
$partner_info['pos_info']['f_state'] = 'Bucuresti';                         // optional. Maximum 50 characters. Delivery country, if different from $partner_info['invoice']['f_state']
$partner_info['pos_info']['f_city'] = 'Bucuresti';                          // optional. Maximum 50 characters. Delivery city, if different from $partner_info['invoice']['f_city']

$partner_info['sms2client'] = 1;                                // boolean. Should PO send SMS to client when a payment is authorized
$partner_info['auto_settled_no_of_days'] = 0;                   // -1 -> 5, Numbers of days to autosettle, -1 never, 0 same day

$f_request['partner_info'] = $partner_info;

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

$partnerid = $po->register_partner($f_request, 23);
