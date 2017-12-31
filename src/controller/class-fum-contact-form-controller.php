<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Contact_Form_Controller
{
    public static function create_contact_form()
    {
        $form = self::get_form();
        if (isset($_REQUEST[Fum_Conf::$fum_unique_name_field_name])) {
            foreach ($form->get_input_fields() as $input_field) {
                if (isset($_REQUEST[$input_field->get_name()])) {
                    $input_field->set_value($_REQUEST[$input_field->get_name()]);
                }
            }
            $form->get_input_field('fum_captcha')->set_validate_callback(
                ['Fum_Contact_Form_Controller', 'validate_capctcha_callback']
            );
            $form->get_input_field(Fum_Conf::$fum_input_field_email)->set_validate_callback(
                ['Fum_Html_Input_Field', 'mail_address_callback']
            );
            if (true === $form->validate()) {
                $email = '';
                switch ($form->get_input_field('fum_send_to')->get_value()) {
                    case 'general':
                        $email = 'info@dhv-jugend.de';
                        break;
                    case 'homepage':
                        $email = 'hp@dhv-jugend.de';
                        break;
                    case 'event':
                        $email = 'info@dhv-jugend.de';
                        break;
                }
                Fum_Mail::sendHtmlMail(
                    $email,
                    sanitize_text_field($_REQUEST['fum_subject']),
                    nl2br(sanitize_textarea_field($_REQUEST['fum_text'])),
                    sanitize_email(trim($_REQUEST[Fum_Conf::$fum_input_field_email]))
                );
                echo '<p><strong>Deine Nachricht wurde versendet, wir melden uns sobald wie möglich.</strong></p>';
                return;
            }
        }


        Fum_Form_View::output($form);

    }


    public static function validate_capctcha_callback(Fum_Html_Input_Field $input_field)
    {
        if (strtolower($input_field->get_value()) == 'münchen') {
            return true;
        }
        return new WP_Error($input_field->get_unique_name(), 'Deine Antwort war leider falsch.');
    }

    private static function get_form()
    {
        $form = new Fum_Html_Form(
            Fum_Conf::$fum_contact_form_unique_name,
            Fum_Conf::$fum_contact_form_unique_name,
            '#',
            new Html_Method_Type_Enum(Html_Method_Type_Enum::POST)
        );
        $send_to = new Fum_Html_Input_Field(
            'fum_send_to',
            'fum_send_to',
            new Html_Input_Type_Enum(Html_Input_Type_Enum::SELECT),
            'Anliegen',
            'fum_send_to',
            true
        );
        $send_to->set_possible_values(
            [
                ['title' => 'Allgemein', 'value' => 'general'],
                ['title' => 'Homepage', 'value' => 'homepage'],
                ['title' => 'Event', 'value' => 'event'],
            ]
        );
        $form->add_input_field($send_to);
        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_email));
        $form->add_input_field(
            new Fum_Html_Input_Field(
                'fum_subject',
                'fum_subject',
                new Html_Input_Type_Enum(Html_Input_Type_Enum::TEXT),
                'Betreff',
                'fum_subject',
                true
            )
        );
        $form->add_input_field(
            new Fum_Html_Input_Field(
                'fum_text',
                'fum_text',
                new Html_Input_Type_Enum(Html_Input_Type_Enum::TEXTAREA),
                'Text',
                'fum_text',
                true
            )
        );

        $form->add_input_field(
            new Fum_Html_Input_Field(
                'fum_captcha',
                'fum_captcha',
                new Html_Input_Type_Enum(Html_Input_Type_Enum::TEXT),
                'Wo war die Jugendkommissionssitzung (Sie war in München)?',
                'fum_captcha',
                false
            )
        );


        $form->add_input_field(Fum_Html_Input_Field::get_input_field(Fum_Conf::$fum_input_field_submit));
        return $form;
    }
}