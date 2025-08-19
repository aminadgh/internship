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
            // Admin sees all statistics
            $stats = [
                'contacts' => Contact::count(),
                'groups' => ContactGroup::count(),
                'messages' => Message::count(),
                'users' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'agents' => User::where('role', 'agent')->count(),
                'total_messages' => Message::count(),
                'total_sent' => MessageRecipient::where('status', 'sent')->count(),
                'total_delivered' => MessageRecipient::where('status', 'delivered')->count(),
                'total_read' => MessageRecipient::where('status', 'read')->count(),
                'total_failed' => MessageRecipient::whereIn('status', ['failed', 'undelivered'])->count(),
                'total_pending' => MessageRecipient::where('status', 'pending')->count(),
                // Add cost tracking statistics
                'total_cost' => '$' . number_format(Message::sum('total_cost'), 2),
                'monthly_cost' => '$' . number_format(Message::whereMonth('created_at', now()->month)->sum('total_cost'), 2),
                'avg_cost_per_message' => '$' . number_format(Message::where('total_cost', '>', 0)->avg('total_cost'), 4),
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
            // Agent sees only their statistics
            $stats = [
                'contacts' => Contact::whereHas('group', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count(),
                'groups' => ContactGroup::where('user_id', $user->id)->count(),
                'messages' => Message::where('user_id', $user->id)->count(),
                'users' => 0, // Agents don't see user count
                'total_messages' => Message::where('user_id', $user->id)->count(),
                'total_sent' => MessageRecipient::whereHas('message', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'sent')->count(),
                'total_delivered' => MessageRecipient::whereHas('message', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'delivered')->count(),
                'total_read' => MessageRecipient::whereHas('message', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'read')->count(),
                'total_failed' => MessageRecipient::whereHas('message', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->whereIn('status', ['failed', 'undelivered'])->count(),
                'total_pending' => MessageRecipient::whereHas('message', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'pending')->count(),
                // Add cost tracking statistics
                'total_cost' => '$' . number_format(Message::where('user_id', $user->id)->sum('total_cost'), 2),
                'monthly_cost' => '$' . number_format(Message::where('user_id', $user->id)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_cost'), 2),
                'avg_cost_per_message' => '$' . number_format(Message::where('user_id', $user->id)
                    ->where('total_cost', '>', 0)
                    ->avg('total_cost'), 4),
            ];
            
            // Recent Messages with more details
            $recentMessages = \App\Models\Message::where('user_id', $user->id)
                ->orderByDesc('sent_at')
                ->take(5)
                ->get()
                ->map(function ($msg) {
                    $recipients = $msg->recipients();
                    $totalRecipients = $recipients->count();
                    $sentCount = $recipients->where('status', 'sent')->count();
                    $failedCount = $recipients->where('status', 'failed')->count();
                    
                    return [
                        'id' => $msg->id,
                        'content' => $msg->content,
                        'sent_at' => $msg->sent_at,
                        'total_recipients' => $totalRecipients,
                        'sent_count' => $sentCount,
                        'failed_count' => $failedCount,
                        'success_rate' => $totalRecipients > 0 ? round(($sentCount / $totalRecipients) * 100, 1) : 0,
                    ];
                });
            
            // Recent Contacts
            $recentContacts = \App\Models\Contact::where('user_id', $user->id)
                ->with('group')
                ->orderByDesc('created_at')
                ->take(5)
                ->get(['id', 'full_name', 'phone_number', 'group_id', 'created_at']);
            
            // Recent Groups
            $recentGroups = \App\Models\ContactGroup::where('user_id', $user->id)
                ->withCount('contacts')
                ->orderByDesc('created_at')
                ->take(5)
                ->get(['id', 'name', 'area_name', 'created_at']);
            
            // Message Statistics (last 7 days)
            $messageStats = \App\Models\Message::selectRaw('DATE(sent_at) as date, COUNT(*) as count')
                ->where('user_id', $user->id)
                ->whereNotNull('sent_at')
                ->groupBy('date')
                ->orderByDesc('date')
                ->take(7)
                ->get();
            
            // Top Groups by Contact Count
            $topGroups = \App\Models\ContactGroup::where('user_id', $user->id)
                ->withCount('contacts')
                ->orderByDesc('contacts_count')
                ->take(5)
                ->get(['id', 'name', 'area_name']);
            
            return \Inertia\Inertia::render('DashboardAgent', [
                'stats' => $stats,
                'recentMessages' => $recentMessages,
                'recentContacts' => $recentContacts,
                'recentGroups' => $recentGroups,
                'messageStats' => $messageStats,
                'topGroups' => $topGroups,
                'user' => $user,
            ]);
        }
    }
} 