<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDataToRipsGenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('rips_genders')->where('code', 'I')->update(['name' => 'Indeterminado o Intersexual']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){ }
}
