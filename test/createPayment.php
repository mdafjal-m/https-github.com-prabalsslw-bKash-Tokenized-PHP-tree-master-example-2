<?php 
	include '../lib/Tokenized.php';

	use Bkash\Library\Tokenized;

	$create_payment = new Tokenized("W");

	$post_data = array();
	echo "<pre>";

	$post_data = [
		'amount' => 15,
		'merchantInvoiceNumber' => null,//strtoupper(uniqid()),
		'payerReference' => '01770618575'
	];

	$data = $create_payment->createAgreement($post_data);
	print_r($data);

	// $paymentID = 'TR0000G01637176103208';

	// $data = $create_payment->executeAgreement($paymentID);
	// print_r($data);

	// $post_data = [
	// 	'amount' => 15,
	// 	'merchantInvoiceNumber' => strtoupper(uniqid()),
	// 	'payerReference' => '01770618575'
	// ];

	// $data = $create_payment->createPayment($post_data);
	// print_r($data);
?>