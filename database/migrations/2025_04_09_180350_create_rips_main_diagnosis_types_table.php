<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsMainDiagnosisTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_main_diagnosis_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_main_diagnosis_types')->insert([
            ['code' => '01', 'name' => 'Impresión diagnóstica'],
            ['code' => '02', 'name' => 'Confirmado nuevo'],
            ['code' => '03', 'name' => 'Confirmado repetido'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_main_diagnosis_types');
    }
}
