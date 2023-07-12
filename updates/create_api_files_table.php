<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;

class CreateApiFilesTable extends Migration
{
    public function up()
    {
        Schema::create('waka_wformwidgets_api_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('disk_name');
            $table->string('file_name');
            $table->integer('file_size');
            $table->string('content_type');
            $table->string('title')->nullable();
            $table->string('api_src')->nullable();
            $table->string('api_id')->nullable();
            $table->string('api_state')->nullable();
            $table->json('api_metas')->nullable();
            $table->json('api_opts')->nullable();
            $table->json('api_errors')->nullable();
            $table->text('description')->nullable();
            $table->string('field')->nullable()->index();
            $table->string('attachment_id')->index()->nullable();
            $table->string('attachment_type')->index()->nullable();
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_wformwidgets_api_files');
    }
}
