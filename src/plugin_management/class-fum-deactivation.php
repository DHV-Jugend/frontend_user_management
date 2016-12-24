<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Deactivation {

	public static function deactivate_plugin() {
		error_log( 'fum_deactivate_plugin' );


		Fum_Post::remove_all_fum_posts();
		delete_option( Fum_Conf::$fum_register_login_page_name );
		delete_option( Fum_Conf::$fum_edit_page_name );
		delete_option( Fum_Conf::$fum_event_registration_page );

		delete_option( Fum_Html_Form::get_option_name_forms() );
		$activation_email = new Fum_Activation_Email();
		$activation_email->plugin_deactivated();
	}

} 