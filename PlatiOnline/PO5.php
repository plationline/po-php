<?php
namespace PlatiOnline;

use sylouuu\Curl\Method as Curl;
use phpseclib\Crypt\AES as AES;
use phpseclib\Crypt\RSA as RSA;

class PO5
{
    // private
    private $f_request 	  							= null;
    private $f_secure 	  							= null;
    private $aes_key 	  							= null;
    private $iv 		  							= null;
    private $iv_itsn 	  							= null;
    private $rsa_key_enc  							= null;
    private $rsa_key_dec  							= null;
    private $url		  							= 'https://secure.plationline.ro/';
    private $url_sv_request_xml 					= 'https://schemas.plationline.ro/po.request.v5.xsd'; 		// any call
    private $url_sv_auth_xml 						= 'https://schemas.plationline.ro/f_message.auth.v5.xsd'; 	// auth
    private $url_sv_auth_url_xml 					= 'https://schemas.plationline.ro/auth.url.response.v5.xsd'; // auth url
    private $url_sv_auth_response_xml 				= 'https://schemas.plationline.ro/auth.response.v5.xsd'; 	// auth response
    private $url_sv_auth_response_soap_xml 			= 'https://schemas.plationline.ro/auth.soap.response.v5.xsd';// auth response soap
    private $url_sv_itsn_xml 						= 'https://schemas.plationline.ro/itsn.v5.xsd'; 				// itsn
    private $url_sv_query_xml 						= 'https://schemas.plationline.ro/f_message.query.v5.xsd'; 	// query
    private $url_sv_itsn_response_xml 				= 'https://schemas.plationline.ro/query.response.v5.xsd'; 	// query response
    private $url_sv_query_by_date_xml 				= 'https://schemas.plationline.ro/f_message.query-by-date.v5.xsd'; 	// query by date
    private $url_sv_query_by_date_response_xml 		= 'https://schemas.plationline.ro/query-by-date.response.v5.xsd'; 	// query response
    private $url_sv_sale_token_xml 					= 'https://schemas.plationline.ro/f_message-sale-by-token.xsd'; 	// sale-by-token
    private $url_sv_sale_token_response_xml 		= 'https://schemas.plationline.ro/po-auth-by-token-response.xsd'; 	// sale-by-token response
    private $url_sv_settle_xml 						= 'https://schemas.plationline.ro/f_message.settle.v5.xsd'; 	// settle
    private $url_sv_settle_response_xml 			= 'https://schemas.plationline.ro/settle.response.v5.xsd'; 	// settle response
    private $url_sv_void_xml 						= 'https://schemas.plationline.ro/f_message.void.v5.xsd'; 	// void
    private $url_sv_void_response_xml 				= 'https://schemas.plationline.ro/void.response.v5.xsd'; 	// void response
    private $url_sv_refund_xml 						= 'https://schemas.plationline.ro/f_message.refund.v5.xsd'; 	// refund
    private $url_sv_refund_response_xml 			= 'https://schemas.plationline.ro/refund.response.v5.xsd'; 	// refund response
    private $url_sv_paylink_xml 					= 'https://schemas.plationline.ro/f_message.paylink.xsd'; 	// paylink
    private $url_sv_paylink_response_xml 			= 'https://schemas.plationline.ro/pay.link.by.trxid.url.response.xsd'; 	// paylink response
    private $url_sv_cancel_recurrence_xml 		    = 'https://schemas.plationline.ro/f_message.cancel.recurrence.v5.xsd'; 	// cancel recurring
    private $url_sv_cancel_recurrence_response_xml  = 'https://schemas.plationline.ro/cancel.recurrence.response.v5.xsd'; 	// cancel recurring response
    private $url_sv_partner_xml                     = 'https://schemas.plationline.ro/f_message.partner.v5.xsd';    // add partner
    private $url_sv_imsn_xml                        = 'https://schemas.plationline.ro/imsn-v1.xsd';    // imsn partner
    private $url_sv_query_partener_xml              = 'https://schemas.plationline.ro/f_message.query-partener.v1.xsd';    // query partener call
    private $url_sv_query_partener_response_xml     = 'https://schemas.plationline.ro/query-partener.response.v1.xsd';    // query partener response
    private $url_sv_query_by_rrn_xml 		        = 'https://schemas.plationline.ro/f_message.query-by-rrn.v1.xsd'; 	// query by rrn
    private $url_sv_sms_xml                         = 'https://schemas.plationline.ro/f_message.sms.v5.xsd';
    private $url_sv_sms_response_xml                = 'https://schemas.plationline.ro/sendSMS.response.v5.xsd';

