<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.informasi_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('informasi_id')->constrained('trx.informasi')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('mst.project');
            $table->foreignId('jabatan_id')->nullable()->constrained('mst.jabatan');
            $table->foreignId('formasi_id')->nullable()->constrained('mst.formasi');
            $table->foreignId('unit_kerja_id')->nullable()->constrained('mst.unit_kerja');
            $table->foreignId('karyawan_id')->nullable()->constrained('mst.karyawan');
            $table->enum('tipe_target', ['PROJECT', 'JABATAN', 'FORMASI', 'UNIT_KERJA', 'KARYAWAN']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.informasi_target');
    }
};
