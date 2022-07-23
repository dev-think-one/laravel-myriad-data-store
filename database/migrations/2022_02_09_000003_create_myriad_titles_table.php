<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyriadTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('myriad-data-store.tables.titles'), function (Blueprint $table) {
            $table->unsignedBigInteger('id', false)->primary();
            $table->string('title')->index();
            $table->unsignedBigInteger('product_type_id')->nullable();
            $table->unsignedBigInteger('current_issue_id')->nullable();
            $table->boolean('active')->default(false);
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
        Schema::dropIfExists(config('myriad-data-store.tables.titles'));
    }
}
