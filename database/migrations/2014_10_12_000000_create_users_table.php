<?php

use App\Models\User;
use App\Models\Util;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('user_id');
                $table->text('username')->unique();
                $table->text('password');
                $table->text('name');
                $table->text('phone')->nullable();
                $table->text('email')->nullable();
                $table->boolean('user_disabled')->default(false);
                $table->bigInteger('util_id');
                $table->bigInteger('created_by')->default(-1);
                $table->bigInteger('updated_by')->default(-1);
                $table->text('remarks')->nullable();
                // $table->rememberToken();
                $table->timestamps();

                // $table->index(['user_id','username', 'email'],'users_index');
        });
        User::create(['username' => 'admin', 'password' => Hash::make('admin'), 'name' => 'Admin', 'util_id' => 1, 'phone' => '0999777888', 'email' => 'abc@zxc.com', 'user_disabled' => false]);
        Util::create(['util_name' => '創產學院辦公室']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
