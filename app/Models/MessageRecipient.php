<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{
    protected $fillable = [
        'message_id',
        'contact_id',
        'status',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
