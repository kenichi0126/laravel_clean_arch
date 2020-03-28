<?php

namespace Switchm\Php\Illuminate\Mail;

use Aws\Ses\SesClient;
use Illuminate\Mail\TransportManager as BaseTransportManager;
use Switchm\Php\Illuminate\Mail\Transport\SesTransport;

class TransportManager extends BaseTransportManager
{
    /**
     * {@inheritdoc}
     */
    protected function createSesDriver()
    {
        $config = array_merge($this->app['config']->get('services.ses', []), [
            'version' => 'latest', 'service' => 'email',
        ]);
        return new SesTransport(new SesClient(
            $this->addSesCredentials($config)
        ));
    }
}
