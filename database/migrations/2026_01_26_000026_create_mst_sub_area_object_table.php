<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mst.sub_area_object', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_area_id')->constrained('mst.sub_area');
            $table->foreignId('object_id')->constrained('mst.object');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst.sub_area_object');
    }
};
