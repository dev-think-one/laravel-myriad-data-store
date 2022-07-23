<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyriadOrderPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('myriad-data-store.tables.order_packages'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_package_type_id')->index();
            $table->unsignedBigInteger('title_id')->index();
            $table->string('start_issue')->index();
            $table->string('end_issue')->index();
            $table->string('status')->index();
            $table->string('stopcode')->index();
            $table->unsignedBigInteger('myriad_package_id')->nullable()->index();
            $table->unsignedBigInteger('remaining_issues')->index();
            $table->unsignedBigInteger('copies')->index();
            $table->json('details')->nullable();
            $table->date('end_issue_dn')->nullable()->index();
            $table->date('start_issue_dn')->nullable()->index();
            $table->timestamps();

            $table->foreign('order_id')
                  ->references('id')
                  ->on(config('myriad-data-store.tables.orders'))
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('myriad-data-store.tables.order_packages'));
    }
}
