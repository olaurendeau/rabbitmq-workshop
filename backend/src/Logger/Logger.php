<?php

namespace Logger;

use RabbitMQ\RabbitMQWrapper;

class Logger
{
    private $application;

    private $rabbitMQ;

    public function __construct($application, RabbitMQWrapper $rabbitMQ)
    {
        $this->application = $application;
        $this->rabbitMQ = $rabbitMQ;
    }

    /**
     * @param array $request Request
     * @param string $message message to log
     */
    public function log($request, $message)
    {
        $log = sprintf(
            "[%s] %s [%s] %s\n",
            (new \DateTime)->format('H:i:s'),
            $request['id'],
            $this->application,
            ucfirst($message)
        );
        // Log in backend/logs/app.log file
        file_put_contents(__DIR__.'/../../logs/app.log', $log, FILE_APPEND);
    }
}
