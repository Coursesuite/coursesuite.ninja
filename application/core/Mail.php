<?php

/**
 * Class Mail
 *
 * Handles everything regarding mail-sending.
 */
class Mail
{
    /** @var mixed variable to collect errors */
    private $error;

    /**
     * Try to send a mail by using PHP's native mail() function.
     * Please note that not PHP itself will send a mail, it's just a wrapper for Linux's sendmail or other mail tools
     *
     * Good guideline on how to send mails natively with mail():
     * @see http://stackoverflow.com/a/24644450/1114320
     * @see http://www.php.net/manual/en/function.mail.php
     */
    public function sendMailWithNativeMailFunction()
    {
        // no code yet, so we just return something to make IDEs and code analyzer tools happy
        return false;
    }

    /**
     * Try to send a mail by using SwiftMailer.
     * Make sure you have loaded SwiftMailer via Composer.
     *
     * @return bool
     */
    public function sendMailWithSwiftMailer()
    {
        // no code yet, so we just return something to make IDEs and code analyzer tools happy
        return false;
    }

    /**
     * Try to send a mail by using PHPMailer.
     * Make sure you have loaded PHPMailer via Composer.
     * Depending on your EMAIL_USE_SMTP setting this will work via SMTP credentials or via native mail()
     *
     * @param $user_email
     * @param $from_email
     * @param $from_name
     * @param $subject
     * @param $body
     * @param $optionals
     *
     * @return bool
     * @throws Exception
     * @throws phpmailerException
     */
    public function sendMailWithPHPMailer($user_email, $from_email, $from_name, $subject, $body, $optionals)
    {
        $mail = new PHPMailer;

        // you should use UTF-8 to avoid encoding issues
        $mail->CharSet = 'UTF-8';

        // if you want to send mail via PHPMailer using SMTP credentials
        if (Config::get('EMAIL_USE_SMTP')) {
            // $mail->CharSet = "text/html; charset=UTF-8;";
            // set PHPMailer to use SMTP
            $mail->IsSMTP();
            // 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors
            $mail->SMTPDebug = 2;
            // enable SMTP authentication
            $mail->SMTPAuth = Config::get('EMAIL_SMTP_AUTH');
            // encryption
            if (Config::get('EMAIL_SMTP_ENCRYPTION')) {
                $mail->SMTPSecure = Config::get('EMAIL_SMTP_ENCRYPTION');
            }
            // set SMTP provider's credentials
            $mail->Host = Config::get('EMAIL_SMTP_HOST');
            $mail->Username = Config::get('EMAIL_SMTP_USERNAME');
            $mail->Password = Config::get('EMAIL_SMTP_PASSWORD');
            $mail->Port = Config::get('EMAIL_SMTP_PORT');
        } else {
            $mail->IsMail();
        }

        // fill mail with data
        if (empty($optionals['altbody'])) {
            $mail->IsHTML(true);
            $mail->CharSet = "text/html; charset=UTF-8;";
        }
        $mail->From = $from_email;
        $mail->FromName = $from_name;
        $mail->AddAddress($user_email);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if (!empty($optionals['altBody'])) {$mail->AltBody = $optionals['altBody'];}
        // hmm
        // LoggingModel::logInternal("sending mail", print_r($mail, true));


        // try to send mail, put result status (true/false into $wasSendingSuccessful)
        // I'm unsure if mail->send really returns true or false every time, tis method in PHPMailer is quite complex
        $wasSendingSuccessful = $mail->Send();

        if ($wasSendingSuccessful) {
            return true;
        } else {
            // if not successful, copy errors into Mail's error property
            $this->error = $mail->ErrorInfo;
            LoggingModel::logInternal("error sending mail", $this->error);
            return false;
        }
    }

