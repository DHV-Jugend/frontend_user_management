<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Option {
	private $name;
	private $title;
	private $description;
	private $value;
	private $option_group;
	private $type; /*Possible types are the html input field types (text,password, checkbox , select etc.) */
	private $possible_values; /*Used for select types, $value is the current value and $possible_values are the possible values which are shown in the dropdown */
	private $multiple_selection;


	public function __construct( $name, $title, $description, $value, Fum_Option_Group $option_group, $type, $possible_values = NULL, $multiple_selection = false ) {
		$this->name               = $name;
		$this->title              = $title;
		$this->description        = $description;
		$this->value              = $value;
		$this->option_group       = $option_group;
		$this->type               = $type;
		$this->possible_values    = $possible_values;
		$this->multiple_selection = $multiple_selection;
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
	 * @param mixed $option_group
	 */
	public function set_option_group( $option_group ) {
		$this->option_group = $option_group;
	}

	/**
	 * @return mixed
	 */
	public function get_option_group() {
		return $this->option_group;
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


	/**
	 * @param mixed $type
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @param mixed $possible_values
	 */
	public function set_possible_values( $possible_values ) {
		$this->possible_values = $possible_values;
	}

	/**
	 * @return mixed[]
	 */
	public function get_possible_values() {
		return $this->possible_values;
	}

	/**
	 * @param mixed $value
	 */
	public function set_value( $value ) {
		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @param boolean $multiple_selection
	 */
	public function set_multiple_selection( $multiple_selection ) {
		$this->multiple_selection = $multiple_selection;
	}

	/**
	 * @return boolean
	 */
	public function get_multiple_selection() {
		return $this->multiple_selection;
	}


}