<?php
namespace BIT\FUM\Settings\Tab;

use C3\WpSettings\Tab\AbstractTab;

/**
 * @author Christoph Bessei
 */
class UserRegistrationTab extends AbstractTab
{
    const SEND_CONFIRMATION_LINK = \Fum_Conf::PREFIX . 'send_confirmation_link';
    const GENERATE_RANDOM_PASSWORD = \Fum_Conf::PREFIX . 'generate_random_password';

    public function getId(): string
    {
        return \Fum_Conf::PREFIX . 'user_registration';
    }

    public function getTitle(): string
    {
        return __('User registration', 'fum_text_domain');
    }

    public function getFields(): array
    {
        return [
            [
                'name' => static::SEND_CONFIRMATION_LINK,
                'label' => __('Send confirmation link after registration', 'fum_text_domain'),
                'type' => 'checkbox',
            ],
            [
                'name' => static::GENERATE_RANDOM_PASSWORD,
                'label' => __('Generate random password', 'fum_text_domain'),
                'type' => 'checkbox',
            ],
        ];
    }
}
