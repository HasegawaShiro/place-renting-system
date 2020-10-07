<?php

use App\Models\User;
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
                // $table->rememberToken();
                $table->timestamps();

                // $table->index(['user_id','username', 'name'],'users_index');
        });
        User::create(['username' => 'admin', 'password' => Hash::make('admin'), 'name' => 'Admin', 'util_id' => 1]);
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
