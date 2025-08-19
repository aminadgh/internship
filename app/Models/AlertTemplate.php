<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'category',
        'priority',
        'is_emergency',
        'broadcast_type',
        'description',
        'icon',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_emergency' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Template categories
    const CATEGORIES = [
        'natural_disaster' => 'Natural Disaster',
        'security_alert' => 'Security Alert',
        'health_emergency' => 'Health Emergency',
        'infrastructure' => 'Infrastructure',
        'weather' => 'Weather Warning',
        'traffic' => 'Traffic Alert',
        'general' => 'General Alert'
    ];

    // Priority levels
    const PRIORITIES = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    // Broadcast types
    const BROADCAST_TYPES = [
        'group' => 'Specific Groups',
        'zone' => 'Zone-based',
        'all' => 'All Contacts'
    ];

    // Get formatted category name
    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? 'Unknown';
    }

    // Get formatted priority name
    public function getPriorityNameAttribute()
    {
        return self::PRIORITIES[$this->priority] ?? 'Normal';
    }

    // Get formatted broadcast type name
    public function getBroadcastTypeNameAttribute()
    {
        return self::BROADCAST_TYPES[$this->broadcast_type] ?? 'Group';
    }

    // Scope for active templates
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for emergency templates
    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    // Scope by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope by priority
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
