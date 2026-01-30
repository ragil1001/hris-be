<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mst.karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Identity & Personal
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('agama')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            
            // Employment Details
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_aktif')->nullable();
            $table->date('tanggal_resign')->nullable();
            $table->enum('status', ['AKTIF', 'RESIGN'])->default('AKTIF');
            $table->enum('control_status', ['KONTRAK', 'HARIAN'])->default('KONTRAK');
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->nullOnDelete();
            $table->foreignId('formasi_id')->nullable()->constrained('formasi')->nullOnDelete();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerja')->nullOnDelete();
            $table->foreignId('penempatan_id')->nullable()->constrained('project')->nullOnDelete();
            $table->string('bu')->nullable(); // Business Unit
            
            // Contact & Address
            $table->string('no_telepon')->nullable(); // Mapped to NO TELP / WA
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('kode_pos')->nullable();

            // Documents / IDs
            $table->string('no_ktp')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('no_bpjs_ketenagakerjaan')->nullable();
            $table->string('npp_bpjs_ketenagakerjaan')->nullable();
            $table->string('no_bpjs_kesehatan')->nullable();
            $table->string('bu_bpjs_kesehatan')->nullable();

            // Compensation
            $table->decimal('gaji_pokok', 15, 2)->nullable();
            $table->decimal('insentif', 15, 2)->nullable();
            $table->decimal('uang_makan', 15, 2)->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('bank')->nullOnDelete();
            $table->string('no_rekening')->nullable();
            $table->string('no_jaminan_pensiun')->nullable();

            // Leave / Cuti
            $table->date('awal_mulai_cuti')->nullable();
            $table->date('masa_berlaku_cuti')->nullable();
            $table->integer('potongan_cuti_bersama')->default(0);
            $table->integer('sisa_cuti')->default(0);
            
            $table->text('keterangan')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst.karyawan');
    }
};
