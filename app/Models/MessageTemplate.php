<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'category',
        'keywords',
        'suggested_groups',
        'is_active',
        'usage_count',
        'user_id'
    ];

    protected $casts = [
        'keywords' => 'array',
        'suggested_groups' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer'
    ];

    // Template categories for smart suggestions
    const CATEGORIES = [
        'weather' => 'Weather Alerts',
        'security' => 'Security Notices',
        'health' => 'Health Updates',
        'infrastructure' => 'Infrastructure Issues',
        'agriculture' => 'Agricultural Alerts',
        'community' => 'Community Announcements',
        'emergency' => 'Emergency Alerts',
        'general' => 'General Information'
    ];

    // Pre-defined templates for Tataouine context
    const DEFAULT_TEMPLATES = [
        [
            'name' => 'Weather Alert - Sandstorm',
            'content' => '⚠️ ALERTE MÉTÉO: Tempête de sable prévue dans votre région. Restez à l\'intérieur et fermez toutes les ouvertures. Évitez de conduire.',
            'category' => 'weather',
            'keywords' => ['sandstorm', 'tempête', 'sable', 'weather', 'météo', 'danger'],
            'suggested_groups' => ['farmers', 'transport', 'residents', 'elderly']
        ],
        [
            'name' => 'Security Notice',
            'content' => '🔒 NOTICE DE SÉCURITÉ: Activité suspecte signalée dans votre secteur. Soyez vigilant et signalez tout comportement inhabituel.',
            'category' => 'security',
            'keywords' => ['security', 'sécurité', 'suspect', 'danger', 'police'],
            'suggested_groups' => ['security', 'residents', 'officials']
        ],
        [
            'name' => 'Health Update',
            'content' => '🏥 MISE À JOUR SANTÉ: Campagne de vaccination disponible au centre de santé local. Rendez-vous sur place ou contactez-nous.',
            'category' => 'health',
            'keywords' => ['health', 'santé', 'vaccination', 'medical', 'hospital'],
            'suggested_groups' => ['health', 'elderly', 'residents']
        ],
        [
            'name' => 'Infrastructure Issue',
            'content' => '🔧 PROBLÈME D\'INFRASTRUCTURE: Interruption d\'électricité prévue pour maintenance. Service rétabli d\'ici [heure].',
            'category' => 'infrastructure',
            'keywords' => ['infrastructure', 'électricité', 'maintenance', 'service', 'interruption'],
            'suggested_groups' => ['business', 'residents', 'officials']
        ],
        [
            'name' => 'Agricultural Alert',
            'content' => '🌾 ALERTE AGRICOLE: Conditions météorologiques favorables pour la plantation. Contactez le service agricole pour conseils.',
            'category' => 'agriculture',
            'keywords' => ['agriculture', 'plantation', 'météo', 'ferme', 'récolte'],
            'suggested_groups' => ['farmers', 'business']
        ],
        [
            'name' => 'Community Meeting',
            'content' => '📢 RÉUNION COMMUNAUTAIRE: Assemblée générale ce [jour] à [heure] à [lieu]. Tous les résidents sont invités.',
            'category' => 'community',
            'keywords' => ['community', 'réunion', 'assemblée', 'générale', 'résidents'],
            'suggested_groups' => ['residents', 'officials', 'youth']
        ]
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get formatted category name
    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? 'General';
    }

    // Check if template matches content
    public function matchesContent($content)
    {
        $content = strtolower($content);
        
        foreach ($this->keywords as $keyword) {
            if (str_contains($content, strtolower($keyword))) {
                return true;
            }
        }
        
        return false;
    }

    // Get relevance score for content
    public function getRelevanceScore($content)
    {
        $content = strtolower($content);
        $score = 0;
        
        foreach ($this->keywords as $keyword) {
            if (str_contains($content, strtolower($keyword))) {
                $score += 1;
            }
        }
        
        return $score;
    }

    // Increment usage count
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    // Scope for active templates
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope by keywords
    public function scopeByKeywords($query, $keywords)
    {
        return $query->whereJsonContains('keywords', $keywords);
    }

    // Get most used templates
    public function scopeMostUsed($query, $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }
}



