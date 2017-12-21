<?php
/**
 * @author Christoph Bessei
 */

namespace BIT\FUM\Settings;

use BIT\FUM\Settings\Tab\BasicTab;
use BIT\FUM\Settings\Tab\MailTab;
use BIT\FUM\Settings\Tab\UserRegistrationTab;

class Settings extends \C3\WpSettings\Settings
{
    public static function register($options = []): \C3\WpSettings\Settings
    {
        $defaultOptions = [
            'pageTitle' => 'Frontend user management',
            'menuTitle' => 'Frontend user management',
            'capability' => 'delete_posts',
            'menuSlug' => \Fum_Conf::PREFIX . 'settings',
        ];

        $settings = \C3\WpSettings\Settings::register(array_merge($defaultOptions, $options));

        $settings->addTab(new BasicTab());
        $settings->addTab(new MailTab());
        $settings->addTab(new UserRegistrationTab());

        return $settings;
    }
}
