<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'full_name',
        'phone_number',
        'group_id',
        'user_id',
    ];

    public function group()
    {
        return $this->belongsTo(ContactGroup::class, 'group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messageRecipients()
    {
        return $this->hasMany(MessageRecipient::class);
    }
}
