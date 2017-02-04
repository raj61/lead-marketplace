<?php

$status=$_POST["status"];
//$firstname=$_POST["firstname"];
//$amount=$_POST["amount"];
$txnid=$_POST["txnid"];
//$productinfo=$_POST["productinfo"];
//$email=$_POST["email"];

         echo "<h3>Your order status is ". $status .".</h3>";
         echo "<h4>Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.</h4>";

?>
<!--Please enter your website homepagge URL -->
<p><a href=http://localhost/testing/success_failure/PayUMoney_form.php> Try Again</a></p>
