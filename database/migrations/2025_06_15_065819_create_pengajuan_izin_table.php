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
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nik', 10)->nullable()->index('fk_izin_karyawan');
            $table->date('tgl_izin')->nullable();
            $table->char('status', 1)->nullable();
            $table->string('keterangan')->nullable();
            $table->char('status_pengajuan', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
