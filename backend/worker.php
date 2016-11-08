<?php

require_once __DIR__.'/vendor/autoload.php';

class InvoiceProcessor implements \Swarrot\Processor\ProcessorInterface
{
    private $logger;

    public function __construct(\Logger\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function process(\Swarrot\Broker\Message $message, array $options)
    {
        $request = json_decode($message->getBody(), true);

        $this->logger->log($request, "Message processed");
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$logger = new \Logger\Logger('worker', $rabbitMQ);

$messageProvider = $rabbitMQ->getMessageProvider('queue.document');
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor')
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new InvoiceProcessor($logger));

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume();
