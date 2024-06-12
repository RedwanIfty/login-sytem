<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'identifier' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$fieldType => $input['identifier'], 'password' => $input['password']])) {
            $role = Auth::user()->role;
            if ($role == 1) {
                return redirect()->intended('/home1');
            } elseif ($role == 2) {
                return redirect()->intended('/home2');
            }

            // return redirect()->intended('/default-home');
        } else {
            $attemptsLeft = session('attempts_left', 0);
            return redirect()->route('login')
                ->with('error', 'Email/Name and Password are incorrect.')
                ->with('attempts_left', $attemptsLeft);
        }
    }

    public function logout(Request $request)
    {
        $identifier = Auth::user()->email; // Use appropriate identifier, e.g., email, username, etc.

        // Clear login attempt cache
        $key = 'login_attempts_' . $identifier;
        $lockoutKey = $key . '_lockout';
        Cache::forget($key);
        Cache::forget($lockoutKey);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
