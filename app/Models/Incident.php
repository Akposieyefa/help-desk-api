<?php

namespace App\Models;

use Database\Factories\IncidentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'data',
        'title',
        'description',
        'user_id',
        'acting_officer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function newFactory(): IncidentFactory
    {
        return IncidentFactory::new();
    }
}
