<?php namespace T9784023\Images\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateImagesListsTable Migration
 */
class CreateImagesListsLinksTable extends Migration
{
    public function up()
    {
        Schema::create('t9784023_images_lists_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_list_id');
            $table->foreignId('image_id');
            $table->string('tag', 60)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
// TODO что-то с индексом, не удается создать уникальный на три поля
            //  $table->unique('image_list_id', 'image_id', 'tag');
            $table->foreign('image_list_id')
                ->references('id')
                ->on('t9784023_images_lists');
            $table->foreign('image_id')
                ->references('id')
                ->on('t9784023_images');

        });
    }

    public function down()
    {
        Schema::dropIfExists('t9784023_images_lists_links');
    }
}
