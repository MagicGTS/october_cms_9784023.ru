<?php namespace T9784023\Schedules\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateSchedulesTable Migration
 */
class CreateSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('t9784023_schedules', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('begin_at')->useCurrent();
            $table->foreignId('hasSchedule_id')->nullable();
            $table->string('hasSchedule_type', 128)->nullable();
            $table->string('location', 60);
            $table->string('url', 256);

        });
    }

    public function down()
    {
        Schema::dropIfExists('t9784023_schedules');
    }
}
