<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\ContactGroup;
use App\Models\Contact;
use App\Models\MessageRecipient;
use App\Models\MessageTemplate;
use App\Services\SmartGroupService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Twilio\Rest\Client;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $groups = ContactGroup::withCount('contacts')->get();
        return Inertia::render('Messages/Index', [
            'groups' => $groups,
        ]);
    }

    public function create()
    {
        $groups = ContactGroup::withCount('contacts')->get();
        return Inertia::render('Messages/Index', [
            'groups' => $groups,
        ]);
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        $query = Message::with(['sender', 'recipients.contact.group'])
            ->orderBy('created_at', 'desc');

        // Role-based filtering: Agents see only their messages, Admins see all
        if ($user->role === 'agent') {
            $query->where('user_id', $user->id);
        }
        // Admin can see all messages (no additional filter needed)

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by group
        if ($request->filled('group_id')) {
            $query->whereHas('recipients.contact', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->whereHas('recipients', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter by sender (only for admins)
        if ($user->role === 'admin' && $request->filled('sender_id')) {
            $query->where('user_id', $request->sender_id);
        }

        $messages = $query->paginate(15);

        // Get role-based statistics
        if ($user->role === 'admin') {
            // Admin sees all statistics
            $stats = [
                'total_messages' => Message::count(),
                'total_sent' => MessageRecipient::where('status', 'sent')->count(),
                'total_delivered' => MessageRecipient::where('status', 'delivered')->count(),
                'total_read' => MessageRecipient::where('status', 'read')->count(),
                'total_failed' => MessageRecipient::whereIn('status', ['failed', 'undelivered'])->count(),
                'total_pending' => MessageRecipient::where('status', 'pending')->count(),
            ];
        } else {
            // Agent sees only their statistics
            $stats = [
                'total_messages' => Message::where('user_id', $user->id)->count(),
                'total_sent' => MessageRecipient::whereHas('message', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'sent')->count(),
                'total_delivered' => MessageRecipient::whereHas('message', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'delivered')->count(),
                'total_read' => MessageRecipient::whereHas('message', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'read')->count(),
                'total_failed' => MessageRecipient::whereHas('message', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->whereIn('status', ['failed', 'undelivered'])->count(),
                'total_pending' => MessageRecipient::whereHas('message', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->where('status', 'pending')->count(),
            ];
        }

        // Get groups for filter dropdown (agents see only their groups, admins see all)
        if ($user->role === 'admin') {
            $groups = ContactGroup::orderBy('name')->get();
        } else {
            $groups = ContactGroup::where('user_id', $user->id)->orderBy('name')->get();
        }

        // Get users for admin filter dropdown
        $users = null;
        if ($user->role === 'admin') {
            $users = \App\Models\User::where('role', 'agent')->orderBy('name')->get();
        }

        return Inertia::render('Messages/History', [
            'messages' => $messages,
            'stats' => $stats,
            'groups' => $groups,
            'users' => $users,
            'user_role' => $user->role,
            'filters' => $request->only(['date_from', 'date_to', 'group_id', 'status', 'sender_id']),
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('SMS request received', [
            'all_data' => $request->all(),
            'has_schedule_type' => $request->has('schedule_type'),
            'has_schedule_date' => $request->has('schedule_date'),
            'has_schedule_time' => $request->has('schedule_time'),
            'schedule_type' => $request->input('schedule_type'),
            'schedule_date' => $request->input('schedule_date'),
            'schedule_time' => $request->input('schedule_time'),
        ]);

        $validated = $request->validate([
            'content' => 'required|string|max:1600',
            'is_emergency' => 'boolean',
            'priority' => 'required_if:is_emergency,1|in:low,normal,high,urgent',
            'broadcast_type' => 'required_if:is_emergency,1|in:group,zone,all',
            'emergency_category' => 'required_if:is_emergency,1|string|in:natural_disaster,security_alert,health_emergency,infrastructure,other',
            'schedule_type' => 'nullable|in:now,later',
            'schedule_date' => 'nullable|date|after_or_equal:today',
            'schedule_time' => 'nullable|date_format:H:i',
            'requires_approval' => 'boolean',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:contact_groups,id',
        ]);

        // Set safe defaults BEFORE any downstream usage
        $validated['is_emergency'] = (bool)($validated['is_emergency'] ?? false);
        $validated['priority'] = $validated['priority'] ?? 'normal';
        $validated['broadcast_type'] = $validated['broadcast_type'] ?? 'group';
        $validated['requires_approval'] = (bool)($validated['requires_approval'] ?? false);
        $validated['group_ids'] = $validated['group_ids'] ?? [];
        $validated['schedule_type'] = $validated['schedule_type'] ?? 'now';
        $validated['schedule_date'] = $validated['schedule_date'] ?? null;
        $validated['schedule_time'] = $validated['schedule_time'] ?? null;
        $validated['emergency_category'] = $validated['emergency_category'] ?? null;

        // Manual validation for group selection
        if ($validated['broadcast_type'] === 'group' && empty($validated['group_ids']) && !$validated['is_emergency']) {
            return back()->withErrors(['group_ids' => 'Please select at least one group for group-based broadcasts.']);
        }

        // Debug: Log the validated data
        \Log::info('SMS request validated', [
            'validated_data' => $validated,
            'schedule_type' => $validated['schedule_type'] ?? 'not_set',
            'schedule_date' => $validated['schedule_date'] ?? 'not_set',
            'schedule_time' => $validated['schedule_time'] ?? 'not_set',
        ]);

        // Process scheduling
        $scheduledAt = null;
        if ($validated['schedule_type'] === 'later' && $validated['schedule_date'] && $validated['schedule_time']) {
            $scheduledAt = $validated['schedule_date'] . ' ' . $validated['schedule_time'];
        }

        // Debug: Log the scheduling processing
        \Log::info('SMS scheduling processed', [
            'schedule_type' => $validated['schedule_type'] ?? 'not_set',
            'schedule_date' => $validated['schedule_date'] ?? 'not_set',
            'schedule_time' => $validated['schedule_time'] ?? 'not_set',
            'scheduledAt' => $scheduledAt,
            'will_schedule' => !is_null($scheduledAt),
        ]);

        // Emergency messages require approval unless user is admin
        if ($validated['is_emergency'] && auth()->user()->role !== 'admin') {
            $validated['requires_approval'] = true;
        }

        // Get recipients based on broadcast type
        $recipients = collect();
        
        // Debug: Log the broadcast type and group_ids
        \Log::info('Recipient selection starting', [
            'broadcast_type' => $validated['broadcast_type'],
            'group_ids' => $validated['group_ids'],
            'group_ids_count' => is_array($validated['group_ids']) ? count($validated['group_ids']) : 'not_array',
            'is_emergency' => $validated['is_emergency'],
        ]);

        // Debug: Check database state
        $totalContacts = \App\Models\Contact::count();
        $totalGroups = \App\Models\ContactGroup::count();
        \Log::info('Database state', [
            'total_contacts' => $totalContacts,
            'total_groups' => $totalGroups,
        ]);
        
        if (!empty($validated['group_ids'])) {
            $selectedGroups = \App\Models\ContactGroup::whereIn('id', $validated['group_ids'])->withCount('contacts')->get();
            \Log::info('Selected groups details', [
                'groups' => $selectedGroups->map(function($group) {
                    return ['id' => $group->id, 'name' => $group->name, 'contacts_count' => $group->contacts_count];
                })
            ]);
        }
        
        switch ($validated['broadcast_type']) {
            case 'all':
                // Send to ALL contacts in the system
                $recipients = \App\Models\Contact::all();
                \Log::info('Selected ALL contacts', ['count' => $recipients->count()]);
                break;
                
            case 'zone':
                // Send to contacts in specific zones (implement zone logic here)
                if (!empty($validated['group_ids'])) {
                    $recipients = \App\Models\Contact::whereIn('group_id', $validated['group_ids'])->get();
                    \Log::info('Selected ZONE contacts', ['count' => $recipients->count(), 'group_ids' => $validated['group_ids']]);
                } else {
                    \Log::warning('No group_ids provided for zone broadcast');
                }
                break;
                
            case 'group':
            default:
                // Send to specific groups
                if (!empty($validated['group_ids'])) {
                    $recipients = \App\Models\Contact::whereIn('group_id', $validated['group_ids'])->get();
                    \Log::info('Selected GROUP contacts', ['count' => $recipients->count(), 'group_ids' => $validated['group_ids']]);
                } else {
                    // Fallback: if no groups selected but broadcast type is 'group', send to ALL contacts
                    // This includes contacts without a group assignment
                    \Log::info('No groups selected, falling back to ALL contacts');
                    $recipients = \App\Models\Contact::all();
                    \Log::info('Fallback: Selected ALL contacts', ['count' => $recipients->count()]);
                }
                break;
        }

        // For emergency broadcasts to all contacts, override recipient selection
        if ($validated['is_emergency'] && $validated['broadcast_type'] === 'all') {
            $recipients = \App\Models\Contact::all();
            \Log::info('Emergency override: Selected ALL contacts', ['count' => $recipients->count()]);
        }

        \Log::info('Final recipient selection', [
            'total_recipients' => $recipients->count(),
            'broadcast_type' => $validated['broadcast_type'],
            'is_emergency' => $validated['is_emergency'],
        ]);

        if ($recipients->isEmpty()) {
            \Log::error('No recipients found for SMS', [
                'broadcast_type' => $validated['broadcast_type'],
                'group_ids' => $validated['group_ids'],
                'is_emergency' => $validated['is_emergency'],
                'total_contacts_in_db' => \App\Models\Contact::count(),
                'total_groups_in_db' => \App\Models\ContactGroup::count(),
            ]);
            return back()->withErrors(['message' => 'No contacts found for the selected broadcast type.']);
        }

        // Check if Twilio is properly configured
        if (empty(config('services.twilio.sid')) || empty(config('services.twilio.token')) || empty(config('services.twilio.from'))) {
            return back()->withErrors(['message' => 'Twilio SMS service is not properly configured. Please check your settings.']);
        }

        // Create message record
        $message = Message::create([
            'content' => $validated['content'],
            'is_emergency' => $validated['is_emergency'],
            'priority' => $validated['priority'],
            'broadcast_type' => $validated['broadcast_type'],
            'emergency_category' => $validated['emergency_category'],
            'scheduled_at' => $scheduledAt,
            'requires_approval' => $validated['requires_approval'],
            'sent_at' => $scheduledAt ? null : now(),
            'user_id' => auth()->id(),
            'total_recipients' => $recipients->count(),
            'total_cost' => 0, // Will be calculated after sending
            'cost_currency' => 'USD',
        ]);

        // If message requires approval, don't send yet
        if ($validated['requires_approval']) {
            return redirect()->route('messages.history')->with('success', 
                'Emergency message created and pending approval. Recipients: ' . $recipients->count()
            );
        }

        // If scheduled for later, don't send now
        if ($scheduledAt) {
            return redirect()->route('messages.history')->with('success', 
                'Message scheduled for ' . $scheduledAt . '. Recipients: ' . $recipients->count()
            );
        }

        // Send SMS immediately (emergency or immediate messages)
        return $this->sendMessage($message, $recipients);
    }

    /**
     * Send SMS message to recipients
     */
    private function sendMessage(Message $message, $recipients)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $from = config('services.twilio.from');

        $sentCount = 0;
        $failedCount = 0;

        // Emergency messages get priority and immediate sending
        $isEmergency = $message->isEmergency();
        
        \Log::info('Starting SMS broadcast', [
            'message_id' => $message->id,
            'is_emergency' => $isEmergency,
            'priority' => $message->priority,
            'broadcast_type' => $message->broadcast_type,
            'recipients_count' => $recipients->count()
        ]);

        foreach ($recipients as $contact) {
            $status = 'pending';
            $errorMessage = null;
            $twilioSid = null;
            $twilioStatus = null;
            
            try {
                // Log the attempt
                \Log::info('Attempting to send SMS', [
                    'message_id' => $message->id,
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone_number,
                    'is_emergency' => $isEmergency,
                    'priority' => $message->priority
                ]);
                
                // Send SMS via Twilio and capture the message object
                $twilioMessage = $twilio->messages->create(
                    $contact->phone_number,
                    ['from' => $from, 'body' => $message->content]
                );
                
                $twilioSid = $twilioMessage->sid;
                $twilioStatus = $twilioMessage->status;
                
                // Map Twilio status to our internal status
                $status = \App\Models\MessageRecipient::TWILIO_STATUS_MAP[$twilioStatus] ?? 'sent';
                
                // Count as sent if Twilio accepted the message (any status other than failed/undelivered)
                if (!in_array($twilioStatus, ['failed', 'undelivered', 'canceled'])) {
                    $sentCount++;
                }
                
                \Log::info('SMS sent successfully', [
                    'message_id' => $message->id,
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone_number,
                    'twilio_sid' => $twilioSid,
                    'twilio_status' => $twilioStatus,
                    'mapped_status' => $status
                ]);
                
            } catch (\Exception $e) {
                $status = 'failed';
                $failedCount++;
                $errorMessage = $e->getMessage();
                
                // Log the detailed error
                \Log::error('SMS sending failed', [
                    'message_id' => $message->id,
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone_number,
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'twilio_sid' => config('services.twilio.sid'),
                    'from_number' => $from
                ]);
            }

            // Calculate cost for this recipient
            $pricing = \App\Models\SmsPricing::getDefaultPricing();
            $messageLength = strlen($message->content);
            $costPerMessage = $pricing ? $pricing->calculateCost($messageLength) : 0;

            MessageRecipient::create([
                'message_id' => $message->id,
                'contact_id' => $contact->id,
                'status' => $status,
                'error_message' => $errorMessage,
                'twilio_sid' => $twilioSid,
                'twilio_status' => $twilioStatus,
                'cost' => $costPerMessage,
                'cost_currency' => 'USD',
                'country_code' => 'US', // Default to US for now
                'last_attempt_at' => now(),
            ]);
        }

        // Update message with total cost
        $message->updateCosts();

        $messageType = $isEmergency ? 'EMERGENCY BROADCAST' : 'SMS';
        $priorityText = $message->priority !== 'normal' ? " ({$message->priority} priority)" : '';
        $totalCost = $message->getFormattedTotalCost();

        return redirect()->route('messages.history')->with('success', 
            "{$messageType} sent successfully! Recipients: {$recipients->count()}, Sent: {$sentCount}, Failed: {$failedCount}{$priorityText}. Total Cost: {$totalCost}"
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Test Twilio configuration
     */
    public function testTwilio()
    {
        try {
            // Check if credentials exist
            if (empty(config('services.twilio.sid')) || empty(config('services.twilio.token')) || empty(config('services.twilio.from'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Twilio credentials are missing',
                    'config' => [
                        'sid_exists' => !empty(config('services.twilio.sid')),
                        'token_exists' => !empty(config('services.twilio.token')),
                        'from_exists' => !empty(config('services.twilio.from'))
                    ]
                ]);
            }

            // Test Twilio client creation
            $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            
            return response()->json([
                'success' => true,
                'message' => 'Twilio configuration is valid',
                'from_number' => config('services.twilio.from')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Twilio test failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ]);
        }
    }

    /**
     * Get smart suggestions for groups and templates based on content
     */
    public function getSmartSuggestions(Request $request)
    {
        $content = $request->input('input', '');
        $userId = auth()->id();
        
        $smartService = new SmartGroupService();
        
        $suggestions = [
            'groups' => $smartService->getSuggestedGroups($content, $userId),
            'combinations' => $smartService->getGroupCombinations($content, $userId),
            'templates' => $smartService->getTemplateSuggestions($content)
        ];
        
        // Return the suggestions as session data to be used on the next request
        session()->flash('smart_suggestions', $suggestions);
        
        return back()->with('smart_suggestions', $suggestions);
    }

    /**
     * Test Twilio webhook simulation (for development/testing)
     */
    public function testWebhook()
    {
        // Simulate a Twilio status callback
        $testData = [
            'MessageSid' => 'test_message_sid_123',
            'MessageStatus' => 'delivered',
            'ErrorCode' => null,
            'ErrorMessage' => null
        ];

        // Find a message recipient to test with
        $recipient = \App\Models\MessageRecipient::whereNotNull('twilio_sid')->first();
        
        if (!$recipient) {
            // Create a test recipient if none exists
            $message = \App\Models\Message::first();
            $contact = \App\Models\Contact::first();
            
            if ($message && $contact) {
                $recipient = \App\Models\MessageRecipient::create([
                    'message_id' => $message->id,
                    'contact_id' => $contact->id,
                    'status' => 'sent',
                    'twilio_sid' => 'test_message_sid_123',
                    'twilio_status' => 'sent',
                    'last_attempt_at' => now(),
                ]);
            }
        }

        if ($recipient) {
            // Update the recipient status
            $recipient->update([
                'twilio_status' => 'delivered',
                'status' => 'delivered',
                'delivered_at' => now(),
                'last_attempt_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook simulation successful',
                'recipient_updated' => [
                    'id' => $recipient->id,
                    'old_status' => 'sent',
                    'new_status' => 'delivered',
                    'delivered_at' => $recipient->delivered_at
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No recipients found to test with'
        ]);
    }

    /**
     * Display SMS cost analytics
     */
    public function costAnalytics(Request $request)
    {
        $user = auth()->user();
        
        // Get date range from request or default to last 30 days
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        $query = Message::query();
        
        // Filter by user role
        if ($user->role === 'agent') {
            $query->where('user_id', $user->id);
        }
        
        // Filter by date range
        $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        
        // Get cost statistics
        $totalCost = $query->sum('total_cost');
        $totalMessages = $query->count();
        $avgCostPerMessage = $totalMessages > 0 ? $totalCost / $totalMessages : 0;
        
        // Get cost by category
        $costByCategory = $query->selectRaw('emergency_category, SUM(total_cost) as total_cost, COUNT(*) as message_count')
            ->groupBy('emergency_category')
            ->get();
            
        // Get cost by priority
        $costByPriority = $query->selectRaw('priority, SUM(total_cost) as total_cost, COUNT(*) as message_count')
            ->groupBy('priority')
            ->get();
            
        // Get daily cost trend
        $dailyCosts = $query->selectRaw('DATE(created_at) as date, SUM(total_cost) as daily_cost, COUNT(*) as message_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Get cost by user (admin only)
        $costByUser = null;
        if ($user->role === 'admin') {
            $costByUser = Message::selectRaw('users.name, SUM(messages.total_cost) as total_cost, COUNT(*) as message_count')
                ->join('users', 'messages.user_id', '=', 'users.id')
                ->whereBetween('messages.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_cost')
                ->get();
        }
        
        return Inertia::render('Messages/CostAnalytics', [
            'stats' => [
                'total_cost' => $totalCost,
                'total_messages' => $totalMessages,
                'avg_cost_per_message' => $avgCostPerMessage,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'costByCategory' => $costByCategory,
            'costByPriority' => $costByPriority,
            'dailyCosts' => $dailyCosts,
            'costByUser' => $costByUser,
            'user_role' => $user->role,
        ]);
    }
}
