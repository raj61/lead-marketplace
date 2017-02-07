<?php
$status=$_POST["status"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"];
$txnid=$_POST["txnid"];
$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];

      if($status == "success"){
          echo "<h3>Thank You. Your order status is ". $status .".</h3>";
          echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
          echo "<h4>We have received a payment of Rs. " . $amount . ". Soon you will be allocated expected educash.</h4>";
      }
      else{
        echo "<h3>Your order status is ". $status .".</h3>";
        echo "<h4>Your transaction id for this transaction is ".$txnid.". You may retry making the payment.</h4>";
      }
?>
