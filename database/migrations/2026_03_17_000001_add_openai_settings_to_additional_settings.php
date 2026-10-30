<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop leftover columns if a previous partial migration added any
        if (Schema::hasTable('additional_settings')) {
            Schema::table('additional_settings', function (Blueprint $table) {
                foreach (['openai_api_key', 'openai_model', 'openai_image_model', 'openai_enabled', 'openai_generate_images'] as $col) {
                    if (Schema::hasColumn('additional_settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (!Schema::hasTable('ai_settings')) {
            Schema::create('ai_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->text('openai_api_key')->nullable();
                $table->string('openai_model', 100)->default('gpt-4o-mini');
                $table->string('openai_image_model', 100)->default('gpt-image-1');
                $table->tinyInteger('openai_enabled')->default(0);
                $table->tinyInteger('openai_generate_images')->default(1);
                $table->timestamps();
            });

            DB::table('ai_settings')->insert([
                'id' => 1,
                'openai_api_key' => null,
                'openai_model' => 'gpt-4o-mini',
                'openai_image_model' => 'gpt-image-1',
                'openai_enabled' => 0,
                'openai_generate_images' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('landing_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('landing_pages', 'lp_tagline')) {
                $table->string('lp_tagline', 500)->nullable()->after('lp_title');
            }
            if (!Schema::hasColumn('landing_pages', 'lp_cta_text')) {
                $table->string('lp_cta_text', 120)->nullable()->after('lp_tagline');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');

        Schema::table('landing_pages', function (Blueprint $table) {
            foreach (['lp_tagline', 'lp_cta_text'] as $col) {
                if (Schema::hasColumn('landing_pages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
