<?php

class Frontend_User_Management
{
    protected $option_fum_category_id = 'fum_category';
    protected static $plugin_path = NULL;

    public function __construct()
    {
        //This change should only be visible for sites with experimental branch

        spl_autoload_register(array($this, 'autoload'));
        //Set path to plugin dir
        Frontend_User_Management::$plugin_path = plugin_dir_path(__FILE__);
        Fum_Initialisation::initiate_plugin();
        add_filter('tc_post_metas', array($this, 'remove_meta'));
    }

    function remove_meta($html)
    {
        if (get_post()->post_type == Fum_Conf::$fum_post_type) {
            return '';
        }
        return $html;
    }

    public function autoload($class_name)
    {

        if ('Fum_Conf' === $class_name) {
            require_once(Frontend_User_Management::$plugin_path . 'fum_conf.php');
        }

        if ('Options' === $class_name) {
            require_once(Frontend_User_Management::$plugin_path . 'options.php');
        }

        //Because of sucking wordpress name conventions class name != file name, convert it manually
        $class_name = 'class-' . strtolower(str_replace('_', '-', $class_name) . '.php');
        if (file_exists(Frontend_User_Management::$plugin_path . 'class/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'class/' . $class_name);
        } elseif (file_exists(Frontend_User_Management::$plugin_path . 'controller/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'controller/' . $class_name);
        } elseif (file_exists(Frontend_User_Management::$plugin_path . 'model/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'model/' . $class_name);
        } elseif (file_exists(Frontend_User_Management::$plugin_path . 'view/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'view/' . $class_name);
        } elseif (file_exists(Frontend_User_Management::$plugin_path . 'view/fum_option_pages/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'view/fum_option_pages/' . $class_name);
        } elseif (file_exists(Frontend_User_Management::$plugin_path . 'utility/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'utility/' . $class_name);
        } elseif (file_exists(Frontend_User_Management::$plugin_path . 'plugin_management/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'plugin_management/' . $class_name);
        } elseif (file_exists(Frontend_User_Management::$plugin_path . 'interface/' . $class_name)) {
            require_once(Frontend_User_Management::$plugin_path . 'interface/' . $class_name);
        }
    }

    /**
     * @return null|string
     */
    public static function get_plugin_path()
    {
        return self::$plugin_path;
    }


}

new Frontend_User_Management(); //start plugin

