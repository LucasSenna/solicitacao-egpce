<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'capacity',
        'active',
        'sort_order',
    ];
}
