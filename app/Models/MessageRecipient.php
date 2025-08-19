<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'contact_id',
        'status',
        'error_message',
        'twilio_sid',
        'twilio_status',
        'cost',
        'cost_currency',
        'carrier',
        'country_code',
        'delivered_at',
        'read_at',
        'attempts',
        'last_attempt_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'attempts' => 'integer',
        'cost' => 'decimal:4',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_READ = 'read';
    const STATUS_FAILED = 'failed';
    const STATUS_UNDELIVERED = 'undelivered';

    // Twilio status mapping
    const TWILIO_STATUS_MAP = [
        'queued' => self::STATUS_SENT,      // Twilio accepted the message
        'sending' => self::STATUS_SENT,     // Twilio is sending the message
        'sent' => self::STATUS_SENT,        // Message was sent
        'delivered' => self::STATUS_DELIVERED, // Message was delivered
        'undelivered' => self::STATUS_UNDELIVERED,
        'failed' => self::STATUS_FAILED,
        'canceled' => self::STATUS_FAILED,
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // Helper methods
    public function isDelivered()
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_READ]);
    }

    public function isFailed()
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_UNDELIVERED]);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getStatusColor()
    {
        return match($this->status) {
            self::STATUS_DELIVERED, self::STATUS_READ => 'green',
            self::STATUS_SENT => 'blue',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_FAILED, self::STATUS_UNDELIVERED => 'red',
            default => 'gray'
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            self::STATUS_DELIVERED, self::STATUS_READ => 'check-circle',
            self::STATUS_SENT => 'paper-airplane',
            self::STATUS_PENDING => 'clock',
            self::STATUS_FAILED, self::STATUS_UNDELIVERED => 'x-circle',
            default => 'question-mark-circle'
        };
    }

    // Calculate cost for this recipient
    public function calculateCost(): float
    {
        $pricing = \App\Models\SmsPricing::getPricing($this->country_code ?? 'US', $this->carrier);
        if (!$pricing) {
            $pricing = \App\Models\SmsPricing::getDefaultPricing();
        }
        
        if (!$pricing) {
            return 0;
        }

        $messageLength = strlen($this->message->content);
        return $pricing->calculateCost($messageLength);
    }

    // Get formatted cost
    public function getFormattedCost(): string
    {
        return '$' . number_format($this->cost, 4);
    }

    // Update cost for this recipient
    public function updateCost(): void
    {
        $this->cost = $this->calculateCost();
        $this->cost_currency = 'USD';
        $this->save();
    }
}
