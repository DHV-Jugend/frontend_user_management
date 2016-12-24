<?php

/**
 * @author  Christoph Bessei
 * @version 0.01
 */
class Fum_Activation_Email
{
    private static $activation_key_field = 'fum_user_activation_key';
    private static $active_user_value = 'active';


    public static function plugin_activated()
    {
        //Get all user IDs
        $options = array('fields' => 'ID');
        $user_IDs = get_users($options);

        //add the activation key field to all current users and mark them as activated
        foreach ($user_IDs as $user_ID) {

            //Check if meta key already exists, this is possible if the plugin was only deactivated and now gets reactivated
            $activation_key_field_exists = get_user_meta($user_ID, self::$activation_key_field, true);
            if (!empty($activation_key_field_exists)) {
                break;
            }
            add_user_meta($user_ID, self::$activation_key_field, self::$active_user_value);
        }
    }

    public function plugin_deactivated()
    {
        self::delete_not_activated_users();

        //Get all user IDs and do not sort them, we do not need a sorted result and it just costs time
        $options = array('fields' => 'ID');
        $user_IDs = get_users($options);
        // remove activation key field from all users
        foreach ($user_IDs as $user_ID) {
            delete_user_meta($user_ID, self::$activation_key_field);
        }
    }

    /**
     * Deletes users who are not activated
     */
    protected static function delete_not_activated_users()
    {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        $options = array('fields' => 'ID',
            'meta_key' => self::$activation_key_field,
            'meta_value' => self::$active_user_value,
            'meta_compare' => '!=',
        );

        $user_IDs = get_users($options);
        foreach ($user_IDs as $user_ID) {
            wp_delete_user($user_ID);
        }
    }

    public static function new_user_registered($user_id)
    {
        //Create new activation key and add it to the user meta
        $activation_key = self::create_activation_key();
        update_user_meta($user_id, self::$activation_key_field, $activation_key);

        self::fum_new_user_notification($user_id, $_REQUEST[Fum_Conf::$fum_input_field_password]);
    }

    private static function create_activation_key()
    {
        //Generate random activation key, without special characters (has to be url friendly)
        return wp_generate_password(15, false);
    }

    //TODO Make activation link more secure with an extra field which contains the username, so we do not check every activation code
    public static function activate_user($content)
    {
        if (isset($_GET[self::$activation_key_field])) {
            $url_activation_key = trim($_GET[self::$activation_key_field]);
            if (empty($url_activation_key)) {
                return '<strong>' . __('Invalid activation key') . '</strong>' . $content;
            }
            //Get all user IDs
            $options = array('fields' => 'ID');
            $user_ids = get_users($options);
            foreach ($user_ids as $user_id) {
                if (update_user_meta($user_id, self::$activation_key_field, self::$active_user_value, $url_activation_key)) {
//					return '<strong>' . __( 'Activation of user was successful' ) . '</strong>' . $content;
                    return '<p><strong>Die Aktivierung deines Accounts war erfolgreich</strong></p>' . $content;
                }
            }
            return '<p><strong>Die Aktivierung deines Accounts ist fehlgeschlagen, der Aktivierungscode scheint fehlerhaft zu sein</strong></p>' . $content;
        }
        return $content;
    }

    public static function authenticate($user)
    {
        //Authentication already failed, just return $user
        if (is_wp_error($user)) {
            return $user;
        }
        if (get_user_meta($user->ID, self::$activation_key_field, true) === self::$active_user_value) {
            return $user;
        }
//		return new WP_Error( 'not activated', __( 'User is not active, have you checked your mails for the activation link?' ) );
        return new WP_Error('not_activated', 'Benutzer ist nicht aktiv, hast du den Aktivierungslink in der Willkommen E-Mail benutzt?');
    }

    private static function fum_new_user_notification($user_id, $password = false)
    {
        $user = new WP_User($user_id);
        $activation_code = get_user_meta($user->ID, self::get_activation_key_field(), true);

        //Send activation link only if the admin wants to use activation links, otherwise activate user directly
        if (get_option(Fum_Conf::$fum_register_form_use_activation_mail_option)) {
            $send_activation_link = true;
        } else {
            update_user_meta($user_id, self::get_activation_key_field(), self::get_active_user_value());
            $send_activation_link = false;
        }

        //Send password only if it was generated randomly
        if (!get_option(Fum_Conf::$fum_register_form_generate_password_option)) {
            $password = false;
        }
        //Send the welcome mail only if it contains useful informations (activation link and/or password)
        if (false === $password && false === $send_activation_link) {
            return;
        }

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

//		$message = sprintf( __( 'New user registration on your blog %s:', 'frontend-user-management' ), get_option( 'blogname' ) ) . "\r\n\r\n";
//		$message .= sprintf( __( 'Username: %s', 'frontend-user-management' ), $user_login ) . "\r\n";
//		$message .= sprintf( __( 'E-mail: %s', 'frontend-user-management' ), $user_email ) . "\r\n";
//		if ( false !== $password ) {
//			$message .= sprintf( __( 'Password: %s', 'frontend-user-management' ), $password ) . "\r\n";
//		}
//		if ( false !== $send_activation_link ) {
//			$message .= sprintf( __( 'Activation Link: %s', 'frontend-user-management' ), get_home_url() . "?" . self::get_activation_key_field() . "=" . $activation_code ) . "\r\n";
//		}
//
//		$title = sprintf( __( 'Welcome to %s', 'frontend-user-management' ), get_option( 'blogname' ) );

        //DHV-Jugend

        $title = 'Herzlich willkommen bei der DHV-Jugend';
        $message = 'Herlich willkommen bei der DHV-Jugend' . "\n";
        $message .= 'Dein Benutzername: ' . $user_login . "\n";
        $message .= 'Deine Emailadresse: ' . $user_email . "\n";
        if (false !== $password) {
            $message .= 'Dein Passwort: ' . $password . "\n";
        }
        if (false !== $send_activation_link) {
            $message .= 'Der Aktivierungslink für deinen Account:' . "\n";
            $message .= get_home_url() . "?" . self::get_activation_key_field() . "=" . $activation_code . "\n";
            $message .= 'Ohne deinen Account zu aktivieren, kannst du dich nicht einloggen' . "\n";
        }

        $message .= 'Wir wünschen dir viel Spaß mit der DHV-Jugend!';
        try {
            Ems_Event_Registration::send_mail_via_smtp($user_email, $title, $message);
        } catch (Exception $e) {
            echo $user_email . ' ' . $title . ' ' . $message . "<br/><br/>";
            echo $e->getMessage();
        }
    }

    /**
     * Returns the field name where the activation key is stored in the user meta
     * @return string
     */
    public static function get_activation_key_field()
    {
        return self::$activation_key_field;
    }

    /**
     * @return string
     */
    public static function get_active_user_value()
    {
        return self::$active_user_value;
    }
}

if (!function_exists('wp_new_user_notification')) :
//Overwrite wp_new_user_notification from pluggable.php
//TODO else: throw exception that there is a plugin conflict because two plugins overwrite wp_new_user_notification
endif;