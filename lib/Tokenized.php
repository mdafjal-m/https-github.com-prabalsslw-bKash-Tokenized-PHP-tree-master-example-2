<?php 
	namespace Bkash\Library;

	require_once(__DIR__."/BkashAbstract.php");

	class Tokenized extends BkashAbstract
	{
	    protected $secretdata = [];
	    protected $data = [];
	    protected $config = [];
	    protected $pgwmode;

		public function __construct($pgwmode) {

	        $this->config = include(__DIR__.'/../config/bkash.php');
	        date_default_timezone_set('Asia/Dhaka');

	        $this->pgwmode = $pgwmode;
	        $this->setAppkey($this->config['app_key']);
	        $this->setAppsecret($this->config['app_secret']);
	        $this->setUsername($this->config['username']);
	        $this->setPassword($this->config['password']);
	        $this->setCallbackUrl($this->config['callbackUrl']);
	        $this->setAgreementCallbackUrl($this->config['agreementCallbackUrl']);

	        if($this->config['is_sandbox']) {
	        	$this->setEnv($this->config['sandboxBaseUrl']);
	        } else {
	        	$this->setEnv($this->config['liveBaseUrl']);
	        }

	        if($this->pgwmode == 'W') {
	        	$this->setIsAgreement('0001');
	        } else if($this->pgwmode == 'WO') {
	        	$this->setIsAgreement('0011');
	        }

	        if($this->config['is_capture']) {
	        	$this->setCapture('authorization');
	        } else {
	        	$this->setCapture('sale');
	        }
	        $token_api_response = json_decode($this->grantToken(), true);
	       	$this->setToken($token_api_response['id_token']);
	    }

	    public function grantToken() {
	    	$this->secretdata['app_key'] = $this->getAppkey();
	    	$this->secretdata['app_secret'] = $this->getAppsecret();
	    	$this->setApiurl($this->getEnv().$this->config['grantTokenUrl']);

	    	$header = [
				'Content-Type:application/json',
				'password:'.$this->getPassword(),                                                               
		        'username:'.$this->getUsername()                                                          
		    ];	
		    if (!file_exists("../config/token.json")) {
		    	$response = $this->Post($this->secretdata, $header);
		    	$token_response = json_decode($response, true);

		    	if(isset($token_response['id_token']) && $token_response['id_token'] != "") {

		    		$token_creation_time = date('Y-m-d H:i:s');
		    		$json_token = json_encode(['id_token' => $token_response['id_token'], 'refresh_token' => $token_response['refresh_token'] ,'created_time' => $token_creation_time], JSON_PRETTY_PRINT);

					file_put_contents("../config/token.json", $json_token);

					return $response;
			    }
			    else {
			    	return ['libMsg' => 'Error in token creation'];
			    }
			}
			else if(file_exists("../config/token.json")) {
				$previous_token = json_decode(file_get_contents("../config/token.json"), true);

				$token_creation_time = date('Y-m-d H:i:s');
				$token_start_time = new \DateTime($previous_token['created_time']);
				$token_end_time = $token_start_time->diff(new \DateTime($token_creation_time));

				if($token_end_time->days > 0 || $token_end_time->d > 0 || $token_end_time->h > 0 || $token_end_time->i > 50) 
				{
					$refresh_token_response = json_decode($this->refreshToken($previous_token['refresh_token']), true);
	
					if(isset($refresh_token_response['id_token']) && $refresh_token_response['id_token'] != "") {
						$retoken_creation_time = date('Y-m-d H:i:s');
			    		$rejson_token = json_encode(['id_token' => $refresh_token_response['id_token'], 'refresh_token' => $refresh_token_response['refresh_token'] ,'created_time' => $retoken_creation_time], JSON_PRETTY_PRINT);

						file_put_contents("../config/token.json", $rejson_token);

						return json_encode($refresh_token_response);
					}
					else if(!empty($refresh_token_response['statusCode']))
					{
						return json_encode($refresh_token_response);
					}
				}
				else {
					return json_encode($previous_token);
				}
			}
	    }

	    public function refreshToken($refresh_token_id) {
	    	$this->secretdata['app_key'] = $this->getAppkey();
	    	$this->secretdata['app_secret'] = $this->getAppsecret();
	    	$this->secretdata['refresh_token'] = $refresh_token_id;
	    	$this->setApiurl($this->getEnv().$this->config['refreshTokenUrl']);

	    	$header = [
				'Content-Type:application/json',
				'password:'.$this->getPassword(),                                                               
		        'username:'.$this->getUsername()                                                          
		    ];	

	    	$response = $this->Post($this->secretdata, $header);
	    	return $response;
	    }

	    public function createAgreement($postdata) {
	    	$this->readyAgreementParameter($postdata);
	    	$this->setApiurl($this->getEnv().$this->config['createAgreementUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Post($this->data, $header);
		    $status = json_decode($response, true);

		    if(isset($status['agreementStatus']) && $status['agreementStatus'] == "Initiated") {
				return $status;
		    } 
		    else if(isset($status['statusCode']) && $status['statusCode'] != "") {
		    	return $status;
		    }
		    else {
		    	return ['libMsg' => 'Unable to create agreement'];
		    }
	    }

	    public function executeAgreement($payment_id) {
	    	$this->setApiurl($this->getEnv().$this->config['executeAgreementUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $this->data['paymentID'] = $payment_id;

		    $response = $this->Post($this->data, $header);
		    
			if($response) {
			    $status = json_decode($response, true);

			    if(isset($status['agreementStatus']) && $status['agreementStatus'] != "") {
					return $status;
			    } 
			    else if(isset($status['statusCode']) && $status['statusCode'] != "") {
			    	return $status;
			    }
			    else {
			    	return ['libMsg' => 'Error in execute agreement'];
			    }
			}
			else {
				$response = $this->queryPayment($payment_id);
				$status = json_decode($response, true);

			    if(isset($status['transactionStatus']) && $status['transactionStatus'] != "") {
					return $status;
			    } 
			    else if(isset($status['statusCode']) && $status['statusCode'] != "") {
			    	return $status;
			    }
			    else {
			    	return $status;
			    }
			}
	    }

	    public function createPayment($postdata) {
	    	$this->readyParameter($postdata);
	    	$this->setApiurl($this->getEnv().$this->config['createPaymentUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Post($this->data, $header);
		    $status = json_decode($response, true);

		    if(isset($status['transactionStatus']) && $status['transactionStatus'] == "Initiated") {
				$this->redirect($status['bkashURL']);
		    } 
		    else if(isset($status['statusCode']) && $status['statusCode'] != "") {
		    	return $status;
		    }
		    else {
		    	return ['libMsg' => 'Unable to create Bkash URL'];
		    }
	    }

	    public function executePayment($payment_id) {
	    	$this->setApiurl($this->getEnv().$this->config['executePaymentUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $this->data['paymentID'] = $payment_id;

		    $response = $this->Post($this->data, $header);
			if($response) {
			    $status = json_decode($response, true);

			    if(isset($status['transactionStatus']) && $status['transactionStatus'] != "") {
					return $status;
			    } 
			    else if(isset($status['statusCode']) && $status['statusCode'] != "") {
			    	return $status;
			    }
			    else {
			    	return ['libMsg' => 'Error in execute payment'];
			    }
			}
			else {
				$response = $this->queryPayment($payment_id);
				$status = json_decode($response, true);

			    if(isset($status['transactionStatus']) && $status['transactionStatus'] != "") {
					return $status;
			    } 
			    else if(isset($status['statusCode']) && $status['statusCode'] != "") {
			    	return $status;
			    }
			    else {
			    	return $status;
			    }
			}
	    }

	    public function queryPayment($payment_id) {
	    	$this->setApiurl($this->getEnv().$this->config['queryUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $this->data['paymentID'] = $payment_id;

		    $response = $this->Post($this->data, $header);

		    if($response) {
		    	return $response;
		    }
		    else {
		    	return ['libMsg' => 'Error in query payment'];
		    }
	    }

	    public function searchTransaction($trxid) {
	    	$this->setApiurl($this->getEnv().$this->config['searchTranUrl'].$trxid);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Get($header);
		    $decoded_response = json_decode($response, true);

	    	return $decoded_response;
	    }

	    public function refundTransaction($postdata) {
	    	$this->readyRefundParameter($postdata);
	    	$this->setApiurl($this->getEnv().$this->config['refundUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Post($this->data, $header);
		    $refund_response = json_decode($response, true);

		    if((isset($refund_response['transactionStatus']) && $refund_response['transactionStatus'] != "") && (isset($refund_response['originalTrxID']) && $refund_response['originalTrxID'] != "")) {
		    	return $refund_response;
		    }
		    else if(isset($refund_response['statusCode']) && $refund_response['statusCode'] != "") {
		    	return $refund_response;
		    }
		    else {
		    	return ['libMsg' => 'Refund API not responding'];
		    }
	    }

	    public function refundStatus($postdata) {
	    	$this->readyRefundStatusParameter($postdata);
	    	$this->setApiurl($this->getEnv().$this->config['refundStatusUrl']);

	    	$header = [ 
		        'Content-Type:application/json',
		        'authorization:'.$this->getToken(),
		        'x-app-key:'.$this->getAppkey()                                                   
		    ];

		    $response = $this->Post($this->data, $header);
		    $refund_query_response = json_decode($response, true);

		    if(isset($refund_query_response['statusCode']) && $refund_query_response['statusCode'] != "") {
		    	return $refund_query_response;
		    }
		    else {
		    	return $refund_query_response;
		    }
	    }

	    public function capturePayment($payment_id) {
	    	if($this->config['is_capture']) {
	    		$this->setApiurl($this->getEnv().$this->config['capturePaymentUrl'].$payment_id);

		    	$header = [ 
			        'Content-Type:application/json',
			        'authorization:'.$this->getToken(),
			        'x-app-key:'.$this->getAppkey()                                                   
			    ];

			    $response = $this->Post("", $header);
			    $status = json_decode($response, true);

			    if(isset($status['transactionStatus']) && $status['transactionStatus'] == "Completed") {
					return $response;
			    } else {
			    	return "Unable to capture payment! Reason: ". $status['statusCode']." - ".$status['errorMessage'];
			    }
	    	} else {
	    		return "Trying to capture payment in sale mode!";
	    	}
	    	
	    }

	    public function readyAgreementParameter(array $param) {
	    	$this->data['mode'] = '0000';
	    	$this->data['payerReference'] = (isset($param['payerReference'])) ? $param['payerReference'] : '01111111111';
	    	$this->data['callbackURL'] = $this->getAgreementCallbackUrl();
	    	$this->data['amount'] = (isset($param['amount'])) ? $param['amount'] : null;
	    	$this->data['currency'] = "BDT";
	    	$this->data['intent'] = $this->getCapture();
	    	$this->data['merchantInvoiceNumber'] = (isset($param['merchantInvoiceNumber'])) ? $param['merchantInvoiceNumber'] : null;

	    	return $this->data;
	    }

	    public function readyParameter(array $param) {
	    	$this->data['mode'] = (isset($param['mode'])) ? $param['mode'] : $this->getIsAgreement();
	    	$this->data['payerReference'] = (isset($param['payerReference'])) ? $param['payerReference'] : '01111111111';
	    	$this->data['callbackURL'] = $this->getCallbackUrl();
	    	if($this->pgwmode != "WO") {
	        	$this->data['agreementID'] = (isset($param['agreementID'])) ? $param['agreementID'] : null;
	        }
	    	$this->data['amount'] = (isset($param['amount'])) ? $param['amount'] : null;
	    	$this->data['currency'] = "BDT";
	    	$this->data['intent'] = $this->getCapture();
	    	$this->data['merchantInvoiceNumber'] = (isset($param['merchantInvoiceNumber'])) ? $param['merchantInvoiceNumber'] : null;
	    	$this->data['merchantAssociationInfo'] = (isset($param['merchantAssociationInfo'])) ? $param['merchantAssociationInfo'] : null;

	    	return $this->data;
	    }

	    public function readyRefundParameter(array $param) {
	    	$this->data['paymentID'] = (isset($param['paymentID'])) ? $param['paymentID'] : null;
	    	$this->data['amount'] = (isset($param['amount'])) ? $param['amount'] : null;
	    	$this->data['trxID'] = (isset($param['trxID'])) ? $param['trxID'] : null;
	    	$this->data['sku'] = (isset($param['sku'])) ? $param['sku'] : null;
	    	$this->data['reason'] = (isset($param['reason'])) ? $param['reason'] : null;

	    	return $this->data;
	    }

	    public function readyRefundStatusParameter(array $param) {
	    	$this->data['paymentID'] = (isset($param['paymentID'])) ? $param['paymentID'] : null;
	    	$this->data['trxID'] = (isset($param['trxID'])) ? $param['trxID'] : null;

	    	return $this->data;
	    }
	}