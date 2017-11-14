<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Option
{
    protected $name;
    protected $title;
    protected $description;
    protected $value;
    protected $class;
    protected $option_group;
    /**
     * Possible types are the html input field types (text,password, checkbox , select etc.)
     * @var string
     */
    protected $type;
    /**
     * Used for select types, $value is the current value and $possible_values are the possible values which are shown in the dropdown
     * @var array
     */
    protected $possible_values;
    protected $multiple_selection;

    /**
     * Content is printed before the input field, maybe useful for something :-)
     * @var string
     */
    protected $pre_option_html;

    /**
     * Content is printed after the input field, e.g. useful if you want to add some javascript
     * @var string
     */
    protected $post_option_html;


    public function __construct($name, $title, $description, $value, Fum_Option_Group $option_group, $type, $possible_values = [], $multiple_selection = false, $pre_option_html = '', $post_option_html = '')
    {
        $this->name = $name;
        $this->title = $title;
        $this->description = $description;
        $this->value = $value;
        $this->option_group = $option_group;
        $this->type = $type;
        $this->possible_values = $possible_values;
        $this->multiple_selection = $multiple_selection;
        $this->pre_option_html = $pre_option_html;
        $this->post_option_html = $post_option_html;
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
     * @param mixed $option_group
     */
    public function set_option_group($option_group)
    {
        $this->option_group = $option_group;
    }

    /**
     * @return mixed
     */
    public function get_option_group()
    {
        return $this->option_group;
    }

    /**
     * @param mixed $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * @param mixed $description
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function get_description()
    {
        return $this->description;
    }


    /**
     * @param mixed $type
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @param mixed $possible_values
     */
    public function set_possible_values($possible_values)
    {
        $this->possible_values = $possible_values;
    }

    /**
     * @return mixed[]
     */
    public function get_possible_values()
    {
        return $this->possible_values;
    }

    /**
     * @param mixed $value
     */
    public function set_value($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * @param boolean $multiple_selection
     */
    public function set_multiple_selection($multiple_selection)
    {
        $this->multiple_selection = $multiple_selection;
    }

    /**
     * @return boolean
     */
    public function get_multiple_selection()
    {
        return $this->multiple_selection;
    }

    /**
     * @param string $post_option_html
     */
    public function set_post_option_html($post_option_html)
    {
        $this->post_option_html = $post_option_html;
    }

    /**
     * @return string
     */
    public function get_post_option_html()
    {
        return $this->post_option_html;
    }

    /**
     * @param string $pre_option_html
     */
    public function set_pre_option_html($pre_option_html)
    {
        $this->pre_option_html = $pre_option_html;
    }

    /**
     * @return string
     */
    public function get_pre_option_html()
    {
        return $this->pre_option_html;
    }

    /**
     * @param mixed $class
     */
    public function set_class($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function get_class()
    {
        return $this->class;
    }


}