<?php

/**
 * Plugin Name: Frontend User Management
 * Plugin URI: https://github.com/SchwarzwaldFalke/frontend-user-management
 * Description: Plugin which allows user to register, login and edit their user profile in frontend. It also adds activation mails during user registration
 * Version: 0.07
 * Author: Christoph Bessei
 * Author URI: https://www.schwarzwald-falke.de
 * License: GPL v2
 */

require_once(__DIR__ . '/src/frontend-user-management.php');
new Frontend_User_Management(); //start plugin

//Have to be called from main plugin file? Couldn't get it working in other places
register_activation_hook(__FILE__, array('Fum_Activation', 'activate_plugin'));
register_deactivation_hook(__FILE__, array('Fum_Deactivation', 'deactivate_plugin'));
register_uninstall_hook(__FILE__, array('Fum_Uninstallation', 'uninstall_plugin'));

// Execute action so dependent plugins can hook here
do_action('frontend_user_management_plugin_loaded');