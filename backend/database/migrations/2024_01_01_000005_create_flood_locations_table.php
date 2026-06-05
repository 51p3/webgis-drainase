<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flood_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('district_id')->constrained();
            $table->foreignId('village_id')->constrained();
            $table->decimal('flood_depth', 5, 2);
            $table->string('flood_duration')->nullable();
            $table->text('cause')->nullable();
            $table->text('description')->nullable();
            $table->json('geometry')->nullable();
            $table->timestamps();
            $table->index('district_id');
            $table->index('village_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flood_locations');
    }
};
