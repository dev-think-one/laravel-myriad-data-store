<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyriadContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('myriad-data-store.tables.contacts'), function (Blueprint $table) {
            $table->unsignedBigInteger('id', false)->primary();
            $table->unsignedBigInteger('crm_contact_id')->nullable()->index();
            $table->unsignedBigInteger('contact_type_id')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->json('details')->nullable();
            $table->json('communications')->nullable();
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
        Schema::dropIfExists(config('myriad-data-store.tables.contacts'));
    }
}
