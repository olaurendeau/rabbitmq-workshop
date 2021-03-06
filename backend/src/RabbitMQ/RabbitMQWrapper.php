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
        $this->connect();
    }

    private function connect()
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
        $queue = new \AMQPQueue($this->channel);
        $queue->setName($queueName);

        // Create queue in case it does not exists
        try {
            $message = $queue->get();
            if ($message) {
                $queue->nack($message->getDeliveryTag(), AMQP_REQUEUE);
            }
        } catch (\AMQPQueueException $e) {
            $this->connect();

            $queue = new \AMQPQueue($this->channel);
            $queue->setName($queueName);
            $queue->setFlags(AMQP_DURABLE);
            $queue->declareQueue();
        }

        return new PeclPackageMessageProvider($queue);
    }

    public function getMessagePublisher($exchangeName)
    {
        $exchange = new \AMQPExchange($this->channel);
        $exchange->setName($exchangeName);

        return new PeclPackageMessagePublisher($exchange);
    }

    public function createTemporaryQueue($queueName, $bindings)
    {
        $queue = new \AMQPQueue($this->channel);
        $queue->setName($queueName);
        $queue->setArgument('x-expires', 10000);
        $queue->declareQueue();
        foreach ($bindings as $exchangeName => $routingKey) {
            $queue->bind($exchangeName, $routingKey);
        }
    }
}
