<?php
$appear = '';

require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

class EduCash_Helper
{
	private $educash_lead_cost = 1;
	public function addEduCashToCurrentUser($amount)
	{
		$userId = wp_get_current_user()->ID;
		return $this->addEduCashToUser($userId, $amount);
	}

	public function removeEduCashFromCurrentUser($amount)
	{
		$userId = wp_get_current_user()->ID;
		return $this->removeEduCashFromUser($userId, $amount);
	}

	public function getEduCashForCurrentUser()
	{
		$userId = wp_get_current_user()->ID;
		return $this->getEduCashForUser($userId);
	}

	public function addEduCashToUser($userId, $amount)
	{

	}

	public function removeEduCashFromUser($user_id, $amount)
	{
		$databaseHelper = new DataBase_Helper();
		$currentEduCashValue = $databaseHelper->get_educash_for_user($user_id);
		$newEduCashValue = $currentEduCashValue - $this->educash_lead_cost;
		$transaction_cost = -$this->educash_lead_cost;
		if ($newEduCashValue > 0) {
			$insertion_status = $databaseHelper->add_educash_transaction($user_id, $transaction_cost, "Unlocked a lead");
			return "Success : $insertion_status";
		}
		return "Insufficient Funds";
	}

	public function getEduCashForUser($userId)
	{

	}

}

?>
