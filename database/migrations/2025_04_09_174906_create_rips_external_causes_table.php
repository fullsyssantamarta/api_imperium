<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsExternalCausesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_external_causes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_external_causes')->insert([
            ['code' => '21','name' => 'Accidente de trabajo'],
            ['code' => '22','name' => 'Accidente en el hogar'],
            ['code' => '23','name' => 'Accidente de tránsito de origen común'],
            ['code' => '24','name' => 'Accidente de tránsito de origen  laboral'],
            ['code' => '25','name' => 'Accidente en el entorno educativo'],
            ['code' => '26','name' => 'Otro tipo de accidente'],
            ['code' => '27','name' => 'Evento catastrófico de origen natural'],
            ['code' => '28','name' => 'Lesión por agresión'],
            ['code' => '29','name' => 'Lesión auto infligida'],
            ['code' => '30','name' => 'Sospecha de violencia física'],
            ['code' => '31','name' => 'Sospecha de violencia psicológica'],
            ['code' => '32','name' => 'Sospecha de violencia sexual'],
            ['code' => '33','name' => 'Sospecha de negligencia y abandono'],
            ['code' => '34','name' => 'IVE relacionado con peligro a la Salud o  vida de la mujer'],
            ['code' => '35','name' => 'IVE por malformación congénita  incompatible con la vida'],
            ['code' => '36','name' => 'IVE por violencia sexual, incesto o por inseminación artificial o  transferencia de ovulo fecundado no consentida'],
            ['code' => '37','name' => 'Evento adverso en salud'],
            ['code' => '38','name' => 'Enfermedad general'],
            ['code' => '39','name' => 'Enfermedad laboral'],
            ['code' => '40','name' => 'Promoción y mantenimiento de la salud – intervenciones individuales'],
            ['code' => '41','name' => 'Intervención colectiva'],
            ['code' => '42','name' => 'Atención de población materno perinatal'],
            ['code' => '43','name' => 'Riesgo ambiental'],
            ['code' => '44','name' => 'Otros eventos Catastróficos'],
            ['code' => '45','name' => 'Accidente de mina antipersonal – MAP'],
            ['code' => '46','name' => 'Accidente de Artefacto Explosivo Improvisado – AEI'],
            ['code' => '47','name' => 'Accidente de Munición Sin Explotar- MUSE'],
            ['code' => '48','name' => 'Otra víctima de conflicto armado colombiano'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_external_causes');
    }
}
