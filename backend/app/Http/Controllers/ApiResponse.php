<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;

/**
 *
 */
trait ApiResponse
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, String $message, $httpCode = 200)
    {
        $response = [
            'error' => false,
            'code' => "0",
            'message' => $message,
            'requestedAt' => Carbon::now(),
            'respondedAt' => Carbon::now(),
            'data' => $result,
        ];

        return response()->json($response, $httpCode);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError(String $error, String|int $code, $httpCode = 400)
    {
        $response = [
            'error' => true,
            'code' => $code . "",
            'message' => $error,
            'requestedAt' => Carbon::now(),
            'respondedAt' => Carbon::now(),
            'data' => null,
        ];

        return response()->json($response, $httpCode);
    }
}