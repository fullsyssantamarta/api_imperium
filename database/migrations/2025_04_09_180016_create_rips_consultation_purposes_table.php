<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsConsultationPurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_consultation_purposes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_consultation_purposes')->insert([
            ['code' => '11', 'name' => 'VALORACION INTEGRAL PARA LA PROMOCION Y MANTENIMIENTO'],
            ['code' => '12', 'name' => 'DETECCION TEMPRANA DE ENFERMEDAD GENERAL'],
            ['code' => '13', 'name' => 'DETECCION TEMPRANA DE ENFERMEDAD LABORAL'],
            ['code' => '14', 'name' => 'PROTECCION ESPECIFICA'],
            ['code' => '15', 'name' => 'DIAGNOSTICO'],
            ['code' => '16', 'name' => 'TRATAMIENTO'],
            ['code' => '17', 'name' => 'REHABILITACION'],
            ['code' => '18', 'name' => 'PALIACION'],
            ['code' => '19', 'name' => 'PLANIFICACION FAMILIAR Y ANTICONCEPCION'],
            ['code' => '20', 'name' => 'PROMOCION Y APOYO A LA LACTANCIA MATERNA'],
            ['code' => '21', 'name' => 'ATENCION BASICA DE ORIENTACION FAMILIAR'],
            ['code' => '22', 'name' => 'ATENCION PARA EL CUIDADO PRECONCEPCIONAL'],
            ['code' => '23', 'name' => 'ATENCION PARA EL CUIDADO PRENATAL'],
            ['code' => '24', 'name' => 'INTERRUPCION VOLUNTARIA DEL EMBARAZO'],
            ['code' => '25', 'name' => 'ATENCION DEL PARTO Y PUERPERIO'],
            ['code' => '26', 'name' => 'ATENCION PARA EL CUIDADO DEL RECIEN NACIDO'],
            ['code' => '27', 'name' => 'ATENCION PARA EL SEGUIMIENTO DEL RECIEN NACIDO'],
            ['code' => '28', 'name' => 'PREPARACION PARA LA MATERNIDAD Y LA PATERNIDAD'],
            ['code' => '29', 'name' => 'PROMOCION DE ACTIVIDAD FISICA'],
            ['code' => '30', 'name' => 'PROMOCION DE LA CESACION DEL TABAQUISMO'],
            ['code' => '31', 'name' => 'PREVENCION DEL CONSUMO DE SUSTANCIAS PSICOACTIVAS'],
            ['code' => '32', 'name' => 'PROMOCION DE LA ALIMENTACION SALUDABLE'],
            ['code' => '33', 'name' => 'PROMOCION PARA EL EJERCICIO DE LOS DERECHOS SEXUALES Y DERECHOS REPRODUCTIVOS'],
            ['code' => '34', 'name' => 'PROMOCION PARA EL DESARROLLO DE HABILIDADES PARA LA VIDA'],
            ['code' => '35', 'name' => 'PROMOCION PARA LA CONSTRUCCION DE ESTRATEGIAS DE AFRONTAMIENTO FRENTE A SUCESOS VITALES'],
            ['code' => '36', 'name' => 'PROMOCION DE LA SANA CONVIVENCIA Y EL TEJIDO  SOCIAL'],
            ['code' => '37', 'name' => 'PROMOCION DE UN AMBIENTE SEGURO Y DE CUIDADO Y PROTECCION DEL AMBIENTE'],
            ['code' => '38', 'name' => 'PROMOCION DEL EMPODERAMIENTO PARA EL EJERCICIO DEL DERECHO A LA SALUD'],
            ['code' => '39', 'name' => 'PROMOCION PARA LA ADOPCION DE PRACTICAS DE CRIANZA Y CUIDADO PARA LA SALUD'],
            ['code' => '40', 'name' => 'PROMOCION DE LA CAPACIDAD DE LA AGENCIA Y CUIDADO DE LA SALUD'],
            ['code' => '41', 'name' => 'DESARROLLO DE HABILIDADES COGNITIVAS'],
            ['code' => '42', 'name' => 'INTERVENCION COLECTIVA'],
            ['code' => '43', 'name' => 'MODIFICACION DE LA ESTETICA CORPORAL (FINES ESTETICOS)'],
            ['code' => '44', 'name' => 'OTRA'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_consultation_purposes');
    }
}
