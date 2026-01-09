<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\Status;
use App\Models\ReopenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
            ]);

            // Get first active status or create "Open" as default
            $defaultStatus = Status::whereNull('deleted_at')->orderBy('id')->first();
            
            if (!$defaultStatus) {
                $defaultStatus = Status::create([
                    'description' => 'Open',
                    'color' => '#08ef4dff'
                ]);
            }

            // Generate unique ticket number
            $ticketNumber = $this->generateTicketNumber();

            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'title' => $request->title,
                'description' => $request->description,
                'status_id' => $defaultStatus->id,
                'priority_id' => null, // Manager will set this
                'category_id' => null, // Manager will set this
                'user_id' => auth()->id(),
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('ticket_attachments', $filename, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            return redirect()->route('dashboard')->with('success', 'Ticket submitted successfully!');
        } catch (\Exception $e) {
            Log::error('Ticket creation error: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit ticket. Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::withTrashed()->with([
                'user:id,name,email,role', 
                'status_relation', 
                'priority_relation', 
                'category_relation', 
                'comments.user:id,name,email,role', 
                'comments.attachments', 
                'attachments',
                'assignedTo:id,name,email,role',
                'escalationRequestedBy:id,name,email,role',
                'escalations.requestedBy:id,name,email,role',
                'escalations.escalatedBy:id,name,email,role',
                'escalations.resolvedBy:id,name,email,role',
                'latestEscalation.requestedBy:id,name,email,role',
                'latestEscalation.escalatedBy:id,name,email,role',
                'latestEscalation.resolvedBy:id,name,email,role',
                'reopenRequests.requestedBy:id,name,email,role',
                'reopenRequests.respondedBy:id,name,email,role'
            ])->findOrFail($id);

            // Check authorization
            if ($ticket->user_id !== auth()->id() && auth()->user()->role === 'customer') {
                abort(403, 'Unauthorized action.');
            }

            // Format the response
            $ticketUserId = $ticket->user_id; // Capture for use in closure
            
            // Get latest escalation if exists
            $latestEscalation = $ticket->latestEscalation;
            
            // Get latest reopen request if exists
            $latestReopenRequest = $ticket->latestReopenRequest;
            
            $response = [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'is_deactivated' => $ticket->trashed(),
                'status' => $ticket->status_relation ? $ticket->status_relation->description : 'Pending',
                'status_id' => $ticket->status_id,
                'status_color' => $ticket->status_relation ? $ticket->status_relation->color : '#6b7280',
                'priority' => $ticket->priority_relation ? $ticket->priority_relation->description : null,
                'priority_id' => $ticket->priority_id,
                'category' => $ticket->category_relation ? $ticket->category_relation->description : null,
                'category_id' => $ticket->category_id,
                'customer_name' => $ticket->user ? $ticket->user->name : 'N/A',
                'assigned_to_name' => $ticket->assignedTo ? $ticket->assignedTo->name : null,
                'assigned_to_id' => $ticket->assigned_to,
                'created_at_formatted' => $this->formatDate($ticket->created_at),
                'updated_at_formatted' => $this->formatDate($ticket->updated_at),
                'escalation_requested' => $latestEscalation ? true : false,
                'escalation_reason' => $latestEscalation ? $latestEscalation->reason : null,
                'escalation_id' => $latestEscalation ? $latestEscalation->id : null,
                'escalation_requested_by_name' => ($latestEscalation && $latestEscalation->requestedBy) ? $latestEscalation->requestedBy->name : null,
                'escalation_requested_by_id' => $latestEscalation ? $latestEscalation->requested_by_id : null,
                'escalation_requested_at_formatted' => $latestEscalation ? $this->formatDate($latestEscalation->requested_at) : null,
                'escalation_escalated_by_name' => ($latestEscalation && $latestEscalation->escalated_by_id && $latestEscalation->escalatedBy) ? $latestEscalation->escalatedBy->name : null,
                'escalation_escalated_at_formatted' => ($latestEscalation && $latestEscalation->escalated_at) ? $this->formatDate($latestEscalation->escalated_at) : null,
                'escalation_resolved_by_name' => ($latestEscalation && $latestEscalation->resolved_by_id && $latestEscalation->resolvedBy) ? $latestEscalation->resolvedBy->name : null,
                'escalation_resolved_at_formatted' => ($latestEscalation && $latestEscalation->resolved_at) ? $this->formatDate($latestEscalation->resolved_at) : null,
                'escalation_resolution_notes' => ($latestEscalation && $latestEscalation->resolution_notes) ? $latestEscalation->resolution_notes : null,
                'reopen_requested' => $latestReopenRequest && $latestReopenRequest->status === 'pending' ? true : false,
                'reopen_reason' => $latestReopenRequest ? $latestReopenRequest->reason : null,
                'reopen_status' => $latestReopenRequest ? $latestReopenRequest->status : null,
                'reopen_requested_by_name' => ($latestReopenRequest && $latestReopenRequest->requestedBy) ? $latestReopenRequest->requestedBy->name : null,
                'reopen_requested_by_id' => $latestReopenRequest ? $latestReopenRequest->requested_by_id : null,
                'reopen_requested_at_formatted' => $latestReopenRequest ? $this->formatDate($latestReopenRequest->requested_at) : null,
                'reopen_responded_by_name' => ($latestReopenRequest && $latestReopenRequest->responded_by_id && $latestReopenRequest->respondedBy) ? $latestReopenRequest->respondedBy->name : null,
                'reopen_responded_at_formatted' => ($latestReopenRequest && $latestReopenRequest->responded_at) ? $this->formatDate($latestReopenRequest->responded_at) : null,
                'escalations' => $ticket->escalations->map(function ($escalation) use ($ticket) {
                    return [
                        'id' => $escalation->id,
                        'reason' => $escalation->reason,
                        'requested_by_id' => $escalation->requested_by_id,
                        'requested_by_name' => $escalation->requestedBy ? $escalation->requestedBy->name : 'Unknown',
                        'requested_by_role' => $escalation->requestedBy ? $escalation->requestedBy->role : 'unknown',
                        'requested_at' => $escalation->requested_at->timestamp,
                        'requested_at_formatted' => $this->formatDate($escalation->requested_at),
                        'escalated_by_id' => $escalation->escalated_by_id,
                        'escalated_by_name' => $escalation->escalated_by_id && $escalation->escalatedBy ? $escalation->escalatedBy->name : null,
                        'escalated_by_role' => $escalation->escalated_by_id && $escalation->escalatedBy ? $escalation->escalatedBy->role : null,
                        'escalated_at_formatted' => $escalation->escalated_at ? $this->formatDate($escalation->escalated_at) : null,
                        'resolved_by_id' => $escalation->resolved_by_id,
                        'resolved_by_name' => $escalation->resolved_by_id && $escalation->resolvedBy ? $escalation->resolvedBy->name : null,
                        'resolved_by_role' => $escalation->resolved_by_id && $escalation->resolvedBy ? $escalation->resolvedBy->role : null,
                        'resolved_at_formatted' => $escalation->resolved_at ? $this->formatDate($escalation->resolved_at) : null,
                        'resolution_notes' => $escalation->resolution_notes,
                        'ticket_number' => $ticket->ticket_number,
                        'ticket_status' => $ticket->status_relation ? $ticket->status_relation->description : 'N/A',
                        'customer_name' => $ticket->user ? $ticket->user->name : 'N/A',
                        'customer_email' => $ticket->user ? $ticket->user->email : null,
                    ];
                }),
                'reopenRequests' => $ticket->reopenRequests->map(function ($reopenRequest) {
                    return [
                        'id' => $reopenRequest->id,
                        'reason' => $reopenRequest->reason,
                        'status' => $reopenRequest->status,
                        'requested_by_id' => $reopenRequest->requested_by_id,
                        'requested_by_name' => $reopenRequest->requestedBy ? $reopenRequest->requestedBy->name : 'Unknown',
                        'requested_by_role' => $reopenRequest->requestedBy ? $reopenRequest->requestedBy->role : 'unknown',
                        'requested_at' => $reopenRequest->requested_at->timestamp,
                        'requested_at_formatted' => $this->formatDate($reopenRequest->requested_at),
                        'responded_by_id' => $reopenRequest->responded_by_id,
                        'responded_by_name' => $reopenRequest->responded_by_id && $reopenRequest->respondedBy ? $reopenRequest->respondedBy->name : null,
                        'responded_by_role' => $reopenRequest->responded_by_id && $reopenRequest->respondedBy ? $reopenRequest->respondedBy->role : null,
                        'responded_at_formatted' => $reopenRequest->responded_at ? $this->formatDate($reopenRequest->responded_at) : null,
                        'remarks' => $reopenRequest->remarks,
                    ];
                }),
                'attachments' => $ticket->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_path' => $attachment->file_path,
                        'file_size' => $attachment->file_size,
                    ];
                }),
                'comments' => $ticket->comments->map(function ($comment) use ($ticketUserId) {
                    return [
                        'id' => $comment->id,
                        'user_id' => $comment->user_id,
                        'user_name' => $comment->user ? $comment->user->name : 'Unknown',
                        'user_role' => $comment->user ? $comment->user->role : 'unknown',
                        'content' => $comment->content,
                        'is_customer' => $comment->user_id === $ticketUserId,
                        'created_at' => $comment->created_at->timestamp,
                        'created_at_formatted' => $this->formatDate($comment->created_at),
                        'attachments' => $comment->attachments->map(function ($attachment) {
                            return [
                                'id' => $attachment->id,
                                'file_name' => $attachment->file_name,
                                'file_path' => $attachment->file_path,
                                'file_size' => $attachment->file_size,
                            ];
                        }),
                    ];
                }),
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Ticket show error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load ticket details: ' . $e->getMessage()], 500);
        }
    }

    public function reply(Request $request, $id)
    {
        try {
            $request->validate([
                'message' => 'nullable|string',
                'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
            ]);

            $ticket = Ticket::findOrFail($id);

            // Check authorization
            if ($ticket->user_id !== auth()->id() && !in_array(auth()->user()->role, ['agent', 'manager', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }

            // If there's a message, create comment
            if ($request->filled('message')) {
                $comment = TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                    'content' => $request->message,
                ]);
            }

            // If there are only attachments without message
            if ($request->hasFile('attachments') && !$request->filled('message')) {
                $comment = TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                    'content' => '[Attachment]',
                ]);
            }

            // Handle file attachments
            if ($request->hasFile('attachments') && isset($comment)) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('ticket_attachments', $filename, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'comment_id' => $comment->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Auto-change status to "Awaiting Customer Reply" when staff (agent/manager/admin) replies
            if (in_array(auth()->user()->role, ['agent', 'manager', 'admin'])) {
                $awaitingStatus = Status::where('description', 'Awaiting Customer Reply')->first();
                if ($awaitingStatus) {
                    $ticket->status_id = $awaitingStatus->id;
                    $ticket->save();
                }
            }

            $ticket->touch(); // Update the updated_at timestamp

            // Return updated ticket info
            return response()->json([
                'success' => true, 
                'message' => 'Reply sent successfully!',
                'updated_at' => $this->formatDate($ticket->fresh()->updated_at)
            ]);
        } catch (\Exception $e) {
            Log::error('Ticket reply error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error sending reply: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status_id' => 'required|exists:statuses,id',
            ]);

            $ticket = Ticket::findOrFail($id);

            // Check authorization - only agents, managers, and admins can update status
            if (!in_array(auth()->user()->role, ['agent', 'manager', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }

            $ticket->update([
                'status_id' => $request->status_id,
            ]);

            $status = Status::find($request->status_id);

            return response()->json([
                'success' => true, 
                'message' => 'Status updated successfully!',
                'status' => $status->description,
                'status_color' => $status->color,
                'updated_at' => $this->formatDate($ticket->fresh()->updated_at)
            ]);
        } catch (\Exception $e) {
            Log::error('Status update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }

    public function assignAgent(Request $request, $id)
    {
        try {
            $request->validate([
                'agent_id' => 'required|exists:users,id',
            ]);

            $ticket = Ticket::findOrFail($id);

            // Check authorization - only managers and admins can assign tickets
            if (!in_array(auth()->user()->role, ['manager', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }

            $ticket->update([
                'assigned_to' => $request->agent_id,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Ticket assigned successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Assign agent error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error assigning ticket: ' . $e->getMessage()], 500);
        }
    }

    public function downloadAttachment($path)
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->download($fullPath);
    }

    private function generateTicketNumber()
    {
        do {
            $number = rand(1000000, 9999999);
        } while (Ticket::where('ticket_number', $number)->exists());

        return $number;
    }

    private function formatDate($date)
    {
        $date = $date->setTimezone('Asia/Manila');
        $now = now()->setTimezone('Asia/Manila');
        
        if ($date->isToday()) {
            return 'Today, ' . $date->format('g:i A');
        } elseif ($date->isYesterday()) {
            return 'Yesterday, ' . $date->format('g:i A');
        } elseif ($date->year === $now->year) {
            return $date->format('F j, g:i A');
        } else {
            return $date->format('F j, Y, g:i A');
        }
    }

    public function requestReopen(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            $ticket = Ticket::findOrFail($id);

            // Check authorization - only ticket owner can request reopen
            if ($ticket->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }

            // Check if ticket is closed
            $closedStatus = Status::where('description', 'Closed')->first();
            if (!$closedStatus || $ticket->status_id !== $closedStatus->id) {
                return response()->json(['success' => false, 'message' => 'Only closed tickets can be reopened.'], 400);
            }

            // Check if there's already a pending reopen request
            $pendingRequest = $ticket->reopenRequests()->where('status', 'pending')->first();
            if ($pendingRequest) {
                return response()->json(['success' => false, 'message' => 'There is already a pending reopen request for this ticket.'], 400);
            }

            // Create reopen request
            ReopenRequest::create([
                'ticket_id' => $ticket->id,
                'requested_by_id' => auth()->id(),
                'reason' => $request->reason,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            // Update ticket status to "Reopen Requested"
            $reopenRequestedStatus = Status::where('description', 'Reopen Requested')->first();
            if ($reopenRequestedStatus) {
                $ticket->update([
                    'status_id' => $reopenRequestedStatus->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reopen request submitted successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Reopen request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error submitting reopen request: ' . $e->getMessage()], 500);
        }
    }

    public function respondToReopenRequest(Request $request, $id, $reopenRequestId)
    {
        try {
            $request->validate([
                'action' => 'required|in:accept,decline',
            ]);

            $ticket = Ticket::findOrFail($id);

            // Check authorization - only admin and manager can respond
            if (!in_array(auth()->user()->role, ['admin', 'manager'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }

            $reopenRequest = ReopenRequest::where('ticket_id', $ticket->id)
                ->where('id', $reopenRequestId)
                ->where('status', 'pending')
                ->firstOrFail();

            if ($request->action === 'accept') {
                // Update reopen request status
                $reopenRequest->update([
                    'status' => 'accepted',
                    'responded_by_id' => auth()->id(),
                    'responded_at' => now(),
                ]);

                // Reopen the ticket - set status to Open
                $openStatus = Status::where('description', 'Open')->first();
                if ($openStatus) {
                    $ticket->update([
                        'status_id' => $openStatus->id,
                        'resolved_at' => null,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Reopen request accepted. Ticket has been reopened.',
                    'action' => 'accept'
                ]);
            } else {
                // Decline the request
                $reopenRequest->update([
                    'status' => 'declined',
                    'responded_by_id' => auth()->id(),
                    'responded_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Reopen request declined.',
                    'action' => 'decline'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Respond to reopen request error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error responding to reopen request: ' . $e->getMessage()], 500);
        }
    }
}
