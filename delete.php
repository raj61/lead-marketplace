<?php
	function app_output_buffer() {
	  ob_start();
	} // soi_output_buffer

	add_action('init', 'app_output_buffer');
	function edugorilla_lead_delete()
    {
    	$iid = $_REQUEST['iid'];
    	$pl_delete_form = $_REQUEST['pl_delete_form'];
    	global $wpdb;
	    $q = "select * from {$wpdb->prefix}edugorilla_lead_details where id=$iid";
    	$leads_datas = $wpdb->get_results($q, 'ARRAY_A');
    	foreach($leads_datas as $leads_data);
    	$lead_name = $leads_data['name'];
    
    	if($pl_delete_form == "self")
        {
	        $wpdb->delete($wpdb->prefix . 'edugorilla_lead_details', array('id' => $iid));
        	wp_redirect(admin_url('admin.php?page=Listing', 'http')); 
        	exit;
        }
?>
	<div class="wrap">
		<h1>Promotional Leads List</h1>
    <form method="get" action="admin.php">
    	<input type="hidden" name ="page" value="edugorilla-delete-lead">
    	<input type=hidden name="iid" value="<?php echo $iid; ?>">
    	<table class="widefat fixed" cellspacing="0">
			<tbody>
				<tr>
					<th>Do you want to delete <?php echo $lead_name; ?> ?</th>
				</tr>
            	<tr>
                		<td><input type="submit"  class="button button-primary" value="Yes">   <a href="admin.php?page=Listing" class="button button-primary">No</a></td>
            	</tr>
				
			</tbody>
		</table>
        <input type=hidden name="pl_delete_form" value="self">
    </form>
	</div>
<?php
    }
?>