<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketEscalation;
use App\Models\ReopenRequest;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Get all tickets statistics
        $totalTickets = Ticket::count();
        $openTickets = Ticket::whereHas('status_relation', function($q) {
            $q->where('description', '!=', 'Closed');
        })->count();
        
        $closedTickets = Ticket::whereHas('status_relation', function($q) {
            $q->where('description', 'Closed');
        })->count();
        
        $urgentTickets = Ticket::whereHas('priority_relation', function($q) {
            $q->where('description', 'Urgent');
        })->whereHas('status_relation', function($q) {
            $q->where('description', '!=', 'Closed');
        })->count();

        // Escalations
        $pendingEscalations = TicketEscalation::whereNull('resolved_at')->whereNull('escalated_at')->count();
        $escalationsToday = TicketEscalation::whereDate('requested_at', today())->count();

        // Reopen Requests
        $pendingReopenRequests = ReopenRequest::where('status', 'pending')->count();
        
        // User statistics
        $totalUsers = User::count();
        $agentCount = User::where('role', 'agent')->count();
        $managerCount = User::where('role', 'manager')->count();
        $customerCount = User::where('role', 'customer')->count();

        // Tickets by status
        $ticketsByStatus = Ticket::select('status_id', DB::raw('count(*) as count'))
            ->groupBy('status_id')
            ->with('status_relation')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status_relation->description ?? 'Unknown',
                    'count' => $item->count,
                    'color' => $item->status_relation->color ?? '#6B7280'
                ];
            });

        // Tickets by priority
        $ticketsByPriority = Ticket::select('priority_id', DB::raw('count(*) as count'))
            ->groupBy('priority_id')
            ->with('priority_relation')
            ->get()
            ->map(function ($item) {
                return [
                    'priority' => $item->priority_relation->description ?? 'Unknown',
                    'count' => $item->count,
                    'color' => $item->priority_relation->color ?? '#6B7280'
                ];
            });

        // Tickets by category
        $ticketsByCategory = Ticket::select('category_id', DB::raw('count(*) as count'))
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->with('category_relation')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category_relation->description ?? 'Uncategorized',
                    'count' => $item->count,
                    'color' => $item->category_relation->color ?? '#6B7280'
                ];
            });

        // Add uncategorized count
        $uncategorizedCount = Ticket::whereNull('category_id')->count();
        if ($uncategorizedCount > 0) {
            $ticketsByCategory->push([
                'category' => 'Uncategorized',
                'count' => $uncategorizedCount,
                'color' => '#9CA3AF'
            ]);
        }

        // Recent urgent tickets
        $urgentTicketsList = Ticket::whereHas('priority_relation', function($q) {
            $q->where('description', 'Urgent');
        })->whereHas('status_relation', function($q) {
            $q->where('description', '!=', 'Closed');
        })->with(['user', 'assignedTo', 'priority_relation', 'status_relation', 'category_relation'])
          ->latest()
          ->take(5)
          ->get();

        // Pending escalations list
        $pendingEscalationsList = TicketEscalation::whereNull('resolved_at')
            ->whereNull('escalated_at')
            ->with(['ticket.user', 'ticket.priority_relation', 'requestedBy'])
            ->latest('requested_at')
            ->take(5)
            ->get();

        // Pending reopen requests
        $pendingReopenRequestsList = ReopenRequest::where('status', 'pending')
            ->with(['ticket.user', 'requestedBy'])
            ->latest('requested_at')
            ->take(5)
            ->get();

        // Unassigned tickets
        $unassignedTickets = Ticket::whereNull('assigned_to')
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->with(['user', 'priority_relation', 'status_relation', 'category_relation'])
            ->latest()
            ->take(5)
            ->get();

        // Tickets created in the last 7 days (for chart)
        $ticketTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $ticketTrend[] = [
                'date' => $date->format('M d'),
                'count' => Ticket::whereDate('created_at', $date)->count()
            ];
        }

        return view('admin.dashboard', compact(
            'totalTickets',
            'openTickets',
            'closedTickets',
            'urgentTickets',
            'pendingEscalations',
            'escalationsToday',
            'pendingReopenRequests',
            'totalUsers',
            'agentCount',
            'managerCount',
            'customerCount',
            'ticketsByStatus',
            'ticketsByPriority',
            'ticketsByCategory',
            'urgentTicketsList',
            'pendingEscalationsList',
            'pendingReopenRequestsList',
            'unassignedTickets',
            'ticketTrend'
        ));
    }
}
