<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('nik', 10)->primary();
            $table->string('nama_lengkap', 100);
            $table->string('jabatan', 20);
            $table->string('no_hp', 13)->nullable();
            $table->string('foto', 30)->nullable();
            $table->char('kode_dept', 5)->index('fk_karyawan_dept');
            $table->string('password');
            $table->string('remember_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('karyawan');
    }
};
