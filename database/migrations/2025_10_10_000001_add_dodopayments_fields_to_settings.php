<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDodopaymentsFieldsToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('additional_settings', function (Blueprint $table) {
            $table->string('dodopayments_mode', 10)->nullable()->after('nowpayments_ipn_secret');
            $table->string('dodopayments_api_key', 255)->nullable()->after('dodopayments_mode');
            $table->string('dodopayments_business_id', 255)->nullable()->after('dodopayments_api_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('additional_settings', function (Blueprint $table) {
            $table->dropColumn(['dodopayments_mode', 'dodopayments_api_key', 'dodopayments_business_id']);
        });
    }
}

