<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SpaceRequest extends Model
{
    protected $table = 'space_requests';

    protected $fillable = [
        'institution_name',

        'responsible_name',
        'responsible_role',
        'responsible_email',
        'responsible_phone',

        'event_title',
        'start_date',
        'end_date',
        'time_slot',
        'participants_quantity',

        'objective',
        'target_audience',

        'general_notes',
        'selected_spaces',
        'selected_spaces_snapshot',
        'accepted_terms_at',
        'responsibility_term_path',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'accepted_terms_at' => 'datetime',
        'participants_quantity' => 'integer',
        'selected_spaces' => 'array',
        'selected_spaces_snapshot' => 'array',
    ];

    public function spaces(): BelongsToMany
    {
        return $this->belongsToMany(Space::class, 'space_request_space');
    }
}
