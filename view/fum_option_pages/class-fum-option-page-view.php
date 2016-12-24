<?php
/**
 * @author  Christoph Bessei
 * @version 0.01
 */
class Fum_Option_Page_View {
	public static function print_option_page() {

		if ( $_GET['page'] === Fum_Option_Page_Controller::$parent_slug ) {
			$option_page = Fum_Option_Page_Controller::$pages[0];
		}
		else {
			foreach ( Fum_Option_Page_Controller::$pages as $page ) {
				if ( $_GET['page'] === $page->get_name() ) {
					$option_page = $page;
					break;
				}
			}
		}


		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Frontend User Management - <?php _e( $option_page->get_title(), 'fum_text_domain' ); ?></h2>
			<?php foreach ( $option_page->get_option_groups() as $option_group ): ?>
				<?php /*var @$option_group Fum_Option_Group*/ ?>
				<form method="post" action="options.php">
					<table class="form-table">
						<?php foreach ( $option_group->get_options() as $option ): ?>
							<?php /*var @option Fum_Option*/ ?>
							<tr valign="top">
								<th scope="row" title="<?php _e( $option->get_title(), 'fum_text_domain' ); ?>"><?php _e( $option->get_title(), 'fum_text_domain' ); ?></th>
								<td>
									<?php
									switch ( $option->get_type() ) {
										case 'text':
											?>
											<input type="text" name="<?php echo $option->get_name(); ?>" value="<?php echo $option->get_value(); ?>" />
											<?php

											break;
										case 'password':

											?>

											<input type="password" name="<?php echo $option->get_name(); ?>" value="<?php echo $option->get_value(); ?>" />
											<?php
											break;
										case 'checkbox':
											?>
											<input type="checkbox" name="<?php echo( $option->get_name() ); ?>" value="1" <?php checked( $option->get_value(), 1 ); ?>/>
											<?php
											break;
										case 'radio':
											foreach ( $option->get_possible_values() as $value ): ?>
												<input type="radio" name="<?php echo( $option->get_name() ); ?>" value="<?php echo $value; ?>" <?php checked( $option->get_value(), $value ); ?>/> <?php echo $value; ?>
												<br />
											<?php
											endforeach;
											break;
										case 'select':
											?>
											<select name="<?php echo $option->get_name(); ?>" <?php echo ( $option->get_multiple_selection() ) ? 'multiple="multiple"' : '' ?>>
												<?php foreach ( $option->get_possible_values() as $value ): ?>
													<option value="<?php echo $value ?>" <?php selected( $option->get_value(), $value ); ?>><?php echo $value ?></option>
												<?php endforeach; ?>
											</select>
											<?php
											break;

										case 'textarea':
											?>
											<textarea name="<?php echo( $option->get_name() ); ?>"><?php echo( $option->get_value() ); ?></textarea>
											<?php
											break;
									}
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php
					settings_fields( $option_group->get_name() );
					do_settings_sections( $option_group->get_name() );
					submit_button();
					?>
				</form>
			<?php endforeach; ?>
		</div>
	<?php
	}
}
