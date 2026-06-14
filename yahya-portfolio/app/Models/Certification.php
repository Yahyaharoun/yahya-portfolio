<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $organization
 * @property string|null $credential_id
 * @property Carbon $issued_at
 * @property Carbon|null $expires_at
 * @property string|null $image_path
 * @property string|null $verification_url
 * @property string $type
 * @property bool $is_featured
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
final class Certification extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'organization',
        'credential_id',
        'issued_at',
        'expires_at',
        'image_path',
        'verification_url',
        'type',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'issued_at'   => 'date',
            'expires_at'  => 'date',
            'is_featured' => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isVerifiable(): bool
    {
        return filled($this->verification_url);
    }
}
