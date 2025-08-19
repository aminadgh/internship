<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactGroup::withCount('contacts')
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Filter by search term
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('area_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by user (for agents, only show their groups)
        if (auth()->user()->role === 'agent') {
            $query->where('user_id', auth()->id());
        }

        $groups = $query->paginate(15);

        // Get statistics
        $stats = [
            'total_groups' => ContactGroup::count(),
            'total_contacts' => \App\Models\Contact::count(),
            'groups_with_contacts' => ContactGroup::has('contacts')->count(),
            'empty_groups' => ContactGroup::doesntHave('contacts')->count(),
        ];

        return Inertia::render('Contacts/Groups/Index', [
            'groups' => $groups,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Contacts/Groups/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'area_name' => 'nullable|string|max:255',
            'geometry' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        ContactGroup::create($validated);

        return redirect()->route('groups.index')->with('success', 'Group created successfully.');
    }

    public function edit(ContactGroup $group)
    {
        // Check if user can edit this group
        if (auth()->user()->role === 'agent' && $group->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Contacts/Groups/Form', [
            'group' => $group,
        ]);
    }

    public function update(Request $request, ContactGroup $group)
    {
        // Check if user can edit this group
        if (auth()->user()->role === 'agent' && $group->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'area_name' => 'nullable|string|max:255',
            'geometry' => 'nullable|string',
        ]);

        $group->update($validated);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy(ContactGroup $group)
    {
        // Check if user can delete this group
        if (auth()->user()->role === 'agent' && $group->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if group has contacts
        if ($group->contacts()->count() > 0) {
            return back()->withErrors(['message' => 'Cannot delete group with contacts. Please move or delete contacts first.']);
        }

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}
