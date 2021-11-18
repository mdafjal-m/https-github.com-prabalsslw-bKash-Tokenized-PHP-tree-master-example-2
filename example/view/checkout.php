<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Checkout</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <br><br><br>
            <h2 align="center">Payment Review & Checkout</h2><hr>
            <div class="panel panel-default col-md-6 col-md-offset-3">
                <div class="panel-body">
                    <h4>Payment Details</h4>
                    Complete your purchase by providing your payment details<hr>
                    <form action="bKash/pay.php" method="post">
                        <div class="form-group">
                            <label for="wallet">Wallet/Reference No</label>
                            <input type="text" class="form-control" id="wallet" name="wallet" required autocomplete="off" placeholder="Wallet/Reference No" style="border-radius: 2px; height: 40px;">
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" step="any" class="form-control" id="amount" name="amount" required autocomplete="off" placeholder="Amount" min="1" max="10000" style="border-radius: 2px; height: 40px;">
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" required> I agree with terms conditions and privacy policy</label>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary btn-block">Pay <span id="btnamount"></span> ৳</button>
                    </form>
                </div>
            </div>
        </div>
    </body>

    <script type="text/javascript">
        $(document).on('input', 'input[name^="amount"]' ,function(e) {
            var amot = $(this).val();
            $('#btnamount').html(amot);
        });
    </script>
</html>

<?php 
    if(!empty($_SESSION['msg'])){
        unset($_SESSION['msg']);
    }
?>