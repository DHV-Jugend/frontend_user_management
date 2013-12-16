<?php
/**
 * @author Christoph Bessei
 * @version
 */

class Fum_Html_Input_Field extends Fum_Observable implements Fum_Observer {
	private static $option_name_input_fields = 'fum_input_fields';

	private $unique_name;
	private $name;
	private $title;
	private $type;
	private $id;
	private $classes;
	private $size;
	private $value;
	/**
	 * @var array $possible_values
	 * $possible_values contains an array with title and value as index
	 * title is the printed text and value is the input field value
	 */
	private $possible_values;
	private $do_action;
	private $required;
	private $validate_callback = NULL;
	private $validate_params = array();
	/** @var bool|WP_Error $validation_result false if validate() wasn't called, WP_Error if error occured, true if validate was fine */
	private $validation_result = false;

	public function __construct( $unique_name, $name, Html_Input_Type_Enum $type, $title, $id, $required ) {
		$this->unique_name = $unique_name;
		$this->id          = $id;
		$this->name        = $name;
		$this->title       = $title;
		$this->type        = $type;
		$this->required    = $required;

	}

	/**
	 * Adds an Fum_Html_Input_Field to the database
	 *
	 * @param Fum_Html_Input_Field $input_field Fum_Html_Input_Field which should be stored in the database
	 *
	 * @return bool  return value of update_option(), true if add was successful, otherwise false
	 * @throws Exception The exception is thrown if $unique_name is already used for another input field
	 */
	public static function add_input_field( Fum_Html_Input_Field $input_field ) {
		$input_fields = self::get_input_fields();
		foreach ( $input_fields as $cur_input_field ) {
			if ( $cur_input_field->get_unique_name() === $input_field->get_unique_name() ) {
				throw new Exception( '$unique_name is already used' );
			}
		}
		$input_fields[] = $input_field;
		return self::update_input_fields( $input_fields );
	}


	/**
	 * Stores new input fields in the database, OVERWRITES! the previously stored input fields
	 *
	 * @param Fum_Html_Input_Field[] $input_fields the input fields
	 *
	 * @return bool return value of update_option(), true if add was successful, otherwise false
	 */
	public static function set_input_fields( array $input_fields ) {
		return self::update_input_fields( $input_fields );
	}

	/**
	 * Get Fum_Html_Input_Field by object or $unique_name
	 *
	 * @param $input_field Fum_Html_Input_Field|string  Fum_Html_Input_Field object of the input field or $unique_name
	 *
	 * @return Fum_Html_Input_Field|bool returns the searched Fum_Html_Input_Field or false if it's not found
	 */
	public static function get_input_field( $input_field ) {
		$unique_name = $input_field;
		if ( $input_field instanceof Fum_Html_Input_Field ) {
			$unique_name = $input_field->get_unique_name();
		}
		$input_fields = self::get_input_fields();
		foreach ( $input_fields as $input_field ) {
			if ( $input_field->get_unique_name() === $unique_name ) {
				return $input_field;
			}
		}
		return false;
	}

	/**
	 * Get all stored Fum_Html_Input_Field from the database
	 * @return Fum_Html_Input_Field[]
	 */
	public static function get_all_input_fields() {
		return self::get_input_fields();
	}

	/**
	 * Deletes a Fum_Html_Input_Field from the database
	 *
	 * @param $input_field Fum_Html_Input_Field|string The Fum_Html_Input_Field object or the unique name
	 *
	 * @return bool true if delete was successful, otherwise false
	 */
	public static function delete_input_field( $input_field ) {
		$unique_name = $input_field;
		if ( $input_field instanceof Fum_Html_Input_Field ) {
			$unique_name = $input_field->get_unique_name();
		}
		$input_fields = self::get_input_fields();
		foreach ( $input_fields as $key => $input_field ) {
			if ( $input_field->get_unique_name() === $unique_name ) {
				unset( $input_fields[$key] );
				return self::update_input_fields( $input_fields );
			}
		}
		return false;
	}

