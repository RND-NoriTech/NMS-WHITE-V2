<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nms_wifi_connections', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | LIBRENMS PORT LINK (NO FK, SAME PATTERN AS nms_site_devices)
            |--------------------------------------------------------------------------
            |
            | ports.port_id is int(10) unsigned, so the matching Laravel column
            | type is unsignedInteger.
            |
            */

            $table->unsignedInteger('librenms_port_id')
                ->nullable()
                ->after('librenms_device_id');

            $table->index('librenms_port_id');
        });
    }

    public function down(): void
    {
        Schema::table('nms_wifi_connections', function (Blueprint $table) {
            $table->dropIndex(['librenms_port_id']);

            $table->dropColumn('librenms_port_id');
        });
    }
};
