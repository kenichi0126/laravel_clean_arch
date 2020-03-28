<?php

namespace Switchm\Php\Illuminate\Mail\Transport;

use Illuminate\Mail\Transport\Transport as BaseTransport;
use Swift_Mime_SimpleMessage;
use Swift_Transport;

abstract class Transport extends BaseTransport implements Swift_Transport
{
    private static $developmentAddress = [
        'system-test@switch-m.com',
    ];

    protected function convertDevelopmentMode(Swift_Mime_SimpleMessage $message): Swift_Mime_SimpleMessage
    {
        $address = self::$developmentAddress;

        $bodyHeader = '';

        if (!empty($message->getTo())) {
            $bodyHeader .= ' * @to: ';
            $to = implode(', ', array_keys($message->getTo()));
            $bodyHeader .= $to;

            $message->setTo($address);
        }

        if (!empty($message->getCc())) {
            $bodyHeader .= ' * @cc: ';
            $cc = implode(', ', array_keys($message->getCc()));
            $bodyHeader .= $cc;

            $message->setCc($address);
        }

        if (!empty($message->getBcc())) {
            $bodyHeader .= ' * @bcc: ';
            $bcc = implode(', ', array_keys($message->getBcc()));
            $bodyHeader .= $bcc;

            $message->setBcc($address);
        }

        if (strlen($bodyHeader) > 0) {
            $body = '';
            $body .= '/**' . PHP_EOL;
            $body .= ' * Development mode' . PHP_EOL;
            $body .= ' *' . PHP_EOL;
            $body .= $bodyHeader . PHP_EOL;
            $body .= ' */' . PHP_EOL;
            $body .= PHP_EOL;
            $body .= PHP_EOL;
            $body .= $message->getBody();

            $message->setBody($body);
        }

        return $message;
    }
}
