<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create schemas for organizing tables
        DB::statement('CREATE SCHEMA IF NOT EXISTS auth');
        DB::statement('CREATE SCHEMA IF NOT EXISTS mst');
        DB::statement('CREATE SCHEMA IF NOT EXISTS trx');
        DB::statement('CREATE SCHEMA IF NOT EXISTS opt');

        // Grant privileges
        DB::statement('GRANT ALL ON SCHEMA auth TO postgres');
        DB::statement('GRANT ALL ON SCHEMA mst TO postgres');
        DB::statement('GRANT ALL ON SCHEMA trx TO postgres');
        DB::statement('GRANT ALL ON SCHEMA opt TO postgres');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS opt CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS trx CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS mst CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS auth CASCADE');
    }
};
