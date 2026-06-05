<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drainages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('district_id')->constrained();
            $table->foreignId('village_id')->constrained();
            $table->decimal('length', 10, 2);
            $table->decimal('width', 10, 2);
            $table->decimal('height', 10, 2);
            $table->enum('type', ['U-Ditch', 'Concrete', 'Stone Masonry', 'Earth Channel']);
            $table->enum('condition', ['Good', 'Moderate', 'Damaged'])->default('Moderate');
            $table->text('description')->nullable();
            $table->json('geometry')->nullable();
            $table->timestamps();
            $table->index('district_id');
            $table->index('village_id');
            $table->index('condition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drainages');
    }
};
