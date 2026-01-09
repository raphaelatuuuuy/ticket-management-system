<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('priorities', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique priority identifier');
            $table->string('description', 100)->comment('Priority level name (e.g., Low, Medium, High, Urgent)');
            $table->string('color', 50)->default('yellow')->comment('Hex color code for UI display');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priorities');
    }
};
