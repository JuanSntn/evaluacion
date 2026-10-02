<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collaborator extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'correo',
        'correo_hash',
        'rfc',
        'rfc_hash',
        'domicilio_fiscal',
        'curp',
        'curp_hash',
        'numero_seguridad_social',
        'nss_hash',
        'fecha_inicio_laboral',
        'tipo_contrato',
        'departamento',
        'puesto',
        'salario_diario',
        'salario',
        'state_id',
    ];

    protected $hidden = [
        'correo_hash',
        'rfc_hash',
        'curp_hash',
        'nss_hash',
    ];

    protected function casts(): array
    {
        return [
            'nombre' => 'encrypted',
            'correo' => 'encrypted',
            'rfc' => 'encrypted',
            'domicilio_fiscal' => 'encrypted',
            'curp' => 'encrypted',
            'numero_seguridad_social' => 'encrypted',
            'tipo_contrato' => 'encrypted',
            'departamento' => 'encrypted',
            'puesto' => 'encrypted',
            'salario_diario' => 'encrypted',
            'salario' => 'encrypted',
            'fecha_inicio_laboral' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
