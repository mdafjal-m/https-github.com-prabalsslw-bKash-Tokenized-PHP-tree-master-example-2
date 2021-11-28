<?php 
    session_start();
    include '../../lib/Tokenized.php';

    use Bkash\Library\Tokenized;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if(!empty($_POST['paybtn'])) {
            if(!empty($_POST['pgmethod']) && $_POST['pgmethod'] == "WO") {
                if(!empty($_POST['amount']) && !empty($_POST['wallet'])) {
                    $wallet = !empty($_POST['wallet']) ? $_POST['wallet'] : '';
                    $amount = !empty($_POST['amount']) ? $_POST['amount'] : '';

                    $bKash = new Tokenized("WO");
                    $post_data = [
                        'amount' => $amount,
                        'merchantInvoiceNumber' => strtoupper(uniqid()),
                        'payerReference' => $wallet
                    ];

                    $response = $bKash->createPayment($post_data);
                    if(isset($response['statusCode']) && $response['statusCode'] != "" && $response['statusCode'] != "0000"){
                        $_SESSION['msg'] = "<div class='alert alert-warning'><strong>Payment Failed</strong><br>".$response['statusMessage']."</div>";
                        header('Location: ../view/bKash/pay.php');
                    }
                }
                else {
                    $_SESSION['msg'] = "<div class='alert alert-warning'>Required data missing!</div>";
                    header('Location: ../view/bKash/pay.php');
                }
            }
            else if(!empty($_POST['pgmethod']) && $_POST['pgmethod'] == "W") {
                if(!empty($_POST['amount']) && !empty($_POST['wallet'])) {
                    
                    $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : '';
                    $amount = (!empty($_POST['amount'])) ? $_POST['amount'] : '';

                    $bKash = new Tokenized("W");
                    $post_data = [
                        'amount' => $amount,
                        'merchantInvoiceNumber' => strtoupper(uniqid()),
                        'payerReference' => $user
                    ];

                    $response = $bKash->createAgreement($post_data);
                    if(isset($response['statusCode']) && $response['statusCode'] != "" && $response['statusCode'] != "0000"){
                        $_SESSION['msg'] = "<div class='alert alert-warning'><strong>Payment Failed</strong><br>".$response['statusMessage']."</div>";
                        header('Location: ../view/bKash/pay.php');
                    }
                }
                else {
                    $_SESSION['msg'] = "<div class='alert alert-warning'>Required data missing!</div>";
                    header('Location: ../view/bKash/pay.php');
                }
            }
            else if(!empty($_POST['pgmethod'])) {
                if(!empty($_POST['amount']) && !empty($_POST['pgmethod'])) {

                    $bKash = new Tokenized("W");
                    $post_data = [
                        'amount' => !empty($_POST['amount']) ? $_POST['amount'] : '',
                        'merchantInvoiceNumber' => strtoupper(uniqid()),
                        'agreementID' => !empty($_POST['pgmethod']) ? $_POST['pgmethod'] : ''
                    ];

                    $data = $bKash->createPayment($post_data);
                    if(isset($response['statusCode']) && $response['statusCode'] != "" && $response['statusCode'] != "0000"){
                        $_SESSION['msg'] = "<div class='alert alert-warning'><strong>Payment Failed</strong><br>".$response['statusMessage']."</div>";
                        header('Location: ../view/bKash/pay.php');
                    }
                }
                else {
                    $_SESSION['msg'] = "<div class='alert alert-warning'>Required data missing!</div>";
                    header('Location: ../view/bKash/pay.php');
                }
            }
            else {
                $_SESSION['msg'] = "<div class='alert alert-warning'>Required data missing!</div>";
                header('Location: ../view/bKash/pay.php');
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