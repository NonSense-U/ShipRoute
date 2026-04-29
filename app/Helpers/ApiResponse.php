<?php
namespace App\Helpers;

class ApiResponse
{
    public static function success(string $message = 'Success', $data = null, int $statusCode = 200)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public static function fail(string $message = 'Error', $errors = null, int $statusCode = 500)
    {
        $response = [
            'status' => 'fail',
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
