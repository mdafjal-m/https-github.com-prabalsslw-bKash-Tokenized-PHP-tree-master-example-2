<?php 
    session_start();

    $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : '';

    $transaction_json = file_get_contents("../model/transactions.json");
    $transaction_data = json_decode($transaction_json, true);

    // if(!empty($_SESSION['user'])) {
        $trxid = !empty($_GET['trxid']) ? $_GET['trxid'] : '';
        $amount = !empty($_GET['amount']) ? $_GET['amount'] : '';
    // }
    // else {
        // header('Location: bKash/pay.php');
    // }
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
                            <button type="button" class="close pull-left" aria-label="Back"><a href="bKash/pay.php"><span aria-hidden="true" class="glyphicon glyphicon-menu-left" style="font-size:12px; font-weight: bold;padding-right: 5px;"></span></a></button>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <ul class="nav nav-tabs" style="font-size:14px;">
                                <li class="active"><a class="nav-link" data-toggle="tab" href="#refund">Refund</a></li>
                                <li><a class="nav-link" data-toggle="tab" href="#refund_status">Status</a></li>
                                <li><a class="nav-link" data-toggle="tab" href="#search">Search</a></li>
                            </ul>
                            <div class="tab-content">
                                <img src="bKash/bkashlogo.png" alt="bKash" width="100%" height="90%" style=" display: block;margin-left: auto;margin-right: auto;">
                                <?php if(!empty($_SESSION['msg'])) {echo $_SESSION['msg'];} ?>
                                
                                <div id="refund" class="tab-pane fade in active">
                                    <h5 style="font-size: 15px; font-weight: bold;">Initiate Refund</h5><hr>
                                    <form action="../controller/initrefund.php" method="post">
                                        <div class="list-group">
                                            <span class="list-group-item">
                                                <input type="text" class="form-control" name="trxid" required autocomplete="off" placeholder="bKash TrxID" value="<?= $trxid ?>" <?= (!empty($trxid)) ? 'readonly':'' ?> style="height: 40px; border: 0px;">
                                            </span>
                                            <span class="list-group-item">
                                               <input type="number" step="any" class="form-control" name="amount" required autocomplete="off" placeholder="Full/Partial Amount" max="<?= $amount ?>" min="1" value="<?= $amount ?>" style=" height: 40px; border: 0px;">
                                            </span>
                                            <span class="list-group-item">
                                                <input type="text" class="form-control" name="sku" required autocomplete="off" placeholder="Product SKU" style="height: 40px; border: 0px;">
                                            </span>
                                            <span class="list-group-item">
                                                <input type="text" class="form-control" name="reason" required autocomplete="off" placeholder="Refund Reason" style="height: 40px; border: 0px;">
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-warning btn-block" name="refundbtn" value="Refund"/>
                                        </div>
                                    </form>
                                </div>
                                <div id="refund_status" class="tab-pane fade in">
                                    <form action="../../controller/login.php" method="post">
                                        <div class="list-group">
                                            <span class="list-group-item">
                                                <input type="text" class="form-control" name="trxid" required autocomplete="off" placeholder="TrxID" style="height: 40px; border: 0px;">
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-default btn-block" name="loginbtn" value="Login"/>
                                        </div>
                                    </form>
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
                        url : "../controller/ajax-search.php",
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