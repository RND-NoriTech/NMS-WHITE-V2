<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nms_site_devices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('nms_sites')
                ->cascadeOnDelete();

            $table->unsignedInteger('device_id');

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('nms_teams')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['site_id', 'device_id']);
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nms_site_devices');
    }
};
