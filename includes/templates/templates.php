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
$onehopclass = '';
$onehopmsg = '';

$addtemplate = filter_input( INPUT_GET, 'add', FILTER_VALIDATE_INT );
$edittemplate = filter_input( INPUT_GET, 'edit', FILTER_VALIDATE_INT );

$url = get_admin_url().'admin.php?page=templates&action=templates';
if ( $addtemplate ) {
	$url .= '&add=1';
} elseif ( $edittemplate ) {
	$url .= '&edit='.$edittemplate;
}

if ( ! isset( $_POST['btnaddsubmit'] ) && ! isset( $_POST['btneditsubmit'] ) && $addtemplate ) {
	$_POST['wp_name'] = '';
	$_POST['wp_body'] = '';
}

if ( $addtemplate || $edittemplate ) {
	$message = OnehopSMSPlugin::onehop_show_error();
	if ( isset( $message ) ) {
			$onehopclass = 'error';
			$onehopmsg = $message;
	}
}
?>
<div class="<?php echo esc_html( $onehopclass ); ?>"><p><?php echo esc_html( $onehopmsg ); ?></p></div>
<?php if ( $addtemplate ) {?>
<div class="panel-heading">Add Template</div>
<div class="form-wrapper">
	<form method="post" class="sms-form" action='<?php
		echo esc_html( $url );
		?>'>
		<div class="div_fields">
			<label>Template Name : <span class="smsRequired">*</span></label>
			<input type="text" style="direction:ltr;" id="wp_name" name="wp_name" value="<?php
				echo esc_html( wp_unslash( $_POST['wp_name'] ) );
				?>" maxlength="50" /><br/>
		</div>
		<?php
			$nonce = wp_create_nonce( 'onehop_manage_template' );
		?>
		<input type="hidden" id ="wp_nonce" name="wp_nonce" value="<?php echo esc_html( $nonce ); ?>" />
		<div class="div_fields">
			<label>Template Placeholders : </label>
			<select id="wp_type" name="wp_type" style="width: 14%;min-width: 14%;">
				<option value="">-select type-</option>
				<option value="customer">customer</option>
				<option value="order">order</option>
				<option value="product">product</option>
			</select>
			<select id="wp_placeholder" name="wp_placeholder" style="width: 14%;min-width: 14%;">
				<option value="">-select placeholder-</option>
			</select>
			<input type="submit" class="btn btn-default" value="Insert" id="btninsert" name="btninsert">
			<br/>
		</div>
		<div class="div_fields">
			<label>Template Body : <span class="smsRequired">*</span></label>
			<textarea style="direction:ltr;" id="wp_body" name="wp_body" rows="7" maxlength="700"><?php
				echo esc_html( wp_unslash( $_POST['wp_body'] ) );
				?></textarea><br/>
			<span class="formdescription">You can write upto 700 characters.</span>
		</div>
		<div class="div_fields">
			<label> </label>
			<input type="submit" class="btn btn-default" value="Submit" name="btnaddsubmit">
		</div>
	</form>
</div>
<?php } elseif ( $edittemplate ) {?>
<div class="panel-heading">Update Template</div>
<div class="form-wrapper">
	<form method="post" class="sms-form" action='<?php
		echo esc_html( $url );
		?>'>
		<div class="div_fields">
			<label>Template Name : <span class="smsRequired">*</span></label>
			<input type="text" style="direction:ltr;" id="wp_name" name="wp_name" value="<?php
				echo esc_html( wp_unslash( $_POST['wp_name'] ) );
				?>" maxlength="50" /><br/>
		</div>
		<?php
			$nonce = wp_create_nonce( 'onehop_manage_template' );
		?>
		<input type="hidden" id ="wp_nonce" name="wp_nonce" value="<?php echo $nonce; ?>" />
		<div class="div_fields">
			<label>Template Placeholders : </label>
			<select id="wp_type" name="wp_type" style="width: 14%;min-width: 14%;">
				<option value="">-select type-</option>
				<option value="customer">customer</option>
				<option value="order">order</option>
				<option value="product">product</option>
			</select>
			<select id="wp_placeholder" name="wp_placeholder" style="width: 14%;min-width: 14%;">
				<option value="">-select placeholder-</option>
			</select>
			<input type="submit" class="btn btn-default" value="Insert" id="btninsert" name="btninsert">
			<br/>
		</div>
		<div class="div_fields">
			<label>Template Body : <span class="smsRequired">*</span></label>
			<textarea style="direction:ltr;" id="wp_body" name="wp_body" rows="7" maxlength="700"><?php
				echo esc_html( wp_unslash( $_POST['wp_body'] ) );
				?></textarea><br/>
			<span class="formdescription">You can write upto 700 characters.</span>
		</div>
		<div class="div_fields">
			<label> </label>
			<input type="submit" class="btn btn-default" value="Submit" name="btneditsubmit">
		</div>
	</form>
</div>
<?php } else { ?>
<form action='<?php
	echo esc_html( $url );
	?>' method="post">
	<div class="panel-heading"><i></i>List of Templates</div>
	<div class="form-wrapper">
		<button name="addTemplateBtn" class="btn btn-default">
		<span style="font-size: 16px;padding-right: 5px; bold;">+</span>Add Template</button>
		<table width="100%" border="0" class="templatesList">
			<tr>
				<th>No.</th>
				<th>Template Name</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
			<?php
			if ( $tempate_list ) {
				foreach ( $tempate_list as $index => $template ) {
				?>
			<tr>
				<?php
					$id = $template->temp_id;
					?>
				<td><?php
					echo esc_html( $index + 1 );
					?></td>
				<td><?php
					echo esc_html( $template->temp_name );
					?></td>
				<td><button name="editTemplateBtn" value="<?php
					echo esc_html( $id );
					?>" class="smstemplatebtn"><i class="icon-edit" style="font-size: 20px;"></i>edit</button></td>
				<?php
					$nonce = wp_create_nonce( 'onehop_template-delete-' . $id );
					?>
				<td><a href="javascript:;" data-id="<?php
					echo esc_html( $id );
					?>" data-nonce="<?php
					echo esc_html( $nonce );
					?>" class="delete_template"><i class="icon-trash smsediticon"></i>delete</a></td>
			</tr>
			<?php
				}
			} else {
				?>
			<tr>
				<td colspan="3" align="center"> <b>No template available.</b></td>
			</tr>
			<?php
			}
				?>
		</table>
	</div>
</form>
<?php } ?>
