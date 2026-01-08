<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiExceptionHandler
{
    public static function handle(Throwable $e, Request $request): \Illuminate\Http\JsonResponse
    {
        // --- 1. Determine status and message ---
        $statusCode = 500;
        $message = 'Server Error';
        $errors = null;

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Resource not found';
        } elseif ($e instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Unauthenticated';
        } elseif ($e instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Validation Error';
            $errors = $e->errors();
        } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $statusCode = $e->getStatusCode();
            $message = $e->getMessage() ?: 'Error';
        } else {
            $message = $e->getMessage();
        }

        // --- 2. Log the exception with full context ---
        Log::channel('api_errors')->error('API Exception', [
            'status_code' => $statusCode,
            'message' => $message,
            'exception_class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'stack' => $e->getTraceAsString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->except(['password', 'password_confirmation']),
            'user_id' => Auth::id(),
        ]);

        // --- 3. Prepare JSON response ---
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
