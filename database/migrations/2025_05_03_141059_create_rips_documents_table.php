<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('appointment_id')->comment('transaccion y usuario');
            $table->string('invoice_number')->comment('numFactura')->nullable();
            $table->string('note_type')->comment('tipoNota')->nullable();
            $table->string('note_number')->comment('numNota')->nullable(); // NA NC ND RS
            $table->string('xml_filename')->comment('xmlFevFile')->nullable();
            $table->json('services')->comment('servicios');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('appointment_id')->references('id')->on('rips_appointments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_documents');
    }
}
