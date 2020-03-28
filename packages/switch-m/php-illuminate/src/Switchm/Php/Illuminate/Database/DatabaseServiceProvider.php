<?php

namespace Switchm\Php\Illuminate\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Database\DatabaseServiceProvider as BaseDatabaseServiceProvider;

class DatabaseServiceProvider extends BaseDatabaseServiceProvider
{
    public function boot(): void
    {
        Connection::resolverFor('pgsql', function (...$args) {
            return new PostgresConnection(...$args);
        });

        Connection::resolverFor('redshift', function (...$args) {
            return new RedshiftConnection(...$args);
        });

        $this->app->bind('db.connector.redshift', PostgresConnector::class);
    }
}
