<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<?php
  add_shortcode('educash_payment','educash_payment');
  function educash_payment($atts,$content = null)
  {
      if(is_user_logged_in())
      {

        global $current_user;
        get_currentuserinfo();
        $out = get_option("current_rate");
        $conversion=0;
        $conversion=$out['rate'];
        $credentials = get_option("payumoney_parameters");
        $background = plugins_url('payumoney/background.png',__FILE__);
        $allocation_page = plugins_url('',__FILE__);
        $allocation_page = str_replace('inc','frontend/class-EduCash-Helper.php',$allocation_page);
        $next_page = plugins_url('payumoney/PayUMoney_form.php',__FILE__);
        $redirect_url = plugins_url('payumoney/success.php',__FILE__);
        $payumoney_logo = plugins_url('payumoney/PayUMoney_logo.png',__FILE__);
        $netbanking_logo = plugins_url('payumoney/netbanking.png',__FILE__);
        ?>

        <div class="pay_card" ng-app="" ng-init="amount='0';" style="background-image: url("<?php echo $background;?>");">
          <div class ="pay_card2">
            <form name="tranaction_form" method="post" action="">
              <input name="userid" id="userid" value="<?php echo $current_user->id; ?>" type="hidden" />
              <input name="allocation_page" id="allocation_page" value="<?php echo $allocation_page; ?>" type="hidden" />
              <input name="page" id="page" value="<?php ?>" type="hidden" />
              <input name="email" id="email" value="<?php echo $current_user->user_email; ?>" type="hidden" />
              <input name="firstname" id="firstname" value="<?php echo $current_user->user_firstname; ?>" type="hidden" />
              <input name="lastname" id="lastname" value="<?php echo $current_user->user_lastname; ?>" type="hidden" />
              <input name="txnid" id="txnid" value="<?php echo $credentials['password'];  ?>" type="hidden"/>
              <input name="rate" id="rate" value="<?php echo $conversion; ?>" type="hidden"/>
              <input name="saltid" id="saltid" value="<?php echo $credentials['user_id']; ?>" type="hidden"/>
              <input name="furl" id="furl" value="<?php echo $redirect_url; ?>" type="hidden"/>

              <b><span class="pay_heading1">Enter number of educash you want</span></br></b>
              <br><div class="pay_inputbox1"><input  ng-model="amount" type="number" name="amount" placeholder="0" style="font-size: 20pt; text-align: center; font-weight: bold;" required/></div>
              <div class="pay_output_amount"><b>Total =  {{amount*<?php echo $conversion; ?>}} Rs. </b></div>
              <b><h3><p class="conversion">*(1 educash is equal to <?php echo $conversion; ?> Rs)</p></h3></b></br>
              <b><span class="pay_heading1">Please select Payment Method </span></b></br></br>
              <button onClick=this.form.action="<?php echo $next_page;?>" class="pay_button1"><img src="<?php echo $payumoney_logo;?>"></img></button>
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
