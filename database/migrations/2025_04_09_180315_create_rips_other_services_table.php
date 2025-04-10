<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsOtherServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_other_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_other_services')->insert([
            ['code' => '01', 'name' => 'DISPOSITIVOS MEDICOS E INSUMOS'],
            ['code' => '02', 'name' => 'TRASLADOS'],
            ['code' => '03', 'name' => 'ESTANCIAS'],
            ['code' => '04', 'name' => 'SERVICIOS COMPLEMENTARIOS'],
            ['code' => '05', 'name' => 'HONORARIOS'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_other_services');
    }
}
