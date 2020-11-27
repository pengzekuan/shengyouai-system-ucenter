<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 用户平台授权表
 * Class CreateUcUPlatformTable
 */
class CreateUcUPlatformTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uc_u_platform', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->comment('用户编号');
            $table->smallInteger('platformId')->default(1)->comment('平台类型 1 微信小程序 2 微信公众号 3 微信开放平台');
            $table->string('appId', 128)->comment('三方授权平台应用id');
            $table->string('openId', 128)->comment('三方平台用户唯一标识');
            $table->boolean('subscribe')->default(0)->comment('三方平台关注状态');
            $table->string('remark', 64)->nullable()->comment('三方平台用户备注信息');
            $table->string('sessionKey', 255)->nullable()->comment('三方平台授权密钥');
            $table->boolean('disabled')->default(0)->comment('三方平台禁用');
            $table->dateTime('accessDateTime')->comment('授权时间');
            $table->dateTime('accessDeadline')->comment('授权失效时间');
            $table->string('nickName', 64)->nullable()->comment('三方平台特征数据');
            $table->smallInteger('sex')->default(1)->comment('三方平台用户性别 0 未知 1 男 2 女');
            $table->string('avatar', 255)->nullable()->comment('三方平台用户头像');
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
        Schema::dropIfExists('uc_u_platform');
    }
}
