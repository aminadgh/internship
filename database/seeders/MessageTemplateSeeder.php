<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MessageTemplate;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Weather Alert - Sandstorm',
                'content' => '⚠️ ALERTE MÉTÉO: Tempête de sable prévue dans votre région. Restez à l\'intérieur et fermez toutes les ouvertures. Évitez de conduire.',
                'category' => 'weather',
                'keywords' => ['sandstorm', 'tempête', 'sable', 'weather', 'météo', 'danger'],
                'suggested_groups' => ['farmers', 'transport', 'residents', 'elderly'],
                'is_active' => true,
                'usage_count' => 0
            ],
            [
                'name' => 'Security Notice',
                'content' => '🔒 NOTICE DE SÉCURITÉ: Activité suspecte signalée dans votre secteur. Soyez vigilant et signalez tout comportement inhabituel.',
                'category' => 'security',
                'keywords' => ['security', 'sécurité', 'suspect', 'danger', 'police'],
                'suggested_groups' => ['security', 'residents', 'officials'],
                'is_active' => true,
                'usage_count' => 0
            ],
            [
                'name' => 'Health Update',
                'content' => '🏥 MISE À JOUR SANTÉ: Campagne de vaccination disponible au centre de santé local. Rendez-vous sur place ou contactez-nous.',
                'category' => 'health',
                'keywords' => ['health', 'santé', 'vaccination', 'medical', 'hospital'],
                'suggested_groups' => ['health', 'elderly', 'residents'],
                'is_active' => true,
                'usage_count' => 0
            ],
            [
                'name' => 'Infrastructure Issue',
                'content' => '🔧 PROBLÈME D\'INFRASTRUCTURE: Interruption d\'électricité prévue pour maintenance. Service rétabli d\'ici [heure].',
                'category' => 'infrastructure',
                'keywords' => ['infrastructure', 'électricité', 'maintenance', 'service', 'interruption'],
                'suggested_groups' => ['business', 'residents', 'officials'],
                'is_active' => true,
                'usage_count' => 0
            ],
            [
                'name' => 'Agricultural Alert',
                'content' => '🌾 ALERTE AGRICOLE: Conditions météorologiques favorables pour la plantation. Contactez le service agricole pour conseils.',
                'category' => 'agriculture',
                'keywords' => ['agriculture', 'plantation', 'météo', 'ferme', 'récolte'],
                'suggested_groups' => ['farmers', 'business'],
                'is_active' => true,
                'usage_count' => 0
            ],
            [
                'name' => 'Community Meeting',
                'content' => '📢 RÉUNION COMMUNAUTAIRE: Assemblée générale ce [jour] à [heure] à [lieu]. Tous les résidents sont invités.',
                'category' => 'community',
                'keywords' => ['community', 'réunion', 'assemblée', 'générale', 'résidents'],
                'suggested_groups' => ['residents', 'officials', 'youth'],
                'is_active' => true,
                'usage_count' => 0
            ]
        ];

        foreach ($templates as $template) {
            MessageTemplate::create($template);
        }
    }
}



