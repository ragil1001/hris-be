<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.ayah_ibu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('mst.karyawan')->onDelete('cascade');
            $table->string('nama_ayah');
            $table->string('nama_ibu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.ayah_ibu');
    }
};
