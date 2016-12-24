<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Front_End_Form {
	public function add_form_posts() {

		Fum_Post::fum_register_post_type();
		$register_post_id       = Fum_Post::add_post( Fum_Conf::$fum_register_login_page_name, 'Login', '[' . Fum_Conf::$fum_register_login_page_name . ']' );
		$edit_post_id           = Fum_Post::add_post( Fum_Conf::$fum_edit_page_name, 'Profil editieren', '[' . Fum_Conf::$fum_edit_page_name . ']' );
		$register_event_post_id = Fum_Post::add_post( Fum_Conf::$fum_event_registration_page, 'Eventregistrierung', '[' . Fum_Conf::$fum_event_registration_page . ']' );


		return array(
			Fum_Conf::$fum_register_login_page_name => $register_post_id,
			Fum_Conf::$fum_edit_page_name           => $edit_post_id,
			Fum_Conf::$fum_event_registration_page  => $register_event_post_id,
		);
	}

	/**
	 * Buffer the content if the post is a front end form
	 * This is necessary because we may use wp_redirect()
	 */
	public function buffer_content_if_front_end_form() {


		ob_start();
	}


	public function use_ssl_on_front_end_form( $force_ssl ) {
		global $post;

		$custom_post_type = Fum_Conf::$fum_post_type;

		$post_type = get_post_type( $post );


		if ( force_ssl_login() && $post_type === $custom_post_type ) {
			return true;
		}
		return $force_ssl;
	}
}