<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Contact extends Model
{
    use HasApiTokens, SoftDeletes;

    protected $guarded = ['id'];
    protected $appends = ['fullName'];

    protected $hidden = ['password', 'api_token'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }
}
