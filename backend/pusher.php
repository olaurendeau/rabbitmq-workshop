<?php

require_once __DIR__.'/vendor/autoload.php';

$loop   = React\EventLoop\Factory::create();
$pusher = new Pusher\Pusher();

// Create a consumer that will check for messages every half a second and consume up to 10 at a time.
$rabbitMQ = new RabbitMQ\RabbitMQWrapper();
$consumer = new RabbitMQ\ReactConsumer($rabbitMQ->getMessageProvider('queue.push'), $loop, 0.5, 10);
$consumer->on('consume', array($pusher, 'onPush'));


// Set up our WebSocket server for clients wanting real-time updates
$webSock = new React\Socket\Server($loop);
$webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new Ratchet\Wamp\WampServer(
                $pusher
            )
        )
    ),
    $webSock
);

$loop->run();
