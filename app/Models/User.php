<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ADMIN_PROFILE_FULL_ACCESS = 'full_access';

    public const ADMIN_PROFILE_MUNICIPALITY_ONLY = 'municipality_only';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'admin_profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'admin_profile' => 'string',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }

    public static function adminProfileOptions(): array
    {
        return [
            self::ADMIN_PROFILE_FULL_ACCESS => 'Acesso completo (estado, município e cessão de espaço)',
            self::ADMIN_PROFILE_MUNICIPALITY_ONLY => 'Somente solicitações de município',
        ];
    }

    public function getAdminProfileAttribute(?string $value): string
    {
        return $value === self::ADMIN_PROFILE_MUNICIPALITY_ONLY
            ? self::ADMIN_PROFILE_MUNICIPALITY_ONLY
            : self::ADMIN_PROFILE_FULL_ACCESS;
    }

    public function canManageAllRequestTypes(): bool
    {
        return (bool) $this->is_admin && $this->admin_profile === self::ADMIN_PROFILE_FULL_ACCESS;
    }

    public function isMunicipalityOnlyAdmin(): bool
    {
        return (bool) $this->is_admin && $this->admin_profile === self::ADMIN_PROFILE_MUNICIPALITY_ONLY;
    }

    public function canAccessTrainingRequests(): bool
    {
        return (bool) $this->is_admin;
    }

    public function canAccessSpaceRequests(): bool
    {
        return $this->canManageAllRequestTypes();
    }

    public function canAccessTrainingRequest(?TrainingRequest $request = null): bool
    {
        if (! $this->canAccessTrainingRequests()) {
            return false;
        }

        if (! $request) {
            return true;
        }

        if ($this->isMunicipalityOnlyAdmin()) {
            return $request->isMunicipality();
        }

        return true;
    }

    public function applyTrainingRequestsScope(Builder $query): Builder
    {
        if ($this->isMunicipalityOnlyAdmin()) {
            return $query->municipality();
        }

        return $query;
    }
}
