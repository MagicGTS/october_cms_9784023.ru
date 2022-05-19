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
        Schema::create('t9784023_cources__h_alt_w', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('name', 256);
            $table->decimal('cost', 6, 2);
            $table->mediumText('description')->nullable();
            $table->tinyInteger('hours');
            $table->string('learning_form', 256);
            $table->mediumText('fines_description')->nullable();
            $table->string('fines_links', 256);
            $table->mediumText('whom_needs')->nullable();
            $table->mediumText('what_will_learn')->nullable();
            $table->mediumText('schedule_description')->nullable();
            $table->mediumText('howto_sign_up')->nullable();
            $table->string('result_img_folder', 256);
            $table->string('files_folder', 256);
            $table->string('img_background', 256);
            $table->string('img_rounded', 256);
            $table->mediumText('schould_to_know')->nullable();
            $table->string('youtube_url', 256);

        });
    }

    public function down()
    {
        Schema::dropIfExists('t9784023_cources__h_alt_w');
    }
}
