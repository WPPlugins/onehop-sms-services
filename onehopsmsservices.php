<?php
/**
 * Plugin Name: Onehop SMS Services
 * Plugin URI: http://screen-magic.com
 * Description: Easily Send SMSes on Wordpress. Search, Compare and Buy the best SMS products.
 Switch providers with one click using Labels.
 * Version: 1.0.2
 * Author: Screen-Magic Mobile Media Inc.
 * Author URI: http://screen-magic.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: onehop-sms-services
 *
 * @package OnehopSMSServices
 * @author  Screen-Magic Mobile Media Inc.
 */

/**
 * Onehop SMS Services is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Onehop SMS Services is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Onehop SMS Services. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );
define( 'ONEHOP_ADMIN_URL', get_admin_url() );
$onehop_sms_plugin = new OnehopSMSPlugin();

/**
 * OnehopSMSPlugin Class Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */
class OnehopSMSPlugin
{
	/**
	 * Plugin admin url.
	 *
	 * @var string
	 */
	public $onehhop_admin_url = ONEHOP_ADMIN_URL;

	/**
	 * Validation Erors.
	 *
	 * @var array
	 */
	public static $onehhop_reg_errors;

	/**
	 * Wordpresss database prefix.
	 *
	 * @var string
	 */
	protected $onehop_tb_prefix;

	/**
	 * Wordpresss database.
	 *
	 * @var object
	 */
	protected $onehop_wp_db;

	/**
	 * To check menu is added or not.
	 *
	 * @var bool
	 */
	private static $onehop_menuadded = false;

	/**
	 * API key from Configuration table.
	 *
	 * @var string
	 */
	protected $onehop_api;

	/**
	 * Onehop Send SMS API URL.
	 *
	 * @var string
	 */
	private static $onehop_sendsms_url;

	/**
	 * Onehop Get Labels API URL.
	 *
	 * @var string
	 */
	private static $onehop_label_url;

	/**
	 * Onehop Validate Key API URL.
	 *
	 * @var string
	 */
	private static $onehop_validatekey_url;

	/**
	 * Onehop plugin directory path.
	 *
	 * @var string
	 */
	private $plugin_dir_url;

	/**
	 * Onehop Constructor.
	 */
	public function __construct() {

		$this->onehop_wp_db = $GLOBALS['wpdb'];
		$this->onehop_tb_prefix = $GLOBALS['wpdb']->prefix;
		$this->onehop_api = get_option( 'ONEHOP_SEND_SMS_API' );
		$this->plugin_dir_url = plugin_dir_url( __FILE__ );
		self::$onehop_sendsms_url = 'http://api.onehop.co/v1/sms/send/';
		self::$onehop_label_url = 'http://api.onehop.co/v1/labels/';
		self::$onehop_validatekey_url = 'http://api.onehop.co/v1/api_key/validate/';
		$this->onehop_start();

		register_activation_hook(
			__FILE__,
			array( &$this,'onehop_activate' )
		);
		register_uninstall_hook(
			__FILE__,
			array( 'OnehopSMSPlugin','onehop_uninstall' )
		);

		add_action(
			'admin_enqueue_scripts',
			array( &$this, 'onehop_admin_assets' )
		);
		add_action(
			'admin_menu',
			array( &$this, 'onehop_menu' )
		);
		add_action(
			'wp_print_scripts',
			array( &$this, 'onehop_ajax_script' )
		);
		add_action(
			'wp_ajax_fill_placeholder',
			array( &$this, 'onehop_fill_placeholder_callback' )
		);
		add_action(
			'wp_ajax_delete_template',
			array( &$this, 'onehop_delete_template_callback' )
		);
		add_action(
			'wp_ajax_fill_body',
			array( &$this, 'onehop_fill_body_by_template_callback' )
		);

		add_action(
			'woocommerce_thankyou',
			array( &$this, 'onehop_order_confirmed' )
		);
		add_action(
			'woocommerce_order_status_completed',
			array( &$this, 'onehop_order_completed' )
		);
		add_action(
			'woocommerce_order_status_processing',
			array( &$this, 'onehop_order_processed' )
		);
		add_action(
			'woocommerce_order_status_on-hold',
			array( &$this, 'onehop_order_onhold' )
		);
		add_action(
			'save_post',
			array( &$this,'onehop_save_post' )
		);
		add_action(
			'delete_postmeta',
			array( &$this,'onehop_delete_postmeta_data' )
		);
		add_action(
			'woocommerce_save_product_variation',
			array( &$this,'onehop_save_product_variation' ),
			10,
			2
		);
	}

