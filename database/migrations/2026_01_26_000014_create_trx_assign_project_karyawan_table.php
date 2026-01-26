<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.assign_project_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('mst.karyawan');
            $table->foreignId('project_id')->constrained('mst.project');
            $table->foreignId('formasi_id')->nullable()->constrained('mst.formasi');
            $table->foreignId('jabatan_id')->nullable()->constrained('mst.jabatan');
            $table->date('tanggal_assign');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['AKTIF', 'NONAKTIF'])->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->index(['karyawan_id', 'project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.assign_project_karyawan');
    }
};
