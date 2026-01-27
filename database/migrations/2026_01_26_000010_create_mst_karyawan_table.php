<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.karyawan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nik')->unique()->index();
            $table->string('nama')->index();
            $table->date('tanggal_masuk')->index();
            $table->date('tanggal_resign')->nullable()->index();
            $table->date('tanggal_aktif')->index();
            $table->enum('status_karyawan', ['KONTRAK', 'TETAP']);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('email')->nullable();
            $table->string('alamat')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('no_ktp')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('no_bpjs_kesehatan')->nullable();
            $table->string('npp_bpjs_kesehatan')->nullable();
            $table->string('bu_bpjs_kesehatan')->nullable();
            $table->string('no_bpjs_ketenagakerjaan')->nullable();
            $table->string('npp_bpjs_ketenagakerjaan')->nullable();
            $table->string('no_npwp')->nullable();
            $table->enum('status_pernikahan', ['MENIKAH', 'BELUM_MENIKAH', 'CERAI'])->nullable();
            $table->unsignedTinyInteger('jumlah_anak')->default(0);
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('nama_institusi')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('mst.bank');
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('insentif', 15, 2)->default(0);
            $table->decimal('uang_makan', 15, 2)->default(0);
            $table->decimal('potongan', 15, 2)->default(0);
            $table->foreignId('formasi_id')->nullable()->constrained('mst.formasi');
            $table->foreignId('jabatan_id')->nullable()->constrained('mst.jabatan');
            $table->foreignId('unit_kerja_id')->constrained('mst.unit_kerja');
            $table->foreignId('penempatan_id')->nullable()->constrained('mst.project');
            $table->date('awal_mulai_cuti')->nullable();
            $table->date('masa_berlaku_cuti')->nullable();
            $table->unsignedSmallInteger('potongan_cuti_bersama')->default(0);
            $table->unsignedSmallInteger('sisa_cuti')->default(12);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('auth.users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.karyawan');
    }
};
