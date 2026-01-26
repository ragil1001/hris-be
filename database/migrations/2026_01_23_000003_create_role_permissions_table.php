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
        Schema::create('auth.role_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->foreign('role_id')
                ->references('id')
                ->on('auth.roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on('auth.permissions')
                ->onDelete('cascade');

            $table->unique(['role_id', 'permission_id']);
            $table->index('role_id');
            $table->index('permission_id');
        });

        // Add foreign key to users table
        Schema::table('auth.users', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('auth.roles')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auth.users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });

        Schema::dropIfExists('auth.role_permissions');
    }
};
