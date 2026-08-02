<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->boolean('linen_changed')->default(true);
            $table->boolean('bathroom_sanitized')->default(true);
            $table->boolean('amenities_restocked')->default(true);
            $table->boolean('appliances_checked')->default(true);
            $table->boolean('minibar_checked')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('inspected_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_inspections');
    }
};
