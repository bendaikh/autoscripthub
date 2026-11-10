<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('items', 'source_url')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('source_url', 2048)->nullable()->after('installation_video_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('items', 'source_url')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('source_url');
            });
        }
    }
};
