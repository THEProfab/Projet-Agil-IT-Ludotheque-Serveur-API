<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
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

    public function render($request, Throwable $e) {
        Log::info("Erreur : " . $e);
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return ResponseBuilder::error(403, null, ['User have not permission for this request'], 403);
        }

        return parent::render($request, $e);
    }


}
