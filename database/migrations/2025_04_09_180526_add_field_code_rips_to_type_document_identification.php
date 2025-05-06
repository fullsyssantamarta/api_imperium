<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldCodeRipsToTypeDocumentIdentification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('type_document_identifications', function (Blueprint $table) {
            $table->string('code_rips', 2)->nullable()->after('code');
        });

        // Actualizar los registros con los códigos RIPS
        DB::table('type_document_identifications')->updateOrInsert(['id' => 1], ['code_rips' => 'RC']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 2], ['code_rips' => 'TI']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 3], ['code_rips' => 'CC']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 4], ['code_rips' => 'CN']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 5], ['code_rips' => 'CE']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 6], ['code_rips' => 'NI']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 7], ['code_rips' => 'PA']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 8], ['code_rips' => 'DE']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 9], ['code_rips' => 'NI']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 11], ['code_rips' => 'PE']);
        DB::table('type_document_identifications')->updateOrInsert(['id' => 12], ['code_rips' => 'PT']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('type_document_identification', function (Blueprint $table) {
            $table->dropColumn('code_rips');
        });
    }
}
