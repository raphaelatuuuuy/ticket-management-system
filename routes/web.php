<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Manager\ManagerTicketController;
use App\Http\Controllers\Agent\AgentTicketController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirect based on role
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'manager') {
        return redirect()->route('manager.dashboard');
    } elseif ($user->role === 'agent') {
        return redirect()->route('agent.dashboard');
    }
    
    // Default customer dashboard
    return view('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Ticket creation for customers
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    
    // Shared ticket routes for all authenticated users
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::get('/tickets/attachment/{path}', [TicketController::class, 'downloadAttachment'])->where('path', '.*')->name('tickets.attachment');
    
    // Reopen request routes
    Route::post('/tickets/{id}/request-reopen', [TicketController::class, 'requestReopen'])->name('tickets.requestReopen');
    Route::post('/tickets/{id}/reopen-requests/{reopenRequestId}/respond', [TicketController::class, 'respondToReopenRequest'])->name('tickets.respondToReopenRequest');
});

// Admin routes - role check done in controllers
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{id}/role', [UserManagementController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    
    // Configuration routes
    Route::get('/configuration', [\App\Http\Controllers\Admin\ConfigurationController::class, 'index'])->name('configuration.index');
    Route::post('/configuration/status', [\App\Http\Controllers\Admin\ConfigurationController::class, 'storeStatus'])->name('configuration.status.store');
    Route::patch('/configuration/status/{id}', [\App\Http\Controllers\Admin\ConfigurationController::class, 'updateStatus'])->name('configuration.status.update');
    Route::delete('/configuration/status/{id}', [\App\Http\Controllers\Admin\ConfigurationController::class, 'destroyStatus'])->name('configuration.status.destroy');
    Route::post('/configuration/category', [\App\Http\Controllers\Admin\ConfigurationController::class, 'storeCategory'])->name('configuration.category.store');
    Route::patch('/configuration/category/{id}', [\App\Http\Controllers\Admin\ConfigurationController::class, 'updateCategory'])->name('configuration.category.update');
    Route::delete('/configuration/category/{id}', [\App\Http\Controllers\Admin\ConfigurationController::class, 'destroyCategory'])->name('configuration.category.destroy');
    Route::post('/configuration/priority', [\App\Http\Controllers\Admin\ConfigurationController::class, 'storePriority'])->name('configuration.priority.store');
    Route::patch('/configuration/priority/{id}', [\App\Http\Controllers\Admin\ConfigurationController::class, 'updatePriority'])->name('configuration.priority.update');
    Route::delete('/configuration/priority/{id}', [\App\Http\Controllers\Admin\ConfigurationController::class, 'destroyPriority'])->name('configuration.priority.destroy');
    
    // Ticket Management
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/{id}/assign', [AdminTicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{id}/status', [AdminTicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{id}/request-escalation', [AdminTicketController::class, 'requestEscalation'])->name('tickets.requestEscalation');
    Route::post('/tickets/{id}/escalate', [AdminTicketController::class, 'escalate'])->name('tickets.escalate');
    Route::post('/tickets/{id}/reopen', [AdminTicketController::class, 'reopen'])->name('tickets.reopen');
    Route::post('/tickets/{id}/reopen-request/{reopenRequestId}/{action}', [AdminTicketController::class, 'handleReopenRequest'])->name('tickets.handleReopenRequest');
    Route::post('/tickets/{ticketId}/escalations/{escalationId}/assign', [AdminTicketController::class, 'assignEscalation'])->name('tickets.escalations.assign');
    Route::post('/tickets/{ticketId}/escalations/{escalationId}/resolve', [AdminTicketController::class, 'markAsResolved'])->name('tickets.escalations.resolve');
    Route::post('/tickets/{ticketId}/escalations/{escalationId}/reject', [AdminTicketController::class, 'rejectEscalation'])->name('tickets.escalations.reject');
    Route::delete('/tickets/{id}', [AdminTicketController::class, 'destroy'])->name('tickets.destroy');
    Route::post('/tickets/{id}/restore', [AdminTicketController::class, 'restore'])->name('tickets.restore');
});

// Manager routes - role check done in controllers
Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/tickets', [ManagerTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/{id}/assign', [ManagerTicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{id}/status', [ManagerTicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{id}/request-escalation', [ManagerTicketController::class, 'requestEscalation'])->name('tickets.requestEscalation');
    Route::post('/tickets/{ticketId}/escalations/{escalationId}/assign', [ManagerTicketController::class, 'assignEscalation'])->name('tickets.escalations.assign');
    Route::post('/tickets/{ticketId}/escalations/{escalationId}/escalate', [ManagerTicketController::class, 'escalateToAdmin'])->name('tickets.escalations.escalate');
    Route::post('/tickets/{ticketId}/escalations/{escalationId}/resolve', [ManagerTicketController::class, 'resolveEscalation'])->name('tickets.escalations.resolve');
    Route::post('/tickets/{ticketId}/escalations/{escalationId}/reject', [ManagerTicketController::class, 'rejectEscalation'])->name('tickets.escalations.reject');
    Route::post('/tickets/{id}/reopen', [ManagerTicketController::class, 'reopen'])->name('tickets.reopen');
    Route::post('/tickets/{id}/reopen-request/{reopenRequestId}/{action}', [ManagerTicketController::class, 'handleReopenRequest'])->name('tickets.handleReopenRequest');
});

// Agent routes - role check done in controllers
Route::middleware(['auth'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/tickets', [AgentTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/{id}/status', [AgentTicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{id}/request-escalation', [AgentTicketController::class, 'requestEscalation'])->name('tickets.escalate');
    Route::post('/tickets/{id}/verify-and-close', [AgentTicketController::class, 'verifyAndClose'])->name('tickets.verifyAndClose');
    Route::post('/tickets/{id}/request-re-escalation', [AgentTicketController::class, 'requestReEscalation'])->name('tickets.reEscalate');
    Route::post('/tickets/{id}/request-reopen', [AgentTicketController::class, 'requestReopenTicket'])->name('tickets.requestReopen');
    Route::post('/tickets/{id}/reopen-request/{reopenRequestId}/{action}', [AgentTicketController::class, 'handleReopenRequest'])->name('tickets.handleReopenRequest');
});

require __DIR__.'/auth.php';