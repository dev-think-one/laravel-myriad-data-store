<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyriadProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('myriad-data-store.tables.product_types'), function (Blueprint $table) {
            $table->unsignedBigInteger('id', false)->primary();
            $table->string('type')->index();
            $table->string('category')->index();
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
        Schema::dropIfExists(config('myriad-data-store.tables.product_types'));
    }
}
