<?php

namespace Database\Seeders;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();

        if (empty($userIds)) {
            $this->call(UserSeeder::class);
            $userIds = User::query()->pluck('id')->all();
        }

        $incidents = [
            [
                'type' => 'bug',
                'title' => 'Login timeout issue',
                'description' => 'Users are timing out on login after inactivity.',
                'acting_officer' => 'System Admin',
            ],
            [
                'type' => 'request',
                'title' => 'Access request pending',
                'description' => 'Support request was submitted for a new reporting role.',
                'acting_officer' => 'IT Support',
            ],
            [
                'type' => 'security',
                'title' => 'Failed VPN authorization',
                'description' => 'User authentication attempts triggered a security review.',
                'acting_officer' => 'Security Team',
            ],
        ];

        foreach ($incidents as $incident) {
            Incident::query()->create([
                'user_id' => $userIds[array_rand($userIds)],
                'type' => $incident['type'],
                'title' => $incident['title'],
                'description' => $incident['description'],
                'data' => now(),
                'acting_officer' => $incident['acting_officer'],
            ]);
        }
    }
}
