<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_zones')->insert([
            ['code' => '01', 'name' => 'Rural'],
            ['code' => '02', 'name' => 'Urbano'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_zones');
    }
}
