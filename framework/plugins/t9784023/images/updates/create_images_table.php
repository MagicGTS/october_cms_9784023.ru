<?php namespace T9784023\Images\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateImagesTable Migration
 */
class CreateImagesTable extends Migration
{
    public function up()
    {
        Schema::create('t9784023_images', function (Blueprint $table) {
            $table->id();
            $table->string('path', 2048);
            $table->string('mime', 255)->default('image/*');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down()
    {
        Schema::dropIfExists('t9784023_images');
    }
}