	/**
	 * Wordpress hook automatically called after plugin installation. Create tables in database.
	 */
	public static function onehop_activate() {

		include plugin_dir_path( __FILE__ ).'/includes/hooks/activation.php';
		include_once ABSPATH.'wp-admin/includes/upgrade.php';
		dbDelta( $create_rulesets );
		dbDelta( $create_template );
	}

	/**
	 * Wordpress hook automatically called after plugin uninstall. Remove tables and other entries from database.
	 */
	public static function onehop_uninstall() {

		include_once ABSPATH.'wp-admin/includes/upgrade.php';
		wp_cache_delete( 'onehop-drop-table-ruleset' );
		$GLOBALS['wpdb']->query( $GLOBALS['wpdb']->prepare( 'DROP TABLE IF EXISTS ' . $GLOBALS['wpdb']->prefix . 'onehop_sms_rulesets;' ) );
		wp_cache_delete( 'onehop-drop-table-templates' );
		$GLOBALS['wpdb']->query( $GLOBALS['wpdb']->prepare( 'DROP TABLE IF EXISTS ' . $GLOBALS['wpdb']->prefix . 'onehop_sms_templates;' ) );
		delete_option( 'ONEHOP_SEND_SMS_API' );
		delete_option( 'ONEHOP_ADMIN_MOBILE' );
	}

	/**
	 * Initialize plugin menu
	 */
	public function onehop_initialize_menu() {

		$role = get_role( 'administrator' );

		$role->add_cap( 'wpsms_welcome' );
		$role->add_cap( 'wpsms_config' );
		$role->add_cap( 'wpsms_sendsms' );
		$role->add_cap( 'wpsms_automation' );
		$role->add_cap( 'wpsms_templates' );
	}

	/**
	 * Register plugin CSS
	 */
	public function onehop_admin_assets() {

		wp_register_style(
			'sms-admin',
			plugin_dir_url( __FILE__ ).'assets/css/onehopsmsservice.css',
			true,
			'1.1'
		);
		wp_enqueue_style( 'sms-admin' );
	}

	/**
	 * Init action
	 */
	private function onehop_start() {

		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include ABSPATH.'wp-includes/pluggable.php';
		}

