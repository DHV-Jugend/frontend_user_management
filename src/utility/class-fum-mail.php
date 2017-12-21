<?php

use BIT\FUM\Exception\MailNotSendException;
use BIT\FUM\Settings\Tab\MailTab;
use BIT\FUM\Utility\StringUtility;

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Mail
{
    const MAIL_CHARSET = 'UTF-8';

    /**
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string|null $replyTo
     * @param array $additionalOptions
     * @return bool
     * @throws \BIT\FUM\Exception\MailNotSendException
     * @throws \phpmailerException
     */
    public static function sendHtmlMail(
        string $to,
        string $subject,
        string $message,
        string $replyTo = null,
        array $additionalOptions = []
    ) {
        return static::sendMail($to, $subject, $message, true, $replyTo, $additionalOptions);
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string|null $replyTo
     * @param array $additionalOptions
     * @return bool
     * @throws \BIT\FUM\Exception\MailNotSendException
     * @throws \phpmailerException
     */
    public static function sendPlainMail(
        string $to,
        string $subject,
        string $message,
        string $replyTo = null,
        array $additionalOptions = []
    ) {
        return static::sendMail($to, $subject, $message, false, $replyTo, $additionalOptions);
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string|null $replyTo CSV list of Reply-To addresses
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
        string $replyTo = null,
        bool $isHtml = false,
        array $additionalOptions = []
    ) {
        $phpMailer = new \PHPMailer();
        $phpMailer->CharSet = static::MAIL_CHARSET;
        $phpMailer->isHTML($isHtml);

        $phpMailer->addAddress($to);

        $phpMailer->Subject = $subject;
        $phpMailer->Body = $message;

        foreach (StringUtility::trimExplode(',', $replyTo) as $entry) {
            $phpMailer->addReplyTo($entry);
        }

        static::determineAndSetMailOptions($phpMailer);
        foreach ($additionalOptions as $name => $value) {
            $phpMailer->$name = $value;
        }

        if (defined('WRITE_MAILS_TO_FILE') && WRITE_MAILS_TO_FILE) {
            error_log('SEND MAIL');
            error_log('TO: ' . var_export($phpMailer->getToAddresses(), true));
            error_log('REPLY-TO:' . var_export($phpMailer->getReplyToAddresses(), true));
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

        // Set Reply-To addresses, if there are none set so far
        if (empty($phpMailer->getReplyToAddresses())) {
            $replyToAddresses = MailTab::get(MailTab::REPLY_TO_ADDRESSES);
            foreach (StringUtility::trimExplode(',', $replyToAddresses) as $replyToAddress) {
                $phpMailer->addReplyTo($replyToAddress);
            }
        }


        // Set BCC addresses
        $bccAddresses = MailTab::get(MailTab::BCC_ADDRESSES);
        foreach (StringUtility::trimExplode(',', $bccAddresses) as $bccAddress) {

            $phpMailer->addBCC($bccAddress);
        }

        return $phpMailer;
    }
}