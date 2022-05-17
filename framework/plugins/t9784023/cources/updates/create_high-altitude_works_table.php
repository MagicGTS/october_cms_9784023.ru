<?php namespace T9784023\Cources\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateHighAltitudeWorksTable Migration
 */
class CreateHighAltitudeWorksTable extends Migration
{
    public function up()
    {
        Schema::create('t9784023_cources_high-altitude_works', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->decimal('cost', 6, 2);
            $table->foreignId('img_background_id')->nullable();
            $table->foreignId('img_rounded_id')->nullable();
            $table->mediumText('description')->nullable();
            $table->tinyInteger('hours');
            $table->string('learning_form', 256);
            $table->mediumText('fines_description')->nullable();
            $table->string('fines_links', 256);
            $table->foreignId('schedule_id')->nullable();
            $table->mediumText('whom_needs')->nullable();
            $table->mediumText('what_will_learn')->nullable();
            $table->mediumText('schedule_description')->nullable();
            $table->mediumText('howto_sign_up')->nullable();
            $table->string('result_img_folder', 256);
            $table->mediumText('schould_to_know')->nullable();
            $table->string('youtube_url', 256);
            $table->foreignId('program_id')->nullable();

            $table->foreign('img_background_id')
                ->references('id')
                ->on('t9784023_images')
                ->onDelete('cascade');

            $table->foreign('img_rounded_id')
                ->references('id')
                ->on('t9784023_images')
                ->onDelete('cascade');

            $table->foreign('schedule_id')
                ->references('id')
                ->on('t9784023_schedules')
                ->onDelete('cascade');

            $table->foreign('program_id')
                ->references('id')
                ->on('t9784023_learningprograms')
                ->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('t9784023_cources_high-altitude_works');
    }
}
