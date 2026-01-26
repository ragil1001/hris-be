<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.tukar_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mst.project');
            $table->date('tanggal_shift');
            $table->foreignId('shift_id')->constrained('mst.shift');
            $table->foreignId('karyawan_pengaju_id')->constrained('mst.karyawan');
            $table->foreignId('karyawan_target_id')->constrained('mst.karyawan');
            $table->enum('status', ['DIAJUKAN', 'DITERIMA', 'DITOLAK', 'DIBATALKAN'])->index();
            $table->text('catatan_pengaju')->nullable();
            $table->text('catatan_target')->nullable();
            $table->dateTime('tanggal_pengajuan');
            $table->dateTime('tanggal_konfirmasi')->nullable();
            $table->timestamps();
            $table->index(['project_id', 'tanggal_shift', 'shift_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.tukar_shift');
    }
};
