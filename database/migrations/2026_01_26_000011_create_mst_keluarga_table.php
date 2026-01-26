<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('mst.karyawan')->onDelete('cascade');
            $table->enum('hubungan', ['ISTRI', 'SUAMI', 'ANAK']);
            $table->string('nama');
            $table->string('no_ktp')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->unsignedTinyInteger('urutan_anak')->nullable();
            $table->unique(['karyawan_id', 'hubungan', 'urutan_anak']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.keluarga');
    }
};
