<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.area_sub_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('mst.area');
            $table->foreignId('sub_area_id')->constrained('mst.sub_area');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.area_sub_area');
    }
};
