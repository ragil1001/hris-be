<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mst.project_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mst.project')->onDelete('cascade');
            $table->foreignId('izin_id')->constrained('mst.izin')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['project_id', 'izin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst.project_izin');
    }
};
