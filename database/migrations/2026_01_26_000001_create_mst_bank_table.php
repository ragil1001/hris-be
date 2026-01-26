<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.bank', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank')->unique();
            $table->boolean('is_active')->default(1)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.bank');
    }
};
