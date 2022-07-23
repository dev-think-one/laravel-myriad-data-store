<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyriadDespatchTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('myriad-data-store.tables.despatch_types'), function (Blueprint $table) {
            $table->unsignedBigInteger('id', false)->primary();
            $table->string('type')->index();
            $table->unsignedBigInteger('despatch_category_id')->index();
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
        Schema::dropIfExists(config('myriad-data-store.tables.despatch_types'));
    }
}
