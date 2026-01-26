<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimEntity extends Model
{
    protected $table = 'claim_entities';
    
    protected $fillable = ['name', 'type', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
