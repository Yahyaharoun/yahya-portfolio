<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string $email
 * @property string|null $organization
 * @property string $motive
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referrer
 * @property string|null $country_code
 * @property string|null $cv_version
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class CvDownload extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'organization',
        'motive',
        'ip_address',
        'user_agent',
        'referrer',
        'country_code',
        'cv_version',
    ];

    protected function casts(): array
    {
        return [];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeRecruiters(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('motive', 'recruitment');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeThisMonth(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
