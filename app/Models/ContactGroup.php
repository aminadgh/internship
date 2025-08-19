<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area_name',
        'user_id',
        'description',
        'category',
        'tags',
        'is_active',
        'geometry'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean'
    ];

    // Group categories for smart targeting
    const CATEGORIES = [
        'emergency' => 'Emergency Contacts',
        'officials' => 'Local Officials',
        'farmers' => 'Farmers',
        'business' => 'Business Owners',
        'residents' => 'General Residents',
        'youth' => 'Youth Groups',
        'elderly' => 'Elderly Care',
        'health' => 'Healthcare Workers',
        'security' => 'Security Personnel',
        'transport' => 'Transport Workers'
    ];

    // Smart tags for content-based targeting
    const SMART_TAGS = [
        'weather' => ['farmers', 'transport', 'residents'],
        'security' => ['security', 'officials', 'residents'],
        'health' => ['health', 'elderly', 'residents'],
        'infrastructure' => ['business', 'residents', 'transport'],
        'agriculture' => ['farmers', 'business'],
        'education' => ['youth', 'residents'],
        'emergency' => ['emergency', 'officials', 'security'],
        'community' => ['residents', 'youth', 'elderly']
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get formatted category name
    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? 'General';
    }

    // Check if group matches content keywords
    public function matchesContent($content)
    {
        $content = strtolower($content);
        $keywords = $this->getKeywords();
        
        foreach ($keywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }
        
        return false;
    }

    // Get relevant keywords for this group
    public function getKeywords()
    {
        $keywords = [];
        
        // Add category-based keywords
        switch ($this->category) {
            case 'farmers':
                $keywords = ['weather', 'rain', 'drought', 'harvest', 'crops', 'agriculture', 'farming'];
                break;
            case 'emergency':
                $keywords = ['emergency', 'urgent', 'danger', 'evacuation', 'safety', 'alert'];
                break;
            case 'officials':
                $keywords = ['official', 'announcement', 'policy', 'decision', 'meeting', 'government'];
                break;
            case 'health':
                $keywords = ['health', 'medical', 'hospital', 'doctor', 'vaccination', 'sickness'];
                break;
            case 'security':
                $keywords = ['security', 'police', 'crime', 'safety', 'investigation', 'patrol'];
                break;
            case 'business':
                $keywords = ['business', 'market', 'commerce', 'trade', 'economic', 'financial'];
                break;
            case 'transport':
                $keywords = ['transport', 'road', 'traffic', 'vehicle', 'travel', 'commute'];
                break;
            case 'residents':
                $keywords = ['community', 'neighborhood', 'local', 'residents', 'area', 'district'];
                break;
        }
        
        // Add custom tags
        if ($this->tags) {
            $keywords = array_merge($keywords, $this->tags);
        }
        
        return array_unique($keywords);
    }

    // Get relevance score for content
    public function getRelevanceScore($content)
    {
        $content = strtolower($content);
        $keywords = $this->getKeywords();
        $score = 0;
        
        foreach ($keywords as $keyword) {
            if (str_contains($content, $keyword)) {
                $score += 1;
            }
        }
        
        return $score;
    }

    // Scope for active groups
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope by tags
    public function scopeByTags($query, $tags)
    {
        return $query->whereJsonContains('tags', $tags);
    }
}
