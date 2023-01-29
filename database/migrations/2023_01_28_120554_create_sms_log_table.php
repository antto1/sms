<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(config('sms.dblog.table'))) {
            Schema::create(config('sms.dblog.table'), function (Blueprint $table) {
                $table->id();
                $table->string('mobile');
                $table->text('data');
                $table->tinyInteger('is_sent')->default(0);
                $table->text('result')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('sms.dblog.table'));
    }
}
