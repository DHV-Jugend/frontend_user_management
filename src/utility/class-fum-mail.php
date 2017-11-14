<?php

/**
 * @author Christoph Bessei
 * @version
 */
class Fum_Mail
{
    /**
     * @param $email
     * @param $subject
     * @param $message
     * @param string $reply_to
     * @throws \Exception
     */
    public static function sendMail($email, $subject, $message, $reply_to = 'info@dhv-jugend.de')
    {
        $mail = new PHPMailer();
        $mail->CharSet = 'utf-8';

        // Use SMTP if host is set
        if (!empty(get_option('fum_smtp_host'))) {
            $mail->IsSMTP();
            $mail->Host = get_option('fum_smtp_host'); // Specify main and backup server
            if (!empty(get_option('fum_smtp_username'))) {
                $mail->SMTPAuth = true;
                $mail->Username = get_option('fum_smtp_username');
                $mail->Password = get_option('fum_smtp_password');
            }
            $mail->SMTPSecure = 'tls'; // Enable encryption, 'ssl' also accepted
            $mail->Port = 587;
        }

        $mail->AddReplyTo($reply_to);

        $mail->From = get_option('fum_smtp_sender');
        $mail->FromName = get_option('fum_smtp_sender_name');
        $mail->addAddress($email);
        $mail->Sender = $reply_to;
        $mail->addBCC('anmeldungen@test.dhv-jugend.de');

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $message;

        if (defined('WRITE_MAILS_TO_FILE') && WRITE_MAILS_TO_FILE) {
            error_log('SEND MAIL');
            error_log('TO: ' . var_export($mail->getToAddresses(), true));
            error_log('Subject: ' . $mail->Subject);
            error_log('Body: ' . $mail->Body);
        } else {
            if (!$mail->send()) {
                throw new Exception("Could not sent mail, maybe your server has a problem? " . $mail->ErrorInfo);
            }
        }
    }
}