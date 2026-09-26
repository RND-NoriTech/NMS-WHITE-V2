<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nms_hardware', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('nms_sites')
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('nms_teams')
                ->nullOnDelete();

            $table->string('model');
            $table->string('device_id');
            $table->string('gateway')->nullable();
            $table->string('mac_address')->nullable();

            $table->string('username')->nullable();
            $table->text('password')->nullable();

            $table->string('status')->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['site_id', 'device_id']);
            $table->index('mac_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nms_hardware');
    }
};