<?php

require_once __DIR__.'/vendor/autoload.php';

class JsonRpcResponseProcessor implements \Swarrot\Processor\ProcessorInterface {

    public function process(\Swarrot\Broker\Message $message, array $options)
    {
        $request = json_decode($message->getBody(), true);

        $generator = new \Generator\InvoiceGenerator();
        $generator->generateAndSend($request['params']['email']);
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$messageProvider = $rabbitMQ->getMessageProvider('queue.document');
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor')
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new JsonRpcResponseProcessor());

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume();
