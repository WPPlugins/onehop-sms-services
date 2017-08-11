<?php
/**
 * Onehopsmsservice Configuration Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );

/**
 * Button click event on Configuration Page. Called from main file.
 *
 * @param string $postnonce for nonce verification.
 */
function onehopsmsservice_config_index( $postnonce ) {

	do_action( 'onehop_config' );

	$returnarray = array();
	$iserror     = false;
	$_POST['wp_status'] = 0;

	if ( ! wp_verify_nonce( $postnonce, 'onehop_button_submit' ) ) {
		OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Security Error.' );
		return $returnarray;
	}

	OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
	$wp_api = isset( $_POST['wp_api'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['wp_api'] ) ) ) : '';
	if ( ! isset( $wp_api ) || strlen( $wp_api ) === 0 ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'API Key is required.' );
	} else {
		$success  = OnehopSMSPlugin::onehop_validate_key( $wp_api );
		if ( 'success' !== $success->status ) {
			$iserror = true;
			OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'API Key not valid.' );
		}
	}

	$wp_mobile = isset( $_POST['wp_admin_mobile'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['wp_admin_mobile'] ) ) ) : '';
	if ( ! isset( $wp_mobile ) || strlen( $wp_mobile ) === 0 ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Admin Mobile Number is required.' );
	} elseif ( ! preg_match( '/^[0-9]*$/', $wp_mobile ) ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Admin Mobile number is not valid.' );
	}

	if ( false === $iserror ) {
		$_POST['wp_status'] = 1;
		update_option( 'ONEHOP_SEND_SMS_API', $wp_api );
		update_option( 'ONEHOP_ADMIN_MOBILE', $wp_mobile );
		$returnarray['action'] = 'config_success';
	}

	return $returnarray;

}

