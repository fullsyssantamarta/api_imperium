<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsProcedureCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_procedure_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->text('description')->nullable();
            $table->string('is_enabled');
            $table->timestamps();
        });

        // Insert data from CSV
        $this->insertDataFromCsv();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rips_procedure_codes');
    }

    protected function insertDataFromCsv()
    {
        $csvFile = public_path('csv/rips_procedure_codes.csv');
        $data = array_map('str_getcsv', file($csvFile));
        $header = array_shift($data); // Get the header row

        foreach ($data as $row) {
            $rowData = array_combine($header, $row);
            DB::table('rips_procedure_codes')->insert([
                'code' => $rowData['Codigo'],
                'name' => $rowData['Nombre'],
                'description' => $rowData['Descripcion'] ?? null,
                'is_enabled' => $rowData['Habilitado'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
