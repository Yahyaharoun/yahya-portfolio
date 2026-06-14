<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $target_platform
 * @property int $click_count
 * @property \Illuminate\Support\Carbon $tracked_date
 * @property int $tracked_hour
 * @property string|null $country_code
 * @property Carbon $last_updated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class ClickTracking extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'target_platform',
        'click_count',
        'tracked_date',
        'tracked_hour',
        'country_code',
        'last_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'tracked_date'    => 'date',
            'click_count'     => 'integer',
            'tracked_hour'    => 'integer',
            'last_updated_at' => 'datetime',
        ];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeForPlatform(
        \Illuminate\Database\Eloquent\Builder $query,
        string $platform
    ): \Illuminate\Database\Eloquent\Builder {
        return $query->where('target_platform', $platform);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeToday(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereDate('tracked_date', today());
    }
}
