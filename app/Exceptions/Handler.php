<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'newpassword',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * This is a JSON API: every exception is rendered as JSON. Internal details
     * (exception class, file, line, stack) are only included when APP_DEBUG is
     * enabled so that production responses never leak server internals.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function render($request, Throwable $e): JsonResponse
    {
        $status = 500;
        $headers = [];

        if ($e instanceof ValidationException) {
            $status = $e->status;
        } elseif ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $headers = $e->getHeaders();
        }

        $response = [
            'message' => $this->publicMessage($e, $status),
        ];

        if ($e instanceof ValidationException) {
            $response['errors'] = $e->errors();
        }

        if (config('app.debug')) {
            $response['exception'] = get_class($e);
            $response['code_error'] = $e->getCode();
            $response['description'] = 'file: '.$e->getFile().' in line '.$e->getLine();
            if ($e->getPrevious()) {
                $response['info'] = get_class($e->getPrevious()).': '.$e->getPrevious()->getMessage();
            }
        }

        return response()->json($response, $status, $headers);
    }

    /**
     * Choose a message that is safe to expose to API consumers.
     */
    private function publicMessage(Throwable $e, int $status): string
    {
        if ($status < 500 || config('app.debug')) {
            return $e->getMessage() !== '' ? $e->getMessage() : (JsonResponse::$statusTexts[$status] ?? 'Error');
        }

        return 'Server Error';
    }
}
