<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {

    public $connection = "database.".config('bot-licker.db_connection');
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('botlickers', function (Illuminate\Database\Schema\Blueprint $table) {

            $table->id();
            $table->string('ip')->nullable()->index();
            $table->string('iso_3166_2')->nullable();

            $table->unsignedInteger('rule_id')->nullable();

            $table->datetime('expiry')->nullable();
            $table->timestamps();

        });

        Schema::create('botlicker_rules', function (Illuminate\Database\Schema\Blueprint $table) {

            $table->id();
            $table->string('matches')->nullable();
            $table->string('action')->nullable();
            $table->timestamp('expiry')->nullable();

        });

        Schema::create('botlicker_logs', function (Illuminate\Database\Schema\Blueprint $table) {

            $table->id();
            $table->string('ip')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->useCurrent();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
