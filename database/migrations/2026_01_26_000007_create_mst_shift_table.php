<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mst.project')->onDelete('cascade');
            $table->string('kode_shift');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.shift');
    }
};
