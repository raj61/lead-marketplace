<?php
$appear = '';

require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

class EduCash_Helper
{
	private $eduCashMetaDataKey = 'edu_cash_amount';

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
		$eduCashValue = get_user_meta($user_id, $key = $this->eduCashMetaDataKey, $single = true);
		if (!$eduCashValue) {
			//Initially everyone gets 10 eduCash
			$eduCashValue = 10;
			$meta_id = add_user_meta($user_id, $this->eduCashMetaDataKey, $eduCashValue);
			if ($meta_id == false) {
				return "Unable to Create EduCash Meta Tag";
			}
		}
		$newEduCashValue = $eduCashValue - $amount;
		if ($newEduCashValue < 0) {
			return "Not Enough Funds";
		}
		$databaseHelper = new DataBase_Helper();
		$databaseHelper->add_educash_transaction($user_id, $newEduCashValue, "Unlocked a lead");
		$update_status = update_user_meta($user_id, $this->eduCashMetaDataKey, $newEduCashValue, $eduCashValue);
		if ($update_status != true) {
			return "Unable to update EduCash Meta Tag";
		}
		return "Success";
	}

	public function getEduCashForUser($userId)
	{

	}

}

?>
