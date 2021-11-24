<?php 
    session_start();
    include '../../lib/Tokenized.php';

    use Bkash\Library\Tokenized;

    if(!empty($_POST['id'])) {
        $id = $_POST['id'];

        $bKash = new Tokenized("WO");
        $response = json_encode($bKash->searchTransaction($id));

        echo $response;
    }  
    else {
        echo "Access Denied!";
    }

?>