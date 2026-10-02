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
            | PROVIDER METADATA FIELDS
            |--------------------------------------------------------------------------
            |
            | The vendor provider column already exists on this table (from the
            | original module), so it is reused. Only missing fields are added.
            |
            */

            $table->string('wireless_mode')
                ->nullable()
                ->after('ssid');

            $table->string('radio_mac_address')
                ->nullable()
                ->after('mac_address');

            $table->string('master_interface')
                ->nullable()
                ->after('radio_mac_address');

            $table->timestamp('metadata_synced_at')
                ->nullable();

            $table->string('metadata_sync_status')
                ->nullable();

            $table->text('metadata_sync_message')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nms_wifi_connections', function (Blueprint $table) {
            $table->dropColumn([
                'wireless_mode',
                'radio_mac_address',
                'master_interface',
                'metadata_synced_at',
                'metadata_sync_status',
                'metadata_sync_message',
            ]);
        });
    }
};
