<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id()->comment('Primary key - Unique category identifier');
            $table->string('description', 100)->comment('Category name/description (e.g., Billing, Technical Support)');
            $table->string('color', 50)->default('blue')->comment('Hex color code for UI display');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
