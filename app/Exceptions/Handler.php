<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Custom response handler for api routes in case caller did not sent accept json header
     *
     * Author: arehman
     *
     * @param $request
     * @param $e
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    private function handleResponse($request, $e)
    {
        return $request->is('api/*') || $request->expectsJson()
            ? $this->prepareJsonResponse($request, $e)
            : $this->prepareResponse($request, $e);
    }

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

        // For some reason page 404 always returns http response
        // Convert response to json if API route or expectsJson
        $this->renderable(function (NotFoundHttpException $e, $request) {
            // if there is a message then it means model not found otherwise it means page not found
            if ($e->getMessage()) {
                $msg = 'Record not found.';
                $e = new NotFoundHttpException($msg, $e);
            }

            return $this->handleResponse($request, $e);
        });

        // Custom error message for authentication
        $this->renderable(function (AuthenticationException $e, $request) {
            $msg = 'Un authorized access or login expired, please log in and try again';
            $e = new AuthenticationException($msg, $e->guards(), $e->redirectTo());

            return $this->unauthenticated($request, $e);
        });
    }
}
