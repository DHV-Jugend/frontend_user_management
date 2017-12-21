<?php

use BIT\FUM\Settings\Tab\BasicTab;
use BIT\FUM\Settings\Tab\UserRegistrationTab;

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Activation
{
    private static $general_input_fields = [
        Html_Input_Type_Enum::TEXT => [],
        Html_Input_Type_Enum::PASSWORD => [],
        Html_Input_Type_Enum::SELECT => [],
        Html_Input_Type_Enum::CHECKBOX => [],
        Html_Input_Type_Enum::SUBMIT => [
            'fum_input_field_submit' => ['Abschicken', false],
        ],
    ];
    private static $user_input_fields = [
        Html_Input_Type_Enum::TEXT => [
            //Default input field names
            'fum_input_field_username' => ['Username', true],
            'fum_input_field_email' => ['E-Mail', true, ['Fum_Html_Input_Field', 'mail_address_callback']],
            'fum_input_field_last_name' => ['Nachname', false],
            'fum_input_field_first_name' => ['Vorname', false],
            'fum_input_field_website' => ['Website', false],
            'fum_input_field_display_name' => ['Öffentlicher Name', false],

            //DHV-Jugend input field names
            'fum_input_field_birthday' => ['Geburtstag', false, ['Fum_Html_Input_Field', 'date_callback']],
            'fum_input_field_street' => ['Straße', false],
            'fum_input_field_city' => ['Stadt', false],
            'fum_input_field_postcode' => [
                'Postleitzahl',
                false,
                ['Fum_Html_Input_Field', 'integer_callback'],
                ['length' => [4, 5]],
            ],
            'fum_input_field_state' => ['Bundesland', false],
            'fum_input_field_phone_number' => ['Telefonnummer', false],
            'fum_input_field_mobile_number' => ['Handynummer', false],
            'fum_input_field_dhv_member_number' => ['DHV MitgliedsNr.', false],
            'fum_input_field_license_number' => ['Lizenznummer (Fußgänger: Feld bitte leer lassen)', false],

            'fum_input_field_emergency_contact_surname' => ['Notfallkontakt Nachname', false],
            'fum_input_field_emergency_contact_forename' => ['Notfallkontakt Vorname', false],
            'fum_input_field_emergency_phone_number' => ['Notfallkontakt Telefonnummer', false],
        ],
        Html_Input_Type_Enum::PASSWORD => [
            'fum_input_field_password' => ['Password', true],
            'fum_input_field_new_password' => ['New password', true],
            'fum_input_field_new_password_check' => ['Confirm new password', true],
        ],
        Html_Input_Type_Enum::SELECT => [],
        Html_Input_Type_Enum::CHECKBOX => [
            'fum_input_field_premium_participant' => ['Schüler, Azubi, Student', false,],
        ],
        Html_Input_Type_Enum::SUBMIT => [],
    ];
    private static $event_input_fields = [
        Html_Input_Type_Enum::TEXT => [],
        Html_Input_Type_Enum::PASSWORD => [],
        Html_Input_Type_Enum::SELECT => [
            'fum_input_field_select_event' => ['Event', true, null, null, ['Bassano', 'Ski & Fly']],
            'fum_input_field_aircraft' => [
                'Fluggerät',
                false,
                null,
                null,
                //Possible values array
                [
                    ['title' => 'Gleitschirm', 'value' => 'gleitschirm'],
                    ['title' => 'Drachen', 'value' => 'drachen'],
                    ['title' => 'Fußgänger', 'value' => 'fussgaenger'],
                ],
            ],
        ],
        Html_Input_Type_Enum::CHECKBOX => [
            'fum_input_field_search_ride' => ['Suche Mitfahrgelegenheit', false],
            'fum_input_field_offer_ride' => ['Biete Mitfahrgelgenheit', false],
            'fum_input_field_accept_agb' => [
                'Ich habe die <a href="https://www.dhv-jugend.de/teilnahmebedingungen-haftungserklaerung/">Haftungserklärung</a> für DHV-Jugend Events gelesen und akzeptiere diese',
                true,
            ],
        ],
        Html_Input_Type_Enum::SUBMIT => [],
    ];

    /**
     * @throws \C3\WpSettings\Exception\InvalidSettingsTabException
     */
    public static function activate_plugin()
    {
        $front_end_form = new Fum_Front_End_Form();
        $post_ids = $front_end_form->add_form_posts();
        add_option(Fum_Conf::$fum_register_login_page_name, $post_ids[Fum_Conf::$fum_register_login_page_name]);
        add_option(Fum_Conf::$fum_edit_page_name, $post_ids[Fum_Conf::$fum_edit_page_name]);
        add_option(Fum_Conf::$fum_event_registration_page, $post_ids[Fum_Conf::$fum_event_registration_page]);

        Fum_Activation_Email::plugin_activated();

        /*Set default value of options*/

        //Disable activation emails by default
        if (false === UserRegistrationTab::get(UserRegistrationTab::SEND_CONFIRMATION_LINK)) {
            UserRegistrationTab::update(UserRegistrationTab::SEND_CONFIRMATION_LINK, 0);
        }

        //Let wordpress generate the password of new user
        if (false === UserRegistrationTab::get(UserRegistrationTab::GENERATE_RANDOM_PASSWORD)) {
            UserRegistrationTab::update(UserRegistrationTab::GENERATE_RANDOM_PASSWORD, 1);
        }

        //Do NOT hide wp-login.php by default
        if (false === BasicTab::get(BasicTab::USE_FRONTEND_LOGIN)) {
            BasicTab::update(BasicTab::USE_FRONTEND_LOGIN, 0);
        }

        self::create_default_input_fields();

        self::create_login_form();
        self::create_register_form();
        self::create_change_password_form();
        self::create_edit_form();
        self::create_event_register_form();
        self::create_applied_events_form();
    }

    private static function create_default_input_fields()
    {
        //http://stackoverflow.com/questions/16793015/how-to-merge-multidimensional-arrays-while-preserving-keys
        $all_input_fields = array_replace_recursive(
            self::$user_input_fields,
            self::$event_input_fields,
            self::$general_input_fields
        );

        foreach ($all_input_fields as $type => $input_fields) {
            foreach ($input_fields as $name => $input_field) {
                if (Fum_Html_Input_Field::is_unique_name_used(Fum_Conf::$$name)) {
                    Fum_Html_Input_Field::delete_input_field(Fum_Conf::$$name);
                }
                $field = new Fum_Html_Input_Field(
                    Fum_Conf::$$name,
                    Fum_Conf::$$name,
                    new Html_Input_Type_Enum($type),
                    $input_field[0],
                    Fum_Conf::$$name,
                    $input_field[1]
                );
                if (isset($input_field[2])) {
                    $field->set_validate_callback($input_field[2]);
                }
                if (isset($input_field[3])) {
                    $field->set_validate_params($input_field[3]);
                }
                if (isset($input_field[4])) {
                    $field->set_possible_values($input_field[4]);
                }
                Fum_Html_Input_Field::add_input_field($field);
            }
        }
    }


    private
    static function create_login_form()
    {

        $form = new Fum_Html_Form(Fum_Conf::$fum_login_form_unique_name, 'Login', '#');

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_username));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_password));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_submit));

        if (Fum_Html_Form::is_unique_name_already_used($form->get_unique_name())) {
            Fum_Html_Form::delete_form($form);
        }
        Fum_Html_Form::add_form($form);
    }

    private static function create_register_form()
    {

        $form = new Fum_Html_Form(Fum_Conf::$fum_register_form_unique_name, 'Registration Form', '#');

        //Required fields
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_username));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_email));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_first_name));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_last_name));

        //DHV-Jugend input field names
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_birthday));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_street));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_city));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_postcode));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_state));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_phone_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_mobile_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_dhv_member_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_license_number));

        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_contact_surname)
        );
        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_contact_forename)
        );
        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_phone_number)
        );

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_submit));


        if (Fum_Html_Form::is_unique_name_already_used($form->get_unique_name())) {
            Fum_Html_Form::delete_form($form);
        }
        Fum_Html_Form::add_form($form);
    }

    private static function create_change_password_form()
    {
        //Create change password fom
        $form = new Fum_Html_Form(Fum_Conf::$fum_change_password_form_unique_name, 'Passwort ändern', '#');

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_password));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_new_password));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_new_password_check));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_submit));

        if (Fum_Html_Form::is_unique_name_already_used($form->get_unique_name())) {
            Fum_Html_Form::delete_form($form);
        }
        Fum_Html_Form::add_form($form);
    }

    private static function create_edit_form()
    {

        $form = new Fum_Html_Form(Fum_Conf::$fum_edit_form_unique_name, 'Profil editieren', '#');


        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_email));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_last_name));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_first_name));


        //DHV-Jugend input field names
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_birthday));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_street));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_city));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_postcode));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_state));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_phone_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_mobile_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_dhv_member_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_license_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_premium_participant));


        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_contact_surname)
        );
        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_contact_forename)
        );
        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_phone_number)
        );

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_submit));


        if (Fum_Html_Form::is_unique_name_already_used($form->get_unique_name())) {
            Fum_Html_Form::delete_form($form);
        }
        Fum_Html_Form::add_form($form);
    }

    private static function create_event_register_form()
    {
        $form = new Fum_Html_Form(Fum_Conf::$fum_event_register_form_unique_name, 'Eventregistrierung', '#');

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_select_event));


        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_last_name));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_first_name));


        //DHV-Jugend input field names
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_birthday));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_street));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_city));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_postcode));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_state));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_phone_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_mobile_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_email));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_dhv_member_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_license_number));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_aircraft));

        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_contact_surname)
        );
        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_contact_forename)
        );
        $form->add_input_field(
            Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_emergency_phone_number)
        );

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_search_ride));
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_offer_ride));

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_accept_agb));

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_submit));

        if (Fum_Html_Form::is_unique_name_already_used($form->get_unique_name())) {
            Fum_Html_Form::delete_form($form);
        }
        Fum_Html_Form::add_form($form);
    }

    private static function create_applied_events_form()
    {
        $form = new Fum_Html_Form(Fum_Conf::$fum_user_applied_event_form_unique_name, 'Eventverwaltung', '#');

        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_submit));

        if (Fum_Html_Form::is_unique_name_already_used($form->get_unique_name())) {
            Fum_Html_Form::delete_form($form);
        }
        Fum_Html_Form::add_form($form);
    }

    /**
     * Returns array with unique names of event input fields with the following format:
     * [0] => select_event
     * [1] => fum_search_ride
     * [2] => fum_offer_ride
     * [3] => fum_accept_agb
     * @return array
     */
    public static function get_event_input_fields()
    {
        return self::convert_input_field_array(self::$event_input_fields);
    }

    /**
     * @return array
     */
    public static function get_general_input_fields()
    {
        return self::convert_input_field_array(self::$general_input_fields);
    }

    /**
     * @return array
     */
    public static function get_user_input_fields()
    {
        return self::convert_input_field_array(self::$user_input_fields);
    }

    private static function convert_input_field_array($arr)
    {
        $user_fields = [];
        foreach ($arr as $type_input_fields) {
            foreach ($type_input_fields as $name => $value) {
                $user_fields[] = Fum_Conf::$$name;
            }
        }
        return $user_fields;
    }
}