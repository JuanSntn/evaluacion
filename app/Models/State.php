<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'clave',
        'clave_curp',
        'nombre',
    ];

    public function collaborators(): HasMany
    {
        return $this->hasMany(Collaborator::class);
    }
}