	/**
	 * Checks if an $unique_name is already used for another input field
	 *
	 * @param $unique_name string
	 *
	 * @return bool returns true if $unique_name is already used, false if not
	 */
	public static function is_unique_name_used( $unique_name ) {
		$input_fields = self::get_input_fields();
		foreach ( $input_fields as $input_field ) {
			if ( $input_field->get_unique_name() === $unique_name ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * @return string
	 */
	public static function get_option_name_input_fields() {
		return self::$option_name_input_fields;
	}

	/**
	 * @return Fum_Html_Input_Field[]
	 */
	private static function get_input_fields() {
		$return = get_option( self::get_option_name_input_fields() );
		if ( is_array( $return ) ) {
			return $return;
		}
		return array();
	}

	private static function update_input_fields( $input_fields ) {
		return update_option( self::$option_name_input_fields, $input_fields );
	}


	/**
	 * @param string $classes
	 */
	public function set_classes( $classes ) {
		if ( $this->classes !== $classes ) {
			$this->classes = $classes;
			$this->setChanged();

		}
	}

	/**
	 * @return string
	 */
	public function get_classes() {
		return $this->classes;
	}

	/**
	 * @param string $id
	 */
	public function set_id( $id ) {
		if ( $this->id !== $id ) {
			$this->id = $id;
			$this->setChanged();

		}
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $name
	 */
	public function set_name( $name ) {
		if ( $this->name !== $name ) {
			$this->name = $name;
			$this->setChanged();

		}
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param string $size
	 */
	public function set_size( $size ) {
		if ( $this->size !== $size ) {
			$this->size = $size;
			$this->setChanged();

		}
	}

	/**
	 * @return string
	 */
	public function get_size() {
		return $this->size;
	}

	/**
	 * @param Html_Input_Type_Enum $type
	 */
	public function set_type( Html_Input_Type_Enum $type ) {
		if ( $this->type !== $type ) {
			$this->type = $type;
			$this->setChanged();

		}
	}

	/**
	 * @return Html_Input_Type_Enum
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @param string $title
	 */
	public function set_title( $title ) {
		if ( $this->title !== $title ) {
			$this->title = $title;
			$this->setChanged();

		}
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param string $value
	 */
	public function set_value( $value ) {
		if ( $this->value !== $value ) {
			$this->value = $value;
			$this->setChanged();
		}
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @param array $possible_values
	 */
	public function set_possible_values( $possible_values ) {
		if ( $this->possible_values !== $possible_values ) {
			$this->possible_values = $possible_values;
		}
	}

	/**
	 * @return array
	 */
	public function get_possible_values() {
		return $this->possible_values;
	}

	/**
	 * @param mixed $unique_name
	 */
	public function set_unique_name( $unique_name ) {
		if ( $this->unique_name !== $unique_name ) {
			$this->unique_name = $unique_name;
		}
	}

	/**
	 * @return mixed
	 */
	public function get_unique_name() {
		return $this->unique_name;
	}

	/**
	 * @param null $do_action
	 */
	public function set_do_action( $do_action ) {
		if ( $this->do_action !== $do_action ) {
			$this->do_action = $do_action;
		}
	}

	/**
	 * @return null
	 */
	public function get_do_action() {
		return $this->do_action;
	}

	/**
	 * @param mixed $required
	 */
	public function set_required( $required ) {
		if ( $this->required !== $required ) {
			$this->required = $required;
		}
	}

	/**
	 * Returns true/false if the input field is required
	 * @return bool
	 */
	public function get_required() {
		return $this->required;
	}

	/**
	 * @param null $validate_callback
	 */
	public function set_validate_callback( $validate_callback ) {
		if ( $this->validate_callback !== $validate_callback ) {
			$this->validate_callback = $validate_callback;
		}
	}

	/**
	 * @return null
	 */
	public function get_validate_callback() {
		return $this->validate_callback;
	}


	/**
	 * @param bool $force_new_validation
	 *
	 * @return bool|WP_Error
	 * @throws Exception
	 */
	public function validate( $force_new_validation = false ) {
		if ( $force_new_validation || false === $this->validation_result ) {
			if ( NULL === $this->validate_callback ) {
				if ( true === $this->get_required() ) {
					//If field is required and no special validate callback is set, we check if if's NOT empty
					$this->validation_result = Fum_Html_Input_Field::not_empty_callback( $this );
				}
				else {
					$this->validation_result = true;
				}
			}
			else {
				if ( ! is_callable( $this->validate_callback ) ) {
					throw new Exception( 'Validation callback is not callable!' );
				}
				$this->validation_result = call_user_func( $this->validate_callback, $this, $this->validate_params );
			}
		}
		return $this->validation_result;

	}

	/**
	 * @return bool
	 */
	public function is_validated() {
		if ( false === $this->validation_result ) {
			return false;
		}
		return true;
	}

	/**
	 * @param bool|WP_Error $validated
	 */
	public function set_validation_result( $validated ) {
		$this->validation_result = $validated;
	}

	/**
	 * @return bool|WP_Error
	 */
	public function get_validation_result() {
		return $this->validation_result;
	}

	public function save() {
		$validation = $this->validation_result;
		if ( false === $this->validation_result ) {
			$validation              = $this->validate();
			$this->validation_result = $validation;
		}

		if ( true === $validation ) {
			$this->notifyObservers();
		}
		return $validation;
	}

	public function update( Fum_Observable $o ) {
	}


	/**
	 * Example validation callback function, checks if the input field is empty
	 *
	 * @param Fum_Html_Input_Field $input_field
	 *
	 * @return bool|WP_Error
	 */
	private static function not_empty_callback( Fum_Html_Input_Field $input_field, $params = array() ) {
		$value = trim( $input_field->get_value() );
		if ( ! empty( $value ) ) {
			return true;
		}
		return new WP_Error( $input_field->get_unique_name(), 'Das Feld darf nicht leer sein' );
	}

	/**
	 * Example validation callback function, checks if the input field contains an valid e-mail address (only format of mail address)
	 *
	 * @param Fum_Html_Input_Field $input_field
	 *
	 * @return bool|WP_Error
	 */
	private static function mail_address_callback( Fum_Html_Input_Field $input_field, $params = array() ) {
		$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
// Run the preg_match() function on regex against the email address
		if ( preg_match( $regex, $input_field->get_value() ) ) {
			return true;
		}
		else {
			return new WP_Error( $input_field->get_unique_name(), 'Die E-Mailadresse hat ein ungültiges Format' );
		}
	}
}