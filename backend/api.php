<?php

header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/vendor/autoload.php';

// Basic server, call function based on "method" passing "id" & "params" parameters
$request = json_decode(file_get_contents('php://input'), true);
echo json_encode($request['method']($request['id'], isset($request['params']) ? $request['params'] : null));

function createDocument($id, $params)
{
    exec('php generator.php '.$params['name'].'.pdf', $output);

    $response = ['id' => $id, 'result' => $output[0]];

    return $response;
}
