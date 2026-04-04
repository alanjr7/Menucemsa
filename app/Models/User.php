<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
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
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    public function isDirMedico(): bool
    {
        return $this->role === 'dirmedico';
    }

    public function isEmergencia(): bool
    {
        return $this->role === 'emergencia';
    }

    public function isCaja(): bool
    {
        return $this->role === 'caja';
    }

    public function isGerente(): bool
    {
        return $this->role === 'gerente';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isFarmacia(): bool
    {
        return $this->role === 'farmacia';
    }

    public function isUti(): bool
    {
        return $this->role === 'uti';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function medico()
    {
        return $this->hasOne(Medico::class, 'id_usuario', 'id');
    }

    public function cajaSessions()
    {
        return $this->hasMany(CajaSession::class, 'usuario_id');
    }
}
