<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.project_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mst.project')->onDelete('cascade');
            $table->foreignId('jabatan_id')->constrained('mst.jabatan')->onDelete('cascade');
            $table->boolean('is_active')->default(1)->index();
            $table->timestamps();
            $table->unique(['project_id', 'jabatan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.project_jabatan');
    }
};
