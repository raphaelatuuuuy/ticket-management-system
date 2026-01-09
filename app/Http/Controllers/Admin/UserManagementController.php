<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users (including all admins).
     */
    public function index(Request $request)
    {
        // Only admins can access this page
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Start query with soft deleted users included (show all users)
        $query = User::withTrashed();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'deactivated') {
                $query->whereNotNull('deleted_at');
            }
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        // Validate sort fields
        $allowedSortFields = ['name', 'email', 'created_at', 'id'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        // Get per page value
        $perPage = $request->input('per_page', 10);

        $users = $query->paginate($perPage)->withQueryString();

        return view('admin.users.manage', compact('users'));
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        // Only admins can access this page
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::withTrashed()->findOrFail($id);

        // Prevent viewing yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot view your own profile through user management. Please use your profile page.');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        // Only admins can access this page
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::withTrashed()->findOrFail($id);

        // Prevent editing yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot edit your own account. Please use your profile page.');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        // Only admins can update users
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::withTrashed()->findOrFail($id);

        // Prevent updating yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot modify your own account. Please use your profile page.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:50', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:customer,agent,manager,admin'],
            'status' => ['required', 'in:active,deactivated'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Handle status change
        if ($request->status === 'active' && $user->trashed()) {
            $user->restore();
        } elseif ($request->status === 'deactivated' && !$user->trashed()) {
            $user->delete();
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Update the user's role.
     */
    public function updateRole(Request $request, $id)
    {
        // Only admins can update roles
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::withTrashed()->findOrFail($id);

        // Prevent changing your own role
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot modify your own role.');
        }

        $request->validate([
            'role' => 'required|in:customer,agent,manager,admin',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', 'User role updated successfully.');
    }

    /**
     * Soft delete the specified user.
     */
    public function destroy($id)
    {
        // Only admins can delete users
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::withTrashed()->findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate yourself.');
        }

        $user->delete();

        return back()->with('success', 'User deactivated successfully.');
    }
}