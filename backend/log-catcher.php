<?php

require_once __DIR__.'/vendor/autoload.php';

class LogProcessor implements \Swarrot\Processor\ProcessorInterface
{
    public function process(\Swarrot\Broker\Message $message, array $options)
    {
        $log = json_decode($message->getBody(), true);

        echo $log['message'];
    }
}

if (!isset($argv[1])) {
    exit("Please specify a routing key to listen on\n Example : php log-catcher.php \"#\"");
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();

$queueName = 'queue.log-catcher.'.uniqid();
$rabbitMQ->createTemporaryQueue($queueName, ['amq.topic' => $argv[1]]);

$messageProvider = $rabbitMQ->getMessageProvider($queueName);
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor')
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new LogProcessor());

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume();
