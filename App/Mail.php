<?php

namespace App;

use Mailgun\Mailgun;

/**
 * Mail class
 */
class Mail
{
    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text only content of the message
     * @param string $html HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $subject, $text, $html)
    {
        // Instantiate the SDK with API credentials
        $mg = Mailgun::create(Config::mailgunAPIKey());

        // Compose and send message
        $mg->messages()->send(Config::mailgunDomain(), [
            'from'    => 'bob@example.com',
            'to'      => $to,
            'subject' => $subject,
            'text'    => $text,
            'html'    => $html,
        ]);
    }
}
