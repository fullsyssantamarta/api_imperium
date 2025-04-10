<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsCollectionConceptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_collection_concepts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_collection_concepts')->insert([
            ['code' => '01', 'name' => 'COPAGO', 'description' => 'COPAGO'],
            ['code' => '02', 'name' => 'CUOTA MODERADORA', 'description' => 'CUOTA MODERADORA'],
            ['code' => '03', 'name' => 'PAGOS COMPARTIDOS EN PLANES VOLUNTARIOS DE SALUD', 'description' => 'PAGOS COMPARTIDOS EN PLANES VOLUNTARIOS DE SALUD'],
            ['code' => '04', 'name' => 'ANTICIPO', 'description' => 'ANTICIPO'],
            ['code' => '05', 'name' => 'NO APLICA', 'description' => 'NO APLICA'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_collection_concepts');
    }
}
