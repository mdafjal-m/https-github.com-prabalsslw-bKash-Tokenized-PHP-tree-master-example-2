<?php 
    session_start();

    $user = (!empty($_SESSION['user'])) ? $_SESSION['user'] : '';

    $transaction_json = file_get_contents("../model/transactions.json");
    $transaction_data = json_decode($transaction_json, true);

    if(!empty($_SESSION['user'])) {
        $trxid = !empty($_GET['trxid']) ? $_GET['trxid'] : '';
        $amount = !empty($_GET['amount']) ? $_GET['amount'] : '';
    }
    else {
        header('Location: bKash/pay.php');
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
                            <button type="button" class="close pull-left" aria-label="Back"><a href="bKash/pay.php"><span aria-hidden="true" class="glyphicon glyphicon-menu-left" style="font-size:12px; font-weight: bold;padding-right: 10px;"></span></a></button>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <ul class="nav nav-tabs" style="font-size:14px;">
                                <li class="active"><a class="nav-link" data-toggle="tab" href="#refund">Refund</a></li>
                                <?php if(!empty($_SESSION['user'])) { ?>
                                    <li><a class="nav-link" data-toggle="tab" href="#refund_status">Status</a></li>
                                <?php } ?>
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

    //         $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
    //     var $old_tab = $($(e.target).attr("href"));
    //     var $new_tab = $($(e.relatedTarget).attr("href"));

    //     if($new_tab.index() < $old_tab.index()){
    //         $old_tab.css('position', 'relative').css("right", "0").show();
    //         $old_tab.animate({"right":"-100%"}, 300, function () {
    //             $old_tab.css("right", 0).removeAttr("style");
    //         });
    //     }
    //     else {
    //         $old_tab.css('position', 'relative').css("left", "0").show();
    //         $old_tab.animate({"left":"-100%"}, 300, function () {
    //             $old_tab.css("left", 0).removeAttr("style");
    //         });
    //     }
    // });

    // $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
    //     var $new_tab = $($(e.target).attr("href"));
    //     var $old_tab = $($(e.relatedTarget).attr("href"));

    //     if($new_tab.index() > $old_tab.index()){
    //         $new_tab.css('position', 'relative').css("right", "-2500px");
    //         $new_tab.animate({"right":"0"}, 500);
    //     }
    //     else {
    //         $new_tab.css('position', 'relative').css("left", "-2500px");
    //         $new_tab.animate({"left":"0"}, 500);
    //     }
    // });

    // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    //     // your code on active tab shown
    // });
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
            alert(trxid+amount);
        }
    </script>
</html>
<?php 
    if(!empty($_SESSION['msg'])){
        unset($_SESSION['msg']);
    }
?>