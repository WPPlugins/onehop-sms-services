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
$onehopclass = '';
$onehopmsg = '';
$_POST['wp_status'] = isset( $_POST['wp_status'] ) ? absint( $_POST['wp_status'] ) : 0;
if ( ! isset( $_POST['btnsubmit'] ) ) {
	$_POST['wp_api'] = get_option( 'ONEHOP_SEND_SMS_API' );
	$_POST['wp_admin_mobile'] = get_option( 'ONEHOP_ADMIN_MOBILE' );
} else {
	if ( 1 === $_POST['wp_status'] ) {
	    $onehopclass = 'updated';
	    $onehopmsg = 'Your data has been saved successfully.';
	} else {
		$message = OnehopSMSPlugin::onehop_show_error();
		if ( isset( $message ) ) {
			$onehopclass = 'error';
			$onehopmsg = $message;
		}
	}
}
?>
<div class="<?php echo esc_html( $onehopclass ); ?>"><p><?php echo esc_html( $onehopmsg ); ?></p></div>
<div class="panel-heading">API Settings</div>
<div class="form-wrapper">
	<form method="post" class="sms-form" action='<?php echo esc_url( get_admin_url().'admin.php?page=config&action=config' ); ?>'>
		<input type="hidden" name="wp_status" value="<?php echo esc_html( wp_unslash( $_POST['wp_status'] ) ); ?>" />
		<div class="div_fields">
			<label>API Key : <span class="smsRequired">*</span></label>
			<input type="text" style="direction:ltr;" id="wp_api" name="wp_api" 
			value="<?php echo esc_html( wp_unslash( $_POST['wp_api'] ) ); ?>" maxlength="50" /><br/>
			<span class="formdescription">This API Key is used to authenticate in order to send SMS.</span>
		</div>
		<div class="div_fields">
			<label>Admin Mobile Number : <span class="smsRequired">*</span></label>
			<input type="text" style="direction:ltr;" id="wp_admin_mobile" name="wp_admin_mobile" 
			value="<?php echo esc_html( wp_unslash( $_POST['wp_admin_mobile'] ) ); ?>" maxlength="10" />
		</div>
		<div class="div_fields">
			<label>&nbsp;</label>
			<input type="submit" class="btn btn-default" value="Submit" name="btnsubmit">
		</div>
	</form>
</div>
