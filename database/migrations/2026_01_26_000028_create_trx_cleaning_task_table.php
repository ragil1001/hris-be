<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.cleaning_task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaning_program_id')->constrained('trx.cleaning_program');
            $table->foreignId('karyawan_id')->constrained('mst.karyawan');
            $table->date('tanggal');
            $table->foreignId('shift_id')->constrained('mst.shift');
            $table->enum('status', ['SELESAI', 'BELUM_SELESAI', 'TIDAK_SELESAI']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->dateTime('waktu_pengerjaan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('mst.karyawan');
            $table->unique(['cleaning_program_id', 'karyawan_id', 'tanggal', 'shift_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.cleaning_task');
    }
};
