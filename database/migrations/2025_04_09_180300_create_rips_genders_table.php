<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsGendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_genders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        // Insert default data
        DB::table('rips_genders')->insert([
            ['code' => 'H', 'name' => 'Hombre'],
            ['code' => 'I', 'name' => 'Indeterminado o Itersexual'],
            ['code' => 'M', 'name' => 'Mujer'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_genders');
    }
}
