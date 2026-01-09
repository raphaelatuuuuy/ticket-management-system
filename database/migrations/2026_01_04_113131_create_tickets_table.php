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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique ticket identifier (BIGINT AUTO_INCREMENT)');
            
            $table->string('ticket_number', 50)->unique()->comment('Auto-generated unique ticket number for display (e.g., TKT-1241232)');
            
            $table->string('title', 255)->comment('Short ticket title/summary of the issue');
            
            $table->text('description')->comment('Detailed description of the issue or request');
            
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('restrict')->comment('Foreign key - Current ticket status (Open, In Progress, Resolved, etc.)');
            
            $table->foreignId('priority_id')->nullable()->constrained('priorities')->onDelete('restrict')->comment('Foreign key - Urgency/importance level (Low, Medium, High, Urgent)');
            
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null')->comment('Foreign key - Ticket category/type (e.g., Billing, Technical Support, Login Issue)');
            
            $table->unsignedInteger('user_id')->comment('Foreign key - Customer/user who created the ticket');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedInteger('assigned_to')->nullable()->comment('Foreign key - Agent/manager assigned to handle this ticket');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamp('resolved_at')->nullable();

            $table->softDeletes();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
