<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrainingRequest extends Model
{
    public const SCOPE_STATE = 'state';

    public const SCOPE_MUNICIPALITY = 'municipality';

    protected $table = 'training_requests';

    protected $fillable = [
        'protocol',
        'request_scope',
        'institution_name',
        'holder_name',
        'holder_role',
        'requester_name',
        'requester_role',
        'requester_phone',
        'requester_email',
        'event_type',
        'participants_count',
        'training_type',
        'class_type',
        'target_audience',
        'leaders_participation',
        'objectives',
        'content_expectation',
        'request_letter_path',
        'status',
        'admin_notes',
        'terms_accepted',
    ];

    protected $casts = [
        'request_scope' => 'string',
        'participants_count' => 'integer',
        'leaders_participation' => 'boolean',
        'terms_accepted' => 'boolean',
    ];

    public static function scopeOptions(): array
    {
        return [
            self::SCOPE_STATE => 'Estado',
            self::SCOPE_MUNICIPALITY => 'Município',
        ];
    }

    public static function scopeLabel(?string $scope): string
    {
        if ($scope === self::SCOPE_MUNICIPALITY) {
            return self::scopeOptions()[self::SCOPE_MUNICIPALITY];
        }

        return self::scopeOptions()[self::SCOPE_STATE];
    }

    public function scopeForScope(Builder $query, ?string $scope): Builder
    {
        if ($scope === self::SCOPE_MUNICIPALITY) {
            return $query->where('request_scope', self::SCOPE_MUNICIPALITY);
        }

        if ($scope === self::SCOPE_STATE) {
            return $query->where(function (Builder $query): void {
                $query
                    ->where('request_scope', self::SCOPE_STATE)
                    ->orWhereNull('request_scope');
            });
        }

        return $query;
    }

    public function scopeState(Builder $query): Builder
    {
        return $query->forScope(self::SCOPE_STATE);
    }

    public function scopeMunicipality(Builder $query): Builder
    {
        return $query->forScope(self::SCOPE_MUNICIPALITY);
    }

    public function getRequestScopeAttribute(?string $value): string
    {
        return $value === self::SCOPE_MUNICIPALITY
            ? self::SCOPE_MUNICIPALITY
            : self::SCOPE_STATE;
    }

    public function getScopeLabelAttribute(): string
    {
        return self::scopeLabel($this->request_scope);
    }

    public function isState(): bool
    {
        return $this->request_scope === self::SCOPE_STATE;
    }

    public function isMunicipality(): bool
    {
        return $this->request_scope === self::SCOPE_MUNICIPALITY;
    }
}
