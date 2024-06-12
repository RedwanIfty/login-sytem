<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ThrottleLoginAttempts
{
    protected $maxAttempts = 3;
    protected $lockoutTime = 2; // 2 minutes
    protected $decayMinutes = 20;

    public function handle($request, Closure $next)
    {
        $identifier = $request->input('identifier');

        if (!$identifier) {
            Log::info('No identifier provided, skipping throttle.');
            return $next($request); // If no identifier is provided, proceed with the request
        }

        $key = 'login_attempts_' . $identifier;
        $lockoutKey = $key . '_lockout';

        $lockoutEndTime = Cache::get($lockoutKey);
        $lockoutTimeRemaining = $lockoutEndTime ? max(0, Carbon::parse($lockoutEndTime)->diffInSeconds(Carbon::now(), false)) : 0;

        // Check if the user is still in lockout period before proceeding
        if ($lockoutTimeRemaining > 0) {
            $minutes = ceil($lockoutTimeRemaining / 60);
            Log::info('User is still locked out.', [
                'identifier' => $identifier,
                'lockoutTimeRemaining' => $lockoutTimeRemaining,
                'lockoutEndTime' => $lockoutEndTime
            ]);
            $request->session()->flash('error', "Too many login attempts. Please try again in $minutes minutes.");
            return redirect()->route('login');
        }

        $response = $next($request);

        // After response is generated, check if the user is redirected back to login
        if ($response->isRedirection() && $request->routeIs('login')) {
            $location = $response->headers->get('Location');

            if ($location && strpos($location, route('login')) === 0) {
                $attempts = Cache::get($key, 0) + 1;
                Cache::put($key, $attempts, $this->decayMinutes * 60);

                Log::info('Login attempt.', ['identifier' => $identifier, 'attempts' => $attempts]);

                if ($attempts >= $this->maxAttempts) {
                    $lockoutEndTime = Carbon::now()->addMinutes($this->lockoutTime);
                    Cache::put($lockoutKey, $lockoutEndTime, $this->lockoutTime * 60);
                    Log::info('User locked out due to too many login attempts.', [
                        'identifier' => $identifier,
                        'lockoutEndTime' => $lockoutEndTime
                    ]);
                    $request->session()->flash('error', 'Too many login attempts. Please try again in 2 minutes.');
                    return redirect()->route('login');
                } else {
                    $request->session()->flash('attempts_left', $this->maxAttempts - $attempts);
                    Log::info('Attempts left.', ['identifier' => $identifier, 'attempts_left' => $this->maxAttempts - $attempts]);
                }
            }
        }

        // Check if the user is authenticated after the response
        if ($response->status() == 302 && Cache::has($lockoutKey)) {
            Log::info('User is still locked out after successful authentication.', [
                'identifier' => $identifier,
                'lockoutEndTime' => $lockoutEndTime
            ]);
            $request->session()->flash('error', 'Too many login attempts. Please try again at ' . Carbon::parse($lockoutEndTime)->toTimeString());
            return redirect()->route('login');
        }

        // Check if the user is authenticated after the response and clear login attempts
        if (Auth::check()) {
            Cache::forget($key);
            Cache::forget($lockoutKey);
            Log::info('Authenticated user. Clearing login attempts.', ['identifier' => $identifier]);
        }

        return $response;
    }
}
