<?php

require_once __DIR__.'/vendor/autoload.php';

for ($i = 0; $i < 100; $i++) {
    $rabbitmq = new \RabbitMQ\RabbitMQWrapper();
    $rabbitmq->publish('amq.direct', new \Swarrot\Broker\Message(json_encode([
        'id' => uniqid(),
        'method' => 'createDocument',
        'params' => [
            'email' => 'john.doe@tutu.com'
        ]
    ])));
}
