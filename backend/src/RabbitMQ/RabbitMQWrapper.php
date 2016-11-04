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
        $this->getMessagePublisher($exchangeName)->publish($message, $routingKey);
    }

    public function getMessageProvider($queueName)
    {
        return new PeclPackageMessageProvider($this->getAMQPQueue($queueName));
    }

    public function getAMQPQueue($queueName)
    {
        $queue = new \AMQPQueue($this->channel);
        $queue->setName($queueName);

        return $queue;
    }

    public function getMessagePublisher($exchangeName)
    {
        $exchange = new \AMQPExchange($this->channel);
        $exchange->setName($exchangeName);

        return new PeclPackageMessagePublisher($exchange);
    }
}
