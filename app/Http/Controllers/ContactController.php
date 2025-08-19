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

        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'phone_number' => [
                    'required',
                    'string',
                    'regex:/^(\+216|00216)?[0-9]{8}$/',
                    'unique:contacts,phone_number',
                ],
                'group_id' => 'required|string',
                'new_group' => 'nullable|array',
            ]);

            \Log::info('Validated data', ['data' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors());
        }

        // Handle group assignment
        if (str_starts_with($validated['group_id'], 'temp_')) {
            \Log::info('Creating new group from map drawing');
            $groupData = $validated['new_group'] ?? null;
            \Log::info('Group data', ['data' => $groupData]);

            if (!$groupData) {
                \Log::error('Group data is missing for temporary group');
                return back()->withErrors(['group_id' => 'Group data is required for new area']);
            }

            try {
                $group = ContactGroup::create([
                    'name' => $groupData['name'],
                    'area_name' => $groupData['area_name'] ?? $groupData['name'],
                    'geometry' => $groupData['geometry'],
                    'user_id' => auth()->id(),
                ]);

                \Log::info('Created new group successfully', ['group_id' => $group->id, 'name' => $group->name]);
                $validated['group_id'] = $group->id;
            } catch (\Exception $e) {
                \Log::error('Failed to create group', ['error' => $e->getMessage(), 'data' => $groupData]);
                return back()->withErrors(['group_id' => 'Failed to create new group: ' . $e->getMessage()]);
            }
        } else {
            // Validate that the group exists
            $request->validate([
                'group_id' => 'exists:contact_groups,id',
            ]);
        }

        $validated['user_id'] = auth()->id();
        
        try {
            $contact = Contact::create($validated);
            \Log::info('Created contact successfully', ['contact_id' => $contact->id, 'name' => $contact->full_name]);
            return redirect()->route('contacts.index')->with('success', 'Contact created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create contact', ['error' => $e->getMessage(), 'data' => $validated]);
            return back()->withErrors(['error' => 'Failed to create contact: ' . $e->getMessage()]);
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
        try {
            \Log::info('CSV import started', [
                'user_id' => auth()->id(),
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize()
            ]);
            
            $request->validate([
                'file' => 'required|file|max:2048', // Accept any file, we'll validate content type
                'mapping' => 'required|array',
                'mapping.full_name' => 'required|string',
                'mapping.phone_number' => 'required|string',
                'mapping.group_name' => 'required|string',
            ]);
            
            // Additional file validation
            $file = $request->file('file');
            $allowedMimeTypes = ['text/csv', 'text/plain', 'application/csv', 'application/octet-stream'];
            $fileMimeType = $file->getMimeType();
            
            \Log::info('File validation', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $fileMimeType,
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize()
            ]);
            
            if (!in_array($fileMimeType, $allowedMimeTypes) && $file->getClientOriginalExtension() !== 'csv') {
                return back()->withErrors([
                    'file' => 'Invalid file type. Please upload a CSV file. Detected type: ' . $fileMimeType
                ]);
            }
            $mapping = $request->input('mapping');
            $userId = auth()->id();
            
            \Log::info('CSV import mapping', ['mapping' => $mapping]);
            
            // Read file content directly instead of storing to disk
            $fileContent = file_get_contents($file->getRealPath());
            
            \Log::info('File reading details', [
                'file_size' => strlen($fileContent),
                'first_100_chars' => substr($fileContent, 0, 100)
            ]);
            
            // Parse CSV content manually for better reliability
            $lines = explode("\n", $fileContent);
            $headers = str_getcsv(array_shift($lines)); // Get first line as headers
            
            \Log::info('CSV headers detected', ['headers' => $headers]);
            
            if (empty($headers) || count($headers) < 3) {
                return back()->withErrors([
                    'file' => 'Invalid CSV format. File must have at least 3 columns: Full Name, Phone Number, and Group.'
                ]);
            }
            
            $records = [];
            foreach ($lines as $lineNumber => $line) {
                if (trim($line) === '') continue; // Skip empty lines
                
                $row = str_getcsv($line);
                \Log::info('Processing CSV row', [
                    'line_number' => $lineNumber + 2,
                    'row_data' => $row,
                    'row_count' => count($row)
                ]);
                
                if (count($row) >= 3) {
                    $records[] = array_combine($headers, $row);
                } else {
                    \Log::warning('Skipping invalid row', [
                        'line_number' => $lineNumber + 2,
                        'row_data' => $row,
                        'expected_columns' => 3,
                        'actual_columns' => count($row)
                    ]);
                }
            }
            
            \Log::info('CSV parsing completed', [
                'total_lines' => count($lines),
                'valid_records' => count($records),
                'headers' => $headers
            ]);
            
            $imported = 0;
            $failed = 0;
            $errors = [];
        foreach ($records as $i => $row) {
            $fullName = trim($row[$mapping['full_name']] ?? '');
            $phone = trim($row[$mapping['phone_number']] ?? '');
            $groupName = trim($row[$mapping['group_name']] ?? '');
            
            // Validate required fields
            if (empty($fullName) || empty($phone) || empty($groupName)) {
                $failed++;
                $errors[] = "Row " . ($i + 2) . ": Missing required data (Name: '$fullName', Phone: '$phone', Group: '$groupName')";
                continue;
            }
            
            // Validate phone number format (Tunisian format)
            if (!preg_match('/^(\+216|00216)?[2459][0-9]{7}$/', $phone)) {
                $failed++;
                $errors[] = "Row " . ($i + 2) . ": Invalid phone number format '$phone'. Must be Tunisian format (e.g., +21612345678)";
                continue;
            }
            
            // Check if phone number already exists
            if (\App\Models\Contact::where('phone_number', $phone)->exists()) {
                $failed++;
                $errors[] = "Row " . ($i + 2) . ": Phone number '$phone' already exists";
                continue;
            }
            
            // Find or create group
            try {
                // Normalize group name to prevent duplicates
                $normalizedGroupName = trim(strtolower($groupName));
                
                // First try to find existing group with case-insensitive search
                $group = \App\Models\ContactGroup::where('user_id', $userId)
                    ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedGroupName])
                    ->first();
                
                if (!$group) {
                    // Create new group only if none exists
                    $group = \App\Models\ContactGroup::create([
                        'name' => trim($groupName), // Keep original case for display
                        'area_name' => trim($groupName),
                        'user_id' => $userId,
                        'geometry' => null, // No geometry for CSV imports
                    ]);
                    
                    \Log::info('Created new group from CSV import', [
                        'group_id' => $group->id,
                        'name' => $group->name,
                        'normalized_name' => $normalizedGroupName
                    ]);
                } else {
                    \Log::info('Found existing group for CSV import', [
                        'group_id' => $group->id,
                        'name' => $group->name,
                        'searched_name' => $groupName,
                        'normalized_name' => $normalizedGroupName
                    ]);
                }
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($i + 2) . ": Failed to create/find group '$groupName': " . $e->getMessage();
                \Log::error('Group creation/finding failed in CSV import', [
                    'group_name' => $groupName,
                    'normalized_name' => $normalizedGroupName,
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
            
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
                $errors[] = "Row " . ($i + 2) . ": Failed to create contact: " . $e->getMessage();
            }
        }
            // No file to delete since we read content directly
            
            \Log::info('CSV import completed', [
                'imported' => $imported,
                'failed' => $failed,
                'total_rows' => $imported + $failed
            ]);
            
            return back()->with([
                'import_success' => true,
                'imported' => $imported,
                'failed' => $failed,
                'import_errors' => $errors,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('CSV import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // No file to clean up since we read content directly
            
            return back()->withErrors([
                'import' => 'Import failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clean up duplicate groups (helper method for fixing existing data)
     */
    public function cleanupDuplicateGroups()
    {
        try {
            $userId = auth()->id();
            $duplicates = \App\Models\ContactGroup::where('user_id', $userId)
                ->selectRaw('LOWER(TRIM(name)) as normalized_name, COUNT(*) as count')
                ->groupBy('normalized_name')
                ->having('count', '>', 1)
                ->get();

            $cleaned = 0;
            foreach ($duplicates as $duplicate) {
                $groups = \App\Models\ContactGroup::where('user_id', $userId)
                    ->whereRaw('LOWER(TRIM(name)) = ?', [$duplicate->normalized_name])
                    ->orderBy('id')
                    ->get();

                // Keep the first group, merge contacts from others
                $keepGroup = $groups->first();
                $deleteGroups = $groups->slice(1);

                foreach ($deleteGroups as $deleteGroup) {
                    // Move contacts to the kept group
                    \App\Models\Contact::where('group_id', $deleteGroup->id)
                        ->update(['group_id' => $keepGroup->id]);
                    
                    // Delete the duplicate group
                    $deleteGroup->delete();
                    $cleaned++;
                }
            }

            return back()->with('success', "Cleaned up $cleaned duplicate groups successfully");
        } catch (\Exception $e) {
            \Log::error('Failed to cleanup duplicate groups', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to cleanup duplicate groups: ' . $e->getMessage()]);
        }
    }
}
