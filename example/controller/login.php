<?php 
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if(!empty($_POST['loginbtn'])) {
            $userwallet = !empty($_POST['userwallet']) ? $_POST['userwallet'] : '';
            $userpassword = !empty($_POST['userpassword']) ? $_POST['userpassword'] : '';

            $user_json = file_get_contents("../model/user.json");
            $user_data = json_decode($user_json, true);
           
            foreach($user_data as $users) 
            {
                if($users['wallet'] == $userwallet && $users['password'] == $userpassword) 
                {
                    $_SESSION['user'] = $users['wallet']; 
                    $_SESSION['msg'] = "<div class='alert alert-warning'>Login Success</div>";

                    header('Location: ../view/bKash/pay.php');
                    break;
                }
                else
                {
                    $_SESSION['msg'] = "<div class='alert alert-warning'>Wrong username or password!</div>";
                    header('Location: ../view/bKash/pay.php');
                }
            }
        }
    }
?>