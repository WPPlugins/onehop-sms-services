<?php
/**
 * Onehopsmsservice Templates Doc Comment
 *
 * @category Class
 * @package  Onehop
 * @author   Screen-Magic Mobile Media Inc.
 * @license  https://www.gnu.org/licenses/gpl-2.0.html
 * @link     http://screen-magic.com
 */

defined( 'ABSPATH' ) || exit( 'No direct script access allowed!' );

/**
 * Button click event on Template List Page. Called from main file.
 *
 * @param string $postnonce for nonce verification.
 */
function onehopsmsservice_templates_index( $postnonce ) {

	$returnarray = array();

	if ( ! wp_verify_nonce( $postnonce, 'onehop_button_submit' ) ) {
		return $returnarray;
	}

	if ( isset( $_POST['addTemplateBtn'] ) ) {
		$returnarray['action'] = 'add_template';
	} elseif ( isset( $_POST['editTemplateBtn'] ) ) {
		$returnarray['action'] = 'edit_template';
		$returnarray['value']  = sanitize_text_field( wp_unslash( $_POST['editTemplateBtn'] ) );
	}
	return $returnarray;
}

/**
 * Button click event on Add Template Page. Called from main file.
 *
 * @param string $postnonce for nonce verification.
 */
function onehopsmsservice_templates_add( $postnonce ) {
	$return = false;

	if ( ! wp_verify_nonce( $postnonce, 'onehop_button_submit' ) ) {
		OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Security Error.' );
		return $return;
	}

	if ( isset( $_POST['btnaddsubmit'] ) ) {
		$iserror                       = false;
		OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
		$wp_name = isset( $_POST['wp_name'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['wp_name'] ) ) ) : '';
		if ( ! isset( $wp_name ) || strlen( $wp_name ) === 0 ) {
			$iserror = true;
			OnehopSMSPlugin::$onehhop_reg_errors->add(
				'error',
				'Template Name is required.'
			);
		}
		$wp_body = isset( $_POST['wp_body'] ) ? trim( wp_kses_data( wp_unslash( $_POST['wp_body'] ) ) ) : '';
		if ( ! isset( $wp_body ) || strlen( $wp_body ) === 0 ) {
			$iserror = true;
			OnehopSMSPlugin::$onehhop_reg_errors->add(
				'error',
				'Template Body is required.'
			);
		}

		if ( false === $iserror ) {
			$currentdate_string = current_time( 'Y-m-d H:i:s', $gmt = 0 );
			$GLOBALS['wpdb']->insert(
				$GLOBALS['wpdb']->prefix.'onehop_sms_templates',
				array(
				'submitdate' => $currentdate_string,
				'temp_name' => $wp_name,
				'temp_body' => $wp_body,
				)
			);
			$return = true;
		}
	}
	return $return;
}

/**
 * Button click event on Edit Template Page. Called from main file.
 *
 * @param int    $val for template id.
 * @param string $postnonce for nonce verification.
 */
function onehopsmsservice_templates_edit( $val, $postnonce ) {
	$return = false;

	if ( ! wp_verify_nonce( $postnonce, 'onehop_button_submit' ) ) {
		$_POST['wp_name'] = '';
		$_POST['wp_body'] = '';
		OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
		OnehopSMSPlugin::$onehhop_reg_errors->add( 'error', 'Security Error.' );
		return $return;
	}

	$val = absint( $val );
	if ( isset( $_POST['btneditsubmit'] ) ) {
		$iserror                       = false;
		OnehopSMSPlugin::$onehhop_reg_errors = new WP_Error;
		$wp_name = isset( $_POST['wp_name'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['wp_name'] ) ) ) : '';
		if ( ! isset( $wp_name ) || strlen( $wp_name ) === 0 ) {
			$iserror = true;
			OnehopSMSPlugin::$onehhop_reg_errors->add(
				'error',
				'Template Name is required.'
			);
		}
		$wp_body = isset( $_POST['wp_body'] ) ? trim( wp_kses_data( wp_unslash( $_POST['wp_body'] ) ) ) : '';
		if ( ! isset( $wp_body ) || strlen( $wp_body ) === 0 ) {
			$iserror = true;
			OnehopSMSPlugin::$onehhop_reg_errors->add(
				'error',
				'Template Body is required.'
			);
		}

		if ( false === $iserror ) {
			wp_cache_delete( 'onehop-template-row-'.$val );
			$GLOBALS['wpdb']->update(
				$GLOBALS['wpdb']->prefix.'onehop_sms_templates',
				array(
				'temp_name' => $wp_name,
				'temp_body' => $wp_body,
				),
				array(
				'temp_id' => $val,
				)
			);
			$return = true;
		}
	} elseif ( $val > 0 ) {
		$tempate_row = wp_cache_get( 'onehop-template-row-'.$val );
		if ( false === $tempate_row ) {
			$tempate_row = $GLOBALS['wpdb']->get_row( $GLOBALS['wpdb']->prepare( 'SELECT * FROM '.$GLOBALS['wpdb']->prefix. 'onehop_sms_templates WHERE temp_id = %s', $val ) );
			wp_cache_set( 'onehop-template-row-'.$val, $tempate_row );
		}

		if ( $tempate_row ) {
			$_POST['wp_name'] = $tempate_row->temp_name;
			$_POST['wp_body'] = $tempate_row->temp_body;
		} else {
			$_POST['wp_name'] = '';
			$_POST['wp_body'] = '';
		}
	}
	return $return;
}
