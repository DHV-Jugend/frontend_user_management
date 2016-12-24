<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Option_Group {
	private $name;
	private $title;
	private $options;
	private $description;

	/**
	 * Create new option group
	 *
	 * @param $name string unique name of the option group
	 */
	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * @param mixed $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param Fum_Option[] $options
	 */
	public function set_options( array $options ) {
		$this->options = $options;
	}

	/**
	 * @return Fum_Option[]
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * @param mixed $title
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param mixed $description
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function get_description() {
		return $this->description;
	}


}
