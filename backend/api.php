<?php

header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/vendor/autoload.php';

// Basic server, call function based on "method" passing "id" & "params" parameters
$request = json_decode(file_get_contents('php://input'), true);
try {
    $response = $request['method']($request);
} catch (\Exception $e) {
    http_response_code(500);
    $response = ['id' => $request['id'], 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]];
}
echo json_encode($response);

function createDocument($request)
{
    $rabbitmq = new \RabbitMQ\RabbitMQWrapper();
    $rabbitmq->publish('amq.direct', new \Swarrot\Broker\Message(json_encode($request)));

    $response = ['id' => $request['id'], 'result' => 'pending'];

    return $response;
}
