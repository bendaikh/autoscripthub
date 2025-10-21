<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set all existing items' regular_price to 0
        // This will hide the Regular License option for all existing products
        DB::table('items')->update(['regular_price' => 0]);
        
        echo "✓ Updated all items: regular_price set to 0\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this - it's a one-time cleanup
        echo "⚠ No reverse action for this migration\n";
    }
};
