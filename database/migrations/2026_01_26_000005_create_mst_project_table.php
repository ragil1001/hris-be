<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.project', function (Blueprint $table) {
            $table->id();
            $table->string('nama_project')->unique();
            $table->boolean('is_active')->default(1)->index();
            $table->date('tanggal_mulai')->nullable();
            $table->string('bagian')->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->unsignedInteger('radius_absensi')->default(0);
            $table->json('pengecualian_formasi')->nullable();
            $table->unsignedSmallInteger('waktu_toleransi')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.project');
    }
};
