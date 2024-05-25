<?php

namespace App\Mixins;

use Closure;

class ResponseFactoryMixin
{
    public function successResponse(): Closure
    {
        return fn ($data = null, $message = 'success', $code = 200) => [
            'code' => $code,
            'data' => $data,
            'message' => $message,
        ];
    }

    public function errorResponse(): Closure
    {
        return function (array|string|int $message = null, $errors = null, $code = 500) {
            return response()->json([
                'message' => $message ?? "Something went wrong!",
                'errors' => $errors,
            ], $code);
        };
    }
}
