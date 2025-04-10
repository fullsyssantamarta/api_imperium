<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsCareModalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_care_modalities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_care_modalities')->insert([
            ['code' => '01', 'name' => 'Intramural'],
            ['code' => '02', 'name' => 'Extramural unidad móvil'],
            ['code' => '03', 'name' => 'Extramural domiciliaria'],
            ['code' => '04', 'name' => 'Extramural jornada de salud'],
            ['code' => '06', 'name' => 'Telemedicina interactiva'],
            ['code' => '07', 'name' => 'Telemedicina no interactiva'],
            ['code' => '08', 'name' => 'Telemedicina telexperticia'],
            ['code' => '09', 'name' => 'Telemedicina telemonitoreo'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_care_modalities');
    }
}
