<?php 
    session_start();

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
                                    <li><a class="nav-link" href="../../controller/logout.php">Log Out</a></li>
                                <?php } ?>
                            </ul>
                            <div class="tab-content">
                                <img src="bkashlogo.png" alt="bKash" width="100%" height="90%" style=" display: block;margin-left: auto;margin-right: auto;">
                                <?php if(!empty($_SESSION['msg'])) {echo $_SESSION['msg'];} ?>
                                
                                <div id="home" class="tab-pane fade in active">
                                    <form action="../../controller/payment.php" method="post">
                                        <input type="hidden" name="amount" value="<?= (!empty($_SESSION['amount']) ? $_SESSION['amount'] : '') ?>">
                                        <input type="hidden" name="wallet" value="<?= (!empty($_SESSION['wallet']) ? $_SESSION['wallet'] : '') ?>">
                                        <div class="list-group">
                                            <a href="#" class="list-group-item">
                                                <div class="radio">
                                                    <label><input type="radio" name="pgmethod" value="WO">Pay Without Agreement</label>
                                                </div>
                                            </a>
                                            <?php if(!empty($_SESSION['user'])) { ?>
                                                <a href="#" class="list-group-item">
                                                    <div class="radio">
                                                        <label><input type="radio" name="pgmethod" value="W">Pay With Agreement</label>
                                                    </div>
                                                </a>
                                            <?php } ?> 
                                        </div>
                   
                                        <input type="submit" name="paybtn" class="btn btn-primary btn-block" value="Pay <?= (!empty($_SESSION['amount']) ? $_SESSION['amount'] : '') ?> ৳"/>
                                    </form>
                                </div>
                                <div id="login" class="tab-pane fade">
                                    <form action="../../controller/login.php" method="post">
                                        <div class="list-group">
                                            <a href="#" class="list-group-item">
                                                <input type="text" class="form-control" name="userwallet" required autocomplete="off" placeholder="Wallet Number" style="height: 40px; border: 0px;">
                                            </a>
                                            <a href="#" class="list-group-item">
                                               <input type="password" class="form-control" name="userpassword" required autocomplete="off" placeholder="Password" style=" height: 40px; border: 0px;">
                                            </a> 
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-default btn-block" name="loginbtn" value="Login"/>
                                        </div>
                                    </form>
                                </div>
                                <div id="account" class="tab-pane fade">
                                    <h3>Account</h3>
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
        });
    </script>
</html>
<?php 
    if(!empty($_SESSION['msg'])){
        unset($_SESSION['msg']);
    }
?>