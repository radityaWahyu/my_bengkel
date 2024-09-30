<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Inertia\Middleware;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LoginResource;
use App\Http\Resources\CustomerLoginResource;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function handle(Request $request, Closure $next)
    {
        return tap(parent::handle($request, $next), function (Response $response) {
            if ($response->headers->has('X-Inertia')) {
                $response->setCache([
                    'no_store' => true,
                ]);
            }
        });
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(
            parent::share($request),
            [
                'auth' => [
                    'user' => fn() => Auth::check() ? new LoginResource($request->user()) : null,
                ],
                'csrf_token' => csrf_token(),
                'flash' => [
                    'error' => fn() => $request->session()->get('error'),
                    'success' => fn() => $request->session()->get('success'),
                ],
            ]
        );
    }
}
