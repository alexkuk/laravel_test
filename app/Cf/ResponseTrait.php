<?php
namespace App\Cf;

use Illuminate\Http\Response;

trait ResponseTrait {
    private function returnJsonResponse($status, $message = '', $httpStatus = 200, $headers = [])
    {
        $headers = array_merge($headers, ['Content-Type' => 'application/json']);
        return new Response(
            json_encode(['status' => $status, 'message' => $message]),
            $httpStatus,
            $headers
        );
    }
}