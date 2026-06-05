<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drainage_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drainage_id')->constrained()->onDelete('cascade');
            $table->string('photo_path');
            $table->text('caption')->nullable();
            $table->date('photo_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drainage_photos');
    }
};
