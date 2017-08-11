<?php
/**
 * Onehopsmsservice Sendsms Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );

/**
 * Process sendsms function.
 *
 * @param string $postnonce for nonce verification.
 */
function onehopsmsservice_sendsms_index( $postnonce ) {

	$returnarray = array();
	$iserror     = false;
	$_POST['wp_status'] = 0;

	if ( ! wp_verify_nonce( $postnonce, 'onehop_button_submit' ) ) {
		OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Security Error.' );
		return $returnarray;
	}

	OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;

	$wp_mobile = isset( $_POST['wp_mobile'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['wp_mobile'] ) ) ) : '';
	if ( ! isset( $wp_mobile ) || strlen( $wp_mobile ) === 0 ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Mobile number is required.' );
	} elseif ( ! preg_match( '/^[0-9]*$/', $wp_mobile ) ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Mobile number is not valid.' );
	}
	$wp_sender = isset( $_POST['wp_sender'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['wp_sender'] ) ) ) : '';
	if ( ! isset( $wp_sender ) || strlen( $wp_sender ) === 0 ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Sender Id is required.' );
	}
	$wp_label = isset( $_POST['wp_label'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['wp_label'] ) ) ) : '';
	if ( ! isset( $wp_label ) || strlen( $wp_label ) === 0 ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Label is required.' );
	}
	$wp_body = isset( $_POST['wp_body'] ) ? trim( wp_kses_data( wp_unslash( $_POST['wp_body'] ) ) ) : '';
	if ( ! isset( $wp_body ) || strlen( $wp_body ) === 0 ) {
		$iserror = true;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Message body is required.' );
	}

	if ( false === $iserror ) {
		$wp_body = preg_replace( '/<br(\s+)?\/?>/i', '\n', $wp_body );

		$postdata = array(
			'label' => $wp_label,
			'source' => '22000',
			'sender_id' => $wp_sender,
			'mobile_number' => $wp_mobile,
			'sms_text' => $wp_body,
		);

		$success  = OnehopSMSPlugin::onehop_send_sms( $postdata );
		if ( $success && isset( $success->status ) && 'submitted' === $success->status ) {
			$_POST['wp_status']    = 1;
			$returnarray['action'] = 'smssend_success';
			$_POST['wp_mobile'] = '';
			$_POST['wp_sender'] = '';
			$_POST['wp_label'] = '';
			$_POST['wp_template'] = '';
			$_POST['wp_body'] = '';
			OnehopSMSPlugin::onehop_save_log( 'SendSMS', $wp_body, $wp_mobile );
		} else {
			OnehopSMSPlugin::$onehhop_reg_errors->add(
				'error',
				'Error while sending the SMS.'
			);
		}
	}

	return $returnarray;
}
