<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $cover_image
 * @property int $sort_order
 * @property bool $is_visible
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class MediaCategory extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'sort_order',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_visible'  => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    /** @return HasMany<MediaGallery, $this> */
    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaGallery::class);
    }

    /** @return HasMany<MediaGallery, $this> */
    public function photos(): HasMany
    {
        return $this->hasMany(MediaGallery::class)->where('type', 'photo');
    }

    /** @return HasMany<MediaGallery, $this> */
    public function videos(): HasMany
    {
        return $this->hasMany(MediaGallery::class)->where('type', 'video');
    }
}
