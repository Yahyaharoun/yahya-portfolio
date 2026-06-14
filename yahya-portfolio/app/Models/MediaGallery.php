<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $media_category_id
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $captured_at
 * @property string|null $location
 * @property string $type
 * @property string $filepath
 * @property string|null $thumbnail_path
 * @property int|null $file_size
 * @property string|null $mime_type
 * @property int|null $width
 * @property int|null $height
 * @property int|null $duration_seconds
 * @property array<string, mixed>|null $exif_data
 * @property bool $is_featured
 * @property bool $is_visible
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
final class MediaGallery extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'media_category_id',
        'title',
        'description',
        'captured_at',
        'location',
        'type',
        'filepath',
        'thumbnail_path',
        'file_size',
        'mime_type',
        'width',
        'height',
        'duration_seconds',
        'exif_data',
        'is_featured',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'captured_at'      => 'date',
            'exif_data'        => 'array',
            'is_featured'      => 'boolean',
            'is_visible'       => 'boolean',
            'file_size'        => 'integer',
            'width'            => 'integer',
            'height'           => 'integer',
            'duration_seconds' => 'integer',
            'sort_order'       => 'integer',
        ];
    }

    /** @return BelongsTo<MediaCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MediaCategory::class, 'media_category_id');
    }

    public function isPhoto(): bool
    {
        return $this->type === 'photo';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function humanFileSize(): string
    {
        if ($this->file_size === null) {
            return 'unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($this->file_size, 1024));

        return number_format($this->file_size / (1024 ** $power), 1).' '.$units[$power];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeVisible(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_visible', true);
    }
}
