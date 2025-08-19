<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'is_emergency',
        'priority',
        'broadcast_type',
        'emergency_category',
        'sent_at',
        'scheduled_at',
        'requires_approval',
        'approved_by',
        'approved_at',
        'user_id',
        'total_cost',
        'total_recipients',
        'cost_currency',
    ];

    protected $casts = [
        'is_emergency' => 'boolean',
        'requires_approval' => 'boolean',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_cost' => 'decimal:4',
        'total_recipients' => 'integer',
    ];

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    // Broadcast type constants
    const BROADCAST_GROUP = 'group';
    const BROADCAST_ALL = 'all';
    const BROADCAST_ZONE = 'zone';

    // Emergency categories
    const EMERGENCY_CATEGORIES = [
        'health' => 'Health Emergency',
        'safety' => 'Safety Alert',
        'utility' => 'Utility Interruption',
        'weather' => 'Weather Warning',
        'security' => 'Security Alert',
        'community' => 'Community Notice',
        'other' => 'Other Emergency'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Check if message is emergency
    public function isEmergency(): bool
    {
        return $this->is_emergency || $this->priority === self::PRIORITY_CRITICAL;
    }

    // Check if message requires approval
    public function needsApproval(): bool
    {
        return $this->requires_approval && !$this->approved_at;
    }

    // Check if message is approved
    public function isApproved(): bool
    {
        return !$this->requires_approval || $this->approved_at;
    }

    // Get priority color for UI
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_CRITICAL => 'red',
            default => 'blue'
        };
    }

    // Get emergency category label
    public function getEmergencyCategoryLabel(): string
    {
        return self::EMERGENCY_CATEGORIES[$this->emergency_category] ?? 'Unknown';
    }

    // Calculate total cost for this message
    public function calculateTotalCost(): float
    {
        $pricing = \App\Models\SmsPricing::getDefaultPricing();
        if (!$pricing) {
            return 0;
        }

        $messageLength = strlen($this->content);
        $costPerMessage = $pricing->calculateCost($messageLength);
        
        return $costPerMessage * $this->recipients()->count();
    }

    // Get formatted total cost
    public function getFormattedTotalCost(): string
    {
        return '$' . number_format($this->total_cost, 4);
    }

    // Get cost per recipient
    public function getCostPerRecipient(): float
    {
        if ($this->total_recipients > 0) {
            return $this->total_cost / $this->total_recipients;
        }
        return 0;
    }

    // Get formatted cost per recipient
    public function getFormattedCostPerRecipient(): string
    {
        return '$' . number_format($this->getCostPerRecipient(), 4);
    }

    // Update total cost and recipient count
    public function updateCosts(): void
    {
        $this->total_recipients = $this->recipients()->count();
        $this->total_cost = $this->calculateTotalCost();
        $this->save();
    }
}
