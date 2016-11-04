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
        if (rand(0,3) == 0) {
            throw new \Exception('Epic fail');
        }

        $request = json_decode($message->getBody(), true);

        $generator = new \Generator\InvoiceGenerator();
        $generator->generateAndSend($request['params']['email']);
        
        echo "Publish push\n";
        $this->rabbitMQ->publish('amq.topic', new \Swarrot\Broker\Message(json_encode([
            'id' => uniqid(),
            'channel' => $request['params']['channel'],
            'request_id' => $request['id'],
            'message' => 'Invoice generated and sent by mail to '.$request['params']['email']
        ])), 'push.'.$request['params']['channel']);


        echo "Message processed\n";
    }
}

$rabbitMQ = new \RabbitMQ\RabbitMQWrapper();
$messageProvider = $rabbitMQ->getMessageProvider('queue.document');
$stack = (new \Swarrot\Processor\Stack\Builder())
    ->push('Swarrot\Processor\Retry\RetryProcessor', $rabbitMQ->getMessagePublisher('amq.fanout'))
    ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
;

$processor = $stack->resolve(new JsonRpcResponseProcessor($rabbitMQ));

$consumer = new \Swarrot\Consumer($messageProvider, $processor);
$consumer->consume(['retry_key_pattern' => 'key_%attempt%']);
