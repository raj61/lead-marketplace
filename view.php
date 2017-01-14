<?php
	function edugorilla_promotion_sent_view()
    {
    	$iid = $_REQUEST['iid'];
    	global $wpdb;
	    $q = "select * from {$wpdb->prefix}edugorilla_lead_details where id=$iid";
    	$promotional_leads_views = $wpdb->get_results($q, 'ARRAY_A');
    	foreach($promotional_leads_views as $promotional_leads_view);
?>
	<div class="wrap">
		<h1>Promotional Leads List</h1>
    	<table class="widefat fixed" cellspacing="0">
			<tbody>
				<tr>
					<th>Contact Person</th>
					<td><?php echo $promotional_leads_view['contact_person']; ?></td>
				</tr>
				
				<tr>
					<th>Institute Name</th>
					<td><?php echo $promotional_leads_view['institute_name']; ?></td>
				</tr>
				
				<tr>
					<th>Institute Address</th>
					<td><?php echo $promotional_leads_view['institute_address']; ?></td>
				</tr>
				
				<tr>
					<th>Email/Status</th>
					<td><?php echo implode(",",array_keys(json_decode($promotional_leads_view['email_status'],1))); ?></td>
				</tr>
				
				<tr>
					<th>Flag</th>
					<td><?php echo $promotional_leads_view['flag']; ?></td>
				</tr>
				
				<tr>
					<th>Date time</th>
					<td><?php echo $promotional_leads_view['date_time']; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
<?php
    }
?>