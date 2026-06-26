<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds application users (admin/staff accounts) — distinct from
 * Formateur/Stagiaire, which are domain entities with their own dedicated
 * seeders. Goes through UserFactory so password hashing and the
 * verified-email default stay centralized in one place rather than
 * duplicated here.
 */
class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Administrateur CMC',
            'email' => 'admin@cmc-data.local',
        ]);

        User::factory()->count(4)->create();
    }
}
