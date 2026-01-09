<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique comment identifier');
            
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade')->comment('Foreign key - Related ticket ID');
            
            $table->unsignedInteger('user_id')->comment('Foreign key - User who created the comment');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->text('content')->comment('Comment/reply text content');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
