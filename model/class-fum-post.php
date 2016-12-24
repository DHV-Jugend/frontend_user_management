<?php
/**
 * @author  Christoph Bessei
 * @version 0.01
 */

class Fum_Post {

	public static function fum_register_post_type() {
		if ( ! post_type_exists( Fum_Conf::$fum_post_type ) ) {
			$args = array(
				'public'              => true,
				'label'               => Fum_Conf::$fum_post_type_label,
				'exclude_from_search' => true,
				'show_ui'             => false,
				'show_in_nav_menus'   => false,
				'rewrite'             => array( 'slug' => '', 'with_front' => false ),
			);
			register_post_type( Fum_Conf::$fum_post_type, $args );
		}
	}

	public static function add_post( $name, $title, $content ) {
		$fum_post = array(
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_author'    => get_current_user_id(),
			'post_status'    => 'publish',
			'post_type'      => Fum_Conf::$fum_post_type,
			'post_content'   => $content,
			'post_name'      => $name,
			'post_title'     => $title,
		);

		$post_id = wp_insert_post( $fum_post );
		return $post_id;
	}

	public static function remove_post_by_id( $post_id ) {
		wp_delete_post( $post_id );
	}

	public static function remove_all_fum_posts() {
		$posts = get_posts( array( 'post_type' => Fum_Conf::$fum_post_type ) );
		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID );
		}
	}
}