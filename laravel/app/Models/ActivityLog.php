<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'action',
        'target_type', 'target_id', 'target_label',
        'meta', 'ip_address',
    ];

    protected $casts = ['meta' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an audit event. Never throws — logging must never break a user-facing action.
     *
     * @param string     $action  e.g. 'company.created', 'assessment.closed'
     * @param Model|null $target  the affected Eloquent model instance
     * @param string     $label   human-readable label for the target
     * @param array      $meta    extra key/value context (will be JSON-encoded)
     * @param int|null   $companyId  defaults to the authenticated user's company_id
     */
    public static function record(
        string $action,
        ?Model $target = null,
        string $label = '',
        array $meta = [],
        ?int $companyId = null
    ): void {
        $user      = auth()->user();
        $companyId = $companyId ?? $user?->company_id;

        try {
            static::create([
                'user_id'      => $user?->id,
                'company_id'   => $companyId,
                'action'       => $action,
                'target_type'  => $target ? class_basename($target) : null,
                'target_id'    => $target?->getKey(),
                'target_label' => $label ?: ($target ? (string) $target->getKey() : null),
                'meta'         => $meta ?: null,
                'ip_address'   => Request::ip(),
            ]);
        } catch (\Throwable) {
            // Silently swallow — audit log failure must never interrupt the request
        }
    }
}
