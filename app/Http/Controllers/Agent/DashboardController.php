<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\ReopenRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'agent') {
            abort(403, 'Unauthorized action.');
        }

        $userId = auth()->id();

        // My tickets statistics
        $myAssignedTickets = Ticket::where('assigned_to', $userId)
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->count();
        
        $myClosedTickets = Ticket::where('assigned_to', $userId)
            ->whereHas('status_relation', function($q) {
                $q->where('description', 'Closed');
            })
            ->count();
        
        $myUrgentTickets = Ticket::where('assigned_to', $userId)
            ->whereHas('priority_relation', function($q) {
                $q->where('description', 'Urgent');
            })
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->count();

        // Tickets by status (my tickets)
        $myTicketsByStatus = Ticket::where('assigned_to', $userId)
            ->select('status_id', DB::raw('count(*) as count'))
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

        // My escalation requests
        $myPendingEscalations = TicketEscalation::where('requested_by_id', $userId)
            ->whereNull('resolved_at')
            ->whereNull('escalated_at')
            ->count();

        $myApprovedEscalations = TicketEscalation::where('requested_by_id', $userId)
            ->whereNotNull('escalated_at')
            ->whereNull('resolved_at')
            ->whereHas('ticket', function($q) {
                $q->whereHas('status_relation', function($sq) {
                    $sq->where('description', '!=', 'Closed');
                });
            })
            ->count();

        // My urgent tickets list
        $urgentTicketsList = Ticket::where('assigned_to', $userId)
            ->whereHas('priority_relation', function($q) {
                $q->where('description', 'Urgent');
            })
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->with(['user', 'priority_relation', 'status_relation', 'category_relation'])
            ->latest()
            ->take(5)
            ->get();

        // My recently assigned tickets
        $recentlyAssignedTickets = Ticket::where('assigned_to', $userId)
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->with(['user', 'priority_relation', 'status_relation', 'category_relation'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // My escalation requests list
        $myEscalationsList = TicketEscalation::where('requested_by_id', $userId)
            ->whereNull('resolved_at')
            ->whereNull('escalated_at')
            ->with(['ticket.user', 'ticket.priority_relation'])
            ->latest('requested_at')
            ->take(5)
            ->get();

        // My ticket trend (last 7 days - tickets assigned to me)
        $myTicketTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $myTicketTrend[] = [
                'date' => $date->format('M d'),
                'closed' => Ticket::where('assigned_to', $userId)
                    ->whereDate('resolved_at', $date)
                    ->count()
            ];
        }

        // Tickets by priority (my tickets)
        $myTicketsByPriority = Ticket::where('assigned_to', $userId)
            ->whereHas('status_relation', function($q) {
                $q->where('description', '!=', 'Closed');
            })
            ->select('priority_id', DB::raw('count(*) as count'))
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

        // Tickets by category (my tickets)
        $myTicketsByCategory = Ticket::where('assigned_to', $userId)
            ->select('category_id', DB::raw('count(*) as count'))
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

        // Add uncategorized count for my tickets
        $myUncategorizedCount = Ticket::where('assigned_to', $userId)
            ->whereNull('category_id')
            ->count();
        if ($myUncategorizedCount > 0) {
            $myTicketsByCategory->push([
                'category' => 'Uncategorized',
                'count' => $myUncategorizedCount,
                'color' => '#9CA3AF'
            ]);
        }

        // My pending escalation requests list
        $myPendingEscalationsList = TicketEscalation::where('requested_by_id', $userId)
            ->whereNull('resolved_at')
            ->whereNull('escalated_at')
            ->with(['ticket.user', 'ticket.priority_relation', 'ticket.status_relation', 'ticket.category_relation'])
            ->latest('requested_at')
            ->take(10)
            ->get();

        // My approved escalation requests list
        $myApprovedEscalationsList = TicketEscalation::where('requested_by_id', $userId)
            ->whereNotNull('escalated_at')
            ->whereNull('resolved_at')
            ->with(['ticket.user', 'ticket.priority_relation', 'ticket.status_relation', 'ticket.category_relation'])
            ->latest('escalated_at')
            ->take(10)
            ->get();

        // My reopen requests statistics
        $myPendingReopenRequests = ReopenRequest::where('requested_by_id', $userId)
            ->where('status', 'pending')
            ->count();

        // My reopen requests list
        $myReopenRequestsList = ReopenRequest::where('requested_by_id', $userId)
            ->with(['ticket.user', 'ticket.priority_relation', 'ticket.status_relation', 'ticket.category_relation'])
            ->latest('requested_at')
            ->take(10)
            ->get();

        return view('agent.dashboard', compact(
            'myAssignedTickets',
            'myClosedTickets',
            'myUrgentTickets',
            'myTicketsByStatus',
            'myPendingEscalations',
            'myApprovedEscalations',
            'urgentTicketsList',
            'recentlyAssignedTickets',
            'myEscalationsList',
            'myTicketTrend',
            'myTicketsByPriority',
            'myTicketsByCategory',
            'myPendingEscalationsList',
            'myApprovedEscalationsList',
            'myPendingReopenRequests',
            'myReopenRequestsList'
        ));
    }
}
