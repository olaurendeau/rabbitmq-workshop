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
     * @param string $requestId Identifier of the request
     * @param string $message message to log
     */
    public function log($requestId, $message)
    {
        // Log in backend/logs/app.log file
        file_put_contents(__DIR__.'/../../logs/app.log', sprintf(
            "[%s] %s - %s (%s)\n",
            (new \DateTime)->format('H:i:s'),
            $this->application,
            $message,
            $requestId
        ), FILE_APPEND);
    }
}