		if ( is_admin() and is_super_admin() ) {
			$this->onehop_initialize_menu();
		}
	}

	/**
	 * Create plugin menu
	 */
	public function onehop_menu() {

		add_menu_page(
			__( 'Onehop SMS Service', 'onehopsmsservice' ),
			__( 'Onehop SMS Service', 'onehopsmsservice' ),
			'onehopsms_sendsms',
			'onehopsmsservice',
			array( &$this,'onehop_welcome_page' ),
			$this->plugin_dir_url . '/assets/images/onehop.png'
		);
		add_submenu_page(
			'onehopsmsservice',
			__( 'Welcome', 'onehopsmsservice' ),
			__( 'Welcome', 'onehopsmsservice' ),
			'wpsms_welcome',
			'welcome',
			array( &$this,'onehop_welcome_page' )
		);
		add_submenu_page(
			'onehopsmsservice',
			__( 'Configuration', 'onehopsmsservice' ),
			__( 'Configuration', 'onehopsmsservice' ),
			'wpsms_config',
			'config',
			array( &$this,'onehop_config_page' )
		);
		if ( get_option( 'ONEHOP_SEND_SMS_API' ) ) {
			$this->onehop_add_config_menu();
		}

		$actions = array();
		$actions['config'] = 'includes/actions/configuration.php';
		$actions['templates'] = 'includes/actions/templates.php';
		$actions['sendsms'] = 'includes/actions/sendsms.php';
		$actions['automation'] = 'includes/actions/automation.php';

		$phpself = get_admin_url().'admin.php';
		$pagename = isset( $_GET['action'] )? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$nonce = wp_create_nonce( 'onehop_button_submit' );

		if ( isset( $pagename ) && isset( $actions[ $pagename ] ) ) {
			$pagename = strtolower( $pagename );
			include_once plugin_dir_path( __FILE__ ).'/'.$actions[ $pagename ];

			$functionname = 'onehopsmsservice_'.$pagename.'_index';
			$returnval = $functionname( $nonce );
			$actionval = (isset( $returnval ) && array_key_exists( 'action', $returnval )) ? $returnval['action'] : '';
			switch ( $actionval ) {
				case 'config_success':
					$this->onehop_add_config_menu();
				break;

				case 'add_template':
					wp_redirect( $phpself.'?page=templates&action=templates&add=1', 301 );
				break;

				case 'edit_template':
					wp_redirect(
						$phpself."?page=templates&action=templates&edit=$returnval[value]",
						301
					);
				break;
			}
		}

		$add = isset( $_GET['add'] ) ? sanitize_text_field( wp_unslash( $_GET['add'] ) ) : '';
		if ( isset( $pagename ) && isset( $add ) ) {
			$pagename = strtolower( $pagename );
			include_once plugin_dir_path( __FILE__ ).'/includes/actions/templates.php';
			$return = onehopsmsservice_templates_add( $nonce );
			if ( $return ) {
				wp_redirect( $phpself.'?page=templates&action=templates', 301 );
			}
		}

		$edit = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : '';
		if ( isset( $pagename ) && isset( $edit ) ) {
			$pagename = strtolower( $pagename );
			include_once plugin_dir_path( __FILE__ ).'/includes/actions/templates.php';
			$return = onehopsmsservice_templates_edit( $edit, $nonce );
			if ( $return ) {
				wp_redirect( $phpself.'?page=templates&action=templates', 301 );
			}
		}
	}

	/**
	 * Open welcome page. Add necessasry css and js.
	 */
	public function onehop_welcome_page() {

		wp_register_style(
			'boootstrap-style',
			plugin_dir_url( __FILE__ ).'assets/css/bootstrap.min.css',
			true,
			'1.1'
		);
		wp_enqueue_style( 'boootstrap-style' );
		wp_register_style(
			'welcome-style',
			plugin_dir_url( __FILE__ ).'assets/css/welcomestyle.css',
			true,
			'1.1'
		);
		wp_enqueue_style( 'welcome-style' );
		wp_register_script(
			'bootstrap-script',
			plugin_dir_url( __FILE__ ).'assets/js/bootstrap.min.js',
			false,
			''
		);
		wp_enqueue_script( 'bootstrap-script' );
		wp_register_script(
			'welcome-script',
			plugin_dir_url( __FILE__ ).'assets/js/welcome.js',
			false,
			''
		);
		wp_enqueue_script( 'welcome-script' );
		include_once plugin_dir_path( __FILE__ ).'/includes/templates/welcome.php';
	}

	/**
	 * Open configuration page.
	 */
	public function onehop_config_page() {

		include_once plugin_dir_path( __FILE__ ).'/includes/templates/configuration.php';
	}

	/**
	 * Open template page. Retrieve required data from database.
	 */
	public function onehop_templates_page() {

		$tempate_list = $this->onehop_wp_db->get_results(
			"select * from {$this->onehop_tb_prefix}onehop_sms_templates order by temp_name"
		);
		include_once plugin_dir_path( __FILE__ ).'/includes/templates/templates.php';
	}

	/**
	 * Open sendsme page. Retrieve required data from database.
	 */
	public function onehop_sendsms_page() {

		$tempate_list = $this->onehop_wp_db->get_results(
			"select * from {$this->onehop_tb_prefix}onehop_sms_templates order by temp_name"
		);
		$label_list = $this->onehop_get_labels();

		include_once plugin_dir_path( __FILE__ ).'/includes/templates/sendsms.php';
	}

	/**
	 * Open automation page. Retrieve required data from database.
	 */
	public function onehop_automation_page() {

		$tempate_list = $this->onehop_wp_db->get_results(
			"select * from {$this->onehop_tb_prefix}onehop_sms_templates order by temp_name"
		);
		$label_list = $this->onehop_get_labels();

		$db_list = $this->onehop_wp_db->get_results( "select * from {$this->onehop_tb_prefix}onehop_sms_rulesets" );

		$mainarray = array();

		$temparray = array();
		$temparray['title'] = 'Order Confirmation';
		$temparray['desc'] = 'Send notifications to your buyers whenever an order is confirmed.';
		$this->onehhop_automation_db_value( $db_list, 'order', $temparray );
		$mainarray['order'] = $temparray;

		$temparray = array();
		$temparray['title'] = 'Order Completed';
		$temparray['desc'] = 'Send notifications to your buyers whenever an order is completed.';
		$this->onehhop_automation_db_value( $db_list, 'completed', $temparray );
		$mainarray['completed'] = $temparray;

		$temparray = array();
		$temparray['title'] = 'Order Processing';
		$temparray['desc'] = 'Send notifications to your buyers whenever an order is processed.';
		$this->onehhop_automation_db_value( $db_list, 'processed', $temparray );
		$mainarray['processed'] = $temparray;

		$temparray = array();
		$temparray['title'] = 'Order On hold';
		$temparray['desc'] = 'Send notifications to your buyers whenever an order is on hold.';
		$this->onehhop_automation_db_value( $db_list, 'onhold', $temparray );
		$mainarray['onhold'] = $temparray;

		$temparray = array();
		$temparray['title'] = 'Out of Stock Alerts';
		$temparray['desc'] = 'Send alerts whenever a product is Out of Stock.';
		$this->onehhop_automation_db_value( $db_list, 'outstock', $temparray );
		$mainarray['outstock'] = $temparray;

		$temparray = array();
		$temparray['title'] = 'Back in Stock Alerts';
		$temparray['desc'] = 'Send Alerts whenever a product is Back in Stock. 
        Ensure you have WooCommerce Product Stock Alert plugin 
        installed for this feature to work.';
		$this->onehhop_automation_db_value( $db_list, 'backstock', $temparray );
		$mainarray['backstock'] = $temparray;

		include_once plugin_dir_path( __FILE__ ).'/includes/templates/automation.php';
	}

	/**
	 * Retrieve automation values from database.
	 *
	 * @param array  $db_list   for database values.
	 * @param string $key       for rulekey.
	 * @param array  $temparray (pass by reference) for temporary array.
	 */
	private function onehhop_automation_db_value( $db_list, $key, &$temparray ) {

		foreach ( $db_list as $db ) {
			if ( $key === $db->rule_name ) {
				$temparray['activate'] = ('1' === $db->active) ? $key : '';
				$temparray['label'] = $db->label;
				$temparray['template'] = $db->template;
				$temparray['sender'] = $db->senderid;
				break;
			}
		}
	}

	/**
	 * Add menu after adding API key in configuration page.
	 */
	private function onehop_add_config_menu() {

		if ( false === self::$onehop_menuadded ) {
			add_submenu_page(
				'onehopsmsservice',
				__( 'Send SMS', 'onehopsmsservice' ),
				__( 'Send SMS', 'onehopsmsservice' ),
				'wpsms_sendsms',
				'sendsms',
				array( &$this,'onehop_sendsms_page' )
			);
			$wocommerceinstalled = in_array(
				'woocommerce/woocommerce.php',
				apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
				true
			);
			if ( $wocommerceinstalled ) {
					  add_submenu_page(
						  'onehopsmsservice',
						  __( 'SMS Automation', 'onehopsmsservice' ),
						  __( 'SMS Automation', 'onehopsmsservice' ),
						  'wpsms_automation',
						  'automation',
						  array( &$this,'onehop_automation_page' )
					  );
			}
			add_submenu_page(
				'onehopsmsservice',
				__( 'Manage Templates', 'onehopsmsservice' ),
				__( 'Manage Templates', 'onehopsmsservice' ),
				'wpsms_templates',
				'templates',
				array( &$this,'onehop_templates_page' )
			);
			self::$onehop_menuadded = true;
		}
	}

	/**
	 * Display validation errors on each page.
	 */
	public static function onehop_show_error() {

		if ( isset( self::$onehhop_reg_errors ) ) {
			foreach ( self::$onehhop_reg_errors->get_error_messages() as $error ) {
				return $error;
			}
		}
	}

	/**
	 * Register script file used for AJAX calls.
	 */
	public function onehop_ajax_script() {

		wp_enqueue_script(
			'ajax-placeholder',
			plugin_dir_url( __FILE__ ).'/assets/js/ajax-call.js',
			array( 'jquery' )
		);
		wp_localize_script(
			'ajax-placeholder',
			'the_ajax_script',
			array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
		);
	}

	/**
	 * Get labels from API using wordpress remote get.
	 *
	 * @return array
	 */
	private function onehop_get_labels() {

		$label_list = array();

		$output = wp_remote_get(
			self::$onehop_label_url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
			'apiKey' => $this->onehop_api,
			),
			'body' => '',
			)
		);

		if ( $output && isset( $output['body'] ) ) {
			$temp = json_decode( $output['body'] );
			if ( $temp && isset( $temp ) && isset( $temp->labelsList ) ) {
				foreach ( $temp->labelsList as $label_val ) {
					array_push( $label_list, $label_val );
				}
			}
		}

		return $label_list;
	}

	/**
	 * AJAX callback function for filling placeholder dropdown on add, edit template page.
	 */
	public function onehop_fill_placeholder_callback() {

		$returnarray = array();

		$response = isset( $_POST['post_var'] ) ? sanitize_text_field( wp_unslash( $_POST['post_var'] ) ) : '';
		$ajax_nonce = 'onehop_manage_template';
		$postnonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $postnonce, $ajax_nonce ) ) {
			wp_die( 'Security Error' );
		}

		switch ( $response ) {
			case 'customer':
				array_push( $returnarray, 'firstname' );
				array_push( $returnarray, 'lastname' );
				array_push( $returnarray, 'email' );
				array_push( $returnarray, 'mobile' );
			break;

			case 'order':
				array_push( $returnarray, 'order id' );
				array_push( $returnarray, 'transaction id' );
				array_push( $returnarray, 'price' );
				array_push( $returnarray, 'discount' );
				array_push( $returnarray, 'shipping address' );
			break;

			case 'product':
				array_push( $returnarray, 'product id' );
				array_push( $returnarray, 'product name' );
			break;
		}
		echo wp_json_encode( $returnarray );
		wp_die();
	}

	/**
	 * AJAX callback function for deleting template on template list page.
	 */
	public function onehop_delete_template_callback() {

		$id = isset( $_POST['post_var'] ) ? absint( $_POST['post_var'] ) : 0;
		$ajax_nonce = 'onehop_template-delete-'.$id;
		$postnonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $postnonce, $ajax_nonce ) ) {
			wp_die( 'Security Error' );
		} else {
			$tempate_row = $this->onehop_wp_db->get_row(
				"select ruleid from {$this->onehop_tb_prefix}onehop_sms_rulesets 
                where template = ".$id
			);
			if ( $tempate_row ) {
				echo 'You cannot delete this template because it is already set in SMS automation.';
			} else {
				$this->onehop_wp_db->delete(
					$this->onehop_tb_prefix.'onehop_sms_templates',
					array( 'temp_id' => $id )
				);
				echo 'Template has been deleted successfully.';
			}
		}
		wp_die();
	}

	/**
	 * AJAX callback function for filling sms body from template body on send sms page.
	 */
	public function onehop_fill_body_by_template_callback() {

		$id = isset( $_POST['post_var'] ) ? absint( $_POST['post_var'] ) : 0;

		$ajax_nonce = 'onehop_fill_body';
		$postnonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $postnonce, $ajax_nonce ) ) {
			wp_die( '' );
		}

		$tempate_row = $this->onehop_wp_db->get_row(
			"select temp_body from {$this->onehop_tb_prefix}onehop_sms_templates 
            where temp_id = ".$id
		);
		if ( $tempate_row ) {
			echo esc_textarea( $tempate_row->temp_body );
		} else {
			echo '';
		}
		wp_die();
	}

	/**
	 * Send SMS via API.
	 *
	 * @param  array $postdata for data send to API.
	 * @return json
	 */
	public static function onehop_send_sms( $postdata ) {

		if ( '' === $postdata && empty( $postdata ) ) {
			return false;
		}

		$output = wp_remote_post(

			self::$onehop_sendsms_url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
			'apiKey' => get_option( 'ONEHOP_SEND_SMS_API' ),
			),
			'body' => $postdata,
			'cookies' => array(),
			)
		);
		if ( $output && isset( $output['response'] ) && 200 === $output['response']['code'] ) {
			if ( isset( $output['body'] ) ) {
				return json_decode( $output['body'] );
			}
		}
		return '';
	}

	/**
	 * Validate API key entered by user on configuration page.
	 *
	 * @param  string $key for key which need to be validated against API.
	 * @return json $output
	 */
	public static function onehop_validate_key( $key ) {

		$output = wp_remote_get(
			self::$onehop_validatekey_url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
			'apiKey' => $key,
			),
			'body' => '',
			)
		);

		if ( $output && isset( $output['body'] ) ) {
			return json_decode( $output['body'] );
		}
		return '';
	}

	/**
	 * Hook called automatically when order placed by user.
	 *
	 * @param string $order_id for order id.
	 */
	public function onehop_order_confirmed( $order_id ) {

		$tempate_row = $this->onehop_get_template( 'order' );
		if ( ! $tempate_row ) {
			return;
		}

		$this->onehop_process_order( $order_id, $tempate_row, 'Order Confirmed' );

		$order = new WC_Order( $order_id );
		foreach ( $order->get_items() as $item ) {
			if ( $item && isset( $item['variation_id'] ) && absint( $item['variation_id'] ) > 0 ) {
				$this->onehop_save_product_variation( $item['variation_id'], 0 );
			} else {
				$this->onehop_out_stock( $item['product_id'], false );
			}
		}
	}

	/**
	 * Hook called automatically when order's status updated as Completed.
	 *
	 * @param string $order_id for order id.
	 */
	public function onehop_order_completed( $order_id ) {

		$tempate_row = $this->onehop_get_template( 'completed' );
		if ( ! $tempate_row ) {
			return;
		}

		$this->onehop_process_order( $order_id, $tempate_row, 'Order Completed' );
	}

	/**
	 * Hook called automatically when order's status updated as Processed.
	 *
	 * @param string $order_id for order id.
	 */
	public function onehop_order_processed( $order_id ) {

		$tempate_row = $this->onehop_get_template( 'processed' );
		if ( ! $tempate_row ) {
			return;
		}

		$this->onehop_process_order( $order_id, $tempate_row, 'Order Processing' );
	}

	/**
	 * Hook called automatically when order's status updated as on hold.
	 *
	 * @param string $order_id for order id.
	 */
	public function onehop_order_onhold( $order_id ) {

		$tempate_row = $this->onehop_get_template( 'onhold' );
		if ( ! $tempate_row ) {
			return;
		}

		$this->onehop_process_order( $order_id, $tempate_row, 'Order On-Hold' );
	}

	/**
	 * Process order before sending SMS.
	 *
	 * @param string $order_id    for order id.
	 * @param array  $tempate_row for template array.
	 * @param string $logkey      for key.
	 */
	public function onehop_process_order( $order_id, $tempate_row, $logkey ) {

		$order = new WC_Order( $order_id );

		$body = nl2br( $tempate_row->temp_body );

		$body = str_replace( '{firstname}', $order->billing_first_name, $body );
		$body = str_replace( '{lastname}', $order->billing_last_name, $body );
		$body = str_replace( '{email}', $order->billing_email, $body );
		$body = str_replace( '{mobile}', $order->billing_phone, $body );

		$body = str_replace( '{order id}', $order->id, $body );
		$body = str_replace( '{transaction id}', $order->get_transaction_id(), $body );

		$formatted_total = $order->get_order_currency().''.$order->get_total();
		$body = str_replace( '{price}', $formatted_total, $body );
		$body = str_replace( '{shipping address}', $order->get_formatted_shipping_address(), $body );

		$idarray = array();
		$namearray = array();

		$product_disc = 0;
		foreach ( $order->get_items() as $item ) {
			$pid = $item['product_id'];
			array_push( $namearray, $item['name'] );
			array_push( $idarray, $pid );

			$regular_price = (float) get_post_meta( $pid, '_regular_price', true );
			$sale_price = (float) get_post_meta( $pid, '_sale_price', true );
			$quantity = (int) $item['qty'];
			if ( $regular_price > 0 && $sale_price > 0 && $quantity ) {
				$product_disc += ($regular_price - $sale_price) * $quantity;
			}

			if ( isset( $item['variation_id'] ) ) {
				$regular_price = (float) get_post_meta( $item['variation_id'], '_regular_price', true );
				$sale_price = (float) get_post_meta( $item['variation_id'], '_sale_price', true );
				if ( $regular_price > 0 && $sale_price > 0 && $quantity > 0 ) {
					$product_disc += ($regular_price - $sale_price) * $quantity;
				}
			}
		}
		$order_discount = (float) $order->get_total_discount();

		if ( $product_disc > 0 ) {
			$order_discount += $product_disc;
		}

		$discount_total = $order->get_order_currency().''.$order_discount;
		$body = str_replace( '{discount}', $discount_total, $body );

		$body = str_replace( '{product id}', implode( ', ', $idarray ), $body );
		$body = str_replace( '{product name}', implode( ', ', $namearray ), $body );
		unset( $idarray );
		unset( $namearray );

		$body = preg_replace( '/<br(\s+)?\/?>/i', "\n", $body );
		$postdata = array(
		 'label' => sanitize_text_field( $tempate_row->label ),
		 'sms_text' => sanitize_text_field( $body ),
		 'source' => '22000',
		 'sender_id' => $tempate_row->senderid,
		 'mobile_number' => $order->billing_phone,
		);
		$return = self::onehop_send_sms( $postdata );
		self::onehop_save_log( $logkey, $body, $order->billing_phone );
	}

	/**
	 * Process when product was out of stock.
	 *
	 * @param string $product_id         for product id.
	 * @param bool   $process_variations for process variations.
	 */
	public function onehop_out_stock( $product_id, $process_variations ) {

		$tempate_row = $this->onehop_get_template( 'outstock' );
		if ( ! $tempate_row ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( $product && isset( $product->stock_status ) && 'outofstock' === $product->stock_status ) {
			$body = nl2br( $tempate_row->temp_body );
			$body = str_replace( '{product id}', $product->id, $body );
			$body = str_replace( '{product name}', $product->get_title(), $body );

			$admin_mobile = get_option( 'ONEHOP_ADMIN_MOBILE' );
			$body = preg_replace( '/<br(\s+)?\/?>/i', "\n", $body );

			$postdata = array(
			 'label' => sanitize_text_field( $tempate_row->label ),
			 'sms_text' => sanitize_text_field( $body ),
			 'source' => '22000',
			 'sender_id' => $tempate_row->senderid,
			 'mobile_number' => $admin_mobile,
			);
			$return = self::onehop_send_sms( $postdata );
			self::onehop_save_log( 'Out of Stock', $body, $admin_mobile );
		}

		if ( $process_variations && isset( $product->product_type ) && 'variable' === $product->product_type  ) {
			$variations = $product->get_available_variations();
			if ( $variations && isset( $variations ) ) {
				foreach ( $variations as $var ) {
					if ( $var && isset( $var['variation_id'] ) ) {
						$this->onehop_process_out_of_stock_product_variation( $var['variation_id'], $tempate_row );
					}
				}
			}
		}
	}


	/**
	 * Hook called automatically when product variation updated.
	 *
	 * @param string $variation_id for $variation id.
	 * @param string $i            the i.
	 */
	public function onehop_save_product_variation( $variation_id, $i ) {

		$out_tempate_row = $this->onehop_get_template( 'outstock' );
		if ( $out_tempate_row ) {
			$this->onehop_process_out_of_stock_product_variation( $variation_id, $out_tempate_row );
		}

		$back_tempate_row = $this->onehop_get_template( 'backstock' );
		if ( $back_tempate_row ) {
			$this->onehop_process_back_of_stock_product_variation( $variation_id, $back_tempate_row );
		}
	}


	/**
	 * Process out of stock for product variation.
	 *
	 * @param string $variation_id for variation id.
	 * @param array  $tempate_row  for template details.
	 */
	private function onehop_process_out_of_stock_product_variation( $variation_id, $tempate_row ) {

		$variation = wc_get_product( $variation_id );
		if ( $variation && false === $variation->is_in_stock() ) {

			$formatted_attributes = $variation->get_formatted_variation_attributes( true );
			$product_name = sprintf( '%s: %s', $variation->get_title(), $formatted_attributes );

			$body = nl2br( $tempate_row->temp_body );

			$body = str_replace( '{product id}', $variation->id, $body );
			$body = str_replace( '{product name}', $product_name, $body );
			$body = preg_replace( '/<br(\s+)?\/?>/i', "\n", $body );

			$admin_mobile = get_option( 'ONEHOP_ADMIN_MOBILE' );
			$postdata = array(
			 'label' => sanitize_text_field( $tempate_row->label ),
			 'sms_text' => sanitize_text_field( $body ),
			 'source' => '22000',
			 'sender_id' => $tempate_row->senderid,
			 'mobile_number' => $admin_mobile,
			);
			$return = self::onehop_send_sms( $postdata );
			self::onehop_save_log( 'Out of Stock Variation', $body, $admin_mobile );
		}
	}

	/**
	 * Process back of stock for product variation.
	 *
	 * @param string $variation_id for variation id.
	 * @param array  $tempate_row  for template details.
	 */
	private function onehop_process_back_of_stock_product_variation( $variation_id, $tempate_row ) {

		$variation = wc_get_product( $variation_id );
		if ( $variation && $variation->is_in_stock() ) {

			$formatted_attributes = $variation->get_formatted_variation_attributes( true );
			$product_name = sprintf( '%s: %s', $variation->get_title(), $formatted_attributes );

			$postmetarray = get_post_meta( $variation_id, '_product_subscriber', true );
			$this->onehop_process_user_for_backstock( $tempate_row, $postmetarray, $variation_id, $product_name );
		}
	}


	/**
	 * Process when product is back in stock.
	 *
	 * @param string $product_id for sending productid.
	 */
	public function onehop_back_stock( $product_id ) {

		$tempate_row = $this->onehop_get_template( 'backstock' );
		if ( ! $tempate_row ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( $product && isset( $product->stock_status ) && 'instock' === $product->stock_status ) {

			$postmetadata = get_post_meta( $product->id, 'onehop_back_of_stock', true );
			if ( ! $postmetadata || ! isset( $postmetadata ) || strlen( $postmetadata ) === 0 ) {
				return;
			}

			$postmetarray = unserialize( $postmetadata );
			$this->onehop_process_user_for_backstock( $tempate_row, $postmetarray, $product->id, $product->get_title() );
		}

		if ( isset( $product->product_type ) && 'variable' === $product->product_type  ) {
			$variations = $product->get_available_variations();
			if ( $variations && isset( $variations ) ) {
				foreach ( $variations as $var ) {
					if ( $var && isset( $var['variation_id'] ) ) {
						$this->onehop_process_back_of_stock_product_variation( $var['variation_id'], $tempate_row );
						delete_post_meta( $var['variation_id'], 'onehop_back_of_stock' );
					}
				}
			}
		}
	}

	/**
	 * Get user details from array and send back stock sms.
	 *
	 * @param array  $tempate_row  for template information.
	 * @param array  $postmetarray for user array.
	 * @param string $product_id   for product id.
	 * @param string $product_name for product name.
	 */
	private function onehop_process_user_for_backstock( $tempate_row, $postmetarray, $product_id, $product_name ) {

		if ( ! $postmetarray || ! isset( $postmetarray ) || count( $postmetarray ) === 0 ) {
			return;
		}

		foreach ( $postmetarray as $email ) {
			$user = get_user_by( 'email', $email );
			if ( ! $user || ! isset( $user ) || ! isset( $user->ID ) ) {
				continue;
			}

			$mobile = get_user_meta( $user->ID, 'billing_phone', true );
			$fname = get_user_meta( $user->ID, 'billing_first_name', true );
			$lname = get_user_meta( $user->ID, 'billing_last_name', true );
			unset( $user );

			$body = nl2br( $tempate_row->temp_body );

			$body = str_replace( '{product id}', $product_id, $body );
			$body = str_replace( '{product name}', $product_name, $body );

			$body = str_replace( '{firstname}', $fname, $body );
			$body = str_replace( '{lastname}', $lname, $body );
			$body = str_replace( '{email}', $email, $body );
			$body = str_replace( '{mobile}', $mobile, $body );

			$body = preg_replace( '/<br(\s+)?\/?>/i', "\n", $body );
			$postdata = array(
			 'label' => sanitize_text_field( $tempate_row->label ),
			 'sms_text' => sanitize_text_field( $body ),
			 'source' => '22000',
			 'sender_id' => $tempate_row->senderid,
			 'mobile_number' => $mobile,
			);
			   $return = self::onehop_send_sms( $postdata );
			delete_post_meta( $product_id, 'onehop_back_of_stock' );
			self::onehop_save_log( 'Back Stock', $body, $mobile );
		}
	}

	/**
	 * Hook called automatically when Post,Product saved from backend.
	 *
	 * @param string $post_id for post id.
	 */
	public function onehop_save_post( $post_id ) {

		$this->onehop_out_stock( $post_id, true );
		$this->onehop_back_stock( $post_id );
	}

	/**
	 * Hook called automatically before meta deleted from Post.
	 *
	 * @param string $meta_ids for meta id.
	 */
	public function onehop_delete_postmeta_data( $meta_ids ) {

		if ( ! isset( $meta_ids ) || count( $meta_ids ) === 0 ) {
			return;
		}
		$meta_string = implode( ',', $meta_ids );
		$meta_string = sanitize_text_field( $meta_string );
		$meta_row = $this->onehop_wp_db->get_row(
			"select post_id, meta_value from 
            {$this->onehop_tb_prefix}postmeta where meta_key = '_product_subscriber' 
            and meta_id in ($meta_string)"
		);
		if ( $meta_row ) {
			add_post_meta( $meta_row->post_id, 'onehop_back_of_stock', $meta_row->meta_value, false );
		}
	}

	/**
	 * Get template from database
	 *
	 * @param  string $key for rule key.
	 * @return array
	 */
	public function onehop_get_template( $key ) {

		$key = sanitize_text_field( $key );
		$tempate_row = $this->onehop_wp_db->get_row(
			"select rs.*, t.temp_body from 
            {$this->onehop_tb_prefix}onehop_sms_rulesets rs inner join {$this->onehop_tb_prefix}onehop_sms_templates t 
            on rs.template = t.temp_id where rs.rule_name = '$key' and rs.active ='1'"
		);

		return $tempate_row;
	}

	/**
	 * Get plugin URL
	 *
	 * @return string
	 */
	public static function onehop_get_plugin_url() {

		return plugin_dir_url( __FILE__ );
	}

	/**
	 * Save into Log file.
	 *
	 * @param string $key    for key.
	 * @param string $data   for log data.
	 * @param string $mobile for mobile.
	 */
	public static function onehop_save_log( $key, $data, $mobile ) {

		$currentdate = current_time( 'Y-m-d H:i:s', $gmt = 0 );

		$temp = array();
		$temp['key'] = $key;
		$temp['date'] = $currentdate;
		$temp['mobile'] = $mobile;
		$temp['smsbody'] = $data;

		$message = wp_json_encode( $temp );
		if ( WP_DEBUG === true ) {
			error_log( $message );
		}
	}
}
