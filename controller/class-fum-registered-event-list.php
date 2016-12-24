<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Registered_Event_list {
	public static function create_applied_event_form() {
		if ( isset( $_REQUEST[Fum_Conf::$fum_unique_name_field_name] ) ) {
			$user_id       = get_current_user_id();
			$registrations = Ems_Event_Registration::get_registrations_of_user( $user_id );
			foreach ( $registrations as $registration ) {
				$event_id = $registration->get_event_post_id();
				if ( isset( $_REQUEST['event_' . $event_id] ) ) {
					Ems_Event_Registration::delete_event_registration( $registration );
					$event_name = get_post( $event_id )->post_title;
					echo '<p><strong>Abgemeldet von: ' . $event_name . '</strong></p>';
				}
			}
		}
		$form          = Fum_Html_Form::get_form( Fum_Conf::$fum_user_applied_event_form_unique_name );
		$registrations = Ems_Event_Registration::get_registrations_of_user( get_current_user_id() );
		if ( empty( $registrations ) ) {
			echo '<p><strong>Du bist für keine Events angemeldet.</strong></p>';
		}
		else {
			echo '<p><strong>Angemeldete Events</strong></p>';

			$type_checkbox = new Html_Input_Type_Enum( Html_Input_Type_Enum::CHECKBOX );
			foreach ( $registrations as $registration ) {
				$id = $registration->get_event_post_id();

				$name = get_post( $id )->post_title;

				//TODO Prepend underscore on the id, because input field name seems not to work with numeric values
				$input_field = new Fum_Html_Input_Field( $name, 'event_' . $id, $type_checkbox, $name, $id, false );
				$form->insert_input_field_before_unique_name( $input_field, Fum_Conf::$fum_input_field_submit );
			}
			$form->get_input_field( Fum_Conf::$fum_input_field_submit )->set_value( 'Abmelden' );
			Fum_Form_View::output( $form );
		}

	}
} 