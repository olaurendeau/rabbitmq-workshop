<?php

header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/vendor/autoload.php';

use Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher;

// Basic server, call function based on "method" passing "id" & "params" parameters
$request = json_decode(file_get_contents('php://input'), true);
echo json_encode($request['method']($request['id'], isset($request['params']) ? $request['params'] : null));

function createDocument($id, $params)
{
    $name = $params['name'].'.pdf';
    exec('php generator.php '.$name, $output);

    $response = ['id' => $id, 'result' => $output[0]];

    return $response;
}

/*
function publishMessage($message)
{
    $publisher = new PeclPackageMessagePublisher();
    $publisher
}
*/
