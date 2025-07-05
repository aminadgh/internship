<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\ContactGroup;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contact::query();
        if ($search = $request->input('search')) {
            $query->where('full_name', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%") ;
        }
        $contacts = $query->with('group')->orderBy('full_name')->get();
        $groups = ContactGroup::all();
        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'search' => $search,
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = ContactGroup::all();
        return Inertia::render('Contacts/Form', [
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Contact store request', $request->all());
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'regex:/^(\+216|00216)?[2459][0-9]{7}$/', // Tunisian phone
                'unique:contacts,phone_number',
            ],
            'group_id' => 'required',
        ]);
        
        \Log::info('Validated data:', $validated);
        
        // Handle new group creation from map drawing
        if (str_starts_with($validated['group_id'], 'temp_')) {
            \Log::info('Creating new group from map drawing');
            // Create new group
            $groupData = $request->input('new_group');
            \Log::info('Group data:', $groupData);
            
            if (!$groupData) {
                \Log::error('Group data is missing');
                return back()->withErrors(['group_id' => 'Group data is required']);
            }
            
            $group = ContactGroup::create([
                'name' => $groupData['name'],
                'area_name' => $groupData['area_name'] ?? $groupData['name'],
                'geometry' => $groupData['geometry'],
                'user_id' => auth()->id(),
            ]);
            
            \Log::info('Created new group:', $group->toArray());
            $validated['group_id'] = $group->id;
        } else {
            // Validate existing group
            $request->validate([
                'group_id' => 'exists:contact_groups,id',
            ]);
        }
        
        $validated['user_id'] = auth()->id();
        $contact = Contact::create($validated);
        \Log::info('Created contact:', $contact->toArray());
        
        return redirect()->route('contacts.index')->with('success', 'Contact created');
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
    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        $groups = ContactGroup::all();
        return Inertia::render('Contacts/Form', [
            'contact' => $contact,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'regex:/^(\+216|00216)?[2459][0-9]{7}$/',
                'unique:contacts,phone_number,' . $contact->id,
            ],
            'group_id' => 'required',
        ]);
        
        // Handle new group creation from map drawing
        if (str_starts_with($validated['group_id'], 'temp_')) {
            // Create new group
            $groupData = $request->input('new_group');
            if (!$groupData) {
                return back()->withErrors(['group_id' => 'Group data is required']);
            }
            
            $group = ContactGroup::create([
                'name' => $groupData['name'],
                'area_name' => $groupData['area_name'] ?? $groupData['name'],
                'geometry' => $groupData['geometry'],
                'user_id' => auth()->id(),
            ]);
            
            $validated['group_id'] = $group->id;
        } else {
            // Validate existing group
            $request->validate([
                'group_id' => 'exists:contact_groups,id',
            ]);
        }
        
        $contact->update($validated);
        return redirect()->route('contacts.index')->with('success', 'Contact updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'mapping' => 'required|array',
            'mapping.full_name' => 'required|string',
            'mapping.phone_number' => 'required|string',
            'mapping.group_name' => 'required|string',
        ]);
        $file = $request->file('file');
        $mapping = $request->input('mapping');
        $userId = auth()->id();
        $path = $file->store('imports');
        $csv = Reader::createFromPath(storage_path('app/' . $path), 'r');
        $csv->setHeaderOffset(0);
        $records = Statement::create()->process($csv);
        $imported = 0;
        $failed = 0;
        $errors = [];
        foreach ($records as $i => $row) {
            $fullName = $row[$mapping['full_name']] ?? null;
            $phone = $row[$mapping['phone_number']] ?? null;
            $groupName = $row[$mapping['group_name']] ?? null;
            if (!$fullName || !$phone || !$groupName) {
                $failed++;
                $errors[] = "Row ".($i+2).": Missing required data.";
                continue;
            }
            // Find or create group
            $group = \App\Models\ContactGroup::firstOrCreate([
                'name' => $groupName,
                'user_id' => $userId,
            ]);
            // Create contact
            try {
                \App\Models\Contact::create([
                    'full_name' => $fullName,
                    'phone_number' => $phone,
                    'group_id' => $group->id,
                    'user_id' => $userId,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row ".($i+2).": ".$e->getMessage();
            }
        }
        Storage::delete($path);
        return response()->json([
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
        ]);
    }
}
