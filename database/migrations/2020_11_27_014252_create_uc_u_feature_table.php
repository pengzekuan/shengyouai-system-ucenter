<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 用户特征表
 * Class CreateUcUFeatureTable
 */
class CreateUcUFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uc_u_feature', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->comment('用户编号');
            $table->string('nickName', 64)->nullable()->comment('用户昵称');
            $table->smallInteger('sex')->default(1)->comment('用户性别 0 未知 1 男 2 女');
            $table->string('avatar', 255)->nullable()->comment('用户头像');
            $table->string('realName', 64)->nullable()->comment('用户真实姓名');
            $table->smallInteger('idType')->default(1)->comment('用户证件类型 1 身份证');
            $table->string('idCard', 18)->nullable()->comment('用户身份id 如身份证、护照等');
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
        Schema::dropIfExists('uc_u_feature');
    }
}
