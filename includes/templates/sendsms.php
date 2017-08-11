<?php
/**
 * Onehopsmsservice SendSMS Doc Comment
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
	$_POST['wp_mobile'] = '';
	$_POST['wp_sender'] = '';
	$_POST['wp_label'] = '';
	$_POST['wp_template'] = '';
	$_POST['wp_body'] = '';
} else {
	if ( 1 === $_POST['wp_status'] ) {
		$onehopclass = 'updated';
	    $onehopmsg = 'SMS sent successfully.';
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
<div class="panel-heading">Send Single SMS</div>
<div class="form-wrapper">
	<form method="post" class="sms-form" action='<?php echo esc_url( get_admin_url().'admin.php?page=sendsms&action=sendsms' ); ?>'>
		<input type="hidden" name="wp_status" value="<?php echo esc_html( wp_unslash( $_POST['wp_status'] ) ); ?>" />
		<div class="div_fields">
			<label>Mobile Number : <span class="smsRequired">*</span></label>
			<input type="text" style="direction:ltr;" id="wp_mobile" name="wp_mobile" 
			value="<?php echo esc_html( wp_unslash( $_POST['wp_mobile'] ) ); ?>" maxlength="10" /><br/>
		</div>
		<div class="div_fields">
			<label>Sender Id : <span class="smsRequired">*</span></label>
			<input type="text" style="direction:ltr;" id="wp_sender" name="wp_sender" 
			value="<?php echo esc_html( wp_unslash( $_POST['wp_sender'] ) ); ?>" maxlength="50" /><br/>
		</div>
		<div class="div_fields">
			<label>Select Label : <span class="smsRequired">*</span></label>
			<select id="wp_label" name="wp_label">
				<option value="">-select label-</option>
				<?php if ( $label_list ) {
					foreach ( $label_list as $lbl ) {
						?>
				<option value="<?php echo $lbl;
					?>" <?php echo ($_POST['wp_label'] === $lbl) ? 'selected' : '';
					?>><?php echo $lbl;
					?></option>
				<?php
					}
} ?>
			</select>
		</div>
		<div class="div_fields">
			<label>Select Template :</label>
			<select id="wp_template" name="wp_template">
				<option value="">-select template-</option>
				<?php if ( $tempate_list ) {
					foreach ( $tempate_list as $index => $template ) {
						?>
				<option value="<?php echo $template->temp_id;
					?>" <?php echo ($_POST['wp_template'] === $template->temp_id) ? 'selected' : '';
					?>><?php echo $template->temp_name;
					?></option>
				<?php
					}
} ?>
			</select>
		</div>
		<?php
			$nonce = wp_create_nonce( 'onehop_fill_body' );
		?>
		<input type="hidden" id ="wp_nonce" name="wp_nonce" value="<?php echo $nonce; ?>" />
		<div class="div_fields">
			<label>Template Body : <span class="smsRequired">*</span></label>
			<textarea id ="wp_body" name="wp_body" rows="7" maxlength="700"><?php
				echo esc_html( wp_unslash( $_POST['wp_body'] ) );
				?></textarea><br/>
			<span class="formdescription">You can write upto 700 characters.</span>
		</div>
		<div class="div_fields">
			<label>&nbsp;</label>
			<input type="submit" class="btn btn-default" value="Submit" name="btnsubmit">
		</div>
	</form>
</div>

