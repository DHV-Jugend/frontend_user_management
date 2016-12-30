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
        $mail->addAddress($email); // Add a recipient
        $mail->Sender = $reply_to;
        $mail->addBCC('anmeldungen@test.dhv-jugend.de');

        $mail->WordWrap = 50; // Set word wrap to 50 characters
        $mail->isHTML(false); // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body = $message;

        if (!$mail->send()) {
            throw new Exception("Could not sent mail, maybe your server has a problem? " . $mail->ErrorInfo);
        }
    }
}