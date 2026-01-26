<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trx.cleaning_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mst.project');
            $table->foreignId('area_id')->constrained('mst.area');
            $table->foreignId('sub_area_id')->constrained('mst.sub_area');
            $table->foreignId('object_id')->constrained('mst.object');
            $table->text('uraian_pekerjaan')->nullable();
            $table->string('pic');
            $table->json('shift_ids');
            $table->enum('tipe_jadwal', ['DAILY', 'WEEKLY', 'MONTHLY']);
            $table->json('hari_weekly')->nullable();
            $table->json('minggu_monthly')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trx.cleaning_program');
    }
};
