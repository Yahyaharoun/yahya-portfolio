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
        DB::table('partnerships')->updateOrInsert(
            ['company' => 'Al-Fikra'],
            [
                'contact_email' => 'contact@al-fikra.com',
                'contact_phone' => '+1234567890',
                'type' => 'Partenaire Stratégique',
                'message' => 'Confiance mutuelle et collaboration',
                'status' => 'treated',
                'logo_path' => 'partnerships/al_fikra.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('partnerships')->updateOrInsert(
            ['company' => 'Rex'],
            [
                'contact_email' => 'hello@rex.com',
                'contact_phone' => '+0987654321',
                'type' => 'Client',
                'message' => 'Projets de développement web',
                'status' => 'treated',
                'logo_path' => 'partnerships/rex.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('partnerships')->whereIn('company', ['Al-Fikra', 'Rex'])->delete();
    }
};
