<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Page {
	private $shortcode;
	private $unique_name;

	/**
	 * @param mixed $shortcode
	 */
	public function set_shortcode( $shortcode ) {
		$this->shortcode = $shortcode;
	}

	/**
	 * @return mixed
	 */
	public function get_shortcode() {
		return $this->shortcode;
	}

	/**
	 * @param mixed $unique_name
	 */
	public function set_unique_name( $unique_name ) {
		$this->unique_name = $unique_name;
	}

	/**
	 * @return mixed
	 */
	public function get_unique_name() {
		return $this->unique_name;
	}


}