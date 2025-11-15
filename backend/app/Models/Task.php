<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Task extends Model
{
    use HasUuids; // Habilita o uso de UUID

    // Desabilita o auto incremento
    public $incrementing = false; 

    protected $fillable = [
        'id', 'user_id', 'prompt', 'status', 'result'
    ];
    
    protected $casts = [
        'result' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
