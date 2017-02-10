<?php 
	
	function table_for_client(){
	global $wpdb;
	$table_name6 = $wpdb->prefix.'edugorilla_client_preferences'; //client preferences
	$sql6 = "CREATE TABLE $table_name6 (
				                            id int(15) NOT NULL AUTO_INCREMENT,				                     
											client_name varchar(200) NOT NULL,
											email_id varchar(200) NOT NULL,
											contact_no varchar(50) NOT NULL,
											preferences varchar(100) NOT NULL,
											location varchar(100) NOT NULL,
											category varchar(100) NOT NULL,
											PRIMARY KEY id (id)
				  					    ) $charset_collate;";	


	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//Creating a table in cureent wordpress
	dbDelta($sql6);
	  					    
	}

//end pluginUninstall function

function send_mail($edugorilla_email_subject , $edugorilla_email_body){
	global $wpdb;
	$table_name = $wpdb->prefix .'edugorilla_client_preferences';
	$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name WHERE preferences = 'Instant_Notifications'" );
	$headers = array('Content-Type: text/html; charset=UTF-8');
	foreach ($client_email_addresses as $cea) {
		add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		$institute_emails_status = wp_mail($cea->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
		remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
	}

	return $institute_emails_status;
}

//function to display client preferences form
function edugorilla_client(){

	if (isset($_POST['submit_client_pref'])) {
		# code...
		$_client_name = $_POST['_client_name'];
		$client_email = $_POST['client_email'];
		$client_contact = $_POST['client_contact'];
		$notification = $_POST['notification'];
		$location = $_POST['location'];
		$category = $_POST['category'];

		/** Error Checking **/
		$c_errors = array();
		if (empty($_client_name)) $c_errors['_client_name'] = "Empty";
		elseif (!preg_match("/([A-Za-z]+)/", $_client_name)) $c_errors['_client_name'] = "Invalid Name";
		

		if (empty($client_contact)) $c_errors['client_contact'] = "Empty";
		elseif (!preg_match("/([0-9]{10}+)/", $client_contact)) $c_errors['client_contact'] = "Invalid Contact Number";


		if (empty($client_email)) $c_errors['client_email'] = "Empty";
		elseif (filter_var($client_email, FILTER_VALIDATE_EMAIL) === false) $c_errors['client_email'] = "Invalid Email Id";

		if(empty($notification)) $c_errors['notification'] = "Select any one option";

		if (empty($location)) $c_errors['location'] = "Empty";
		elseif (!preg_match("/([A-Za-z]+)/", $location)) $c_errors['location'] = "Invalid Name";

		if (empty($category)) $c_errors['category'] = "Empty";
		elseif (!preg_match("/([A-Za-z]+)/", $category)) $c_errors['category'] = "Invalid Name";

		//Insert Data to table
		if(empty($errors)){
		global $wpdb;
		$client_result = $wpdb->insert(
				$wpdb->prefix.'edugorilla_client_preferences',
				array(
					'client_name' => $_client_name,
					'email_id' => $client_email,
					'contact_no' => $client_contact,
					'preferences' => $notification
				)
			);

		if ($client_result)
			$client_success = "Saved Successfully";
		else
			$client_success = "Please try again";
	}
}
	
?>
	<!-- Client Form -->
	<form action="" method="post">
		<p><?php echo $client_success; ?></p>
		<table>
			<tr><td>Name<sup><font color="red">*</font></sup> : </td>
				<td><input type="text" name="_client_name">
					<font color="red"><?php echo $c_errors['_client_name']; ?></font>
				</td></tr>
			<tr><td>Email Id<sup><font color="red">*</font></sup> : </td>
				<td><input type="email" name="client_email">
					<font color="red"><?php echo $c_errors['client_email']; ?></font>
				</td></tr>
			<tr><td>Contact No.<sup><font color="red">*</font></sup> : </td>
				<td><input type="number" name="client_contact">
					<font color="red"><?php echo $c_errors['client_contact']; ?></font>
				</td></tr>
			<tr><td rowspan="4">Notification Preferences<sup><font color="red">*</font></sup> : </td><td><input type="radio" name="notification" value="Instant_Notifications">Instant Notification</td></tr>
			<tr><td><input type="radio" name="notification" value="Daily_Digest">Daily Digest</td></tr>
			<tr><td><input type="radio" name="notification" value="Weekly_Digest">Weekly Digest</td></tr>
			<tr><td><input type="radio" name="notification" value="Monthly_Digest">Monthly Digest<br/>
				<font color="red"><?php echo $c_errors['notification']; ?></font>
			</td></tr>
			<tr><td>Location/State</td><td><input type="text" name="location">
				<font color="red"><?php echo $c_errors['location']; ?></font></td></tr>
			<tr><td>Category</td><td><input type="text" name="category">
				<font color="red"><?php echo $c_errors['category']; ?></font></td></tr>
			<tr><td><input type="submit" name="submit_client_pref"/></td></tr>
		</table>
	</form>
<?php
}
	add_shortcode('client_preference_form','edugorilla_client');

	

	add_action('mail_send_daily','do_this_daily');
	add_action('mail_send_weekly','do_this_weekly');
	add_action('mail_send_monthly','do_this_monthly');

	function my_email_activation(){
		$time_day =  date("y-m-d")." 17:00:00";
		$daily_time=strtotime($time_day);

		$startdate = strtotime("Friday");
		$week_time =  date("y" ,$startdate).'-'.date("m" ,$startdate).'-'.date("d",$startdate)." 12:00:00";
		$weekly_time = strtotime($week_time);

		wp_schedule_event($daily_time,'daily','mail_send_daily');
		wp_schedule_event($weekly_time,'weekly','mail_send_weekly');
		wp_schedule_event($weekly_time,'monthly','mail_send_monthly');			
	}


	function do_this_weekly() {
	//do something weekly 
	// send mail every week at 12PM on Friday
		$edugorilla_email = get_option('email_setting_form_weekly');
		$edugorilla_email_body = stripslashes($edugorilla_email['body']);
		global $wpdb;
		$table_name = $wpdb->prefix .'edugorilla_lead_details';
		$lead_details = $wpdb->get_results( "SELECT * FROM $table_name");
			foreach ($lead_details as $lead_detail) {
					$edugorilla_email_subject = str_replace("{category}", $lead_detail->contact_category, $edugorilla_email['subject']);
					$email_template_datas = array("{Contact_Person}" => $lead_detail->name, "{category}" => $lead_detail->category, "{location}" => $lead_detail->location_id, "{contact no}" => $lead_detail->contact_no, "{email address}" => $lead_detail->email, "{query}" => $lead_detail->query);

					foreach ($email_template_datas as $var => $email_template_data) {
						$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
					}

					//send mail to clients
					global $wpdb;
					$table_name = $wpdb->prefix .'edugorilla_client_preferences';
					$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name WHERE preferences = 'Weekly_Digest'");

					$headers = array('Content-Type: text/html; charset=UTF-8');

					foreach ($client_email_addresses as $cea) {
						add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
						$institute_emails_status = wp_mail($cea->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
						remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
					}

		}
	}

	function do_this_daily() {
		//do something every day
		// send mail every day at 5PM	
		$edugorilla_email = get_option('edugorilla_email_setting1');
		$edugorilla_email_body = stripslashes($edugorilla_email['body']);
		global $wpdb;
		$table_name = $wpdb->prefix .'edugorilla_lead_details';
		$lead_details = $wpdb->get_results( "SELECT * FROM $table_name");
			foreach ($lead_details as $lead_detail) {
					$edugorilla_email_subject = str_replace("{category}", $lead_detail->contact_category, $edugorilla_email['subject']);
					$email_template_datas = array("{Contact_Person}" => $lead_detail->name, "{category}" => $lead_detail->category, "{location}" => $lead_detail->location_id, "{contact no}" => $lead_detail->contact_no, "{email address}" => $lead_detail->email, "{query}" => $lead_detail->query);

					foreach ($email_template_datas as $var => $email_template_data) {
						$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
					}

					//send mail to clients (Instant Mail)
					global $wpdb;
					$table_name = $wpdb->prefix .'edugorilla_client_preferences';
					$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name WHERE preferences = 'Daily_Digest'");

					$headers = array('Content-Type: text/html; charset=UTF-8');

					foreach ($client_email_addresses as $cea) {
						add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
						$institute_emails_status = wp_mail($cea->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
						remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
					}
		}

	}

	function do_this_monthly() {
		//do something every month
		// send mail every month at 12PM on Friday
		$edugorilla_email = get_option('email_setting_form_monthly');
		$edugorilla_email_body = stripslashes($edugorilla_email['body']);
		global $wpdb;
		$table_name = $wpdb->prefix .'edugorilla_lead_details';
		$lead_details = $wpdb->get_results( "SELECT * FROM $table_name");
			foreach ($lead_details as $lead_detail) {
					$edugorilla_email_subject = str_replace("{category}", $lead_detail->contact_category, $edugorilla_email['subject']);
					$email_template_datas = array("{Contact_Person}" => $lead_detail->name, "{category}" => $lead_detail->category, "{location}" => $lead_detail->location_id, "{contact no}" => $lead_detail->contact_no, "{email address}" => $lead_detail->email, "{query}" => $lead_detail->query);

					foreach ($email_template_datas as $var => $email_template_data) {
						$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
					}

					//send mail to clients (Instant Mail)
					global $wpdb;
					$table_name = $wpdb->prefix .'edugorilla_client_preferences';
					$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name WHERE preferences = 'Monthly_Digest'");

					$headers = array('Content-Type: text/html; charset=UTF-8');

					foreach ($client_email_addresses as $cea) {
						add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
						$institute_emails_status = wp_mail($cea->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
						remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
					}
		}

	}


	function my_deactivation() {
		wp_clear_scheduled_hook('mail_send_daily');
		wp_clear_scheduled_hook('mail_send_weekly');
		wp_clear_scheduled_hook('mail_send_monthly');
	}

	//custom cron intervals for weekly and monthly
	add_filter( 'cron_schedules', 'monthly_add_weekly_cron_schedule' );
	function monthly_add_weekly_cron_schedule( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 604800, // 1 week in seconds
        'display'  => __( 'Once Weekly' ),
    );
 
 	 $schedules['monthly'] = array(
        'interval' => 2592000, // 1 month in seconds
        'display'  => __( 'Once Monthly' ),
    );

    return $schedules;
	}


?>