    /**
     * The main mail sending method, this simply calls a certain mail sending method depending on which mail provider
     * you've selected in the application's config.
     *
     * @param $user_email string email
     * @param $from_email string sender's email
     * @param $from_name string sender's name
     * @param $subject string subject
     * @param $body string full mail body text
     * @param $optionals array header,footer,altbody
     * @return bool the success status of the according mail sending method
     */
    public function sendMail($user_email, $from_email, $from_name, $subject, $body, $optionals = '')
    {
        if (Config::get('EMAIL_USED_MAILER') == "phpmailer") {
            // returns true if successful, false if not
            return $this->sendMailWithPHPMailer(
                $user_email, $from_email, $from_name, $subject, $body, $optionals
            );
        }

        if (Config::get('EMAIL_USED_MAILER') == "swiftmailer") {
            return $this->sendMailWithSwiftMailer();
        }

        if (Config::get('EMAIL_USED_MAILER') == "native") {
            return $this->sendMailWithNativeMailFunction();
        }

        if (Config::get('EMAIL_USED_MAILER') == "log") {
            LoggingModel::logInternal("Send email", $user_email, $from_email, $from_name, $subject, $body);
            return true;
            /*
        ob_start();
        var_dump($user_email, $from_email, $from_name, $subject, $body);
        $result = ob_get_clean();
        return error_log($result);
         */
        }
    }

    // sends a html email to the user using a standard template (typically used for login-type emails)
    public function emailUser($to_email, $header, $text, $link = "")
    {
        $from_email = Config::get("EMAIL_STANDARD_NO_REPLY");
        $from_name = Config::get("EMAIL_STANDARD_FROM_NAME");
        $template = KeyStore::find("emailTemplate")->get();

        $html = Text::toHtml($text);

        $email_body = Text::compileHtml($template, array(
            "email_title" => $header,
            "email_body" => $html,
            "email_link" => $link,
        ));

        return $this->sendMail($to_email, $from_email, $from_name, $header, $email_body);

    }

    /*
        we have a few standard emails, so lets just be able to call them as methods to ensure standards
    */
    public function sendPasswordReset($email, $link) {
        $header = "Reset your CourseSuite Password";
        $body = "Hi!\nSomeone (probably you) wants to reset your CourseSuite password.\nWe thought we better confirm this first, so if that's cool with you, click on the link below to start the process.";
        return $this->emailUser($email, $header, $body, $link);
    }

    public function sendPassword($email, $password, $link) {
        $header = "Your CourseSuite Password";
        $body = "Hi!\nWe have generated a new password for you, so keep it safe.\nAnyway, click on the link below to open the site, and enter this as your password:\n\n`$password`\n\nIt's case-sensitive, by the way. Ok, I'm done.";
        return $this->emailUser($email, $header, $body, $link);
    }

    public function sendVerificationAndPassword($email, $password, $link) {
        $header = "Verify your CourseSuite account";
        $body = "Hi!\nSomeone (probably you) is using this email address for their CourseSuite account! Great stuff, but we just need to make sure that it's a valid email address and that it's something you really want.\nOh yeah, you'll need a password to log in, so use this one:\n\n`$password`\n\nLastly, as part of registration we're going to pop you onto our MailChimp list - you won't neccesarily get any mail from us, and you can always manage your subscription preferences through our site.\n\nSo if it's good with you, click on the link below to verify and get started.";
        return $this->emailUser($email, $header, $body, $link);
    }

    public function resendVerification($email, $link)
    {
        $header = "Verify your CourseSuite account";
        $body = "Hello again,\nWe previously tried to send you an account verification link but we guess it got lost or something went wrong with it. It's ok, since this one seems to have found you. Please click on the link below to verify the account.\nBy the way, if you've forgotten your password (or never received it) you'll have to reset it once you are verified (we don't know your password either).";
        return $this->emailUser($email, $header, $body, $link);
    }

    public function sendGoodbye($email) {
        $header = "CourseSuite account closed";
        $body = "Oh ;-(\nWe're sorry to see you go, but we wanted to let you know that your CourseSuite account has now been deleted.\nWell, good-bye!";
        return $this->emailUser($email, $header, $body);
    }

    /**
     * The different mail sending methods write errors to the error property $this->error,
     * this method simply returns this error / error array.
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }


}
