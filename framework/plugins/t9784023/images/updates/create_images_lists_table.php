<?php namespace T9784023\Images\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateImagesListsTable Migration
 */
class CreateImagesListsTable extends Migration
{
    public function up()
    {
        Schema::create('t9784023_images_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasImages_id')->nullable();
            $table->string('hasImages_type', 128)->nullable();
            $table->string('title', 60);
            $table->string('slug', 60)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down()
    {
        Schema::dropIfExists('t9784023_images_lists');
    }
}
