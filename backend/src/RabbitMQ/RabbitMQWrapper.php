<?php

namespace RabbitMQ;

use Swarrot\Broker\Message;
use Swarrot\Broker\MessageProvider\PeclPackageMessageProvider;
use Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher;

class RabbitMQWrapper
{
    private $connection;
    private $channel;

    public function __construct($params = null)
    {
        // Create connection & channel
        $this->connection = new \AMQPConnection($params !== null ? $params : [
            'host' => 'rabbitmq',
            'port' => 5672,
            'vhost' => '/',
            'login' => 'guest',
            'password' => 'guest'
        ]);
        $this->connection->connect();
        $this->channel = new \AMQPChannel($this->connection);
    }

    public function publish($exchangeName, Message $message, $routingKey = null)
    {
        $exchange = new \AMQPExchange($this->channel);
        $exchange->setName($exchangeName);

        $messagePublisher = new PeclPackageMessagePublisher($exchange);
        $messagePublisher->publish($message, $routingKey);
    }

    public function getMessageProvider($queueName)
    {
        $queue = new \AMQPQueue($this->channel);
        $queue->setName($queueName);

        return new PeclPackageMessageProvider($queue);
    }
}
