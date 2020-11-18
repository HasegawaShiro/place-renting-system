<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpinionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opinions', function (Blueprint $table) {
            $table->bigIncrements('opinion_id');
            $table->text('opinion_title');
            $table->longText('opinion_content');
            $table->text('opinion_name');
            $table->text('opinion_email')->nullable();
            $table->text('opinion_phone');
            $table->boolean('opinion_finish');
            $table->bigInteger('created_by')->default(-1);
            $table->bigInteger('updated_by')->default(-1);
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
        Schema::dropIfExists('opinions');
    }
}
