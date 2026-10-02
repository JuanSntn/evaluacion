<?php

namespace App\Models;

use App\Support\BlindIndex;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['role_id', 'name', 'email', 'rfc', 'password', 'address', 'phone', 'website'])]
#[Hidden(['password', 'email_hash', 'rfc_hash'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if ($user->isDirty('email')) {
                $user->email_hash = BlindIndex::email($user->email);
            }

            if ($user->isDirty('rfc')) {
                $user->rfc_hash = BlindIndex::rfc($user->rfc);
            }
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(Collaborator::class);
    }

    public function managedUsers(): HasMany
    {
        return $this->hasMany(ManagedUser::class, 'created_by');
    }

    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'email' => 'encrypted',
            'rfc' => 'encrypted',
            'address' => 'encrypted',
            'phone' => 'encrypted',
            'website' => 'encrypted',
            'password' => 'hashed',
        ];
    }
}
