<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pixels', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 20);
            $table->string('pixel_id', 80);
            $table->string('label')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->unique(['platform', 'pixel_id']);
            $table->index(['platform', 'is_active']);
        });

        DB::table('landing_pixels')->insert([
            'platform' => 'facebook',
            'pixel_id' => '4153934531541650',
            'label' => 'Default Meta Pixel',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pixels');
    }
};
