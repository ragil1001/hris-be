<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique()->index();
            $table->string('password');
            $table->string('nama')->index();
            $table->date('tanggal_masuk')->index();
            $table->date('tanggal_resign')->nullable()->index();
            $table->date('tanggal_aktif')->index();
            $table->enum('status_aktif', ['AKTIF', 'RESIGN'])->index();
            $table->enum('control_status', ['KONTRAK', 'HARIAN'])->index();
            $table->text('keterangan')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->index();
            $table->date('tanggal_lahir');
            $table->unsignedSmallInteger('umur_tahun')->index();
            $table->string('agama');
            $table->string('email')->unique()->index();
            $table->string('no_telp');
            $table->text('alamat');
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('kode_pos', 10);
            $table->string('pendidikan_terakhir');
            $table->string('golongan_darah', 3);
            $table->string('status_tk');
            $table->string('no_kk');
            $table->string('no_ktp')->unique();
            $table->string('bpjs_ketenagakerjaan')->nullable();
            $table->string('npp_bpjs_ketenagakerjaan')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('bu_bpjs_kesehatan')->nullable();
            $table->foreignId('bank_id')->constrained('mst.bank');
            $table->string('no_rekening');
            $table->string('jaminan_pensiun')->nullable();
            $table->string('bu')->nullable();
            $table->decimal('gaji', 15, 2);
            $table->decimal('insentif', 15, 2)->default(0);
            $table->decimal('uang_makan', 15, 2)->default(0);
            $table->foreignId('formasi_id')->nullable()->constrained('mst.formasi');
            $table->foreignId('jabatan_id')->nullable()->constrained('mst.jabatan');
            $table->foreignId('unit_kerja_id')->constrained('mst.unit_kerja');
            $table->foreignId('penempatan_id')->nullable()->constrained('mst.project');
            $table->date('awal_mulai_cuti')->nullable();
            $table->date('masa_berlaku_cuti')->nullable();
            $table->unsignedSmallInteger('potongan_cuti_bersama')->default(0);
            $table->unsignedSmallInteger('sisa_cuti')->default(12);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.karyawan');
    }
};
