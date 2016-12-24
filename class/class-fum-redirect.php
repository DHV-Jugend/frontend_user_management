<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Redirect {

	public static function redirect_wp_logout( $url ) {
		return add_query_arg( array( 'redirect_to' => home_url() ), $url );
	}

	public static function redirect_wp_login_php() {

		if ( isset( $_GET['action'] ) ) {
			$action = $_GET['action'];
		}
		else {
			$action = '';
		}
		$link = add_query_arg( array_merge( $_GET, array( 'action' => $action ) ), get_permalink( get_option( Fum_Conf::$fum_register_login_page_name ) ) );
		wp_safe_redirect( $link );
		exit();
	}

	public static function redirect_own_profile_edit() {
		wp_safe_redirect( get_permalink( get_option( Fum_Conf::$fum_edit_page_name ) ) );
		exit();


	}

	public static function redirect_to_home_if_user_cannot_manage_options() {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_event' ) && $_SERVER['PHP_SELF'] != '/wp-admin/profile.php' ) {
			wp_safe_redirect( home_url() );
		}
	}
} 