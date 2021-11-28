<?php 
    session_start();

    $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : '';
    $transaction_data = $agreement_data = "";
    if(file_exists("../../model/agreements.json")) {
        $agreement_json = file_get_contents("../../model/agreements.json");
        $agreement_data = json_decode($agreement_json, true);
    }
    if(file_exists("../../model/transactions.json")) {
        $transaction_json = file_get_contents("../../model/transactions.json");
        $transaction_data = json_decode($transaction_json, true);
    }

    if(!empty($_POST['wallet']) && !empty($_POST['amount'])) {
        $wallet = !empty($_POST['wallet']) ? $_POST['wallet'] : '';
        $amount = !empty($_POST['amount']) ? $_POST['amount'] : '';

        $_SESSION['wallet'] = $wallet;
        $_SESSION['amount'] = $amount;
    }
    else if(empty($_SESSION['wallet']) && empty($_SESSION['amount'])) {
        header('Location: ../checkout.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>bKash Pay</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <br><br><br>
            <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content" style="border-radius: 0px;">
                        <div class="modal-body">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <ul class="nav nav-tabs" style="font-size:14px;">
                                <li class="active"><a class="nav-link" data-toggle="tab" href="#home">Method</a></li>
                                <?php if(empty($_SESSION['user'])) { ?>
                                    <li><a class="nav-link" data-toggle="tab" href="#login">Login</a></li>
                                <?php } else { ?>
                                    <li><a class="nav-link" data-toggle="tab" href="#account">Account</a></li>
                                <?php } ?>
                                <li><a class="nav-link" data-toggle="tab" href="#search">Search</a></li>
                            </ul>
                            <div class="tab-content">
                                <img src="bkashlogo.png" alt="bKash" width="100%" height="90%" style=" display: block;margin-left: auto;margin-right: auto;">
                                <?php if(!empty($_SESSION['msg'])) {echo $_SESSION['msg'];} ?>
                                
                                <div id="home" class="tab-pane fade in active">
                                    <form action="../../controller/payment.php" method="post">
                                        <input type="hidden" name="amount" value="<?= (!empty($_SESSION['amount']) ? $_SESSION['amount'] : '') ?>">
                                        <input type="hidden" name="wallet" value="<?= (!empty($_SESSION['wallet']) ? $_SESSION['wallet'] : '') ?>">

                                        <div class="list-group" style="font-size: 13px; font-weight: bold;">
                                            <a href="#" class="list-group-item">
                                                <div class="radio">
                                                    <label style="color: #000066;"><input type="radio" name="pgmethod" value="WO">Pay Without Agreement</label>
                                                </div>
                                            </a>
                                            <?php if(!empty($_SESSION['user'])) { ?>
                                                <a href="#" class="list-group-item">
                                                    <div class="radio">
                                                        <label style="color: #000066;"><input type="radio" name="pgmethod" value="W">Pay With Agreement</label>
                                                    </div>
                                                </a>
                                                <?php if($agreement_data != "") {
                                                    foreach($agreement_data as $agreement) 
                                                    {
                                                        if($agreement['payerReference'] == $user) 
                                                        { ?>
                                                            <a href="#" class="list-group-item" style="background: lavenderblush;">
                                                                <div class="radio">
                                                                <label style="font-weight: bold;">
                                                                    <input type="radio" name="pgmethod" value="<?= $agreement['agreementID'] ?>"><?= $agreement['customerMsisdn'] ?>
                                                                </label>
                                                                <label class="pull-right"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color: red;" onclick='cancelAgreement("<?= $agreement['agreementID'] ?>")'></span></label>
                                                                </div>
                                                            </a>
                                                        <?php 
                                                        }
                                                    } ?>
                                            <?php } 
                                            }
                                            ?> 
                                        </div>
                   
                                        <input type="submit" name="paybtn" class="btn btn-primary btn-block" value="Pay <?= (!empty($_SESSION['amount']) ? $_SESSION['amount'] : '') ?> ৳"/>
                                    </form>
                                </div>
                                <div id="login" class="tab-pane fade in">
                                    <form action="../../controller/login.php" method="post">
                                        <div class="list-group">
                                            <span class="list-group-item">
                                                <input type="text" class="form-control" name="userwallet" required autocomplete="off" placeholder="Wallet Number" style="height: 40px; border: 0px;">
                                            </span>
                                            <span href="#" class="list-group-item">
                                               <input type="password" class="form-control" name="userpassword" required autocomplete="off" placeholder="Password" style=" height: 40px; border: 0px;">
                                            </span> 
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-default btn-block" name="loginbtn" value="Login"/>
                                        </div>
                                    </form>
                                </div>
                                <div id="account" class="tab-pane fade in">
                                    <h5><a class="nav-link pull-right" href="../../controller/logout.php">Log Out</a></h5>
                                    <h5 style="font-size: 15px; font-weight: bold;">Payment History</h5><hr>
                                    
                                    <div class="list-group" style="overflow: scroll;height: 300px; font-size: 13px;">
                                        <?php if($transaction_data != "") {
                                            foreach($transaction_data as $transaction) 
                                            {
                                                if($transaction['payerReference'] == $user) 
                                                { ?>
                                                    <a href="#" class="list-group-item">
                                                        <b>TrxID: <?= $transaction['trxID'] ?></b><br>
                                                        <b>Amount:</b> <?= $transaction['amount'] ?><br>
                                                        <b>Invoice:</b> <?= $transaction['merchantInvoiceNumber'] ?><br>
                                                        <b>Date:</b> <?= $transaction['paymentExecuteTime'] ?><br>
                                                        <b>Status: <?= $transaction['transactionStatus'] ?></b><br>
                                                        <button class="btn btn-danger btn-xs pull-right" onclick='refundFunction("<?= $transaction['trxID'] ?>","<?= $transaction['amount'] ?>")'>Refund</button><br>
                                                    </a>
                                                <?php 
                                                }
                                            } 
                                        }
                                            ?> 
                                    </div>

                                </div>
                                <div id="search" class="tab-pane fade in">
                                    <h5 style="font-size: 15px; font-weight: bold;">Search Transaction</h5><hr>
                                    <div class="list-group">
                                        <span class="list-group-item">
                                            <input type="text" class="form-control" name="trxid" id="trxid" autocomplete="off" placeholder="TrxID" style="height: 40px; border: 0px;">
                                        </span>
                                    </div>
                                    <div class="list-group srcdata">
                                        <div id="loader">
                                            <span>Please Wait...</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-info btn-block" id="srctrx">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.bs-example-modal-sm').modal('show')
            
            function alignModal(){
                var modalDialog = $(this).find(".modal-dialog");
                modalDialog.css("margin-top", Math.max(0, ($(window).height() - modalDialog.height()) / 4));
            }

            $(".modal").on("shown.bs.modal", alignModal);

            $(window).on("resize", function(){
                $(".modal:visible").each(alignModal);
            }); 
            $('.bs-example-modal-sm').on('hidden.bs.modal', function () {
                window.location.href = "../checkout.php";
            })
            $("#loader").hide();
            $('#srctrx').click(function(){
                var txid = $('#trxid').val();
                if(txid != "") {
                    var html = "";
                    $.ajax({
                        url : "../../controller/ajax-search.php",
                        method : "POST",
                        data : {id: txid},
                        async : true,
                        dataType : 'json',
                        beforeSend: function(){
                            $("#loader").show();
                        },
                        success: function(data){
                            if(data != null && data.statusCode == "0000"){
                                html += '<a href="#" class="list-group-item">';
                                html += '<b>TrxID: '+data.trxID+'</b><br>';
                                html += '<b>Amount:</b> '+data.amount+'<br>';
                                html += '<b>Customer MSISDN:</b> '+data.customerMsisdn+'<br>';
                                html += '<b>Date:</b> '+data.completedTime+'<br>';
                                html += '<b>Type: </b>'+data.transactionType+'</b><br>';
                                html += '<b>Status: '+data.transactionStatus+'<br>';
                                html += '<button class="btn btn-danger btn-xs pull-right" onclick="refundFunction(\''+data.trxID+'\',\''+data.amount+'\')">Refund</button><br>';
                                html += '</a>';

                                $('.srcdata').html(html);
                            }
                            else if(data.statusCode != "0000") {
                                html += '<a href="#" class="list-group-item">';
                                html += '<b>Sorry!</b><br>';
                                html += data.statusMessage;
                                html += '</a>';

                                $('.srcdata').html(html);
                            }
                        }
                    }); 
                }
                else {
                    alert("Please input TrxID");
                }
            });


        });

        function cancelAgreement(agreement_id) {
            var r = confirm("Are you sure you want to cancel the agreement?");
            if (r == true) {
                window.location.href = "../../controller/cancelAgree.php?id="+agreement_id;
            } 
            else {
                
            }
        }
        
        function refundFunction(trxid, amount) {
            window.location.href = "../refund.php?trxid="+trxid+"&amount="+amount;
        }
    </script>
</html>
<?php 
    if(!empty($_SESSION['msg'])){
        unset($_SESSION['msg']);
    }
?>