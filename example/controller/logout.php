<?php 
	session_start();
	session_destroy();
    header('Location: ../view/checkout.php');
    exit;
?>