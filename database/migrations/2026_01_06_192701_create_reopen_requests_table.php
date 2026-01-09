<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reopen_requests', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique reopen request identifier');
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade')
                ->comment('Foreign key - Ticket being requested to reopen');
            $table->unsignedInteger('requested_by_id')->comment('Foreign key - User who requested the reopen');
            $table->foreign('requested_by_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->text('reason')->comment('Reason for requesting to reopen the ticket');
            
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending')
                ->comment('Status of the reopen request');
            $table->unsignedInteger('responded_by_id')->nullable()->comment('Foreign key - Admin/Manager who responded to the request');
            $table->foreign('responded_by_id')->references('id')->on('users')->onDelete('set null');
            $table->text('remarks')->nullable()->comment('Admin/Manager remarks when approving or declining the request');
            $table->timestamp('requested_at')->comment('When the reopen was requested');
            $table->timestamp('responded_at')->nullable()->comment('When admin/manager responded to the request');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reopen_requests');
    }
};
