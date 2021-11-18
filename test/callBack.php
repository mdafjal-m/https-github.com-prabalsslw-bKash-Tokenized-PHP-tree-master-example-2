<?php
	include '../lib/Tokenized.php';

	use Bkash\Library\Tokenized;


	$execute_payment = new Tokenized("W");

	$status = $_GET['status'];
	$payment_id = $_GET['paymentID'];

	if($status == 'success' && !empty($payment_id))
	{
		$response = $execute_payment->executePayment($payment_id);
		$execute_response = $response;

		if(!empty($execute_response['paymentID']) && !empty($execute_response['trxID']) && !empty($execute_response['transactionStatus']) && $execute_response['transactionStatus'] == "Completed")
		{
			echo "Execute Success<pre>";
			print_r($execute_response);
		}
		else
		{
			print_r($execute_response);
		}
	}
	else if($status == 'failure' && !empty($payment_id))
	{
		echo "<pre>";
		print_r($_GET);
	}
	else if($status == 'cancel' && !empty($payment_id))
	{
		echo "<pre>";
		print_r($_GET);
	}
?>