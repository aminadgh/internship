<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    protected $fillable = [
        'name',
        'area_name',
        'user_id',
        'geometry',
    ];
    protected $casts = [
        'geometry' => 'array',
    ];
    
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'group_id');
    }
}
