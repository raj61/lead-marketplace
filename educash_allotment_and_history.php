<?php
function allocate_educash_form_page()
{
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
    $users_table = $wpdb->prefix.users;
    
//Checking if the admin has filled adequate information to submit the form to allot educash and inserting the legal values in table
    
    if ($_POST['submit']) {
        if (empty($_POST['clientName'])) {
            $clientnamerr = "<span  style='color:red;'>* This field cannot be blank</span>";
        } else {
            $clientName = $_POST['clientName'];
            $check_client = $wpdb->get_var("SELECT COUNT(ID) from $users_table WHERE user_email = '$clientName' ");
            if($check_client == 0){
                $invalid_client = "<span style='color:red'>This client does not exist in our database</span>";
            }
        }
        if (empty($_POST['educash'])) {
            $educasherr = "<span style='color:red;'>* This field cannot be blank</span>";
        } else {
            $educash = $_POST['educash'];
        }
        if ((!empty($_POST['clientName'])) && (!empty($_POST['educash'])) && (!($check_client == 0))) {
            $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientName' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE client_id = '$client_ID_result' ");
            $final_total = $total + $educash;
            if($final_total>=0){
            $money = $_POST['money'];
            $adminName = wp_get_current_user();
            $client_ID = $wpdb->get_var("SELECT ID from $users_table WHERE user_email = '$clientName' ");
            $adminComment = $_POST['adminComment'];
            $time = current_time('mysql');
            $wpdb->insert($table_name3, array(
                'time' => $time,
                'admin_id' => $adminName->ID,
                'client_id' => $client_ID,
                'transaction' => $educash,
                'amount' => $money,
                'comments' => $adminComment
            ));
           }
        }
    }    
//Form to allocate educash
?>
<script>
    function validate_allotment_form() {
    var x = document.forms["myForm"]["clientName"].value;
    var y = document.forms["myForm"]["educash"].value;
    var z = document.forms["myForm"]["money"].value; 
    if (x == "" && (y == "" || y == 0)) {
        document.getElementById('errmsg1').innerHTML = "* This field cannot be blank";
        document.getElementById('errmsg2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (x == "") {
        document.getElementById('errmsg1').innerHTML = "* This field cannot be blank";
        return false;
    }
    if (y == "" || y == 0) {
        document.getElementById('errmsg2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if(z < 0) {
        document.getElementById('errmsg3').innerHTML = "* This field cannot be negative";
    }
    else {return confirm('Do you really want to submit this entry?');}
}
</script>
    <center><h2>Use this form to allocate educash to a client</h2><br/>
    <form name="myForm" method='post' onsubmit="return validate_allotment_form()" action="<?php echo $_SERVER['REQUEST_URI'];?>">
             Client Email (Type the Email Id of the client whom you want to allot educash):<br/><input type='text' id='input1' name='clientName' maxlength='100'>*<br/>
                                                                                                <span style='color:red;' id='errmsg1'></span>
                                                                                                <span><?php echo $clientnamerr; echo $invalid_client;?> </span>
                                                                                                <br/><br/>
             Type the educash to be added in the client's account:<br/><input type='number' id='input2' name='educash' min='-100000000' max='100000000'>*<br/>
                                                                       <span style='color:red;' id='errmsg2'></span>
                                                                       <span><?php echo $educasherr;?> </span>
                                                                       <br/><br/>
             Type the amount of money that the client has paid:<br/><input type='number' id='input3' name='money' min='-100000000' max='100000000'>*<br/>
                                                                       <span style='color:red;' id='errmsg3'></span>
                                                                       <span><?php echo $moneyerr;?> </span>
                                                                       <br/><br/>
             Type your comments here (optional):<br/><textarea rows='4' cols='60' name='adminComment' maxlength='500'></textarea><br/><br/>
             <input type='submit' name='submit'><br/>
             </form></center>
<?php
//Displaying the transaction made just now if the values are legal and sending a mail to respective client otherwise displaying error message

    $client_display_name = $wpdb->get_var("SELECT display_name FROM $users_table WHERE user_email = '$clientName' ");
    if ($_POST['submit'] && (!empty($_POST['clientName'])) && (!empty($_POST['educash'])) && (!($check_client == 0))) {
        if($final_total<0){
           echo "<center><span style='color:red;'>The total balance that the client ".$_POST['clientName']." has
                 is ".$total. ". Your entry will leave this client with negative amount of educash which is not allowed.</span></center>";
        }
        else{
        $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE client_id = '$client_ID' ");
        $sum = 0;
            foreach($results as $e) {
                $educash_add = $e->transaction;
                $sum = $sum + $educash_add;
                if($sum<0){$sum = 0;}
            }
        $edugorilla_email_datas = get_option('edugorilla_email_setting2');
        $edugorilla_email_datas2 = get_option('edugorilla_email_setting3');
        $arr1 = array("{Contact_Person}", "{ReceivedCount}", "{EduCashCount}", "{EduCashUrl}", "<pre>", "</pre>", "<code>", "</code>", "<b>", "</b>");
        $to = $clientName;
        if($educash>0){
        $positive_email_subject = $edugorilla_email_datas['subject'];
        $subject =  $positive_email_subject;
        $arr2 = array($client_display_name, $educash, $sum, "https://edugorilla.com/", "", "", "", "", "", "");
        $positive_email_body = str_replace($arr1, $arr2, $edugorilla_email_datas['body']);
        $message =  $positive_email_body;
            
//Creating invoice
            
        require('pdf_library/invoice_functions.php');
        $pdf = new PDF_Invoice( 'P', 'mm', 'A4' );                                 
        $pdf->AddPage();
        $pdf->Image("https://electronicsguide.000webhostapp.com/wp-content/uploads/2017/01/eg_logo.jpg",10,10,53.898305,60);
        $pdf->right_blocks(70, 25, 20, "EduGorilla");
        $pdf->addCompanyAddress("House No. 4719/A,\n".
                                "Sector 23A,\n" .
                                "Gurgaon - 122002,\n".
                                "India.\n" .
                                "hello@edugorilla.com\n\n".
                                "+91 9410007819");
        $pdf->addClientAddress("MonAdresse\n" .
                               "75000 PARIS\n".
                               "R.C.S. PARIS\n" .
                               "Capital : 1800");
        $pdf->left_blocks(80, 90, "DATE: 03/01/2017");
        $pdf->left_blocks(80, 100, "AMOUNT IN RUPEES: 100/-");
        $pdf->right_blocks(2, 80, 16, "INVOICE");
        $pdf->right_blocks(2, 90, 12, "Bill To:");
        $cols=array( "ITEM"      => 61,
                     "RATE"      => 43,
                     "QUANTITY"  => 43,
                     "AMOUNT"    => 43,);
        $pdf->addCols( $cols);
        $cols=array( "ITEM"      => "C",
                     "RATE"      => "C",
                     "QUANTITY"  => "C",
                     "AMOUNT"    => "C");
        $pdf->addLineFormat( $cols);
        $pdf->addLineFormat($cols);
        $y    = 157;
        $line = array( "ITEM"      => "EDUCASH",
                       "RATE"      => "Rs. 2/-",
                       "QUANTITY"  => "50",
                       "AMOUNT"    => "Rs. 100/-");
        $size = $pdf->addLine( $y, $line );
        $y   += $size + 2;
        $pdf->left_blocks(85, 200, "TOTAL: Rs. 100/-");
        $pdf->left_blocks(75, 220, "PAYMENT MADE: Rs. 100/-");
        $pdf->left_blocks(80, 240, "BALANCE DUE: Rs. 0/-");
        $pdf->right_blocks(25, 200, 16, "Thanks For Your Business");
        
        }
        else{
        $negative_email_subject = $edugorilla_email_datas2['subject'];
        $subject =  $negative_email_subject;
        $negative_educash = $educash*(-1);
        $arr3 = array($client_display_name, $negative_educash, $sum, "https://edugorilla.com/", "", "", "", "", "", "");
        $negative_email_body = str_replace($arr1, $arr3, $edugorilla_email_datas2['body']);
        $message =  $negative_email_body;
        }
	        wp_mail($to, $subject, $message);
        $r = $wpdb->get_row("SELECT * FROM $table_name3 WHERE time = '$time' ");
        echo "<center></p>You have made the following entry just now:</p>";
        echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
        echo "<tr><td>" . $r->id . "</td><td>" . $adminName->user_email . "</td><td>" . $clientName . "</td><td>" . $r->transaction . "</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
        echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
        echo "</table></center><br/><br/>";
      }
   }
}

function transaction_history_form_page()
{
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
    $users_table = $wpdb->prefix.users;
    
//Checking if the admin has filled atleast one field to submit the form to see history 
    
    if ($_POST['Submit']) {
        if (empty($_POST['admin_Name']) && empty($_POST['client_Name']) && empty($_POST['date']) && empty($_POST['date2'])) {
            $all_four_error = "<span style='color:red;'> * All four fields cannot be blank</span>";
        }
    }
    
//Form to see history of educash transactions
    
    echo "<center><h2>Use this form to know the history of educash transactions</h2>";
    echo "<p style='color:green;'>Fill atleast one field<p>";
    echo "<form method='post' action='" . $_SERVER['REQUEST_URI'] . "'>
             Admin Email (Type the email Id of the admin whose history you want to see):<br/><input type='text' name='admin_Name' maxlength='100'><br/><br/>
             Client Email (Type the emailId of the client whose history you want to see):<br/><input type='text' name='client_Name' max='100'><br/><br/>
             Date From: <input type='date' name='date' min='1990-12-31' max='2050-12-31'>
             Date To: <input type='date' name='date2' min='1990-12-31' max='2050-12-31'><br/><br/>
             <input type='submit' name='Submit'><br/>" . $all_four_error . "<br/><br/><br/>
             </form></center>";
    
//Displaying the history of required fields
    
       $admin_Name = $_POST['admin_Name'];
       $client_Name = $_POST['client_Name']; 
       $admin_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$admin_Name' ");
       $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$client_Name' ");
       $date = $_POST['date'];
       $date2 = $_POST['date2'];    
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && !empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && !empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done from ".$_POST['date']." is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done till ".$_POST['date2']." is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && !empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done from ".$_POST['date']." to ".$_POST['date2']." is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
}
?>
