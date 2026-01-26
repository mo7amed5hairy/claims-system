<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = ['name', 'hospital_id'];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }
}
