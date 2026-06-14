<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string|null $long_description
 * @property array<int, string> $tech_stack
 * @property array<int, array{url: string, caption: string, order: int}> $screenshots
 * @property string|null $github_url
 * @property string|null $demo_url
 * @property string|null $thumbnail
 * @property string $status
 * @property string $visibility
 * @property bool $is_featured
 * @property int $sort_order
 * @property int $view_count
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
final class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'long_description',
        'tech_stack',
        'screenshots',
        'github_url',
        'demo_url',
        'thumbnail',
        'status',
        'visibility',
        'is_featured',
        'sort_order',
        'view_count',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack'   => 'array',
            'screenshots'  => 'array',
            'is_featured'  => 'boolean',
            'sort_order'   => 'integer',
            'view_count'   => 'integer',
            'started_at'   => 'date',
            'completed_at' => 'date',
        ];
    }

    /** @return BelongsToMany<ProjectTag, $this> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProjectTag::class, 'project_tag');
    }

    public function isRealized(): bool
    {
        return $this->status === 'realized';
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Scope: only publicly visible, realized projects.
     *
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('visibility', 'public')
                     ->whereNotIn('status', ['archived']);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeFeatured(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_featured', true);
    }
}
