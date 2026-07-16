<?php

namespace App\Models;

use App\Enums\CompanySector;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'sector', 'size', 'legal_name', 'siret', 'ape_code', 'address', 'phone', 'email', 'logo', 'timezone'])]
class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sector' => CompanySector::class,
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function punchDevices(): HasMany
    {
        return $this->hasMany(PunchDevice::class);
    }

    public function punches(): HasMany
    {
        return $this->hasMany(Punch::class);
    }
}
