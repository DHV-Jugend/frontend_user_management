<?php
namespace BIT\FUM\Settings\Tab;

use C3\WpSettings\Tab\AbstractTab;

/**
 * @author Christoph Bessei
 */
class MailTab extends AbstractTab
{
    const SMTP_USE = \Fum_Conf::PREFIX . 'smtp_use';
    const SMTP_HOST = \Fum_Conf::PREFIX . 'smtp_host';
    const SMTP_PORT = \Fum_Conf::PREFIX . 'smtp_port';
    const SMTP_ENCRYPTION = \Fum_Conf::PREFIX . 'smtp_encryption';
    const SMTP_USERNAME = \Fum_Conf::PREFIX . 'smtp_username';
    const SMTP_PASSWORD = \Fum_Conf::PREFIX . 'smtp_password';
    const FROM_ADDRESS = \Fum_Conf::PREFIX . 'from_address';
    const FROM_NAME = \Fum_Conf::PREFIX . 'from_name';
    const REPLY_TO_ADDRESSES = \Fum_Conf::PREFIX . 'reply_to_addresses';
    const BCC_ADDRESSES = \Fum_Conf::PREFIX . 'bcc_addresses';


    public function getId(): string
    {
        return \Fum_Conf::PREFIX . 'mail';
    }

    public function getTitle(): string
    {
        return __('Mail', 'fum_text_domain');
    }

    public function getFields(): array
    {
        return [
            [
                'name' => static::SMTP_USE,
                'label' => __('Use SMTP', 'fum_text_domain'),
                'type' => 'checkbox',
            ],
            [
                'name' => static::SMTP_HOST,
                'label' => __('SMTP Host', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => static::SMTP_PORT,
                'label' => __('SMTP Port', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => static::SMTP_ENCRYPTION,
                'label' => __('SMTP Encryption', 'fum_text_domain'),
                'type' => 'select',
                'options' => [
                    '' => 'None',
                    'tls' => 'TLS',
                    'ssl' => 'SSL',
                ],
            ],
            [
                'name' => static::SMTP_USERNAME,
                'label' => __('SMTP Username', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => static::SMTP_PASSWORD,
                'label' => __('SMTP Password', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => static::FROM_ADDRESS,
                'label' => __('From address', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => static::FROM_NAME,
                'label' => __('From name', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => static::REPLY_TO_ADDRESSES,
                'label' => __('Reply-To addresses (CSV)', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => static::BCC_ADDRESSES,
                'label' => __('BCC addresses (CSV) - Useful to collect mails for debugging', 'fum_text_domain'),
                'type' => 'text',
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ];
    }
}
