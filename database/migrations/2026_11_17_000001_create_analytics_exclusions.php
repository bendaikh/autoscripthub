<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Visitors left out of the site analytics (Admin > Analytics Exclusions):
 * the owner's devices (hashed visitor ids) and IP addresses.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('analytics_exclusions')) {
            Schema::create('analytics_exclusions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type', 10);            // device | ip
                $table->string('value', 191)->index(); // device: sha256 visitor hash; ip: address
                $table->string('label', 100)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->unique(['type', 'value']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_exclusions');
    }
};
