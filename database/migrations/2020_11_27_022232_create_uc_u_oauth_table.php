<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 用户授权记录表
 * Class CreateUcUOauthTable
 */
class CreateUcUOauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uc_u_oauth', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->comment('授权用户编号');
            $table->smallInteger('pId')->default(0)->comment('授权平台,默认本平台授权，无第三方授权');
            $table->dateTime('accessDateTime')->comment('授权时间');
            $table->dateTime('accessDeadline')->comment('授权失效时间');
            $table->string('sessionKey')->comment('授权密钥');
            $table->boolean('disabled')->default(0)->comment('主动取消授权');
            $table->timestamps();

            $table->foreign('uid')
                ->on('uc_u_basic')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uc_u_oauth');
    }
}
