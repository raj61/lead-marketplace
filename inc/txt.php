<?php
    $wploadPath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $wploadPath[0] . '/wp-load.php'));

    if(isset($_POST['educash_amount'])&& isset($_POST['user_id']) && isset($_POST['conversion_karmas']) && isset($_POST['email']))
    {
        if(!empty($_POST['educash_amount']) && !empty($_POST['user_id']) && !empty($_POST['conversion_karmas']) && !empty($_POST['email']))
        {
            $user_id = $_POST['user_id'];
            $conversion = $_POST['conversion_karmas'];
            $balance = mycred_get_users_cred($user_id);
            echo $balance;
            $educash = $_POST['educash_amount'];
            $karmas = $educash*$conversion_karmas;
            echo "<br>".$educash."<br>".$conversion_karmas."<br>".$karmas;
            $email = $_POST['email'];

            if($balance >= $karmas)
            {
               mycred_subtract( 'Deduction',$current_user->id , $karmas, $karmas.'are deducted for purchase of '.$educhash.'educash', date( 'W' ) );

               $email_setting_options = get_option('edugorilla_email_setting2');
               $email_subject = stripslashes($email_setting_options['subject']);
               $email_body = stripslashes($email_setting_options['body']);
               $email_body = str_replace("{educash}", $educash, $email_body);
               $to = $email;
               $headers = array('Content-Type: text/html; charset=UTF-8');
               $value = wp_mail($to,$email_subject,$email_body,$headers);
               $status = "success";
               echo "<h3>Thank You. Your order status is ". $status .".</h3>";
               echo "<h4>Soon you will be allocated expected educash.</h4>";

               $eduCashHelper = new EduCash_Helper();
               $eduCashHelper->addEduCashToUser($user_id, $karmas, $status);
            }
            else{
                echo '<script type="text/javascript">alert("You do not have sufficient karmas to buy educash"); </script>';
            }
        }
    }
    else{
    echo "Sorry, you are not allowed to view this page";
    }
 ?>
