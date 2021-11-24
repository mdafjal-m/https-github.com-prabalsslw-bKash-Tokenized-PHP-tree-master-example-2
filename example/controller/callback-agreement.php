<?php
	session_start();
    include '../../lib/Tokenized.php';

    use Bkash\Library\Tokenized;

	$exexute_agreement = new Tokenized("W");

	$status = $_GET['status'];
	$payment_id = $_GET['paymentID'];

	if($status == 'success' && !empty($payment_id))
	{
		$response = $exexute_agreement->executeAgreement($payment_id);
		$execute_response = $response;

		if(!empty($execute_response['paymentID']) && !empty($execute_response['agreementID']) && !empty($execute_response['agreementStatus']) && $execute_response['agreementStatus'] == "Completed")
		{
			if(!file_exists("../model/agreements.json")) {
				$agreement_data = [
					"payerReference" => $execute_response['payerReference'],
					"customerMsisdn" => $execute_response['customerMsisdn'],
					"agreementID" => $execute_response['agreementID']
				];

				$rejson_response = json_encode(["0" => $agreement_data], JSON_PRETTY_PRINT);
				file_put_contents("../model/agreements.json", $rejson_response);
			}
			else {
				$agreements = json_decode(file_get_contents("../model/agreements.json"), true);
				$agreement_data = [
					"payerReference" => $execute_response['payerReference'],
					"customerMsisdn" => $execute_response['customerMsisdn'],
					"agreementID" => $execute_response['agreementID']
				];
				if(!empty($agreements)) {
					array_push($agreements, $agreement_data);
				}
				else {
					$agreements = [];
					array_push($agreements, $agreement_data);
				}
				file_put_contents("../model/agreements.json", json_encode($agreements, JSON_PRETTY_PRINT));
			}

			echo "Agreement Execute Success<pre>";
			print_r($execute_response);

			$post_data = [
				'amount' => !empty($_SESSION['amount']) ? $_SESSION['amount'] : '',
				'merchantInvoiceNumber' => strtoupper(uniqid()),
				'agreementID' => $execute_response['agreementID']
			];

			$data = $exexute_agreement->createPayment($post_data);
			print_r($data);
			echo "<a href='../view/bKash/pay.php'>Back</a>";
		}
		else
		{
			print_r($execute_response);
			if(isset($execute_response['statusCode']) && $execute_response['statusCode'] != "" && $response['statusCode'] != "0000"){
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