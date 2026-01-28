<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.izin', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('sub_kategori')->nullable();
            $table->unsignedSmallInteger('jumlah_hari')->nullable();
            $table->boolean('is_active')->default(1)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.izin');
    }
};
