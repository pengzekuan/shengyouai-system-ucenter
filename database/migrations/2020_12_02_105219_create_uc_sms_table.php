<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 短信
 * Class CreateUcSmsTable
 */
class CreateUcSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uc_sms', function (Blueprint $table) {
            $table->id();
            $table->string('sid', 128)->comment('短信id');
            $table->string('appId', 128)->comment('短信服务应用id');
            $table->string('cellphone', 11)->comment('手机号');
            $table->string('tid', 32)->comment('模板id');
            $table->string('content')->comment('短信内容');
            $table->boolean('sent')->default(0)->comment('发送状态');
            $table->dateTime('sendTime')->nullable()->comment('发送时间');
            $table->boolean('used')->default(0)->comment('已使用');
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
        Schema::dropIfExists('uc_sms');
    }
}
