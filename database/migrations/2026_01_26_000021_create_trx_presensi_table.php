<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_karyawan_id')->constrained('trx.jadwal_karyawan');
            $table->date('tanggal');
            $table->enum('jenis', ['MASUK', 'PULANG']);
            $table->dateTime('waktu_presensi');
            $table->string('foto');
            $table->decimal('lokasi_longitude', 10, 7);
            $table->decimal('lokasi_latitude', 10, 7);
            $table->enum('status', ['HADIR', 'TERLAMBAT', 'IZIN', 'ALPA', 'LIBUR', 'TPP', 'LEMBUR', 'LEMBUR_PENDING', 'PULANG_CEPAT']);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['jadwal_karyawan_id', 'tanggal', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.presensi');
    }
};
