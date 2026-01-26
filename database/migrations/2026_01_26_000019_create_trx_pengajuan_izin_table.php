<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.pengajuan_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('mst.karyawan');
            $table->foreignId('project_id')->constrained('mst.project');
            $table->foreignId('kategori_izin_id')->constrained('mst.izin');
            $table->string('sub_kategori')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->unsignedSmallInteger('jumlah_hari')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->enum('status', ['DIAJUKAN', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN'])->index();
            $table->text('catatan_admin')->nullable();
            $table->dateTime('tanggal_pengajuan');
            $table->dateTime('tanggal_konfirmasi')->nullable();
            $table->timestamps();
            $table->index(['karyawan_id', 'project_id', 'kategori_izin_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.pengajuan_izin');
    }
};
