<?php

class DataBase_Helper
{
	public function add_educash_transaction($client_id, $educash, $adminComment)
	{
		global $wpdb;
		$transaction_table = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
		$time = current_time('mysql');
		$wpdb->insert($transaction_table, array(
			'time' => $time,
			'admin_id' => -1,
			'client_id' => $client_id,
			'transaction' => $educash,
			'comments' => $adminComment
		));
	}
}


?>