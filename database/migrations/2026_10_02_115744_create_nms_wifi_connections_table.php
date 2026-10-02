<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nms_wifi_connections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('nms_sites')
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('nms_teams')
                ->nullOnDelete();

            $table->string('name');
            $table->string('provider')->nullable();
            $table->string('ssid')->nullable();
            $table->string('device_name')->nullable();
            $table->string('model')->nullable();
            $table->string('device_id')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('gateway')->nullable();

            $table->string('username')->nullable();
            $table->text('password')->nullable();

            $table->string('monitoring_method')->default('manual');
            $table->string('status')->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('site_id');
            $table->index('team_id');
            $table->index('mac_address');
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nms_wifi_connections');
    }
};
