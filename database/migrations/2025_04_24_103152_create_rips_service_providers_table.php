<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsServiceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_service_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('document_type_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('code', 12)->unique();
            $table->string('document_number')->unique();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('document_type_id')->references('id')->on('type_document_identifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_service_providers');
    }
}
