<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nms_site_networks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('nms_sites')
                ->cascadeOnDelete();

            $table->string('management_subnet')->nullable();
            $table->string('gateway')->nullable();
            $table->string('dns_domain')->nullable();

            $table->string('monitoring_method')->default('direct');
            $table->string('vpn_status')->default('unknown');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique('site_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nms_site_networks');
    }
};
