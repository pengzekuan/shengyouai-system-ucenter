<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 用户来源登记
 * Class CreateUcUChannelTable
 */
class CreateUcUChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uc_u_channel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->comment('用户编号');
            $table->smallInteger('type')->default(0)->comment('来源应用类型');
            $table->string('appId')->nullable()->comment('来源应用appId');
            $table->string('scene')->nullable()->comment('来源场景值');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uc_u_channel');
    }
}
