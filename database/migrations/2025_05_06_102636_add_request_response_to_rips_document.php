<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequestResponseToRipsDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rips_documents', function (Blueprint $table) {
            $table->json('request_api')->nullable()->after('services');
            $table->json('response_api')->nullable()->after('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rips_documents', function (Blueprint $table) {
            $table->dropColumn('request_api');
            $table->dropColumn('response_api');
        });
    }
}
