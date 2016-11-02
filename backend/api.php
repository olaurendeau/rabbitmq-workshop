<?php

header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/vendor/autoload.php';

// Basic server, call function based on "method" passing "id" & "params" parameters
$request = json_decode(file_get_contents('php://input'), true);
try {
    $response = $request['method']($request['id'], isset($request['params']) ? $request['params'] : null);
} catch (\Exception $e) {
    http_response_code($e->getCode());
    $response = ['id' => $request['id'], 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]];
}
echo json_encode($response);

function createDocument($id, $params)
{
    exec('php generator.php '.$params['email'], $output, $returnVar);

    if ($returnVar !== 0) {
        throw new \Exception("Invoice generation failed\n".$output[0], 500);
    }

    $response = ['id' => $id, 'result' => 'success'];

    return $response;
}
