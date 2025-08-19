<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\ContactGroup;
use App\Models\Contact;
use App\Models\MessageRecipient;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Twilio\Rest\Client;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = ContactGroup::all();
        return Inertia::render('Messages/Index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = ContactGroup::all();
        return Inertia::render('Messages/Index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:160',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:contact_groups,id',
        ]);

        DB::beginTransaction();
        try {
            $message = Message::create([
                'content' => $validated['content'],
                'sent_at' => now(),
                'user_id' => auth()->id(),
            ]);

            $recipients = Contact::whereIn('group_id', $validated['group_ids'])->get();

            $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            $from = config('services.twilio.from');

            foreach ($recipients as $contact) {
                $status = 'sent';
                try {
                    $twilio->messages->create(
                        $contact->phone_number,
                        [
                            'from' => $from,
                            'body' => $validated['content'],
                        ]
                    );
                } catch (\Exception $e) {
                    $status = 'failed';
                }
                MessageRecipient::create([
                    'message_id' => $message->id,
                    'contact_id' => $contact->id,
                    'status' => $status,
                ]);
            }
            DB::commit();
            return redirect()->route('messages.create')->with('success', 'SMS sent to all selected groups.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to send SMS: ' . $e->getMessage()]);
        }
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
}
