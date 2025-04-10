<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_user_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_user_types')->insert([
            ['code' => '01', 'name' => 'Contributivo cotizante'],
            ['code' => '02', 'name' => 'Contributivo beneficiario'],
            ['code' => '03', 'name' => 'Contributivo adicional'],
            ['code' => '04', 'name' => 'Subsidiado'],
            ['code' => '05', 'name' => 'No afiliado'],
            ['code' => '06', 'name' => 'Especial o Excepción cotizante'],
            ['code' => '07', 'name' => 'Especial o Excepción beneficiario'],
            ['code' => '08', 'name' => 'Personas privadas de la libertad a cargo del Fondo Nacional de Salud'],
            ['code' => '09', 'name' => 'Tomador / Amparado ARL'],
            ['code' => '10', 'name' => 'Tomador / Amparado SOAT'],
            ['code' => '11', 'name' => 'Tomador / Amparado Planes  voluntarios de salud'],
            ['code' => '12', 'name' => 'Particular'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_user_types');
    }
}
