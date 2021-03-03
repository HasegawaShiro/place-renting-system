<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('schedule_id');
            $table->foreignId('place_id');
            $table->text('schedule_title');
            $table->date('schedule_date');
            $table->time('schedule_from');
            $table->time('schedule_to');
            $table->enum('schedule_type', ['conference', 'activity', 'lesson', 'exam', 'lecture', 'camp', 'other']);
            $table->longText('schedule_content')->nullable();
            $table->foreignId('user_id');
            $table->longText('schedule_contact')->nullable();
            $table->longText('schedule_url')->nullable();
            $table->boolean('schedule_repeat');
            $table->integer('schedule_repeat_days')->nullable();
            $table->text('schedule_end');
            $table->date('schedule_end_at')->nullable();
            $table->integer('schedule_end_times')->nullable();
            // $table->longText('schedule_options');
            $table->text('schedule_registrant')->nullable();
            $table->text('schedule_document')->nullable();
            $table->unsignedBigInteger('repeat_id')->nullable();
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
        Schema::dropIfExists('schedules');
    }
}
