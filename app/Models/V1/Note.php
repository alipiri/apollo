<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $guarded = ['id'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
