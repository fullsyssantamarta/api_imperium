<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsUserEntryRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_user_entry_routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_user_entry_routes')->insert([
            ['code' => '01', 'name' => 'Demanda espontánea'],
            ['code' => '02', 'name' => 'Derivado de consulta externa'],
            ['code' => '03', 'name' => 'Derivado de urgencias'],
            ['code' => '04', 'name' => 'Derivado de hospitalización'],
            ['code' => '05', 'name' => 'Derivado de sala de cirugía'],
            ['code' => '06', 'name' => 'Derivado de sala de partos'],
            ['code' => '07', 'name' => 'Recién nacido en la institución'],
            ['code' => '08', 'name' => 'Recién nacido en otra institución'],
            ['code' => '09', 'name' => 'Derivado o referido de hospitalización domiciliaria'],
            ['code' => '10', 'name' => 'Derivado de atención domiciliaria'],
            ['code' => '11', 'name' => 'Derivado de telemedicina'],
            ['code' => '12', 'name' => 'Derivado de jornada de salud'],
            ['code' => '13', 'name' => 'Referido de otra institución'],
            ['code' => '14', 'name' => 'Contrarreferido de otra institución'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_user_entry_routes');
    }
}
