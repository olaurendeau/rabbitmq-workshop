<?php

require_once __DIR__.'/vendor/autoload.php';

class APIServer extends Server\Server
{
    protected function sendVerySlowEmail($request)
    {
        $this->logger->log($request, "Request received");

        $this->rabbitMQ->publish('amq.direct', new \Swarrot\Broker\Message(json_encode($request)));

        $response = ['id' => $request['id'], 'result' => 'pending'];

        return $response;
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$logger = new \Logger\Logger('api', $rabbitMQ);

// Instantiate server & serve api
(new APIServer($logger, $rabbitMQ))->serve();
