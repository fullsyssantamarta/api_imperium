<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRipsCiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rips_cies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->text('name');
            $table->text('description')->nullable();
            $table->string('is_enabled');
            $table->text('extra')->nullable();
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
        Schema::dropIfExists('rips_cies');
    }

    protected function insertDataFromCsv()
    {
        $csvFile = public_path('csv/rips_cies.csv');
        $data = array_map('str_getcsv', file($csvFile));
        $header = array_shift($data); // Get the header row

        foreach ($data as $row) {
            $rowData = array_combine($header, $row);
            DB::table('rips_cies')->insert([
                'code' => $rowData['code'],
                'name' => $rowData['name'],
                'description' => $rowData['description'] ?? null,
                'is_enabled' => $rowData['is_enabled'],
                'extra' => $rowData['extra'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
