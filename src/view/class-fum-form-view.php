<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Form_View {

	public static function output( Fum_Html_Form $form ) {
		echo self::get_html_of_form( $form );
	}


	private static function get_html_of_form( Fum_Html_Form $form, $table = false, $th = true, $return = true ) {
		if ( $return ) {
			ob_start();
		}
		if ( $form->is_validated() && is_wp_error( $form->get_validation_result() ) ):
			foreach ( $form->get_validation_result()->get_error_messages() as $message ):
				?>
				<p><strong><?php _e( $message ); ?></strong></p>
			<?php
			endforeach;
		endif;
		?>
		<form <?php echo $form->get_extra_params(); ?> id="<?php echo $form->get_id(); ?>" name="<?php echo $form->get_name(); ?>" class="<?php echo $form->get_classes(); ?>" action="<?php echo $form->get_action(); ?>" method="<?php echo $form->get_method(); ?>">
			<?php if ($table): ?>
			<table class="form-table">
				<?php endif;

				foreach ( $form->get_input_fields() as $input_field ) {
					//$return is always false, because we started already an output buffer
					self::get_html_of_input_field( $input_field, $table, $th, false );
				}

				if ($table): ?>
			</table>
		<?php endif; //TODO Not sure if this should happen in the view, maybe more a controller thing? ?>
			<input type="hidden" name="<?php echo Fum_Conf::$fum_unique_name_field_name ?>" value="<?php echo $form->get_unique_name(); ?>">
		</form>
		<?php

		if ( $return ) {
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	private static function get_html_of_input_field( Fum_Html_Input_Field $input_field, $table = false, $th = true, $return = true ) {
		if ( $return ) {
			ob_start();
		}

		//If type is submit, we don't need a label or description
		if ( $input_field->get_type() == Html_Input_Type_Enum::HIDDEN ) {
			?>
			<input type="<?php echo $input_field->get_type(); ?>" value="<?php echo $input_field->get_value(); ?>" size="<?php echo $input_field->get_size(); ?>" id="<?php echo $input_field->get_id(); ?>" class="<?php echo $input_field->get_classes(); ?>" name="<?php echo $input_field->get_name(); ?>" />
		<?php
		}
		else if ( $input_field->get_type() == Html_Input_Type_Enum::SUBMIT ) {
			if ( $input_field->get_value() != '' ):
				?>
				<p>
					<input type="<?php echo $input_field->get_type(); ?>" value="<?php echo $input_field->get_value(); ?>" size="<?php echo $input_field->get_size(); ?>" id="<?php echo $input_field->get_id(); ?>" class="<?php echo $input_field->get_classes(); ?>" name="<?php echo $input_field->get_name(); ?>" />
				</p>
			<?php else: ?>
				<p>
					<input type="<?php echo $input_field->get_type(); ?>" value="<?php echo $input_field->get_title(); ?>" size="<?php echo $input_field->get_size(); ?>" id="<?php echo $input_field->get_id(); ?>" class="<?php echo $input_field->get_classes(); ?>" name="<?php echo $input_field->get_name(); ?>" />
				</p>
			<?php
			endif;


		}
		else {


			//title/label is independent of input type, so create it before distinction of cases
			if ( $table ): //Use table to format form
				?>
				<tr id="<?php echo $input_field->get_type(); ?>">
				<?php if ( $th ): ?>
				<th>
			<?php else: ?>
				<td>
			<?php endif; ?>
				<label for="<?php echo $input_field->get_id(); ?>"> <?php _e( $input_field->get_title() );
					if ( $input_field->get_required() ) {
						echo '*';
					} ?></label>
				<?php if ( $th ): ?>
				</th>
			<?php else: ?>
				</td>
			<?php
			endif; ?>
				<td>
			<?php else: //Use label only to format form?>
				<p><label for="<?php echo $input_field->get_id(); ?>"> <?php _e( $input_field->get_title() );
				if ( $input_field->get_required() ) {
					echo '*';
				} ?><br />
			<?php endif;

			switch ( $input_field->get_type() ) {

				case Html_Input_Type_Enum::TEXT:
				case Html_Input_Type_Enum::PASSWORD:
					?>
					<input type="<?php echo $input_field->get_type(); ?>" value="<?php echo $input_field->get_value(); ?>" size="<?php echo $input_field->get_size(); ?>" id="<?php echo $input_field->get_id(); ?>" name="<?php echo $input_field->get_name(); ?>" />
					<?php
					break;
				case Html_Input_Type_Enum::RADIO:
					foreach ( $input_field->get_possible_values() as $possible_value ): ?>
						<input type="radio" name="<?php echo( $input_field->get_name() ); ?>" value="<?php echo $possible_value['value']; ?>" <?php checked( $input_field->get_value(), $possible_value['value'] ); ?>/> <?php echo $possible_value['title']; ?>
						<br />
					<?php
					endforeach;
					?>
					<?php
					break;
				case Html_Input_Type_Enum::CHECKBOX:
					?>
					<input type="checkbox" name="<?php echo( $input_field->get_name() ); ?>" value="1" id="<?php echo $input_field->get_id(); ?>" class="<?php echo $input_field->get_classes(); ?>" <?php checked( $input_field->get_value(), 1 ); ?>/>
					<?php
					break;
				case Html_Input_Type_Enum::SELECT:
					?>
					<select <?php echo $input_field->get_extra_params(); ?> name="<?php echo $input_field->get_name(); ?>" <?php echo( $input_field->get_readonly() ? 'readonly="readonly"' : '' ); ?>>
						<?php foreach ( $input_field->get_possible_values() as $possible_value ): ?>
							<option value="<?php echo $possible_value['value'] ?>" <?php selected( $input_field->get_value(), $possible_value['value'] ); ?>><?php echo $possible_value['title'] ?></option>
						<?php endforeach; ?>
					</select>
					<?php
					break;
				case Html_Input_Type_Enum::TEXTAREA:
					?>
					<textarea id="<?php echo( $input_field->get_id() ); ?>" name="<?php echo( $input_field->get_name() ); ?>"><?php echo( $input_field->get_value() ); ?></textarea>
					<?php
					break;
			}
			if ( $input_field->is_validated() && is_wp_error( $input_field->get_validation_result() ) ):
				foreach ( $input_field->get_validation_result()->get_error_messages() as $message ):
					?>
					<br />
					<strong><?php _e( $message ); ?></strong>
				<?php
				endforeach;
			endif;
			if ( $table ): ?>
				</td>
				</tr>
			<?php else: ?>
				</label>
				</p>
			<?php endif;
			if ( $input_field->get_do_action() != NULL ) {
				do_action( $input_field->get_do_action() );
			}
		}
		if ( $return ) {
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

} 