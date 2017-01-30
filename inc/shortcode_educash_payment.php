<style>
.card{
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    width: 100%;
    padding-left: 120px;
    padding-right: 120px;
    margin-top: 25px;
    text-align: center;
    margin-bottom: 25px;
    <?php $background = plugins_url('payumoney/background.png',__FILE__); ?>
    background-image: url("<?php echo $background;?>");
}

.card2{
  padding-top: 40px;
  padding-bottom: 40px;
  margin-left: 60px;
  margin-right: 60px;
  text-align: center;
}

p{
  padding-bottom: 30px;
  padding-left:
}

.output_amount{
  text-align: center;
  margin-left: 190px;
  margin-right: 190px;
  border: solid #DCDCDC;
  border-width: thin;
  padding-top: 10px;
  margin-top:15px;
  background-color: white;
  width: 320px;
  height: 50px;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

.select2{
  width:15px;
}

.heading1{
  color:  #3AD5A0;
  font-size: 34px;
   text-align: center;
   font-family: 'Alegreya Sans', sans-serif;
   text-shadow: 1px 1px 2px #98FB98;
  }

.button1,.button2,.button3{
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  background-color: #F5F5F5;
  border: solid #DCDCDC;
  border-width: thin;
  width: 160px;
  height: 60px;
  color: grey;
  margin-right: 20px;
}

.inputbox1{
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}


</style>

<?php
  session_start();
  add_shortcode('educash_payment','educash_payment');
  function educash_payment($atts,$content = null)
  {
      if(is_user_logged_in())
      {
        global $current_user;
        get_currentuserinfo();
        $out = get_option("current_rate");
        $conversion=$out['rate'];
        $credentials = get_option("payumoney_parameters");
        //echo $credentials['password'];

        $next_page = plugins_url('payumoney/PayUMoney_form.php',__FILE__);
        $failure = plugins_url('payumoney/failure.php',__FILE__);
        $success = plugins_url('payumoney/success.php',__FILE__);
        $payumoney_logo = plugins_url('payumoney/PayUMoney_logo.png',__FILE__);
        $netbanking_logo = plugins_url('payumoney/netbanking.png',__FILE__);


        ?>

        <div class="card" ng-app="">
          <div class ="card2">
            <form name="tranaction_form" method="post" action="">

              <input name="email" id="email" value="<?php echo $current_user->user_email; ?>" type="hidden" />
              <input name="firstname" id="firstname" value="<?php echo $current_user->user_firstname; ?>" type="hidden" />
              <input name="lastname" id="lastname" value="<?php echo $current_user->user_lastname; ?>" type="hidden" />
              <input name="txnid" id="txnid" value="<?php echo $credentials['password'];  ?>" type="hidden"/>
              <input name="rate" id="rate" value="<?php echo $conversion; ?>" type="hidden"/>
              <input name="saltid" id="saltid" value="<?php echo $credentials['user_id']; ?>" type="hidden"/>
              <input name="surl " id="surl" value="<?php echo $success; ?>" type="hidden"/>
              <input name="furl" id="furl" value="<?php echo $failure; ?>" type="hidden"/>

              <b><span class="heading1">Enter number of educash you want</span></br></b>
              <br><div class="inputbox1"><input  ng-model="amount" type="number" name="amount" placeholder="Enter number of educash you want ..." required/></div>
              <div class="output_amount"><b>Total =  {{amount*<?php echo $conversion; ?>}} Rs. </b></div>
              <b><p class="conversion">*(1 educash is equal to <?php echo $conversion; ?> Rs)</p></b></br>
              <b><span class="heading1">Please select Payment Method </span></b></br></br>
              <button onClick=this.form.action="<?php echo $next_page;?>" class="button1"><img src="<?php echo $payumoney_logo;?>"></img></button>
              <button onClick=this.form.action="" class="button2"><img src="<?php echo $netbanking_logo;?>" img></button>
              <button onClick=this.form.action="" class="button3">MyCreed</button>
            </form>
          </div>
       </div>
      <?php
      }
      else{
        echo "please login to view this page";
      }
  }
?>

<script>
src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js">
</script>
