<?php

namespace Switchm\Php\Illuminate\Mail;

use Illuminate\Mail\MailServiceProvider as BaseMailServiceProvider;

class MailServiceProvider extends BaseMailServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected function registerSwiftTransport(): void
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new TransportManager($app);
        });
    }
}
