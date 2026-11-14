<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Live chat between landing page visitors and the admin (Admin > Live Chat).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('live_chat_conversations')) {
            Schema::create('live_chat_conversations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->char('token', 48)->unique();          // visitor's secret key
                $table->unsignedBigInteger('lp_id')->nullable()->index();
                $table->string('page_path', 191)->nullable();
                $table->string('visitor_name', 100)->nullable();
                $table->string('visitor_email', 150)->nullable();
                $table->char('country_code', 2)->nullable();
                $table->string('status', 10)->default('open')->index(); // open | closed
                $table->unsignedInteger('unread_admin')->default(0);    // visitor msgs not yet seen by admin
                $table->unsignedInteger('unread_visitor')->default(0);  // admin msgs not yet seen by visitor
                $table->dateTime('last_message_at')->nullable()->index();
                $table->dateTime('visitor_seen_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('live_chat_messages')) {
            Schema::create('live_chat_messages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('conversation_id')->index();
                $table->string('sender', 10); // visitor | admin
                $table->text('body');
                $table->dateTime('created_at');
                $table->index(['conversation_id', 'id']);
            });
        }

        if (!Schema::hasTable('live_chat_settings')) {
            Schema::create('live_chat_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('enabled')->default(1);
                $table->string('welcome_message', 500)->nullable();
                $table->timestamps();
            });
            DB::table('live_chat_settings')->insert([
                'id' => 1,
                'enabled' => 1,
                'welcome_message' => 'Hi! Have a question before buying? Send us a message and we will reply right here.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_messages');
        Schema::dropIfExists('live_chat_conversations');
        Schema::dropIfExists('live_chat_settings');
    }
};
