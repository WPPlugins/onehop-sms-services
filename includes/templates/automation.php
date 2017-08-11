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
$onehopclass = '';
$onehopmsg = '';
$_POST['wp_status'] = isset( $_POST['wp_status'] ) ? absint( $_POST['wp_status'] ) : 0;
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
?>
<div class="<?php echo esc_html( $onehopclass ); ?>"><p><?php echo esc_html( $onehopmsg ); ?></p></div>
<div class="form-wrapper">
	<form method="post" class="sms-form" action='<?php
	echo esc_url( get_admin_url().'admin.php?page=automation&action=automation' );
	?>'>
		<input type="hidden" name="wp_status" value="<?php echo esc_html( wp_unslash( $_POST['wp_status'] ) ); ?>" />
		<?php foreach ( $mainarray as $key => $array ) {
			if ( ! isset( $_POST[ 'btnsubmit_'.$key ] ) ) {
				$_POST[ 'wp_activate_'.$key ] = array_key_exists( 'activate', $array ) ? $array['activate'] : '';
				$_POST[ 'wp_label_'.$key ] = array_key_exists( 'label', $array ) ? $array['label'] : '';
				$_POST[ 'wp_template_'.$key ] = array_key_exists( 'template', $array ) ? $array['template'] : '';
				$_POST[ 'wp_sender_'.$key ] = array_key_exists( 'sender', $array ) ? $array['sender'] : '';
			}
			?>
		<div class="panel-heading"><?php echo esc_html( $array['title'] ); ?></div>
		<div class="div_fields">
			<strong><?php echo esc_html( $array['desc'] ); ?><br/></strong>
		</div>
		<div class="div_fields">
			<label>Activate Feature : </label>
			<?php
				$name1 = 'wp_activate_'.$key;
				$value1 = array_key_exists( $name1, $_POST ) ? wp_unslash( $_POST[ $name1 ] ) : '';
				$checked = ($value1 === $key) ? 'checked' : '';
				?>
			<input type="checkbox" name="<?php echo $name1 ?>" value="<?php echo $key ?>"  <?php echo $checked;
				?> /><br/>
		</div>
		<div class="div_fields">
			<label>Sender Id : <span class="smsRequired">*</span></label>
			<?php $name1 = 'wp_sender_'.$key;
				?>
			<input type="text" style="direction:ltr;width: 230px;" id="<?php echo $name1 ?>" 
			name="<?php echo $name1 ?>" value="<?php echo esc_html( wp_unslash( $_POST[ $name1 ] ) );
				?>" maxlength="50" /><br/>
		</div>
		<div class="div_fields">
			<label>Select Label : <span class="smsRequired">*</span></label>
			<?php $name1 = 'wp_label_'.$key;
				?>
			<select id="<?php echo $name1 ?>" name="<?php echo $name1 ?>">
				<option value="">-select label-</option>
				<?php if ( $label_list ) {
					foreach ( $label_list as $lbl ) {
						?>
				<option value="<?php echo $lbl;
					?>" <?php echo ($_POST[ $name1 ] === $lbl) ? 'selected' : '';
					?>><?php echo $lbl;
					?></option>
				<?php
					}
}
					?>
			</select>
		</div>
		<div class="div_fields">
			<label>Select Template : <span class="smsRequired">*</span></label>
			<?php $name1 = 'wp_template_'.$key;
				?>
			<select id="<?php echo $name1 ?>" name="<?php echo $name1 ?>">
				<option value="">-select template-</option>
				<?php if ( $tempate_list ) {
					foreach ( $tempate_list as $index => $template ) {
						?>
				<option value="<?php echo $template->temp_id;
					?>" <?php echo ($_POST[ $name1 ] === $template->temp_id) ? 'selected' : '';
					?>><?php echo $template->temp_name;
					?></option>
				<?php
					}
}
					?>
			</select>
		</div>
		<div class="div_fields">
			<label>&nbsp;</label>
			<input type="submit" class="btn btn-default" value="Submit" name="<?php echo 'btnsubmit_'.$key ?>">
		</div>
		<?php
} ?>
	</form>
</div>
