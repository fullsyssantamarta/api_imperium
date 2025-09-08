<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFieldsToTypeDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::table('type_discounts')->insert([
            [
                'name' => 'Recargo no condicionado',
                'code' => '02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Recargo condicionado',
                'code' => '03',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('type_discounts')->whereIn('code', ['02', '03'])->delete();
    }
}
