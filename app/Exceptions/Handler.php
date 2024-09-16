<?php

namespace App\Exceptions;

use Throwable;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            return inertia('Errors', [
                'status_code' => 503,
            ])->toResponse($request)->setStatusCode($e->getStatusCode());
        });
    }

    public function render($request, Throwable $e)
    {
        $status_code = [404, 500, 503];

        $response = parent::render($request, $e);
        $status = $response->getStatusCode();

        //dd($status);
        if (!app()->environment(['local', 'testing'])) {

            if (in_array($status, $status_code)) {
                return inertia('Errors', [
                    'status_code' => $status,
                ])->toResponse($request)->setStatusCode($status);
            } elseif ($status == 403) {
                return inertia('ErrorsAuthentication')->toResponse($request)->setStatusCode($status);
            } elseif ($status == 419) {
                redirect()->back()->with(['error' => 'Sesi anda telah habis, silahkan merefresh halaman ini.']);
            } else {
                return $response;
            }
        }

        return $response;
    }
}
