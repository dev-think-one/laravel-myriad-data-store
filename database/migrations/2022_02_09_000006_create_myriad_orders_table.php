<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyriadOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('myriad-data-store.tables.orders'), function (Blueprint $table) {
            $table->unsignedBigInteger('id', false)->primary();
            $table->string('status')->index();
            $table->unsignedBigInteger('invoice_contact_id', false)->index();
            $table->unsignedBigInteger('despatch_contact_id', false)->index();
            $table->unsignedBigInteger('agent_contact_id', false)->index();
            $table->date('order_date')->index();
            $table->json('details')->nullable();
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
        Schema::dropIfExists(config('myriad-data-store.tables.orders'));
    }
}
