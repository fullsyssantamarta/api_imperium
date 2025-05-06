<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsServiceGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_service_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_service_groups')->insert([
            ['code' => '01', 'name' => 'Consulta externa'],
            ['code' => '02', 'name' => 'Apoyo diagnóstico y complementación  terapéutica'],
            ['code' => '03', 'name' => 'Internación'],
            ['code' => '04', 'name' => 'Quirúrgicos'],
            ['code' => '05', 'name' => 'Atención inmediata'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_service_groups');
    }
}
