<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.cleaning_task_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaning_task_id')->constrained('trx.cleaning_task');
            $table->enum('tipe', ['BEFORE', 'AFTER']);
            $table->string('file_foto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.cleaning_task_foto');
    }
};
