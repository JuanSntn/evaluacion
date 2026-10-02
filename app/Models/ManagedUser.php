<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagedUser extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'rfc',
        'rfc_hash',
        'address',
        'phone',
        'website',
    ];

    #cifrado de los campos sensibles para guardarlos
    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'rfc' => 'encrypted',
            'address' => 'encrypted',
            'phone' => 'encrypted',
            'website' => 'encrypted',
        ];
    }


    #administrar quien creo el usuario, para que solo el creador pueda editarlo o eliminarlo
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
