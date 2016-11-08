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

        if (rand(0,3) == 0) {
            $this->logger->log($request, "Treatment failed");
            throw new \Exception('Epic fail');
        }

        $generator = new \Mailer\Sender($this->logger);
        $generator->sendVerySlowEmail($request);

        $this->logger->log($request, "Message processed");
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$logger = new \Logger\Logger('worker', $rabbitMQ);

$messageProvider = $rabbitMQ->getMessageProvider('queue.document');
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\Retry\RetryProcessor', $rabbitMQ->getMessagePublisher('amq.fanout'))
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new InvoiceProcessor($logger));

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume(['retry_key_pattern' => 'key_%attempt%']);
