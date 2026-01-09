<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique status identifier');
            $table->string('description', 100)->comment('Status name/description (e.g., Open, In Progress, Closed)');
            $table->string('color', 50)->default('gray')->comment('Hex color code for UI display');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
