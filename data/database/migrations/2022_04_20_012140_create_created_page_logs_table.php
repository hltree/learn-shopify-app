<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreatedPageLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('created_page_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('page_id');
            $table->string('title');
            $table->bigInteger('shop_id');
            $table->string('handle');
            $table->text('body_html');
            $table->string('author');
            $table->string('published_at');
            $table->string('template_suffix')->nullable()->default(null);
            $table->string('admin_graphql_api_id');
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
        Schema::dropIfExists('created_page_logs');
    }
}
