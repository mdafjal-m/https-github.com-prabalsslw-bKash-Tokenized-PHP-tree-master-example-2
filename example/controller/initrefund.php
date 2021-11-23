<?php 
    session_start();
    include '../../lib/Tokenized.php';

    use Bkash\Library\Tokenized;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if(!empty($_POST['refundbtn'])) {
            if(!empty($_POST['trxid']) && !empty($_POST['amount']) && !empty($_POST['sku']) && !empty($_POST['reason'])) {
	            $trxid = !empty($_POST['trxid']) ? $_POST['trxid'] : '';
	            $amount = !empty($_POST['amount']) ? $_POST['amount'] : '';
	            $sku = !empty($_POST['sku']) ? $_POST['sku'] : '';
	            $reason = !empty($_POST['reason']) ? $_POST['reason'] : '';

	            $transaction_json = file_get_contents("../model/transactions.json");
   				$transaction_data = json_decode($transaction_json, true);

   				foreach($transaction_data as $transaction) 
                {
                    if($transaction['trxID'] == $trxid) 
                    {
                    	$paymentId = $transaction['paymentID'];
                    	break;
                    }
                }
                // echo $paymentId;exit;
	            $bKash = new Tokenized("WO");
	            $post_data = [
	                'paymentID' => $paymentId,
	                'amount' => $amount,
	                'trxID' => $trxid,
	                'sku' => $sku,
	                'reason' => $reason
	            ];

	            $response = $bKash->refundTransaction($post_data);

	            if(isset($response['statusCode']) && $response['statusCode'] != "" && $response['statusCode'] != "0000"){
	                $_SESSION['msg'] = "<div class='alert alert-warning'><strong>Payment Failed</strong><br>".$response['statusMessage']."</div>";
	                header('Location: ../view/refund.php');
	            }
	            else if(isset($response['refundTrxID']) && $response['refundTrxID'] != "") {
	            	$_SESSION['msg'] = "<div class='alert alert-warning'><strong>Refund Success</strong><br>Refund TrxID: <b>".$response['refundTrxID']."</b><br>Status: ".$response['transactionStatus']."</div>";

	            	if(!file_exists("../model/refunds.json")) {
						$rejson_response = json_encode(["0" => $response], JSON_PRETTY_PRINT);
						file_put_contents("../model/refunds.json", $rejson_response);
					}
					else {
						$refunds = json_decode(file_get_contents("../model/refunds.json"), true);
						if($refunds != "") {
							array_push($refunds, $response);
						}
						else {
							$refunds = [];
							array_push($refunds, $response);
						}
						file_put_contents("../model/refunds.json", json_encode($refunds, JSON_PRETTY_PRINT));
					}
	                header('Location: ../view/refund.php');
	            }
	            else {
	            	$_SESSION['msg'] = "<div class='alert alert-warning'>".$response['libMsg']."</div>";
                	header('Location: ../view/refund.php');
	            }
            }
            else {
                $_SESSION['msg'] = "<div class='alert alert-warning'>Required data missing!</div>";
                header('Location: ../view/refund.php');
            }
        }
        else {
            echo "Access Denied!";
        }
    }
    else {
        echo "Access Denied!";
    }

?>