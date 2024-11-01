<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<div class='wrap'>
	<h2>UTail settings</h2>
	<form method="POST" action="options.php">
		<?php
		settings_fields('utail-settings');
		do_settings_sections('utail-settings');
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Client ID:</th>
				<td><input type="text" name="utail_client_id" value="<?php echo esc_attr(get_option('utail_client_id')); ?>" ></td>
			</tr>
			<tr valign="top">
				<th scope="row">Secret:</th>
				<td><input type="text" name="utail_secret" value="<?php echo esc_attr(get_option('utail_secret')); ?>" ></td>
			</tr>
			<tr valign="top">
				<th scope="row">Widget Code ID:</th>
				<td><input type="text" name="utail_widget_code_id" value="<?php echo esc_attr(get_option('utail_widget_code_id')); ?>" ></td>
			</tr>
		</table>

		<h3>Appearence</h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">Social Discount background:</th>
				<td><input type="text" name="utail_social_discount_bg" value="<?php echo esc_attr(get_option('utail_social_discount_bg')); ?>" class="color-picker" />
					<p class="description">Cart &amp; Order pages Social Discount row background color.</p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
