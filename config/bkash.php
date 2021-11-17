<?php 
	
	return [
		"callbackUrl" => "http://localhost/tokenized/test/callBack.php",
		"agreementCallbackUrl" => "http://localhost/tokenized/test/agreeCallBack.php",
		"sandboxBaseUrl" => "https://tokenized.sandbox.bka.sh/v1.2.0-beta",
		"liveBaseUrl" => "https://tokenized.pay.bka.sh/v1.2.0-beta",
		"grantTokenUrl" => "/tokenized/checkout/token/grant",
		"refreshTokenUrl" => "/tokenized/checkout/token/refresh",
		"createAgreementUrl" => "/tokenized/checkout/create",
		"executeAgreementUrl" => "/tokenized/checkout/execute",
		"createPaymentUrl" => "/tokenized/checkout/create",
		"executePaymentUrl" => "/tokenized/checkout/execute",
		"capturePaymentUrl" => "/tokenized/checkout/payment/confirm/capture",
		"voidUrl" => "/tokenized/checkout/payment/confirm/capture/void",
		"queryUrl" => "/tokenized/checkout/payment/status",
		"refundUrl" => "/tokenized/checkout/payment/refund",
		"refundStatusUrl" => "/tokenized/checkout/payment/refund",
		"searchTranUrl" => "/tokenized/checkout/general/searchTransaction",
		"app_key" => "7epj60ddf7id0chhcm3vkejtab",
		"app_secret" => "18mvi27h9l38dtdv110rq5g603blk0fhh5hg46gfb27cp2rbs66f",
		"username" => "sandboxTokenizedUser01",
		"password" => "sandboxTokenizedUser12345",
		"proxy" => "",
		"is_sandbox" => true, 	# true - Sandbox, false - Live
		// "agreement" => "WNWO", 	# `W` - With agreement, `WO` - Without agreement/URL Based 
		"is_capture" => false 	# true - Authorization, false - Sale
	];