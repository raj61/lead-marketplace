<?php

function edugorilla_lead_edit(){
    	$lead_edit_form = $_POST['lead_edit_form'];

    if ($lead_edit_form == "self") 
    {
        /** Get Data From Form **/
        $lead_name = $_POST['lead_name'];
        $lead_email= $_POST['lead_email'];
    	$lead_contact_no = $_POST['lead_contact_no'];
        $lead_query= $_POST['lead_query'];
        $iid = $_POST['iid'];

        /** Error Checking **/
        $errors = array();
    
    	if(empty($lead_name)) $errors['lead_name'];
    	if(empty($lead_email)) $errors['lead_email'];
    	if(empty($lead_contact_no)) $errors['lead_contact_no'];
    	if(empty($lead_query)) $errors['lead_query'];
        
    	if(empty($errors))
        {
            	global $wpdb;
                $result = $wpdb->update(
	                $wpdb->prefix . 'edugorilla_lead_details',
                    array(
                        'name' => $lead_name,
                    	'contact_no' => $lead_contact_no,
                    	'email' => $lead_email,
                    	'query' => $lead_query
                    ),
                	array( 'id' =>  $iid)
                );


            if ($result)
            {
                $success = "Updated Successfully";
            }
            else $success = $result;
        }

           
    }
    else
    {
        	global $wpdb;
        	$iid = $_REQUEST['iid'];
	    $q = "select * from {$wpdb->prefix}edugorilla_lead_details where id=$iid";
            $leads_details = $wpdb->get_results($q, 'ARRAY_A');
			foreach($leads_details as $leads_detail);
        	$lead_name = $leads_detail['name'];
        	$lead_email= $leads_detail['contact_no'];
    		$lead_contact_no = $leads_detail['email'];
        	$lead_query= $leads_detail['query'];
        	
     }
    ?>
   
    <div class="wrap">
        <h1>EduGorilla Edit Lead</h1>
        <?php
        if ($success) {
            ?>
            <div class="updated notice">
                <p><?php echo $success; ?></p>
            </div>
            <?php
        }
        ?>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Name<sup><font color="red">*</font></sup></th>
                    <td>
                        <input name="lead_name" value="<?php echo $lead_name; ?>" placeholder="Type name here...">
                        <font color="red"><?php echo $errors['lead_name']; ?></font>
                    </td>
                </tr>
                <tr>
                    <th>Email<sup><font color="red">*</font></sup></th>
                    <td>
                        <input name="lead_email" value="<?php echo $lead_email; ?>" placeholder="Type email(abc@exp.com)">
                        <font color="red"><?php echo $errors['lead_email']; ?></font>
                    </td>
                </tr>
            	<tr>
                    <th>Contact No.<sup><font color="red">*</font></sup></th>
                    <td>
                        <input name="lead_contact_no" value="<?php echo $lead_contact_no; ?>" placeholder="Type Contact No.">
                        <font color="red"><?php echo $errors['lead_contact_no']; ?></font>
                    </td>
                </tr>
            	<tr>
                    <th>Query<sup><font color="red">*</font></sup></th>
                    <td>
                    	<textarea name="lead_query" placeholder="Type Query"><?php echo $lead_query; ?></textarea>
                        <font color="red"><?php echo $errors['lead_query']; ?></font>
                    </td>
                </tr>
                <tr>
                    <th>
                    	<input type="hidden" name="iid" value="<?php echo $_REQUEST['iid']; ?>" >
                        <input type="hidden" name="lead_edit_form" value="self">
                    </th>
                    <td>
                    	<input type="submit" value="Update" class="button button-primary">
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}
?>