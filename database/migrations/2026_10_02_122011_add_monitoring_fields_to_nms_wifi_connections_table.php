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
            | LIBRENMS DEVICE LINK (NO FK, SAME AS nms_site_devices)
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('librenms_device_id')
                ->nullable()
                ->after('gateway');

            $table->string('api_provider')
                ->nullable()
                ->after('librenms_device_id');

            $table->string('external_device_id')
                ->nullable()
                ->after('api_provider');

            $table->index('librenms_device_id');
            $table->index('external_device_id');
        });
    }

    public function down(): void
    {
        Schema::table('nms_wifi_connections', function (Blueprint $table) {
            $table->dropIndex(['librenms_device_id']);
            $table->dropIndex(['external_device_id']);

            $table->dropColumn([
                'librenms_device_id',
                'api_provider',
                'external_device_id',
            ]);
        });
    }
};
