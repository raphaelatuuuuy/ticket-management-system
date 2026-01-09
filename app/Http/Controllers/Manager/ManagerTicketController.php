<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
use App\Models\TicketEscalation;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class ManagerTicketController extends Controller
{
    public function index()
    {
        // Managers can see all tickets except deleted ones
        $tickets = Ticket::with(['user', 'assignedTo', 'status_relation', 'priority_relation', 'category_relation'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Managers can assign to agents and other managers
        $agents = User::whereIn('role', ['agent', 'manager'])
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $statuses = Status::whereNull('deleted_at')->orderBy('id')->get();
        $priorities = Priority::whereNull('deleted_at')->orderBy('id')->get();
        $categories = Category::whereNull('deleted_at')->orderBy('id')->get();

        return view('manager.tickets.index', compact('tickets', 'agents', 'statuses', 'priorities', 'categories'));
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'priority_id' => 'nullable|exists:priorities,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        
        // Check if trying to assign to admin (escalation required)
        $assignedUser = User::find($request->assigned_to);
        if ($assignedUser && $assignedUser->role === 'admin') {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot assign to admin directly. Please use Request Escalation instead.'
            ], 403);
        }
        
        // Get "In Progress" status
        $inProgressStatus = Status::where('description', 'In Progress')->first();

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'priority_id' => $request->priority_id,
            'category_id' => $request->category_id,
            'status_id' => $inProgressStatus ? $inProgressStatus->id : $ticket->status_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket assigned successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        $status = Status::findOrFail($request->status_id);

        $updateData = ['status_id' => $request->status_id];

        // If marking as resolved, set resolved_at
        if ($status->description === 'Resolved' || $status->description === 'Closed') {
            $updateData['resolved_at'] = now();
        }

        $ticket->update($updateData);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function requestEscalation(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        $ticket = Ticket::findOrFail($id);
        
        // Create escalation record
        \App\Models\TicketEscalation::create([
            'ticket_id' => $ticket->id,
            'requested_by_id' => auth()->id(),
            'reason' => $request->reason,
            'requested_at' => now(),
        ]);
        
        // Only update status to "Escalated" if ticket is not closed
        // If closed, keep it closed until admin manually changes it
        $currentStatus = $ticket->status_relation;
        $isClosed = $currentStatus && strtolower($currentStatus->description) === 'closed';
        
        if (!$isClosed) {
            // Get "Escalated" status or create if doesn't exist
            $escalatedStatus = Status::where('description', 'Escalated')->first();
            
            // Update ticket status to Escalated only if not closed
            if ($escalatedStatus) {
                $ticket->update(['status_id' => $escalatedStatus->id]);
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Escalation request submitted successfully. An admin will review this ticket.'
        ]);
    }

    public function reopen($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            
            // Get "Open" status
            $openStatus = Status::where('description', 'Open')->first();
            
            if (!$openStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Open status not found in system.'
                ], 404);
            }
            
            // Update ticket status to Open
            $ticket->update([
                'status_id' => $openStatus->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket reopened successfully.',
                'status_id' => $openStatus->id,
                'status_name' => $openStatus->description,
                'status_color' => $openStatus->color
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reopening ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleReopenRequest($id, $reopenRequestId, $action)
    {
        try {
            // Validate action and map to database enum values
            $statusMap = [
                'approved' => 'accepted',
                'rejected' => 'declined'
            ];
            
            if (!in_array($action, ['approved', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action. Must be "approved" or "rejected".'
                ], 400);
            }

            $ticket = Ticket::findOrFail($id);
            $reopenRequest = \App\Models\ReopenRequest::findOrFail($reopenRequestId);

            // Ensure the reopen request belongs to this ticket
            if ($reopenRequest->ticket_id != $ticket->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reopen request does not belong to this ticket.'
                ], 400);
            }

            // Ensure the request is still pending
            if ($reopenRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This reopen request has already been processed.'
                ], 400);
            }

            // Get remarks from request
            $remarks = request()->input('remarks');

            // Update the reopen request with the correct enum value
            $reopenRequest->update([
                'status' => $statusMap[$action],
                'responded_by_id' => auth()->id(),
                'responded_at' => now(),
                'remarks' => $remarks,
            ]);

            // If approved, reopen the ticket
            if ($action === 'approved') {
                $openStatus = Status::where('description', 'Open')->first();
                
                if ($openStatus) {
                    $ticket->update([
                        'status_id' => $openStatus->id,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Reopen request ' . $action . ' successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing reopen request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign an unassigned escalation request to current user
     */
    public function assignEscalation(Request $request, $ticketId, $escalationId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $escalation = TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->whereNull('escalated_by_id') // Must be unassigned
                ->whereNull('resolved_by_id') // Must not be resolved
                ->firstOrFail();

            // Manager assigns the escalation to themselves
            TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->update([
                    'escalated_by_id' => auth()->id(),
                    'escalated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Escalation assigned successfully. You can now resolve or escalate to admin.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning escalation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Escalate ticket to admin
     */
    public function escalateToAdmin(Request $request, $ticketId, $escalationId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $escalation = TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->whereNull('resolved_by_id') // Not yet resolved
                ->firstOrFail();

            // Find admin user
            $admin = User::where('role', 'admin')->whereNull('deleted_at')->first();
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No admin user found in the system.'
                ], 404);
            }

            // Get 'Escalated' status
            $escalatedStatus = Status::where('description', 'Escalated')->first();
            if (!$escalatedStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Escalated status not found. Please create it first.'
                ], 404);
            }

            // Update escalation record - set to NULL so admin can assign it - use direct query update for extra safety
            TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->whereNull('resolved_by_id')
                ->update([
                    'escalated_by_id' => null,
                    'escalated_at' => now(),
                ]);

            // Update ticket: status = Escalated, keep ticket assignment for now
            $ticket->update([
                'status_id' => $escalatedStatus->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket escalated to admin successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error escalating ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve escalation - mark as resolved and reassign to original agent
     */
    public function resolveEscalation(Request $request, $ticketId, $escalationId)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:1000',
            ]);

            $ticket = Ticket::findOrFail($ticketId);
            $escalation = TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->whereNull('resolved_by_id') // Not yet resolved
                ->firstOrFail();

            // Get 'Resolved' status
            $resolvedStatus = Status::where('description', 'Resolved')->first();
            if (!$resolvedStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resolved status not found in the system.'
                ], 404);
            }

            // Get the agent who was originally assigned to the ticket
            $originalAgent = User::withTrashed()->find($ticket->assigned_to);
            if (!$originalAgent || $originalAgent->role !== 'agent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Original agent not found or invalid.'
                ], 404);
            }
            
            // Update escalation record with provided notes or default message - use direct query update for extra safety
            $resolutionNotes = $request->notes ?: 'Escalation resolved by Manager.';
            
            TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->whereNull('resolved_by_id')
                ->update([
                    'resolved_by_id' => auth()->id(),
                    'resolved_at' => now(),
                    'resolution_notes' => $resolutionNotes,
                ]);

            // Update ticket: status = Resolved, reassign to original agent
            $ticket->update([
                'status_id' => $resolvedStatus->id,
                'assigned_to' => $originalAgent->id,
                'resolved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Escalation marked as resolved successfully.',
                'status_name' => $resolvedStatus->description,
                'status_color' => $resolvedStatus->color,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resolving escalation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject escalation - reassign back to agent with comment
     */
    public function rejectEscalation(Request $request, $ticketId, $escalationId)
    {
        $request->validate([
            'rejection_notes' => 'required|string|min:10|max:500',
        ]);

        try {
            $ticket = Ticket::findOrFail($ticketId);
            $escalation = TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->whereNull('resolved_by_id') // Only allow rejection if not already resolved
                ->firstOrFail();

            // Get 'In Progress' status
            $inProgressStatus = Status::where('description', 'In Progress')->first();
            if (!$inProgressStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'In Progress status not found in the system.'
                ], 404);
            }

            // Get original agent (currently assigned)
            $originalAgent = $ticket->assignedTo;

            // Mark escalation as rejected - use direct query update for extra safety
            TicketEscalation::where('id', $escalationId)
                ->where('ticket_id', $ticketId)
                ->whereNull('resolved_by_id')
                ->update([
                    'resolved_by_id' => auth()->id(),
                    'resolved_at' => now(),
                    'resolution_notes' => 'REJECTED: ' . $request->rejection_notes,
                ]);

            // Update ticket: status = In Progress, keep assigned to agent
            $ticket->update([
                'status_id' => $inProgressStatus->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Escalation rejected. Ticket reassigned to agent.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting escalation: ' . $e->getMessage()
            ], 500);
        }
    }
}
