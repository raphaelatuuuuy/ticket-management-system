<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketEscalation;
use App\Models\ReopenRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'manager') {
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

        // Escalations pending action
        $pendingEscalations = TicketEscalation::whereNull('resolved_at')
            ->whereNull('escalated_at')
            ->whereHas('ticket', function($q) {
                $q->whereHas('status_relation', function($sq) {
                    $sq->where('description', '!=', 'Closed');
                });
            })
            ->count();
        
        // Approved escalations (escalated but not resolved)
        $approvedEscalations = TicketEscalation::whereNotNull('escalated_at')
            ->whereNull('resolved_at')
            ->whereHas('ticket', function($q) {
                $q->whereHas('status_relation', function($sq) {
                    $sq->where('description', '!=', 'Closed');
                });
            })
            ->count();
        
        // Reopen Requests
        $pendingReopenRequests = ReopenRequest::where('status', 'pending')->count();
        
        // Team statistics
        $totalAgents = User::where('role', 'agent')->count();
        
        // Tickets by agent (active agents with tickets)
        $ticketsByAgent = User::where('role', 'agent')
            ->withCount(['assignedTickets' => function($q) {
                $q->whereHas('status_relation', function($sq) {
                    $sq->where('description', '!=', 'Closed');
                });
            }])
            ->having('assigned_tickets_count', '>', 0)
            ->orderByDesc('assigned_tickets_count')
            ->take(5)
            ->get();

        // Unassigned tickets
        $unassignedTickets = Ticket::whereNull('assigned_to')
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->count();

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
            ->with(['ticket.user', 'ticket.assignedTo', 'ticket.priority_relation', 'requestedBy'])
            ->latest('requested_at')
            ->take(5)
            ->get();

        // Approved escalations list
        $approvedEscalationsList = TicketEscalation::whereNotNull('escalated_at')
            ->whereNull('resolved_at')
            ->with(['ticket.user', 'ticket.assignedTo', 'ticket.priority_relation', 'requestedBy'])
            ->latest('escalated_at')
            ->take(10)
            ->get();

        // Pending reopen requests
        $pendingReopenRequestsList = ReopenRequest::where('status', 'pending')
            ->with(['ticket.user', 'ticket.assignedTo', 'requestedBy'])
            ->latest('requested_at')
            ->take(5)
            ->get();

        // Unassigned tickets list
        $unassignedTicketsList = Ticket::whereNull('assigned_to')
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->with(['user', 'priority_relation', 'status_relation', 'category_relation'])
            ->latest()
            ->take(5)
            ->get();

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

        // Tickets created in the last 7 days
        $ticketTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $ticketTrend[] = [
                'date' => $date->format('M d'),
                'count' => Ticket::whereDate('created_at', $date)->count()
            ];
        }

        return view('manager.dashboard', compact(
            'totalTickets',
            'openTickets',
            'closedTickets',
            'urgentTickets',
            'pendingEscalations',
            'approvedEscalations',
            'pendingReopenRequests',
            'totalAgents',
            'ticketsByAgent',
            'unassignedTickets',
            'urgentTicketsList',
            'pendingEscalationsList',
            'approvedEscalationsList',
            'pendingReopenRequestsList',
            'unassignedTicketsList',
            'ticketsByStatus',
            'ticketsByCategory',
            'ticketTrend'
        ));
    }
}
