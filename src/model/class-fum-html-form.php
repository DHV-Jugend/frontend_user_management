<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Html_Form extends Fum_Observable implements Fum_Observer
{
    protected static $option_name_forms = 'fum_forms';

    protected $unique_name;
    protected $name;
    protected $action;
    protected $method;
    protected $id;
    protected $classes;
    protected $extra_params;
    /** @var  Fum_Html_Input_Field[] $input_fields */
    protected $input_fields;
    protected $callback;
    protected $callback_param;
    protected $validation_result = false;

    function __construct($unique_name, $name, $action, Html_Method_Type_Enum $method = NULL, $id = '', $classes = '', $input_fields = [], $callback = NULL, $callback_param = [])
    {
        $this->unique_name = $unique_name;
        $this->name = $name;
        $this->action = $action;
        if (!$method instanceof Html_Method_Type_Enum) {
            $method = new Html_Method_Type_Enum(Html_Method_Type_Enum::POST);
        }
        $this->method = $method;
        $this->id = $id;
        $this->classes = $classes;
        $this->input_fields = $input_fields;
        if (is_array($this->input_fields)) {
            foreach ($this->input_fields as $input_field) {
                /** @var  Fum_Html_Input_Field $input_field */
                $input_field->addObserver($this);
            }
        }
        $this->callback = $callback;
    }

    /**
     * @param Fum_Observable $observable
     */
    public function update(Fum_Observable $observable)
    {
        //Got notification from an input field, set changed to true
        //Notify observer is called on save()
        if (in_array($observable, $this->input_fields)) {
            //Set validation_result to false because we have to revalidate the form now
            $this->validated = false;
            $this->setChanged();
        }
    }

    /**
     * Calls validate() and notifies all observers that they can save the form if validate() returns true
     * It also calls save() on each input_field
     * @return bool|WP_Error returns true if validate() returned true and successfully notified all observers, else returns WP_Error
     */
    public function save()
    {

        foreach ($this->get_input_fields() as $input_field) {
            $input_field->save();
        }

        if (false === $this->validation_result) {
            $this->validation_result = $this->validate();
        }

        if (true === $this->validation_result) {
            $this->notifyObservers();
        }
        return $this->validation_result;
    }

    /**
     * @param bool $force_new_validation
     *
     * @return bool|WP_Error
     * @throws Exception
     */
    public function validate($force_new_validation = false)
    {
        //Check if validation is forced or if no validation_result value is set, else return the validation_result value
        if ($force_new_validation || false === $this->validation_result) {
            //Run validation on input fields
            $validation_fields = true;
            foreach ($this->get_input_fields() as $input_field) {
                if (is_wp_error($input_field->validate($force_new_validation))) {
                    $validation_fields = false;
                }
            }

            //Run validation of form
            if (NULL !== $this->callback) {
                if (!is_callable($this->callback)) {
                    throw new Exception('Validation callback is not callable!');
                }

                $callback_param = $this->callback_param;
                if (false === $validation_fields && !isset($this->callback_param)) {
                    $callback_param = ['error_on_input_field' => true];
                } else if (false === $validation_fields && isset($this->callback_param)) {
                    $callback_param = array_merge($callback_param, ['error_on_input_field' => true]);
                }

                $validation_form = call_user_func($this->callback, $this, $callback_param);
                if (true !== $validation_fields && !is_wp_error($validation_form)) {
                    throw new Exception('If there is an error on validation of an input field, the callback function of the form have to return an WP_Error');
                }
                $this->validation_result = $validation_form;
            } else {
                $this->validation_result = $validation_fields;
                if (false === $validation_fields) {
                    $this->validation_result = new WP_Error();
                }
            }
        }
        return $this->validation_result;
    }


    public function set_values_from_array(array $values)
    {
        //Set form values
        foreach ($this->get_input_fields() as $input_field) {
            if (isset($values[$input_field->get_name()])) {
                $input_field->set_value($values[$input_field->get_name()]);
            }
        }
    }

    /**
     * Adds an HTML form to the database
     *
     * @param Fum_Html_Form $form HTML form which should be stored in the database
     *
     * @return bool  return value of update_option(), true if add was successful, otherwise false
     * @throws Exception The exception is thrown if $unique_name is already used for another form
     */
    public static function add_form(Fum_Html_Form $form)
    {
        $forms = self::get_forms();
        foreach ($forms as $cur_form) {
            if ($cur_form->get_unique_name() === $form->get_unique_name()) {
                throw new Exception('$unique_name "' . $form->get_unique_name() . '" is already used');
            }
        }
        $forms[] = $form;
        return self::update_forms($forms);
    }

    /**
     * Stores new forms in the database, OVERWRITES! the previously stored forms
     *
     * @param Fum_Html_Form[] $forms array of Fum_Html_Form forms
     *
     * @return bool return value of update_option(), true if add was successful, otherwise false
     */
    public static function set_forms(array $forms)
    {
        return self::update_forms($forms);
    }

    /**
     * Get form by object or $unique_name
     *
     * @param $form Fum_Html_Form|string  Html_form object of the form or $unique_name
     *
     * @return Fum_Html_Form|bool returns the searched Fum_Html_Form or false if it's not found
     */
    public static function get_form($form)
    {
        $unique_name = $form;
        if ($form instanceof Fum_Html_Form) {
            $unique_name = $form->get_unique_name();
        }
        $forms = self::get_forms();
        foreach ($forms as $form) {
            if ($form->get_unique_name() === $unique_name) {
                //Add observer
                foreach ($form->get_input_fields() as $input_field) {
                    $input_field->addObserver($form);
                }
                return $form;
            }
        }
        return false;
    }

    /**
     * Get all stored forms from the database
     * @return Fum_Html_Form[]
     */
    public static function get_all_forms()
    {
        return self::get_forms();
    }

    /**
     * Deletes a form from the database
     *
     * @param $form Fum_Html_Form|string The Fum_Html_Form object or the unique name
     *
     * @return bool true if delete was successful, otherwise false
     */
    public static function delete_form($form)
    {
        $unique_name = $form;
        if ($form instanceof Fum_Html_Form) {
            $unique_name = $form->get_unique_name();
        }
        $forms = self::get_forms();
        foreach ($forms as $key => $form) {
            if ($form->get_unique_name() === $unique_name) {
                unset($forms[$key]);
                return self::update_forms($forms);
            }
        }
        return false;
    }

    /**
     * Checks if an $unique_name is already used in another form
     *
     * @param $unique_name string
     *
     * @return bool returns true if $unique_name is already used, false if not
     */
    public static function is_unique_name_already_used($unique_name)
    {
        $forms = self::get_forms();
        foreach ($forms as $form) {
            if ($form->get_unique_name() === $unique_name) {
                return true;
            }
        }
        return false;
    }


    /**
     * @return string option name of the stored forms
     */
    public static function get_option_name_forms()
    {
        return self::$option_name_forms;
    }

    /**
     * @return Fum_Html_Form[]
     */
    private static function get_forms()
    {
        $return = get_option(self::get_option_name_forms());
        if (is_array($return)) {
            return $return;
        }
        return [];

    }

    private static function update_forms($forms)
    {
        return update_option(self::$option_name_forms, $forms);
    }


    /**
     * @param mixed $action
     */
    public function set_action($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function get_action()
    {
        return $this->action;
    }

    /**
     * @param mixed $classes
     */
    public function set_classes($classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return mixed
     */
    public function get_classes()
    {
        return $this->classes;
    }

    /**
     * @param mixed $id
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function get_id()
    {
        return $this->id;
    }

    public function add_input_field(Fum_Html_Input_Field $input_field)
    {
        $input_field->addObserver($this);
        $this->input_fields[] = $input_field;
    }

    public function insert_input_field_after_unique_name(Fum_Html_Input_Field $input_field, $unique_name)
    {
        $found = false;

        foreach ($this->get_input_fields() as $key => $cur_input_field) {
            if ($found) {
                $input_fields = $this->get_input_fields();
                //Insert element into array: http://www.php.net/manual/de/function.array-splice.php#19084
                array_splice($input_fields, $key, 0, [$input_field]);
                $this->set_input_fields($input_fields);
                break;
            }
            if ($cur_input_field->get_unique_name() == $unique_name) {
                $found = true;
            }
        }
    }

    public function insert_input_field_before_unique_name(Fum_Html_Input_Field $input_field, $unique_name)
    {
        foreach ($this->get_input_fields() as $key => $cur_input_field) {
            if ($cur_input_field->get_unique_name() == $unique_name) {
                $input_fields = $this->get_input_fields();
                //Insert element into array: http://www.php.net/manual/de/function.array-splice.php#19084
                array_splice($input_fields, $key, 0, [$input_field]);
                $this->set_input_fields($input_fields);
                break;
            }
        }
    }

    /**
     * @param Fum_Html_Input_Field[] $input_fields
     */
    public function set_input_fields(array $input_fields)
    {
        foreach ($input_fields as $input_field) {
            $input_field->addObserver($this);
        }
        $this->input_fields = $input_fields;
    }

    /**
     * @return Fum_Html_Input_Field[]
     */
    public function get_input_fields()
    {
        return $this->input_fields;
    }

    public function get_unique_names_of_input_fields()
    {
        $names = [];
        foreach ($this->get_input_fields() as $input_field) {
            $names[$input_field->get_unique_name()] = $input_field->get_unique_name();
        }
        return $names;
    }

    public function get_input_field($input_field)
    {
        $name = $input_field;
        if ($input_field instanceof Fum_Html_Input_Field) {
            $name = $input_field->get_unique_name();
        }

        foreach ($this->get_input_fields() as $cur_input_field) {
            if ($cur_input_field->get_unique_name() == $name) {
                return $cur_input_field;
            }
        }
        return NULL;
    }

    /**
     * @param Html_Method_Type_Enum $method
     */
    public function set_method(Html_Method_Type_Enum $method)
    {
        $this->method = $method;
    }

    /**
     * @return Html_Method_Type_Enum
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     * @param mixed $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param mixed $unique_name
     */
    public function set_unique_name($unique_name)
    {
        $this->unique_name = $unique_name;
    }

    /**
     * @return mixed
     */
    public function get_unique_name()
    {
        return $this->unique_name;
    }

    /**
     * @param $callback
     */
    public function set_callback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return null
     */
    public function get_callback()
    {
        return $this->callback;
    }

    /**
     * @return boolean|WP_Error
     */
    public function get_validation_result()
    {
        return $this->validation_result;
    }

    public function is_validated()
    {
        if (false === $this->validation_result) {
            return false;
        }
        return true;
    }

    /**
     * @param mixed $callback_param
     */
    public function set_callback_param($callback_param)
    {
        $this->callback_param = $callback_param;
    }

    /**
     * @return mixed
     */
    public function get_callback_param()
    {
        return $this->callback_param;
    }

    /**
     * Set extra parameters in the <form> tag, could be useful to add js onload or something similar
     *
     * @param string $extra_params the string which should be added to the <form> tag
     */
    public function set_extra_params($extra_params)
    {
        $this->extra_params = $extra_params;
    }

    /**
     * Get extra parameters in the <form> tag, could be useful to add js onload or something similar
     * @return string the string which should be added to the <form> tag
     */
    public function get_extra_params()
    {
        return $this->extra_params;
    }


    public static function validate_change_password_form(Fum_Html_Form $form, $params)
    {
        $return = true;
        $ID = $params['ID'];
        $pass = $form->get_input_field($params['password'])->get_value();
        $new_pass = $form->get_input_field($params['new_password'])->get_value();
        $new_pass_check = $form->get_input_field($params['new_password_check'])->get_value();
        $user = get_userdata($ID);
        if (!$user instanceof WP_User) {
            throw new Exception('Could not find user with id' . $ID);
        }

        /** @var array $user ->data */
        if (!wp_check_password($pass, $user->data->user_pass, $ID)) {
            $form->get_input_field($params['password'])->set_validation_result(new WP_Error($params['password'], 'Das aktuelle Passwort ist falsch'));
        }

        if ($new_pass != $new_pass_check) {
            $form->get_input_field($params['new_password_check'])->set_validation_result(new WP_Error($params['new_password_check'], 'Die Passwörter stimmen nicht überein'));
            $form->get_input_field($params['new_password'])->set_validation_result(new WP_Error($params['new_password'], 'Die Passwörter stimmen nicht überein'));
            $return = false;
        }

        if (empty($new_pass)) {
            $form->get_input_field($params['new_password'])->set_validation_result(new WP_Error($params['new_password'], 'Leere Passwörter sind nicht erlaubt'));
            $return = false;
        } else {
            if (empty($new_pass_check)) {
                $form->get_input_field($params['new_password_check'])->set_validation_result(new WP_Error($params['new_password_check'], 'Leere Passwörter sind nicht erlaubt'));
                $return = false;
            }
        }
        if (false === $return) {
            $return = new WP_Error($form->get_unique_name(), 'Fehler beim Ändern des Passworts');
        }
        return $return;
    }
}