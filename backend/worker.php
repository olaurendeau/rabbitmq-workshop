<?php

require_once __DIR__.'/vendor/autoload.php';

class JsonRpcResponseProcessor implements \Swarrot\Processor\ProcessorInterface
{
    private $logger;

    public function __construct(\Logger\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function process(\Swarrot\Broker\Message $message, array $options)
    {
        if (rand(0,3) == 0) {
            throw new \Exception('Epic fail');
        }

        $request = json_decode($message->getBody(), true);

        $generator = new \Generator\InvoiceGenerator($this->logger);
        $generator->generateAndSend($request['id'], $request['params']['email']);

        $this->logger($request['id'], "Message processed");
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$logger = new \Logger\Logger('worker', $rabbitMQ);

$messageProvider = $rabbitMQ->getMessageProvider('queue.document');
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\Retry\RetryProcessor', $rabbitMQ->getMessagePublisher('amq.fanout'))
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new JsonRpcResponseProcessor($logger));

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume(['retry_key_pattern' => 'key_%attempt%']);
