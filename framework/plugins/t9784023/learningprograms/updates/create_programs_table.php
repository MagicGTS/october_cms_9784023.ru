<?php namespace T9784023\LearningPrograms\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateProgramsTable Migration
 */
class CreateProgramsTable extends Migration
{
    public function up()
    {
        Schema::create('t9784023_learningprograms', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('begin_at')->useCurrent();
            $table->foreignId('hasLearningProgram_id')->nullable();
            $table->string('hasLearningProgram_type', 128)->nullable();
            $table->tinyInteger('hours');
            $table->string('name', 256);

        });
    }

    public function down()
    {
        Schema::dropIfExists('t9784023_learningprograms');
    }
}
