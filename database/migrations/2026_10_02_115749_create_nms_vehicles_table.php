<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nms_vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('nms_sites')
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('nms_teams')
                ->nullOnDelete();

            $table->string('name');
            $table->string('registration_number');
            $table->string('vehicle_type')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color')->nullable();

            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();

            $table->string('tracker_device_id')->nullable();
            $table->string('tracker_imei')->nullable();
            $table->string('tracker_mac_address')->nullable();
            $table->string('tracker_ip_address')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('last_seen_at')->nullable();

            $table->string('status')->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['site_id', 'registration_number']);
            $table->index('site_id');
            $table->index('team_id');
            $table->index('tracker_device_id');
            $table->index('tracker_imei');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nms_vehicles');
    }
};
