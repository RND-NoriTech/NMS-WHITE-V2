<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nms_hardware_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('nms_sites')
                ->cascadeOnDelete();

            $table->foreignId('hardware_id')
                ->constrained('nms_hardware')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('username')->nullable();
            $table->string('action');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['site_id', 'hardware_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nms_hardware_audits');
    }
};