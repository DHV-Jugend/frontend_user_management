<?php
/**
 * @author  Christoph Bessei
 * @version 0.01
 */
class Options {
	public static function init_fum_option_pages() {

	}


	function register_form_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$generate_password_option_name     = Fum_Conf::$fum_register_form_generate_password_option;
		$generate_password_option_value    = get_option( $generate_password_option_name );
		$use_activation_email_option_name  = Fum_Conf::$fum_register_form_use_activation_mail_option;
		$use_activation_email_option_value = get_option( $use_activation_email_option_name );
		?>

		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Frontend User Management - Registration Form</h2>

			<form method="post" action="options.php">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Generate random user password on registration</th>
						<td>
							<input type="checkbox" name="<?php echo $generate_password_option_name; ?>" value="1"' <?php checked( $generate_password_option_value, 1 ) ?>/>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">Use activation e-mail</th>
						<td>
							<input type="checkbox" name="<?php echo $use_activation_email_option_name; ?>" value="1"' <?php checked( $use_activation_email_option_value, 1 ) ?>/>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row">Some Other Option</th>
						<td><input type="text" name="some_other_option" value="<?php echo get_option( 'some_other_option' ); ?>" />
						</td>
					</tr>

				</table>
				<?php
				settings_fields( Fum_Conf::$fum_register_form_option_group );
				do_settings_sections( Fum_Conf::$fum_register_form_option_group );
				submit_button();
				?>
			</form>
		</div>

	<?php
	}
}