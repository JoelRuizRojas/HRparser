<?php

/**
 * Email.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\Email;                            // Namespace declaration

class Email
{
    /**
     * @var $phpmailer Phpmailer library instace
     */
    protected $phpmailer;

    /**
     * Class constructor
     *
     * @param $email_config Email configuration of PHPmailer instance
     * @return none
     */
    public function __construct($email_config)
    {
        // Create Phpmailer instance
        $this->phpmailer = new \PHPMailer\PHPMailer\PHPMailer(true);

        // Set configurations
        $this->phpmailer->isSMTP();                                // User SMTP
        $this->phpmailer->SMTPAuth   = true;                       // Authentication on
        $this->phpmailer->Host       = $email_config['server'];    // Server address
        $this->phpmailer->SMTPSecure = $email_config['security'];  // Type of security
        $this->phpmailer->Port       = $email_config['port'];      // Port
        $this->phpmailer->Username   = $email_config['username'];  // Username
        $this->phpmailer->Password   = $email_config['password'];  // Password
        $this->phpmailer->SMTPDebug  = $email_config['debug'];     // Debug mode
        $this->phpmailer->CharSet    = 'UTF-8';                    // Character encoding
        $this->phpmailer->isHTML(true);                            // Set as HTML email
    }

    /**
     * Sends email using PHPMailer instace
     *
     * @param $from Email address of sender
     * @param $to   Email address of recipient
     * @param $subject Subject of email
     * @param $message Body of email
     * @return bool Email sent correctly flag
     */
    public function sendEmail(string $from, string $to, string $subject, string $message): bool
    {
        // Populate the email and send it
        $this->phpmailer->setFrom($from);                          // Send email from address
        $this->phpmailer->addAddress($to);                         // To email address
        $this->phpmailer->Subject = $subject;                      // Subject of email
        $this->phpmailer->Body = '<!DOCTYPE html><html lang="en-us"><body>'
                                 . $message . '</body></html>';    // Body of email
        $this->phpmailer->AltBody = strip_tags($message);          // Plain text message
        $this->phpmailer->send();                                  // Send the email
        return true;
    }
}

?>
