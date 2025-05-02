<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * "tipoDocumentoIdentificacion" type_document_identifications
     * "numDocumentoIdentificacion"
     * "tipoUsuario"  rips_user_type_id
     * "fechaNacimiento"
     * "codSexo" rips_gender_id
     * "codPaisResidencia" // country_code - countries no tiene campo codigo - se utilizará codigo colombia por defecto
     * "codMunicipioResidencia" municipalities - campo code
     * "codZonaTerritorialResidencia" rips_zones
     * "incapacidad": "NO",
     * "codPaisOrigen": "170", // country_code - se utilizará codigo colombia por defecto
     * @return void
     */
    public function up()
    {
        Schema::create('rips_patients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('type_document_identification_id');
            $table->string('name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('document_number');
            $table->unsignedBigInteger('rips_user_type_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->unsignedBigInteger('rips_gender_id')->nullable();
            $table->string('country_code')->default('170')->nullable();
            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->unsignedBigInteger('rips_zone_id')->nullable();
            $table->string('incapacity')->default('NO')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('type_document_identification_id')->references('id')->on('type_document_identifications');
            $table->foreign('rips_user_type_id')->references('id')->on('rips_user_types');
            $table->foreign('rips_gender_id')->references('id')->on('rips_genders');
            $table->foreign('municipality_id')->references('id')->on('municipalities');
            $table->foreign('rips_zone_id')->references('id')->on('rips_zones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_patients');
    }
}
