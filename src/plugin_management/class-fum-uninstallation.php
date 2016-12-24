<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Uninstallation {
	public static function uninstall_plugin() {
		delete_option( Fum_Conf::$fum_register_form_use_activation_mail_option );


		delete_option( Fum_Conf::$fum_register_form_generate_password_option );


		delete_option( Fum_Conf::$fum_general_option_group_hide_wp_login_php );

	}
} 