<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class AgentTicketController extends Controller
{
    public function index()
    {
        // Agents can see tickets assigned to them, excluding deleted ones
        $tickets = Ticket::with(['user', 'assignedTo', 'status_relation', 'priority_relation', 'category_relation'])
            ->where('assigned_to', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        $statuses = Status::whereNull('deleted_at')->orderBy('id')->get();

        return view('agent.tickets.index', compact('tickets', 'statuses'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        
        // Check if agent is assigned to this ticket
        if ($ticket->assigned_to !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
        
        // Check if agent is assigned to this ticket
        if ($ticket->assigned_to !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Create escalation record
        \App\Models\TicketEscalation::create([
            'ticket_id' => $ticket->id,
            'requested_by_id' => auth()->id(),
            'reason' => $request->reason,
            'requested_at' => now(),
        ]);
        
        // Only update status to "Escalated" if ticket is not closed
        // If closed, keep it closed until manager/admin manually changes it
        $currentStatus = $ticket->status_relation;
        $isClosed = $currentStatus && strtolower($currentStatus->description) === 'closed';
        
        if (!$isClosed) {
            // Get "Escalated" status
            $escalatedStatus = Status::where('description', 'Escalated')->first();
            
            // Update ticket status to Escalated only if not closed
            if ($escalatedStatus) {
                $ticket->update(['status_id' => $escalatedStatus->id]);
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Escalation request submitted successfully. A manager or admin will review this ticket.'
        ]);
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
     * Verify escalation fix and close the ticket
     */
    public function verifyAndClose($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            
            // Check if agent has permission: either assigned to ticket OR requested an escalation
            $hasEscalation = \App\Models\TicketEscalation::where('ticket_id', $ticket->id)
                ->where('requested_by_id', auth()->id())
                ->exists();
            
            if (!$hasEscalation && $ticket->assigned_to !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You cannot close this ticket.'
                ], 403);
            }

            // Get 'Closed' status
            $closedStatus = Status::where('description', 'Closed')->first();
            if (!$closedStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Closed status not found in the system.'
                ], 404);
            }

            // Update ticket status to Closed
            $ticket->update([
                'status_id' => $closedStatus->id,
                'resolved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket verified and closed successfully.',
                'status_name' => $closedStatus->description,
                'status_color' => $closedStatus->color,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in verifyAndClose: ' . $e->getMessage(), [
                'ticket_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error verifying ticket: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Request re-escalation when issue is not resolved
     */
    public function requestReEscalation(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|min:10|max:1000',
            ]);

            $ticket = Ticket::findOrFail($id);
            
            // Check if agent is assigned to this ticket
            if ($ticket->assigned_to !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. This ticket is not assigned to you.'
                ], 403);
            }

            // Get 'Escalated' status
            $escalatedStatus = Status::where('description', 'Escalated')->first();
            if (!$escalatedStatus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Escalated status not found in the system.'
                ], 404);
            }

            // Find the most recent escalation for this ticket
            $latestEscalation = \App\Models\TicketEscalation::where('ticket_id', $ticket->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Get the admin who handled the escalation (or default admin)
            $adminId = null;
            if ($latestEscalation && $latestEscalation->escalated_by_id) {
                // Get the user who escalated (could be manager or admin)
                $escalator = User::find($latestEscalation->escalated_by_id);
                if ($escalator && ($escalator->role === 'admin' || $escalator->role === 'manager')) {
                    // If manager escalated, find admin. If admin, use same admin
                    if ($escalator->role === 'admin') {
                        $adminId = $escalator->id;
                    }
                }
            }

            // If no admin found from escalation, get any admin
            if (!$adminId) {
                $admin = User::where('role', 'admin')->whereNull('deleted_at')->first();
                if (!$admin) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No admin user found in the system.'
                    ], 404);
                }
                $adminId = $admin->id;
            }

            // Always create a NEW escalation to preserve history
            // Old escalations remain untouched (keep their verified/resolved status)
            // Create as PENDING - needs manager approval before going to admin
            \App\Models\TicketEscalation::create([
                'ticket_id' => $ticket->id,
                'requested_by_id' => auth()->id(),
                'reason' => $request->reason,
                'requested_at' => now(),
                // NOT escalated yet - manager needs to review first
                'escalated_by_id' => null,
                'escalated_at' => null,
            ]);

            // Update ticket status but KEEP it assigned to the agent
            // This allows the agent to still work on it while waiting for manager review
            $ticket->update([
                'status_id' => $escalatedStatus->id,
                // Keep assigned_to as current agent
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Re-escalation request submitted. The issue has been sent back for review.',
                'status_name' => $escalatedStatus->description,
                'status_color' => $escalatedStatus->color,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error requesting re-escalation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request to reopen a closed ticket
     */
    public function requestReopenTicket(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|min:10|max:1000',
            ]);

            $ticket = Ticket::findOrFail($id);
            
            // Check if ticket is closed
            $isClosed = $ticket->status_relation && strtolower($ticket->status_relation->description) === 'closed';
            if (!$isClosed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ticket is not closed.'
                ], 400);
            }

            // Check if there's already a pending reopen request from this agent
            $existingRequest = \App\Models\ReopenRequest::where('ticket_id', $ticket->id)
                ->where('requested_by_id', auth()->id())
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a pending reopen request for this ticket.'
                ], 400);
            }

            // Create reopen request
            \App\Models\ReopenRequest::create([
                'ticket_id' => $ticket->id,
                'requested_by_id' => auth()->id(),
                'reason' => $request->reason,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reopen request submitted successfully. A manager or admin will review your request.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting reopen request: ' . $e->getMessage()
            ], 500);
        }
    }
}
