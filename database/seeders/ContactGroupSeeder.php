<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactGroup;
use App\Models\User;

class ContactGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first user (or create one if none exists)
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $groups = [
            [
                'name' => 'Emergency Contacts',
                'area_name' => 'Tataouine Center',
                'category' => 'emergency',
                'tags' => ['urgent', 'response', 'authority'],
                'description' => 'Primary emergency response contacts',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Local Officials',
                'area_name' => 'Tataouine',
                'category' => 'officials',
                'tags' => ['government', 'administration', 'decision'],
                'description' => 'Local government officials and administrators',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Farmers Association',
                'area_name' => 'Tataouine Rural',
                'category' => 'farmers',
                'tags' => ['agriculture', 'crops', 'weather', 'harvest'],
                'description' => 'Local farmers and agricultural workers',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Business Owners',
                'area_name' => 'Tataouine Market',
                'category' => 'business',
                'tags' => ['commerce', 'market', 'economic', 'trade'],
                'description' => 'Local business owners and shopkeepers',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'General Residents',
                'area_name' => 'Tataouine',
                'category' => 'residents',
                'tags' => ['community', 'neighborhood', 'local'],
                'description' => 'General population of Tataouine',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Youth Groups',
                'area_name' => 'Tataouine',
                'category' => 'youth',
                'tags' => ['young', 'students', 'education', 'future'],
                'description' => 'Young people and student groups',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Elderly Care',
                'area_name' => 'Tataouine',
                'category' => 'elderly',
                'tags' => ['senior', 'health', 'care', 'vulnerable'],
                'description' => 'Elderly residents and care providers',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Healthcare Workers',
                'area_name' => 'Tataouine Medical',
                'category' => 'health',
                'tags' => ['medical', 'hospital', 'doctor', 'nurse', 'vaccination'],
                'description' => 'Healthcare professionals and medical staff',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Security Personnel',
                'area_name' => 'Tataouine',
                'category' => 'security',
                'tags' => ['police', 'security', 'safety', 'patrol'],
                'description' => 'Police and security personnel',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Transport Workers',
                'area_name' => 'Tataouine Transport',
                'category' => 'transport',
                'tags' => ['driver', 'vehicle', 'road', 'travel', 'commute'],
                'description' => 'Transport workers and drivers',
                'is_active' => true,
                'user_id' => $user->id
            ]
        ];

        foreach ($groups as $group) {
            ContactGroup::create($group);
        }
    }
}



