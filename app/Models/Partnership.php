<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $company
 * @property string|null $logo_path
 * @property string|null $website
 * @property string $type
 * @property string|null $contact_name
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string $message
 * @property string $status
 * @property string|null $admin_notes
 * @property string|null $ip_address
 * @property Carbon|null $treated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
final class Partnership extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'company',
        'logo_path',
        'website',
        'type',
        'contact_name',
        'contact_email',
        'contact_phone',
        'message',
        'status',
        'admin_notes',
        'ip_address',
        'treated_at',
    ];

    protected function casts(): array
    {
        return [
            'treated_at' => 'datetime',
        ];
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isTreated(): bool
    {
        return $this->status === 'treated';
    }

    public function markAsTreated(): bool
    {
        return $this->update([
            'status'     => 'treated',
            'treated_at' => now(),
        ]);
    }

    public function markInProgress(): bool
    {
        return $this->update(['status' => 'in_progress']);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<self> $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopePending(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereIn('status', ['new', 'in_progress']);
    }
}
