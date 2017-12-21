<?php

use BIT\FUM\Exception\MailNotSendException;
use BIT\FUM\Settings\Tab\MailTab;

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Mail
{
    const MAIL_CHARSET = 'UTF-8';

    /**
     * @param $to
     * @param $subject
     * @param $message
     * @param array $additionalOptions
     * @return bool
     * @throws \BIT\FUM\Exception\MailNotSendException
     * @throws \phpmailerException
     */
    public static function sendHtmlMail($to, $subject, $message, $additionalOptions = [])
    {
        return static::sendMail($to, $subject, $message, true, $additionalOptions);
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array $additionalOptions
     * @return bool
     * @throws \BIT\FUM\Exception\MailNotSendException
     * @throws \phpmailerException
     */
    public static function sendPlainMail(string $to, string $subject, string $message, array $additionalOptions = [])
    {
        return static::sendMail($to, $subject, $message, false, $additionalOptions);
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param bool $isHtml
     * @param array $additionalOptions
     * @return bool
     * @throws \BIT\FUM\Exception\MailNotSendException
     * @throws \phpmailerException
     */
    protected static function sendMail(
        string $to,
        string $subject,
        string $message,
        bool $isHtml = false,
        array $additionalOptions = []
    ) {
        $phpMailer = new \PHPMailer();
        $phpMailer->CharSet = static::MAIL_CHARSET;
        $phpMailer->isHTML($isHtml);

        $phpMailer->addAddress($to);

        $phpMailer->Subject = $subject;
        $phpMailer->Body = $message;

        static::determineAndSetMailOptions($phpMailer);
        foreach ($additionalOptions as $name => $value) {
            $phpMailer->$name = $value;
        }

        if (defined('WRITE_MAILS_TO_FILE') && WRITE_MAILS_TO_FILE) {
            error_log('SEND MAIL');
            error_log('TO: ' . var_export($phpMailer->getToAddresses(), true));
            error_log('Subject: ' . $phpMailer->Subject);
            error_log('Body: ' . $phpMailer->Body);
        } else {
            try {
                $result = $phpMailer->send();
            } catch (\Throwable $e) {
                $result = false;
            }

            if (!$result) {
                throw new MailNotSendException(
                    "Could not sent mail, maybe your server has a problem? " . $phpMailer->ErrorInfo
                );
            }
        }

        return true;
    }

    /**
     * Determine mail options from wp_options and set them to $phpMailer
     *
     * @param \PHPMailer $phpMailer
     * @return \PHPMailer
     * @throws \phpmailerException
     */
    protected static function determineAndSetMailOptions(\PHPMailer $phpMailer)
    {

        $fromAddress = MailTab::get(MailTab::FROM_ADDRESS);
        $fromName = MailTab::get(MailTab::FROM_NAME);

        if (empty($fromName)) {
            $phpMailer->setFrom($fromAddress);
        } else {
            $phpMailer->setFrom($fromAddress, $fromName);
        }

        if (MailTab::get(MailTab::SMTP_USE)) {
            $phpMailer->IsSMTP();
            $phpMailer->Host = MailTab::get(MailTab::SMTP_HOST);
            $phpMailer->Port = MailTab::get(MailTab::SMTP_PORT);

            $smtpUsername = MailTab::get(MailTab::SMTP_USERNAME);
            $smtpPassword = MailTab::get(MailTab::SMTP_PASSWORD);

            if (!empty($smtpUsername) || !empty($smtpPassword)) {
                $phpMailer->SMTPAuth = true;
            }

            if (!empty($smtpUsername)) {
                $phpMailer->Username = $smtpUsername;
            }

            if (!empty($smtpPassword)) {
                $phpMailer->Password = $smtpPassword;
            }

            $smtpEncryption = MailTab::get(MailTab::SMTP_ENCRYPTION);
            if (!empty($smtpEncryption)) {
                $phpMailer->SMTPSecure = $smtpEncryption;
            }
        }

        // Handle Reply-To addresses
        $replyToAddresses = MailTab::get(MailTab::REPLY_TO_ADDRESSES);
        if (!empty($replyToAddresses)) {
            $replyToAddresses = array_map('trim', explode(',', $replyToAddresses));
            foreach ($replyToAddresses as $replyToAddress) {
                if (empty($replyToAddress)) {
                    continue;
                }
                $phpMailer->addReplyTo($replyToAddress);
            }
        }

        // Handle BCC addresses
        $bccAddresses = MailTab::get(MailTab::BCC_ADDRESSES);
        if (!empty($bccAddresses)) {
            $bccAddresses = array_map('trim', explode(',', $bccAddresses));
            foreach ($bccAddresses as $bccAddress) {
                if (empty($bccAddress)) {
                    continue;
                }
                $phpMailer->addBCC($bccAddress);
            }
        }

        return $phpMailer;
    }
}