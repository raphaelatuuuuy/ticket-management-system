<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_attachments', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique attachment identifier');
            
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade')->comment('Foreign key - Parent ticket ID');
            
            $table->foreignId('comment_id')->nullable()->constrained('ticket_comments')->onDelete('cascade')->comment('Foreign key - Related comment ID for reply attachments (null for initial ticket attachments)');
            
            $table->string('file_name', 255)->comment('Original file name uploaded by user (e.g., screenshot.png)');
            
            $table->string('file_path', 255)->comment('Storage path where file is saved (e.g., tickets/abc123.pdf)');
            
            $table->unsignedInteger('file_size')->comment('File size in bytes');
            
            $table->timestamps();
            
            $table->index(['ticket_id', 'comment_id'])->comment('Index for faster lookups by ticket and comment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_attachments');
    }
};
