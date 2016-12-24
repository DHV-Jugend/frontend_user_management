<?php

/**
 * @author  Christoph Bessei
 * @version 0.01
 */
class Fum_Option_Page_View implements Fum_Observer {

	private static $instance = NULL;


	public static function get_instance() {
		if ( NULL === self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
	}

	private function __clone() {
	}

	public function update( Fum_Observable $o ) {
		if ( $o instanceof Fum_Option_Page ) {
			self::print_option_page( $o );
		}
		else {
			throw new Exception( 'Cannot print object of class ' . get_class( $o ) );
		}
	}


	private static function print_option_page( Fum_Option_Page $option_page ) {
		?>
		<div class="wrap">
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
									echo $option->get_pre_option_html();
									switch ( $option->get_type() ) {
										case 'text':
											?>
											<input type="text" id="<?php echo $option->get_name(); ?>" name="<?php echo $option->get_name(); ?>" value="<?php echo $option->get_value(); ?>" class="<?php echo $option->get_class(); ?>" />
											<?php

											break;
										case 'password':

											?>

											<input type="password" id="<?php echo $option->get_name(); ?>" name="<?php echo $option->get_name(); ?>" value="<?php echo $option->get_value(); ?>" />
											<?php
											break;
										case 'checkbox':
											?>
											<input type="checkbox" id="<?php echo $option->get_name(); ?>" name="<?php echo( $option->get_name() ); ?>" value="1" <?php checked( $option->get_value(), 1 ); ?>/>
											<?php
											break;
										case 'radio':
											foreach ( $option->get_possible_values() as $value ): ?>
												<input type="radio" id="<?php echo $option->get_name(); ?>" name="<?php echo( $option->get_name() ); ?>" value="<?php echo $value; ?>" <?php checked( $option->get_value(), $value ); ?>/> <?php echo $value; ?>
												<br />
											<?php
											endforeach;
											break;
										case 'select':
											?>
											<select id="<?php echo $option->get_name(); ?>" name="<?php echo $option->get_name(); ?>" <?php echo ( $option->get_multiple_selection() ) ? 'multiple="multiple"' : '' ?>>
												<?php foreach ( $option->get_possible_values() as $value ): ?>
													<option value="<?php echo $value ?>" <?php selected( $option->get_value(), $value ); ?>><?php echo $value ?></option>
												<?php endforeach; ?>
											</select>
											<?php
											break;

										case 'textarea':
											?>
											<textarea id="<?php echo $option->get_name(); ?>" name="<?php echo( $option->get_name() ); ?>"><?php echo( $option->get_value() ); ?></textarea>
											<?php
											break;
									}
									echo $option->get_post_option_html();
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
