<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasApiTokens, SoftDeletes;
    protected $guarded = ['id'];

    protected $hidden = ['password', 'api_token'];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
