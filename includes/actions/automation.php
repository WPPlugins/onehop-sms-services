<?php
/**
 * Onehopsmsservice Automation Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );

/**
 * Process automation function.
 *
 * @param string $postnonce for nonce verification.
 */
function onehopsmsservice_automation_index( $postnonce ) {

	$returnarray = array();
	$iserror     = false;
	$_POST['wp_status'] = 0;

	if ( ! wp_verify_nonce( $postnonce, 'onehop_button_submit' ) ) {
		OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Security Error.' );
		return $returnarray;
	}

	$keys = array(
		'order',
		'completed',
		'processed',
		'onhold',
		'outstock',
		'backstock',
	);
	foreach ( $keys as $key ) {
		if ( isset( $_POST[ 'btnsubmit_' . $key ] ) ) {
			OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
			$wp_activate = filter_input( INPUT_POST, 'wp_activate_'.$key );

			$wp_sender = isset( $_POST[ 'wp_sender_'.$key ] ) ? trim( sanitize_text_field( wp_unslash( $_POST[ 'wp_sender_'.$key ] ) ) ) : '';
			if ( ! isset( $wp_sender ) || strlen( $wp_sender ) === 0 ) {
				$iserror = true;
				OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Sender Id is required.' );
			}
			$wp_label = isset( $_POST[ 'wp_label_'.$key ] ) ? trim( sanitize_text_field( wp_unslash( $_POST[ 'wp_label_' . $key ] ) ) ) : '';
			if ( ! isset( $wp_label ) || strlen( $wp_label ) === 0 ) {
				$iserror = true;
				OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Label is required.' );
			}
			$wp_template = isset( $_POST[ 'wp_template_'.$key ] ) ? trim( sanitize_text_field( wp_unslash( $_POST[ 'wp_template_' . $key ] ) ) ) : '';
			if ( ! isset( $wp_template ) || strlen( $wp_template ) === 0 ) {
				$iserror = true;
				OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Template is required.' );
			}

			if ( false === $iserror ) {
				wp_cache_delete( 'onehop_sms_rulesets' );
				$GLOBALS['wpdb']->delete($GLOBALS['wpdb']->prefix . 'onehop_sms_rulesets', array(
					'rule_name' => sanitize_text_field( $key ),
				));

				$GLOBALS['wpdb']->insert($GLOBALS['wpdb']->prefix . 'onehop_sms_rulesets', array(
					'rule_name' => sanitize_text_field( $key ),
					'template' => $wp_template,
					'label' => $wp_label,
					'senderid' => $wp_sender,
					'active' => (sanitize_text_field( $wp_activate ) === sanitize_text_field( $key )) ? '1' : '0',
				));
				$_POST['wp_status'] = 1;
			}
			break;
		}
	}
	return $returnarray;
}
