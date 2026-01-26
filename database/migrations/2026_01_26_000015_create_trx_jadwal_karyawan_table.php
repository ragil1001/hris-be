<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.jadwal_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('mst.karyawan');
            $table->foreignId('project_id')->constrained('mst.project');
            $table->date('tanggal');
            $table->foreignId('shift_id')->nullable()->constrained('mst.shift');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['karyawan_id', 'project_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.jadwal_karyawan');
    }
};
