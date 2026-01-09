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
        Schema::create('ticket_escalations', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique escalation identifier');
            $table->unsignedBigInteger('ticket_id')->comment('Foreign key - Related ticket ID');
            $table->unsignedInteger('requested_by_id')->comment('Foreign key - User who requested the escalation');
            $table->text('reason')->comment('Reason/justification for escalation request');
            $table->timestamp('requested_at')->useCurrent();
            $table->unsignedInteger('escalated_by_id')->nullable()->comment('Manager/Admin who escalated to admin');
            $table->timestamp('escalated_at')->nullable()->comment('When escalation was approved');
            $table->unsignedInteger('resolved_by_id')->nullable()->comment('Admin who resolved the escalation');
            $table->timestamp('resolved_at')->nullable()->comment('When escalation was resolved');
            $table->text('resolution_notes')->nullable()->comment('Notes from admin on resolution');
            $table->timestamps();
            
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('requested_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('escalated_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('resolved_by_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_escalations');
    }
};
