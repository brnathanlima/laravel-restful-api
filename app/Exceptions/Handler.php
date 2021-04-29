<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse(
                "Does not exist any {$model} with the specified identificator.",
                Response::HTTP_NOT_FOUND
            );
        }

        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse(
                'Unauthenticated.',
                Response::HTTP_UNAUTHORIZED
            );
        }

        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse(
                $exception->getMessage(),
                Response::HTTP_FORBIDDEN
            );
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(
                'The specified method for the request is invalid.',
                Response::HTTP_METHOD_NOT_ALLOWED
            );
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse(
                'The specified URL cannot be found.',
                Response::HTTP_NOT_FOUND
            );
        }

        if ($exception instanceof HttpException) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getStatusCode()
            );
        }

        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];

            if ($errorCode == 1451) {
                return $this->errorResponse(
                    'Cannot remove this resource permanently. It is related with any other resource.',
                    Response::HTTP_CONFLICT
                );
            }
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse(
            'We are facing an unespected problem. Please try again later',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
