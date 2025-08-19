<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        // Force redirect URL to current host to avoid 127.0.0.1 vs LAN IP mismatches
        $redirectUrl = url('/auth/google/callback');
        \Log::info('Google OAuth redirect init', [
            'redirect_url' => $redirectUrl,
            'host' => $request->getHost(),
            'app_url' => config('app.url'),
        ]);
        return Socialite::driver('google')->redirectUrl($redirectUrl)->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            \Log::info('Google OAuth callback started');
            
            // Ensure Socialite uses the same redirect URL as the initial request
            $redirectUrl = url('/auth/google/callback');
            \Log::info('Google OAuth callback using redirect', [
                'redirect_url' => $redirectUrl,
            ]);
            $googleUser = Socialite::driver('google')->redirectUrl($redirectUrl)->user();
            \Log::info('Google user data received', [
                'id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name
            ]);

            // Check if user already exists by Google ID or email
            $user = User::where('google_id', $googleUser->id)
                       ->orWhere('email', $googleUser->email)
                       ->first();

            if (!$user) {
                \Log::info('Creating new user from Google OAuth');
                
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(uniqid()), // Generate random password
                    'phone_number' => null, // Set to null for Google users
                    'company' => null, // Set to null for Google users
                    'role' => 'agent', // Set default role
                ]);
                
                // Mark Google users as verified (Google already verified their email)
                $user->markEmailAsVerified();

                \Log::info('User created successfully', ['user_id' => $user->id]);

                // Assign default role (agent)
                $agentRole = Role::where('name', 'agent')->first();
                if ($agentRole) {
                    $user->assignRole($agentRole);
                    \Log::info('Agent role assigned to user');
                } else {
                    \Log::warning('Agent role not found');
                }
            } else {
                \Log::info('Existing user found', ['user_id' => $user->id]);
            }

            // Log in the user
            Auth::login($user);
            \Log::info('User logged in successfully', ['user_id' => $user->id]);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            \Log::error('Google OAuth callback failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect('/login')->withErrors(['error' => 'Google authentication failed: ' . $e->getMessage()]);
        }
    }
}
