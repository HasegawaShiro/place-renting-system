<?php

use App\Models\Util;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utils', function (Blueprint $table) {
            $table->bigIncrements('util_id');
            $table->text('util_name');
            $table->longText('remarks')->nullable();
            $table->bigInteger('created_by')->default(-1);
            $table->bigInteger('updated_by')->default(-1);
            $table->timestamps();
        });
        Util::create(['util_name' => '創產學院辦公室']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utils');
    }
}
