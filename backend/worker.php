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
        $request = json_decode($message->getBody(), true);

        $generator = new \Generator\InvoiceGenerator();
        $generator->generateAndSend($request['params']['email']);
        
        echo "Message processed\n";
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$messageProvider = $rabbitMQ->getMessageProvider('queue.document');
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\ExceptionCatcher\ExceptionCatcherProcessor')
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new JsonRpcResponseProcessor($rabbitMQ));

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume();
