<?php

require_once __DIR__.'/vendor/autoload.php';

class JsonRpcResponseProcessor implements \Swarrot\Processor\ProcessorInterface {

    /**
     * @var \RabbitMQ\RabbitMQWrapper
     */
    private $rabbitMQ;

    public function __construct(\RabbitMQ\RabbitMQWrapper $rabbitMQ)
    {
        $this->rabbitMQ = $rabbitMQ;
    }

    public function process(\Swarrot\Broker\Message $message, array $options)
    {
        echo "Message processed\n";
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$messageProvider = $rabbitMQ->getMessageProvider('queue');
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor')
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new JsonRpcResponseProcessor($rabbitMQ));

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume();
