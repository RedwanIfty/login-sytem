<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Http\Exceptions\ThrottleRequestsException;

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
    // public function register(): void
    // {
    //     $this->reportable(function (Throwable $e) {
    //         //
    //     });
    //     $this->renderable(function (ThrottleRequestsException $exception, $request) {
    //         if ($request->is('login')) {
    //             return redirect()->route('login')
    //                 ->with('error', 'Too many login attempts. Please try again in 1 minute.');
    //         }
    //     });
    // }
    public function register()
    {
        $this->renderable(function (ThrottleRequestsException $exception, $request) {
            if ($request->is('login')) {
                return response()->view('auth.too-many-requests', [], 429);
            }
        });
    }
}
