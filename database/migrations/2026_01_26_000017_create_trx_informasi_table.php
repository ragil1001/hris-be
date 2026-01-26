<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.informasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('isi');
            $table->string('file_lampiran')->nullable();
            $table->dateTime('tanggal_publish');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.informasi');
    }
};
