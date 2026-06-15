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
        // Only run raw SQL if using PostgreSQL (Supabase)
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::unprepared('
                -- Hardening visitors table
                DROP POLICY IF EXISTS "Allow anonymous inserts and selects on visitors" ON visitors;
                
                CREATE POLICY "Allow anonymous inserts on visitors"
                ON visitors
                FOR INSERT 
                TO anon
                WITH CHECK (true);

                -- Hardening page_visits table
                DROP POLICY IF EXISTS "Allow anonymous inserts, updates, and selects on page_visits" ON page_visits;
                
                CREATE POLICY "Allow anonymous inserts and updates on page_visits"
                ON page_visits
                FOR INSERT 
                TO anon
                WITH CHECK (true);

                CREATE POLICY "Allow anonymous updates on page_visits"
                ON page_visits
                FOR UPDATE 
                TO anon
                USING (true)
                WITH CHECK (true);
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::unprepared('
                DROP POLICY IF EXISTS "Allow anonymous inserts on visitors" ON visitors;
                DROP POLICY IF EXISTS "Allow anonymous inserts and updates on page_visits" ON page_visits;
                DROP POLICY IF EXISTS "Allow anonymous updates on page_visits" ON page_visits;

                CREATE POLICY "Allow anonymous inserts and selects on visitors"
                ON visitors
                FOR ALL 
                TO anon
                USING (true)
                WITH CHECK (true);

                CREATE POLICY "Allow anonymous inserts, updates, and selects on page_visits"
                ON page_visits
                FOR ALL 
                TO anon
                USING (true)
                WITH CHECK (true);
            ');
        }
    }
};
