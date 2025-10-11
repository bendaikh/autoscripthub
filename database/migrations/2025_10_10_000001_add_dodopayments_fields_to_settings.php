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
        // DodoPayments settings are now stored in .env file instead of database
        // This migration is kept for tracking purposes but doesn't modify the database
        // The additional_settings table was at MySQL row size limit
        
        // Nothing to do here - settings are in config/services.php and .env
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to revert - no database changes were made
    }
}

