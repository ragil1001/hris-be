<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.unit_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit')->unique();
            $table->boolean('is_active')->default(1)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.unit_kerja');
    }
};
