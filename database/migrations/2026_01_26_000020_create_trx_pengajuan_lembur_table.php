<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.pengajuan_lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('mst.karyawan');
            $table->foreignId('project_id')->constrained('mst.project');
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('jenis_lembur', ['K', 'L'])->comment('K=Hari Kerja, L=Hari Libur');
            $table->text('keterangan')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->enum('status', ['DIAJUKAN', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN'])->index();
            $table->text('catatan_admin')->nullable();
            $table->dateTime('tanggal_pengajuan');
            $table->dateTime('tanggal_konfirmasi')->nullable();
            $table->timestamps();
            $table->index(['karyawan_id', 'project_id', 'tanggal_lembur', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.pengajuan_lembur');
    }
};
