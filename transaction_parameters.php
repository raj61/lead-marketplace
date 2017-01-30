<?php
function transaction_parameters(){
  global $wpdb;
?>
        <div class="wrap">
            <h1>PayUMoney Credentials</h1><br>
            <form method="post" action="">
                <table>
                    <tr>
                        <th>Salt Id</th>
                        <td>
                            <input type="text" name="salt" /></br>
                        </td>
                    </tr>
                    <tr>
                        <th>Merchant Id</th>
                        <td>
                            <input type="text" name="mcid"/></br></br>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="submit" class="button button-primary" value="Save"></td>
                    </tr>
                </table>
            </form>
        </div></br></br>
        <div class="wrap">
            <h1>Conversion Rate</h1><br>
            <form method="post" action="">
                <table>
                    <tr>
                        <th>New Rate</th>
                        <td>
                            <input type="number" name="rate"/></br></br>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="submit" class="button button-primary" value="Save"></td>
                    </tr>
                </table>
            </form>
        </div>

	  <?php
		if (isset($_POST['salt']) && isset($_POST['mcid']) )
    {
      $salt = $_POST['salt'];
      $txnid = $_POST['mcid'];
      if(!empty($salt) && !empty($txnid)){

        $credentials1 = array("user_id"=>$salt, "password" =>$txnid);
        update_option("payumoney_parameters",$credentials1);
        $success = "Saved Successfully";

  			echo"<h2>Your salt and merchant id are successfully recieved. Now you can go ahead and continue with transactions</h2>";
        $out = get_option("payumoney_parameters");
       echo $out['user_id'];
       echo $out['password'];
		  }
		  else{
			  echo "<h2>Please fill salt and test key properly </h2><br><br>";
		  }
		}

    if (isset($_POST['rate']))
    {
      if(!empty($_POST['rate']))
        {
          $credentials2 = array("rate"=>$_POST['rate']);
          update_option("current_rate",$credentials2);
          $success = "Saved Successfully";
        }
    }
    ?>
    <table>
    <tr>
        <th>Current Rate = </th>
          <td>1 educash for <?php
           $out = get_option("current_rate");
          echo $out['rate']; ?> Rs</td>
    </tr>
  </table>
    <?php
 }
?>
