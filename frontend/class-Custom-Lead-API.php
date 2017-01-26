<?php

class Custom_Lead_API extends WP_REST_Controller
{

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes()
	{
		$version = '1';
		$namespace = 'marketplace/v' . $version;
		$base = 'leads';
		register_rest_route($namespace, '/' . $base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'get_items'),
				'permission_callback' => array($this, 'get_items_permissions_check'),
				'args' => array(),
			),
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'create_item'),
				'permission_callback' => array($this, 'create_item_permissions_check'),
				'args' => $this->get_endpoint_args_for_item_schema(true),
			),
		));
		register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'get_item'),
				'permission_callback' => array($this, 'get_item_permissions_check'),
				'args' => array(
					'context' => array(
						'default' => 'view',
					),
				),
			),
			array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array($this, 'update_item'),
				'permission_callback' => array($this, 'update_item_permissions_check'),
				'args' => $this->get_endpoint_args_for_item_schema(false),
			),
			array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array($this, 'delete_item'),
				'permission_callback' => array($this, 'delete_item_permissions_check'),
				'args' => array(
					'force' => array(
						'default' => false,
					),
				),
			),
		));
		register_rest_route($namespace, '/' . $base . '/schema', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_public_item_schema'),
		));
		register_rest_route($namespace, '/' . $base . '/details', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_lead_details'),
		));
		register_rest_route($namespace, '/' . $base . '/sethidden', array(
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => array($this, 'persist_hidden_status'),
			'args' => $this->get_endpoint_args_for_item_schema(true),
		));
		register_rest_route($namespace, '/' . $base . '/setunlock', array(
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => array($this, 'persist_unlock_status'),
			'args' => $this->get_endpoint_args_for_item_schema(true),
		));
	}

	/**
	 * Persist unlock status to the database
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function persist_unlock_status($request)
	{
		$lead_id = $request->get_param('lead_id');
		$unlock_status = $request->get_param('unlock_status');
		$userId = wp_get_current_user()->ID;
		return set_card_unlock_status_to_db($userId, $lead_id, $unlock_status);
	}

	/**
	 * Persist hidden status to the database
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function persist_hidden_status($request)
	{
		$lead_id = $request->get_param('lead_id');
		$hidden_status = $request->get_param('hidden_status');
		$userId = wp_get_current_user()->ID;
		set_card_hidden_status_to_db($userId, $lead_id, $hidden_status);

		$data_object = array();
		$data_object[] = "Successfully updated hidden_status to the database";
		$response = new WP_REST_Response($data_object);
		return $response;
	}

	/**
	 * Get details of all the leads
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_lead_details($request)
	{
		//$card1 = new Lead_Card('Rohit', 'Lucknow', 'CEO', 'Nirvana');
		//$card2 = new Lead_Card('Anantharam', 'Chennai', 'CTO', 'Relationship');
		$data_object = get_lead_details_from_db();

		// Create the response object
		$response = new WP_REST_Response($data_object);

		// Add a custom status code
		$response->set_status(201);

		// Add a custom header
		//$response->header( 'Referrer', 'http://www.google.com/' );

		return $response;
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items($request)
	{
		$items = array(); //do a query, call another class, etc
		$data = array();
		foreach ($items as $item) {
			$itemdata = $this->prepare_item_for_response($item, $request);
			$data[] = $this->prepare_response_for_collection($itemdata);
		}

		return new WP_REST_Response($data, 200);
	}

	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item($request)
	{
		//get parameters from request
		$params = $request->get_params();
		$item = array();//do a query, call another class, etc
		$data = $this->prepare_item_for_response($item, $request);

		//return a response or error based on some conditional
		if (1 == 1) {
			return new WP_REST_Response($data, 200);
		} else {
			return new WP_Error('code', __('message', 'text-domain'));
		}
	}

	/**
	 * Create one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function create_item($request)
	{

		$item = $this->prepare_item_for_database($request);

		if (function_exists('slug_some_function_to_create_item')) {
			$data = slug_some_function_to_create_item($item);
			if (is_array($data)) {
				return new WP_REST_Response($data, 200);
			}
		}

		return new WP_Error('cant-create', __('message', 'text-domain'), array('status' => 500));


	}

	/**
	 * Update one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function update_item($request)
	{
		$item = $this->prepare_item_for_database($request);

		if (function_exists('slug_some_function_to_update_item')) {
			$data = slug_some_function_to_update_item($item);
			if (is_array($data)) {
				return new WP_REST_Response($data, 200);
			}
		}

		return new WP_Error('cant-update', __('message', 'text-domain'), array('status' => 500));

	}

	/**
	 * Delete one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function delete_item($request)
	{
		$item = $this->prepare_item_for_database($request);

		if (function_exists('slug_some_function_to_delete_item')) {
			$deleted = slug_some_function_to_delete_item($item);
			if ($deleted) {
				return new WP_REST_Response(true, 200);
			}
		}

		return new WP_Error('cant-delete', __('message', 'text-domain'), array('status' => 500));
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check($request)
	{
		//return true; <--use to make readable by all
		return current_user_can('edit_something');
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check($request)
	{
		return $this->get_items_permissions_check($request);
	}

	/**
	 * Check if a given request has access to create items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function create_item_permissions_check($request)
	{
		return current_user_can('edit_something');
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check($request)
	{
		return $this->create_item_permissions_check($request);
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function delete_item_permissions_check($request)
	{
		return $this->create_item_permissions_check($request);
	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_Error|object $prepared_item
	 */
	protected function prepare_item_for_database($request)
	{
		return array();
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response($item, $request)
	{
		return array();
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params()
	{
		return array(
			'page' => array(
				'description' => 'Current page of the collection.',
				'type' => 'integer',
				'default' => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description' => 'Maximum number of items to be returned in result set.',
				'type' => 'integer',
				'default' => 10,
				'sanitize_callback' => 'absint',
			),
			'search' => array(
				'description' => 'Limit results to those matching a string.',
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}

try {
	$leadAPI = new Custom_Lead_API();
	//$leadAPI->register_routes();
	add_action('rest_api_init', [$leadAPI, 'register_routes']);
} catch (Exception $e) {
	echo 'Message: ' . $e->getMessage();
}

/**
 * Get details of all the leads table from database
 *
 */
function get_lead_details_from_db()
{
	//$card1 = new Lead_Card('Rohit', 'Lucknow', 'CEO', 'Nirvana');
	//$card2 = new Lead_Card('Anantharam', 'Chennai', 'CTO', 'Relationship');
	$cards_object = array();

	global $wpdb;
	$detail_query = "select * from {$wpdb->prefix}edugorilla_lead_details";
	$leads_details = $wpdb->get_results($detail_query, 'ARRAY_A');
	foreach ($leads_details as $leads_detail) {
		$lead_id = $leads_detail['id'];
		$lead_name = $leads_detail['name'];
		$lead_email = $leads_detail['contact_no'];
		$lead_contact_no = $leads_detail['email'];
		$lead_query = $leads_detail['query'];
		$lead_category = $leads_detail['category_id'];
		$lead_location = $leads_detail['location_id'];
		$lead_date_time = $leads_detail['date_time'];
		$mapping_query = "select * from {$wpdb->prefix}edugorilla_lead_client_mapping WHERE lead_id=$lead_id";
		$leads_mapping_details = $wpdb->get_results($mapping_query, 'ARRAY_A');
		$lead_is_unlocked = false;
		$lead_is_hidden = false;
		foreach ($leads_mapping_details as $leads_mapping_detail) {
			$lead_is_unlocked = $leads_mapping_detail['is_unlocked'];
			$lead_is_hidden = $leads_mapping_detail['is_hidden'];
		}
		$db_card = new Lead_Card($lead_id, $lead_name, $lead_email, $lead_contact_no, $lead_query, $lead_category, $lead_location, $lead_date_time, $lead_is_unlocked, $lead_is_hidden);
		$cards_object[] = $db_card;
	}

	return $cards_object;
}

function set_card_hidden_status_to_db($client_id, $lead_id, $hidden_status)
{
	global $wpdb;
	$lead_table = $wpdb->prefix . 'edugorilla_lead_client_mapping';
	$hidden_status = $hidden_status ? '1' : '0';
	$update_query = "UPDATE $lead_table SET is_hidden = '$hidden_status' WHERE $lead_table.lead_id = $lead_id AND $lead_table.client_id = $client_id";
	$hidden_status_update_result = $wpdb->get_results($update_query);
	echo "$hidden_status_update_result";
}

function set_card_unlock_status_to_db($client_id, $lead_id, $unlock_status)
{
	global $wpdb;
	$lead_table = $wpdb->prefix . 'edugorilla_lead_client_mapping';
	$unlock_status = $unlock_status ? '1' : '0';
	$result_status_string = "";
	if ($unlock_status == '1') {
		$eduCashHelper = new EduCash_Helper();
		$eduCashCostForLead = 1;
		$query_status = $eduCashHelper->removeEduCashFromUser($client_id, $eduCashCostForLead);
		if (!str_starts_with($query_status, "Success")) {
			return new WP_Error('EduCashError', $query_status);;
		}
		$result_status_string = $query_status;
	}
	$update_query = "UPDATE $lead_table SET is_unlocked = '$unlock_status' WHERE $lead_table.lead_id = $lead_id AND $lead_table.client_id = $client_id";
	$wpdb->get_results($update_query);

	$data_object = array();
	$data_object[] = "Successfully updated unlock_status to the database : $result_status_string";
	$response = new WP_REST_Response($data_object);
	return $response;
}

function str_starts_with($haystack, $needle)
{
	return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}

function str_ends_with($haystack, $needle)
{
	return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

?>