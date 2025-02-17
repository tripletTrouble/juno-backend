<?php

namespace App\Traits;

trait InteractsWithJson
{
    private function sendJson(mixed $data, int $code = 200, string $message = null, mixed $errors = null) {
        return response()->json([
            'success' => $code >= 200 && $code < 300,
            'message' => $message,
            'data' => $data,
            'errors' => $errors
        ]);
    }
}
