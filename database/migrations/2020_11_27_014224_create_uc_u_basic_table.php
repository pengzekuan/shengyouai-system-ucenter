<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 用户基础表
 * Class CreateUcUBasicTable
 */
class CreateUcUBasicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uc_u_basic', function (Blueprint $table) {
            $table->id();
            $table->string('cellphone', 11)->nullable()->comment('用户手机号，手机号必须保证唯一');
            $table->boolean('disabled')->default(0)->comment('用户禁用标识');
            $table->boolean('debug')->default(0)->comment('测试用户标识');
            $table->timestamps();

            $table->unique('cellphone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uc_u_basic');
    }
}
