<?php

namespace Switchm\Php\Illuminate\Mail\Transport;

use App;
use Aws\Ses\SesClient;
use Swift_Mime_SimpleMessage;
use Switchm\Php\Illuminate\Mail\Transport\Transport as BaseTransport;

class SesTransport extends BaseTransport
{
    /**
     * The Amazon SES instance.
     *
     * @var \Aws\Ses\SesClient
     */
    protected $ses;

    /**
     * SES Arn.
     *
     * @var string
     */
    private static $sesArn = 'arn:aws:ses:us-east-1:406795679565:identity/switch-m.com';

    /**
     * Create a new SES transport instance.
     *
     * @param \Aws\Ses\SesClient $ses
     */
    public function __construct(SesClient $ses)
    {
        $this->ses = $ses;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        if (!App::environment('production')) {
            $this->convertDevelopmentMode($message);
        }

        $this->sendRawEmail($message);

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * {@inheritdoc}
     */
    private function sendEmail(Swift_Mime_SimpleMessage $message)
    {
        $messageId = $this->ses->sendEmail([
            'Source' => key($message->getSender() ?: $message->getFrom()),
            'Destination' => [
                'ToAddresses' => !empty($message->getTo()) ? array_keys($message->getTo()) : [],
                'CcAddresses' => !empty($message->getCc()) ? array_keys($message->getCc()) : [],
                'BccAddresses' => !empty($message->getBcc()) ? array_keys($message->getBcc()) : [],
            ],
            'Message' => [
                'Subject' => [
                    'Charset' => $message->getCharset(),
                    'Data' => $message->getSubject(),
                ],
                'Body' => [
                    'Text' => [
                        'Charset' => $message->getCharset(),
                        'Data' => $message->getBody(),
                    ],
                ],
            ],
            //'ReplyToAddresses' => !empty($message->getReplyTo()) ? array_keys($message->getReplyTo()) : [],
            //'ReturnPath' => $message->getReturnPath(),
            'SourceArn' => self::$sesArn,
        ])->get('MessageId');

        return $messageId;
    }

    private function sendRawEmail(Swift_Mime_SimpleMessage $message)
    {
        $headers = $message->getHeaders();

        $headers->addTextHeader('X-SES-Message-ID', $this->ses->sendRawEmail([
            'RawMessage' => [
                'Data' => $message->toString(),
            ],
            'FromArn' => self::$sesArn,
        ])->get('MessageId'));

        return $headers;
    }
}