    // public
    public $f_login 	= null;
    public $version		= null;
    public $test_mode   = 0;

    public function __construct()
    {
        $this->version = "PO 5.1.0 XML";
        $errors = array();
        $php_min_version = '5.5';
        $curl_min_version = '7.29.0';
        $openssl_min_version = 0x1000100f; //1.0.1
        if (version_compare(phpversion(), $php_min_version, '<')) {
            $errors[] = 'PHP version '.$php_min_version.' is needed to use PlatiOnline kit. Current PHP version: '.phpversion();
        }
        if (!extension_loaded('soap')) {
            $errors[] = 'SOAP extension active is needed to use PlatiOnline kit. The SOAP extension is currently DISABLED!';
        }
        if (!extension_loaded('curl')) {
            $errors[] = 'cURL extension active is needed to use PlatiOnline kit. The cURL extension is currently DISABLED!';
        }
        if (version_compare(curl_version()['version'], $curl_min_version, '<')) {
            $errors[] = 'cURL version '.$curl_min_version.' is needed to use PlatiOnline kit. The cURL version is currently '.curl_version()['version'].'!';
        }
        if (OPENSSL_VERSION_NUMBER < $openssl_min_version) {
            $errors[] = 'OpenSSL version 1.0.1 is needed to use PlatiOnline kit.';
        }
        if (!empty($errors)) { //daca avem erori
            $errors_string = '';
            foreach ($errors as $error) {
                $errors_string .= $error . "; ";
            }
            throw new \Exception($errors_string . 'Please fix the above mentioned errors to use this PlatiOnline kit');
        }
        self::registerAutoload('phpseclib');
        self::registerAutoload('Curl');
    }

    //////////////////////////////////////////////////////////////
    // 						PUBLIC METHODS						//
    //////////////////////////////////////////////////////////////

    // setez cheia RSA pentru criptare
    public function setRSAKeyEncrypt($rsa_key_enc)
    {
        $this->rsa_key_enc = $rsa_key_enc;
    }

    // setez cheia RSA pentru decriptare
    public function setRSAKeyDecrypt($rsa_key_dec)
    {
        $this->rsa_key_dec = $rsa_key_dec;
    }

    // setez initial vector
    public function setIV($iv)
    {
        $this->iv = $iv;
    }

    // setez initial vector ITSN
    public function setIVITSN($iv)
    {
        $this->iv_itsn = $iv;
    }

