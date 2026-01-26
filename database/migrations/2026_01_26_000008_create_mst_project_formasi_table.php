<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.project_formasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mst.project')->onDelete('cascade');
            $table->foreignId('formasi_id')->constrained('mst.formasi')->onDelete('cascade');
            $table->boolean('is_active')->default(1)->index();
            $table->timestamps();
            $table->unique(['project_id', 'formasi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.project_formasi');
    }
};
