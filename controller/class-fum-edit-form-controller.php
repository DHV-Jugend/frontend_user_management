<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Edit_Form_Controller {

	public static function create_edit_form_header() {
	}

	public static function  create_edit_form() {
		$validated = false;

		$form = NULL;
		if ( isset( $_REQUEST[Fum_Conf::$fum_unique_name_field_name] ) ) {
			$form              = Fum_Html_Form::get_form( $_REQUEST[Fum_Conf::$fum_unique_name_field_name] );
			$request_form_name = $_REQUEST[Fum_Conf::$fum_unique_name_field_name];
		}
		else {
			$request_form_name = false;
		}

		if ( $request_form_name !== false ) {
			$validated = self::validate_form( $form );
		}


		if ( is_user_logged_in() ) {
			if ( $request_form_name == Fum_Conf::$fum_edit_form_unique_name && true === $validated ) {
				echo '<p><strong>Profil wurde erfolgreich aktualisiert!</strong></p>';
			}
			else if ( $request_form_name == Fum_Conf::$fum_change_password_form_unique_name && true === $validated ) {

				echo '<p><strong>Passwort erfolgreich geändert!</strong></p>';


				/** @var WP_User $error */
				$credentials[Fum_Conf::$fum_input_field_username] = get_userdata( get_current_user_id() )->data->user_login;
				//Set new password as password for the relog
				$credentials[Fum_Conf::$fum_input_field_password] = $_REQUEST[Fum_Conf::$fum_input_field_new_password];
				//User gets automatically logged out after password change, log him in again
				$user = wp_signon( $credentials, true );

				if ( is_wp_error( $user ) ) {
					$validated = $user;
				}
				else {
					wp_set_auth_cookie( $user->ID );
				}
			}

			$edit_form = Fum_Html_Form::get_form( Fum_Conf::$fum_edit_form_unique_name );
			$edit_form = Fum_User::fill_form( $edit_form );

			$change_password_form = Fum_Html_Form::get_form( Fum_Conf::$fum_change_password_form_unique_name );

			if ( false === $validated && $form instanceof Fum_Html_Form ) {
				switch ( $form->get_unique_name() ) {
					case Fum_Conf::$fum_edit_form_unique_name:
						$edit_form = $form;
						break;
					case Fum_Conf::$fum_change_password_form_unique_name:
						$change_password_form = $form;
						break;

				}
			}
			//Change password form
			Fum_Form_View::output( $change_password_form );

			//Edit profile form
			Fum_Form_View::output( $edit_form );
		}
		else {
			echo '<p></p><strong>Du musst dich einloggen, bevor du dein Profil bearbeiten kannst</strong></p>';
			echo '<p>' . wp_loginout() . '</p>';
		}
	}

	private static function validate_form( Fum_Html_Form $form ) {
		$form->set_values_from_array( $_REQUEST );
		if ( $form->get_unique_name() == Fum_Conf::$fum_change_password_form_unique_name ) {
			$form->set_callback( array( 'Fum_Html_Form', 'validate_change_password_form' ) );
			$params = array(
				'ID'                 => get_current_user_id(),
				'password'           => Fum_Conf::$fum_input_field_password,
				'new_password'       => Fum_Conf::$fum_input_field_new_password,
				'new_password_check' => Fum_Conf::$fum_input_field_new_password_check,
			);
			$form->set_callback_param( $params );
		}
		$error = $form->validate( true );
		if ( is_wp_error( $error ) ) {
			return false;
		}
		Fum_User::observe_object( $form );
		$form->save();
		return true;
	}
}
