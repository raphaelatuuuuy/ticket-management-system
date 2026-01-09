<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Category;
use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $statuses = Status::withTrashed()->orderBy('id')->get();
        $categories = Category::withTrashed()->orderBy('id')->get();
        $priorities = Priority::withTrashed()->orderBy('id')->get();

        return view('admin.configuration.index', compact('statuses', 'categories', 'priorities'));
    }

    // Status methods
    public function storeStatus(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'description' => 'required|string|max:100',
            'color' => 'required|string|max:50',
        ]);

        Status::create([
            'description' => $request->description,
            'color' => $request->color,
        ]);

        return redirect()->route('admin.configuration.index')->with('success', 'Status created successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $status = Status::withTrashed()->findOrFail($id);
        
        $request->validate([
            'description' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'status' => 'required|in:active,deactivated',
        ]);

        $status->update([
            'description' => $request->description,
            'color' => $request->color,
        ]);

        if ($request->status === 'active' && $status->trashed()) {
            $status->restore();
        } elseif ($request->status === 'deactivated' && !$status->trashed()) {
            $status->delete();
        }

        return redirect()->route('admin.configuration.index')->with('success', 'Status updated successfully.');
    }

    public function destroyStatus($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $status = Status::withTrashed()->findOrFail($id);
        $status->delete();

        return redirect()->route('admin.configuration.index')->with('success', 'Status deactivated successfully.');
    }

    // Category methods
    public function storeCategory(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'description' => 'required|string|max:100',
            'color' => 'required|string|max:50',
        ]);

        Category::create([
            'description' => $request->description,
            'color' => $request->color,
        ]);

        return redirect()->route('admin.configuration.index')->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::withTrashed()->findOrFail($id);
        
        $request->validate([
            'description' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'status' => 'required|in:active,deactivated',
        ]);

        $category->update([
            'description' => $request->description,
            'color' => $request->color,
        ]);

        if ($request->status === 'active' && $category->trashed()) {
            $category->restore();
        } elseif ($request->status === 'deactivated' && !$category->trashed()) {
            $category->delete();
        }

        return redirect()->route('admin.configuration.index')->with('success', 'Category updated successfully.');
    }

    public function destroyCategory($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::withTrashed()->findOrFail($id);
        $category->delete();

        return redirect()->route('admin.configuration.index')->with('success', 'Category deactivated successfully.');
    }

    // Priority methods
    public function storePriority(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'description' => 'required|string|max:100',
            'color' => 'required|string|max:50',
        ]);

        Priority::create([
            'description' => $request->description,
            'color' => $request->color,
        ]);

        return redirect()->route('admin.configuration.index')->with('success', 'Priority created successfully.');
    }

    public function updatePriority(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $priority = Priority::withTrashed()->findOrFail($id);
        
        $request->validate([
            'description' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'status' => 'required|in:active,deactivated',
        ]);

        $priority->update([
            'description' => $request->description,
            'color' => $request->color,
        ]);

        if ($request->status === 'active' && $priority->trashed()) {
            $priority->restore();
        } elseif ($request->status === 'deactivated' && !$priority->trashed()) {
            $priority->delete();
        }

        return redirect()->route('admin.configuration.index')->with('success', 'Priority updated successfully.');
    }

    public function destroyPriority($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $priority = Priority::withTrashed()->findOrFail($id);
        $priority->delete();

        return redirect()->route('admin.configuration.index')->with('success', 'Priority deactivated successfully.');
    }
}
