<?php 
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if(!empty($_POST['paybtn'])) {
            if(!empty($_POST['pgmethod']) && $_POST['pgmethod'] == "WO")) {
                if(!empty($_POST['amount']) && !empty($_POST['wallet'])) {
                    echo "Without Agreement";
                }
                else {
                    $_SESSION['msg'] = "Required data missing!<br>";
                    header('Location: ../view/bKash/pay.php');
                }
            }
            else if(!empty($_POST['pgmethod']) && $_POST['pgmethod'] == "W")) {
                if(!empty($_POST['amount']) && !empty($_POST['wallet'])) {
                    echo "With Agreement";
                }
                else {
                    $_SESSION['msg'] = "Required data missing!<br>";
                    header('Location: ../view/bKash/pay.php');
                }
            }
            else if(!empty($_POST['pgmethod'])) {
                if(!empty($_POST['amount']) && !empty($_POST['pgmethod'])) {
                    echo $_POST['pgmethod'];
                }
                else {
                    $_SESSION['msg'] = "Required data missing!<br>";
                    header('Location: ../view/bKash/pay.php');
                }
            }
            else {
                $_SESSION['msg'] = "Wrong in method!<br>";
                header('Location: ../view/bKash/pay.php');
            }
            // $userwallet = !empty($_POST['userwallet']) ? $_POST['userwallet'] : '';
            // $userpassword = !empty($_POST['userpassword']) ? $_POST['userpassword'] : '';

            // $user_json = file_get_contents("../model/user.json");
            // $user_data = json_decode($user_json, true);
           
            // foreach($user_data as $users) 
            // {
            //     if($users['wallet'] == $userwallet && $users['password'] == $userpassword) 
            //     {
            //         $_SESSION['user'] = $users['wallet']; 
            //         $_SESSION['msg'] = "Login Success</br>";

            //         header('Location: ../view/bKash/pay.php');
            //         break;
            //     }
            //     else
            //     {
            //         $_SESSION['msg'] = "Wrong username or password!<br>";
            //         header('Location: ../view/bKash/pay.php');
            //     }
            // }
        }
    }
?>