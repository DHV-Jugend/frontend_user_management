<?php
namespace BIT\FUM\Settings\Tab;

use C3\WpSettings\Tab\AbstractTab;

/**
 * @author Christoph Bessei
 */
class BasicTab extends AbstractTab
{
    const USE_FRONTEND_LOGIN = \Fum_Conf::PREFIX . 'use_frontend_login';
    const HIDE_ADMIN_BAR_FOR_NORMAL_USER = \Fum_Conf::PREFIX . 'hide_admin_bar_for_normal_user';

    public function getId(): string
    {
        return \Fum_Conf::PREFIX . 'basic';
    }

    public function getTitle(): string
    {
        return __('Basic', 'fum_text_domain');
    }

    public function getFields(): array
    {
        return [
            [
                'name' => static::USE_FRONTEND_LOGIN,
                'label' => __('Use frontend login', 'fum_text_domain'),
                'type' => 'checkbox',
            ],
            [
                'name' => static::HIDE_ADMIN_BAR_FOR_NORMAL_USER,
                'label' => __('Hide admin bar for normal user', 'fum_text_domain'),
                'type' => 'checkbox',
            ],
        ];
    }
}
