<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Dashboard {

	private $hidden_cat_id = NULL;
	private $hide_cat = NULL;


	/**
	 * Hides all posts (not pages!) which are in $cat_id from dashboard
	 * If $hide_cat=true it also hides the category itself from the dashboard
	 *
	 * @param $cat_id   ID of the category which should be hidden
	 * @param $hide_cat (optional) boolean, hide also the category (true) or not (false)
	 */
	public function hide_posts_of_category_from_dashboard( $cat_id = NULL, $hide_cat = true ) {

		//First call, add action to function and define $this->hidden_cat_id and $this->hide_cat
		if ( NULL === $this->hidden_cat_id ) {
			$this->hidden_cat_id = $cat_id;
			$this->hide_cat      = $hide_cat;
			add_action( 'pre_get_posts', array( $this, 'hide_posts_of_category_from_dashboard' ), 10, 0 );
			if ( true === $this->hide_cat ) {
				add_filter( 'list_terms_exclusions', array( $this, 'hide_posts_of_category_from_dashboard' ), 10, 1 );
			}
		}
		//Second call, hopefully from do_action now
		else {
			if ( NULL === $cat_id ) {
				$this->remove_posts_of_category_from_query( $this->hidden_cat_id );
			}
			else {
				$exclusions = $cat_id;
				//list_terms_exclusions expects exclusions as return value
				return $this->remove_category_from_dashboard( $exclusions );
			}
		}
	}

	public function hide_dashboard_from_non_admin() {
		add_action( 'admin_menu', array( $this, 'hide_page_from_non_admin' ) );
	}


	public function hide_page_from_non_admin() {
		if ( is_super_admin() ) {
			return;
		}
		else {
			wp_redirect( get_home_url() );
		}
	}

	private function remove_category_from_dashboard( $exclusions ) {
		return $exclusions . " AND ( t.term_id <> " . $this->hidden_cat_id . " )";
	}

	/**
	 * Excludes category from get_posts query
	 *
	 * @param $cat_id array|int An array of category ids or a single category id
	 */
	private function remove_posts_of_category_from_query() {
		set_query_var( 'cat', '-' . $this->hidden_cat_id );
	}
}


