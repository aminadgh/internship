<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $stats = [
                'contacts' => \App\Models\Contact::count(),
                'groups' => \App\Models\ContactGroup::count(),
                'messages' => \App\Models\Message::count(),
                'users' => \App\Models\User::count(),
                'admins' => \App\Models\User::where('role', 'admin')->count(),
                'agents' => \App\Models\User::where('role', 'agent')->count(),
            ];
            $recentUsers = \App\Models\User::orderByDesc('created_at')->take(5)->get(['id','name','email','created_at','role']);
            $recentMessages = \App\Models\Message::orderByDesc('sent_at')->take(5)->get()->map(function ($msg) {
                $status = $msg->recipients()->pluck('status')->unique()->first() ?? 'pending';
                return [
                    'id' => $msg->id,
                    'content' => $msg->content,
                    'sent_at' => $msg->sent_at,
                    'status' => $status,
                    'user' => $msg->user->name ?? null,
                ];
            });
            // System logs: last 5 errors from laravel.log
            $logPath = storage_path('logs/laravel.log');
            $logErrors = [];
            if (file_exists($logPath)) {
                $lines = array_reverse(file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                foreach ($lines as $line) {
                    if (str_contains($line, 'local.ERROR')) {
                        $logErrors[] = $line;
                        if (count($logErrors) >= 5) break;
                    }
                }
            }
            // User activity: last 5 updated users
            $userActivity = \App\Models\User::orderByDesc('updated_at')->take(5)->get(['id','name','email','updated_at','role']);
            // Message stats: messages per day (last 7 days)
            $messageStats = \App\Models\Message::selectRaw('DATE(sent_at) as date, COUNT(*) as count')
                ->whereNotNull('sent_at')
                ->groupBy('date')
                ->orderByDesc('date')
                ->take(7)
                ->get();
            return \Inertia\Inertia::render('DashboardAdmin', [
                'stats' => $stats,
                'recentUsers' => $recentUsers,
                'recentMessages' => $recentMessages,
                'logErrors' => $logErrors,
                'userActivity' => $userActivity,
                'messageStats' => $messageStats,
                'user' => $user,
            ]);
        } else {
            $stats = [
                'contacts' => \App\Models\Contact::where('user_id', $user->id)->count(),
                'groups' => \App\Models\ContactGroup::where('user_id', $user->id)->count(),
                'messages' => \App\Models\Message::where('user_id', $user->id)->count(),
            ];
            $recentMessages = \App\Models\Message::where('user_id', $user->id)
                ->orderByDesc('sent_at')
                ->take(5)
                ->get()
                ->map(function ($msg) {
                    $status = $msg->recipients()->pluck('status')->unique()->first() ?? 'pending';
                    return [
                        'id' => $msg->id,
                        'content' => $msg->content,
                        'sent_at' => $msg->sent_at,
                        'status' => $status,
                    ];
                });
            return \Inertia\Inertia::render('DashboardAgent', [
                'stats' => $stats,
                'recentMessages' => $recentMessages,
                'user' => $user,
            ]);
        }
    }
} 