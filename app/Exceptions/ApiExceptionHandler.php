<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Throwable;

class ApiExceptionHandler
{
    /**
     * Handle the exception and return a custom JSON response.
     */
    public static function handle(Throwable $e, Request $request): Response
    {
        // Only handle API requests or if specific conditions met
        // For this task, we assume all requests handled by this handler are targeted for API response
        // But we can check $request->is('api/*') in bootstrap/app.php before calling this.

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
        }

        else {
            $message = $e->getMessage();
        }

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