    public function sale_by_token($f_request, $f_action = 25)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_payment_sale_by_token', $this->url_sv_sale_token_xml);
        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'sale-by-token',
            'stream_context' => $context,
            'trace' => 1
        ));
        $response = $client->__doRequest($request, $this->url, 'sale-by-token', 1);
        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de autorizare!');
        }
        $this->validate_xml($response, $this->url_sv_sale_token_response_xml);
        return $this->xml_to_object($response);
    }

    public function register_partner($f_request, $f_action = 23) {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'register_partener', $this->url_sv_partner_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'register-partener',
            'stream_context' => $context
        ));

        $response = $client->__doRequest($request, $this->url, 'register-partener', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de autorizare!');
        }

        $register_response = $this->xml_to_object($response);
        if ($this->get_xml_tag_content($register_response, 'PO_ERROR_CODE') == 1) {
            die($this->get_xml_tag_content($register_response, 'PO_ERROR_REASON'));
        } else {
            $X_PARTNER_ID = $this->get_xml_tag_content($register_response, 'X_PARTNER_ID');
            if (!empty($X_PARTNER_ID)) {
                return $X_PARTNER_ID;
            } else {
                die('<b>ERROR</b>: Serverul nu a intors raspuns pentru Register Partener!');
            }
        }
    }

    public function paylink($f_request, $f_action = 21)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_payment_link_by_trxid', $this->url_sv_paylink_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'pay-link-by-trxid',
            'stream_context' => $context
        ));

        $response = $client->__doRequest($request, $this->url, 'pay-link-by-trxid', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de autorizare!');
        }
        $this->validate_xml($response, $this->url_sv_paylink_response_xml);
        $paylink_response = $this->xml_to_object($response);

        if ($this->get_xml_tag_content($paylink_response, 'PO_ERROR_CODE') == 1) {
            throw new \Exception($this->get_xml_tag_content($paylink_response, 'PO_ERROR_REASON'));
        } else {
            $redirect_url 	= $this->get_xml_tag_content($paylink_response, 'PO_REDIRECT_URL');
            $X_TRANS_ID 	= $this->get_xml_tag_content($paylink_response, 'X_TRANS_ID');
            if (!empty($redirect_url)) {
                header('Location: '. $redirect_url);
            } else {
                throw new \Exception('ERROR: Serverul nu a intors URL-ul pentru a finaliza tranzactia!');
            }
        }
    }

    public function auth($f_request, $f_action = 2)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_auth_request', $this->url_sv_auth_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'auth-only',
            'stream_context' => $context
        ));

        $response = $client->__doRequest($request, $this->url, 'auth-only', 1);
        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de autorizare!');
        }

        $this->validate_xml($response, $this->url_sv_auth_url_xml);
        $auth_response = $this->xml_to_object($response);
        if ($this->get_xml_tag_content($auth_response, 'PO_ERROR_CODE') == 1) {
            throw new \Exception($this->get_xml_tag_content($auth_response, 'PO_ERROR_REASON'));
        } else {
            return $auth_response;
        }
    }

    // obtin raspunsul pentru cererea de autorizare
    public function auth_response($f_relay_message, $f_crypt_message)
    {
        return $this->decrypt_response($f_relay_message, $f_crypt_message, $this->url_sv_auth_response_xml);
    }

    // obtin datele din notificarea ITSN
    public function itsn($f_relay_message, $f_crypt_message)
    {
        return $this->decrypt_response($f_relay_message, $f_crypt_message, $this->url_sv_itsn_xml);
    }

	// obtin datele din notificarea IMSN
	public function imsn($f_imsn_message, $f_crypt_message)
	{
		return $this->decrypt_response($f_imsn_message, $f_crypt_message, $this->url_sv_imsn_xml);
	}

    // interogare
    public function query($f_request, $f_action = 0)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_query', $this->url_sv_query_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'query',
            'stream_context' => $context
        ));
        $response = $client->__doRequest($request, $this->url, 'query', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de interogare!');
        }
        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_itsn_response_xml);
        return $this->xml_to_object($response);
    }

	// interogare
	public function query_partener($f_request, $f_action = 40)
	{
		// ne asiguram ca stergem tot ce e in campul f_request
		$this->f_request = null;
		$f_request['f_action'] = $f_action;
		$request = $this->setFRequest($f_request, 'po_query_partener', $this->url_sv_query_partener_xml);

		$opts = array(
			'http' => array(
				'user_agent' => 'PlatiOnline-SOAP',
			),
		);
		$context = stream_context_create($opts);
		$client = new \SoapClient(null, array(
			'location'       => $this->url,
			'uri'            => 'query-partener',
			'stream_context' => $context,
		));
		$response = $client->__doRequest($request, $this->url, 'query-partener', 1);
		if (!isset($response) || empty($response)) {
			die('<b>ERROR</b>: Nu am putut comunica cu serverul PO pentru operatiunea de interogare partener!');
		}
		// validez xml-ul primit ca raspuns de la PO
		$this->validate_xml($response, $this->url_sv_query_partener_response_xml);
		return $this->xml_to_object($response);
	}

    public function query_by_date($f_request, $f_action = 0)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_query', $this->url_sv_query_by_date_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'query-by-date',
            'stream_context' => $context
        ));
        $response = $client->__doRequest($request, $this->url, 'query-by-date', 1);
        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de interogare!');
        }
        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_query_by_date_response_xml);
        return $this->xml_to_object($response);
    }

    // interogare dupa RRN
    public function query_by_rrn($f_request, $f_action = 0)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_query', $this->url_sv_query_by_rrn_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'query-by-rrn',
            'stream_context' => $context
        ));
        $response = $client->__doRequest($request, $this->url, 'query-by-rrn', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de interogare dupa RRN!');
        }
        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_query_by_date_response_xml );
        return $this->xml_to_object($response);
    }

    public function settle($f_request, $f_action = 3)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_settle', $this->url_sv_settle_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'settle',
            'stream_context' => $context
        ));
        $response = $client->__doRequest($request, $this->url, 'settle', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de incasare!');
        }

        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_settle_response_xml);

        return $this->xml_to_object($response);
    }

    public function void($f_request, $f_action = 7)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_void', $this->url_sv_void_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'void',
            'stream_context' => $context
        ));

        $response = $client->__doRequest($request, $this->url, 'void', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de anulare!');
        }

        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_void_response_xml);

        return $this->xml_to_object($response);
    }

    public function refund($f_request, $f_action = 1)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_refund', $this->url_sv_refund_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'refund',
            'stream_context' => $context
        ));
        $response = $client->__doRequest($request, $this->url, 'refund', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de creditare!');
        }

        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_refund_response_xml);

        return $this->xml_to_object($response);
    }

    public function cancel_recurrence($f_request, $f_action = 26)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $request = $this->setFRequest($f_request, 'po_cancel_recurrence', $this->url_sv_cancel_recurrence_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP'
            )
        );
        $context = stream_context_create($opts);
        $client  = new \SoapClient(null, array(
            'location' 		 => $this->url,
            'uri'      		 => 'cancel-recurring',
            'stream_context' => $context
        ));

        $response = $client->__doRequest($request, $this->url, 'cancel-recurring', 1);

        if (empty($response)) {
            throw new \Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de anulare recurenta!');
        }

        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_cancel_recurrence_response_xml);

        return $this->xml_to_object($response);
    }

    public function sms($f_request, $f_action = 11)
    {
        // ne asiguram ca stergem tot ce e in campul f_request
        $this->f_request = null;
        $f_request['f_action'] = $f_action;
        $f_request['f_stamp_to_send'] = date('Y-m-d\TH:i:sP', strtotime($f_request['f_stamp_to_send']));

        $request = $this->setFRequest($f_request, 'po_send_sms', $this->url_sv_sms_xml);

        $opts = array(
            'http' => array(
                'user_agent' => 'PlatiOnline-SOAP',
            ),
        );
        $context = stream_context_create($opts);
        $client = new SoapClient(null, array(
            'location'       => $this->url,
            'uri'            => 'sms',
            'stream_context' => $context,
        ));

        $response = $client->__doRequest($request, $this->url, 'sms', 1);

        if (empty($response)) {
            throw new Exception('ERROR: Nu am putut comunica cu serverul PO pentru operatiunea de trimitere SMS!');
        }

        // validez xml-ul primit ca raspuns de la PO
        $this->validate_xml($response, $this->url_sv_sms_response_xml);

        return $this->xml_to_object($response);
    }

    public function get_xml_tag($object, $tag)
    {
        $children = $object->children;
        foreach ($children as $child) {
            if (trim(strtoupper($child->name)) == trim(strtoupper($tag))) {
                return $child;
            }
        }
        return false;
    }

    public function get_xml_tag_content($object, $tag)
    {
        $children = $object->children;
        foreach ($children as $child) {
            if (trim(strtoupper($child->name)) == trim(strtoupper($tag))) {
                return $child->content;
            }
        }
        return false;
    }

    public function parse_soap_response($soap)
    {
        return $this->xml_to_object($soap);
    }

    //////////////////////////////////////////////////////////////
    // 					END PUBLIC METHODS						//
    //////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////
    // 					  PRIVATE METHODS						//
    //////////////////////////////////////////////////////////////

    private static function registerAutoload($classname)
    {
        spl_autoload_extensions('.php'); // Only Autoload PHP Files
        spl_autoload_register(function ($classname) {
            if (strpos($classname, '\\') !== false) {
                // Namespaced Classes
                $classfile = str_replace('\\', '/', $classname);
                if ($classname[0] !== '/') {
                    $classfile = dirname(__FILE__).'/libraries/'.$classfile.'.php';
                }
                require($classfile);
            }
        });
    }

    // criptez f_request cu AES
    private function AESEnc()
    {
        $this->aes_key 		= substr(hash('sha256', uniqid(), 0), 0, 32);
        $aes 				= new AES();
        $aes->setIV($this->iv);
        $aes->setKey($this->aes_key);
        $this->f_request 	= bin2hex(base64_encode($aes->encrypt($this->f_request)));
    }

    // criptez cheia AES cu RSA
    private function RSAEnc()
    {
        $rsa = new RSA();
        $rsa->loadKey($this->rsa_key_enc);
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        $this->f_secure = base64_encode($rsa->encrypt($this->aes_key));
    }

    // setez f_request, criptez f_request cu AES si cheia AES cu RSA
    private function setFRequest($f_request, $type, $validation_url)
    {
        // aici construiesc XML din array
        $xml = new \SimpleXMLElement('<'.$type.'/>');

        // test mode
        if ($type == 'po_auth_request') {
            if ($this->test_mode == 0) {
                $f_request['f_test_request'] = 0;
            } else {
                $f_request['f_test_request'] = 1;
            }

            $f_request['f_sequence']  = rand(1, 1000);
            $f_request['f_customer_ip'] = $_SERVER['REMOTE_ADDR'];
        }

        if ($type == 'po_payment_sale_by_token') {
            if ($this->test_mode == 0) {
                $f_request['f_test_request'] = 0;
            } else {
                $f_request['f_test_request'] = 1;
            }
            $f_request['f_customer_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        $f_request['f_timestamp'] = date('Y-m-d\TH:i:sP');
        // set f_login
        $f_request['f_login'] = $this->f_login;

        // sortez parametrii alfabetic
        ksort($f_request);

        $this->array2xml($f_request, $xml);
        $this->f_request = $xml->asXML();

        // validez XML conform schemei (parametrul 2)
        $this->validate_xml($this->f_request, $validation_url);

        $this->AESEnc();
        $this->RSAEnc();

        $request 						= array();

        $request['f_login'] 			= $this->f_login;
        $request['f_message']			= $this->f_request;
        $request['f_crypt_message ']	= $this->f_secure;

        $xml_auth_soap = new \SimpleXMLElement('<po_request/>');
        $this->array2xml($request, $xml_auth_soap);
        $xml_auth_soap = $xml_auth_soap->asXML();
        $this->validate_xml($xml_auth_soap, $this->url_sv_request_xml);
        return $xml_auth_soap;
    }

    // function definition to convert array to xml
    private function array2xml($arr, &$xml_arr)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    if (strpos($key, 'coupon') !== false) {
                        $subnode = $xml_arr->addChild("coupon");
                    } else {
                        $subnode = $xml_arr->addChild("$key");
                    }
                    $this->array2xml($value, $subnode);
                } else {
                    $subnode = $xml_arr->addChild("item");
                    $this->array2xml($value, $subnode);
                }
            } else {
                $xml_arr->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    private function validate_xml($poxml, $url)
    {
        libxml_use_internal_errors(true);
        $xml = new \DOMDocument();
        $xml->loadXML($poxml);
        $request = new Curl\Get($url);
        $request->send();
        if ($request->getStatus() != 200) {
            throw new \Exception('Nu am putut obtine schema de validare de la PlatiOnline');
        }
        $schemaPO5 = $request->getResponse();
        if (!$xml->schemaValidateSource($schemaPO5)) {
            $errors = libxml_get_errors();
            $finalmsg   =   array();
            foreach ($errors as $error) {
                throw new \Exception($this->libxml_display_error($error));
            }
            libxml_clear_errors();
            throw new \Exception('INVALID XML');
        }
    }

    private function decrypt_response($f_relay_message, $f_crypt_message, $validation_url)
    {
        if (empty($f_relay_message)) {
            throw new \Exception('Decriptare raspuns - nu se primeste [criptul AES]');
        }
        if (empty($f_crypt_message)) {
            throw new \Exception('Decriptare raspuns - nu se primeste [criptul RSA]');
        }
        $rsa = new RSA();
        $rsa->loadKey($this->rsa_key_dec);
        $rsa->setEncryptionMode(RSA::ENCRYPTION_PKCS1);
        $aes_key = $rsa->decrypt(base64_decode($f_crypt_message));
        if (empty($aes_key)) {
            throw new \Exception('Nu am putut decripta cheia AES din RSA');
        }
        $aes = new AES();
        $aes->setIV($this->iv_itsn);
        $aes->setKey($aes_key);
        $response = $aes->decrypt(base64_decode($this->hex2str($f_relay_message)));
        if (empty($response)) {
            throw new \Exception('Nu am putut decripta mesajul din criptul AES');
        }
        $this->validate_xml($response, $validation_url);
        return $this->xml_to_object($response);
    }

    private function libxml_display_error($error)
    {
        $return = "\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }
        $return .= trim($error->message);
        $return .= "\n";
        return $return;
    }

    private function hex2str($hex)
    {
        $str = '';
        for ($i=0;$i<strlen($hex);$i+=2) {
            $str .= chr(hexdec(substr($hex, $i, 2)));
        }
        return $str;
    }

    private function xml_to_object($xml)
    {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xml, $tags);
        xml_parser_free($parser);

        $elements = array();  // the currently filling [child] XmlElement array
        $stack = array();
        foreach ($tags as $tag) {
            $index = count($elements);
            if ($tag['type'] == "complete" || $tag['type'] == "open") {
                $elements[$index] = new XmlElement;
                $elements[$index]->name = isset($tag['tag'])?$tag['tag']:'';
                $elements[$index]->attributes = isset($tag['attributes'])?$tag['attributes']:'';
                $elements[$index]->content = isset($tag['value'])?$tag['value']:'';
                if ($tag['type'] == "open") {  // push
                    $elements[$index]->children = array();
                    $stack[count($stack)] = &$elements;
                    $elements = &$elements[$index]->children;
                }
            }
            if ($tag['type'] == "close") {  // pop
                $elements = &$stack[count($stack) - 1];
                unset($stack[count($stack) - 1]);
            }
        }
        return $elements[0];  // the single top-level element
    }

    //////////////////////////////////////////////////////////////
    // 					  END PRIVATE METHODS					//
    //////////////////////////////////////////////////////////////
}

class XmlElement
{
    public $name;
    public $attributes;
    public $content;
    public $children;
};
?>
