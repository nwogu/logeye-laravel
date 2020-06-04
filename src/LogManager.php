<?php

namespace Nwogu\Logeye\Laravel;

use Illuminate\Log\LogManager as IlluminateLogManager;

class LogManager extends IlluminateLogManager
{
    /**
     * Get a log driver instance.
     *
     * @param  string|null  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        $logger = parent::driver($driver);

        $this->app->make(Logeye::class)->__invoke($logger);

        return $logger;
    }
}