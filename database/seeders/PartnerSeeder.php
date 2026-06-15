<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert Al-fikra
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

        // Insert Rex
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
}
