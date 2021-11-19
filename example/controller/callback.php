<?php
	session_start();
    include '../../lib/Tokenized.php';

    use Bkash\Library\Tokenized;

	$execute_payment = new Tokenized("WO");

	$status = $_GET['status'];
	$payment_id = $_GET['paymentID'];

	if($status == 'success' && !empty($payment_id))
	{
		$response = $execute_payment->executePayment($payment_id);
		$execute_response = $response;

		if(!empty($execute_response['paymentID']) && !empty($execute_response['trxID']) && !empty($execute_response['transactionStatus']) && $execute_response['transactionStatus'] == "Completed")
		{
			if(!file_exists("../model/transactions.json")) {
				$rejson_response = json_encode(["0" => $execute_response], JSON_PRETTY_PRINT);
				file_put_contents("../model/transactions.json", $rejson_response);
			}
			else {
				$transactions = json_decode(file_get_contents("../model/transactions.json"), true);
				array_push($transactions, $execute_response);
				file_put_contents("../model/transactions.json", json_encode($transactions, JSON_PRETTY_PRINT));
			}
			
			echo "Execute Success<pre>";
			$_SESSION['msg'] = "<div class='alert alert-warning'><strong>Payment Success</strong><br>Transaction ID - ".$execute_response['trxID']."</div>";
			print_r($execute_response);
			echo "<a href='../view/bKash/pay.php'>Back</a>";
		}
		else
		{
			print_r($execute_response);
			if(isset($execute_response['statusCode']) && $execute_response['statusCode'] != ""){
                $_SESSION['msg'] = "<div class='alert alert-warning'><strong>Payment Failed</strong><br>".$execute_response['statusMessage']."</div>";
                header('Location: ../view/bKash/pay.php');
            }
			// echo "<a href='../view/bKash/pay.php'>Back</a>";
		}
	}
	else if($status == 'failure' && !empty($payment_id))
	{
		echo "<pre>";
		print_r($_GET);
		echo "<a href='../view/bKash/pay.php'>Back</a>";
	}
	else if($status == 'cancel' && !empty($payment_id))
	{
		echo "<pre>";
		print_r($_GET);
		echo "<a href='../view/bKash/pay.php'>Back</a>";
	}
?>