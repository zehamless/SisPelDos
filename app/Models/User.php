<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_induk',
        'no_hp',
        'jenis_kelamin',
        'universitas',
        'prodi',
        'link',
        'jabatan_fungsional',
        'pendidikan_tertinggi',
        'status_kerja',
        'status_dosen',
        'status_akun',
        'pembayaran',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }
        return $this->role === 'Internal' || $this->role === 'External' || $this->role === 'admin';
    }

    public function getFilamentName(): string
    {
        // TODO: Implement getFilamentName() method.
        return $this->nama;
    }

    public function mengerjakan()
    {
        return $this->belongsToMany(MateriTugas::class, 'mengerjakan', 'users_id', 'materi_tugas_id')
            ->withPivot('files', 'pesan', 'penilaian')
            ->withTimestamps();
    }
    public function mendaftar()
    {
        return $this->belongsToMany(Pelatihan::class, 'mendaftar', 'users_id', 'pelatihan_id')
            ->withPivot('status', 'files','file_name', 'pesan', 'created_at', 'nama', 'role')
            ->withTimestamps();
    }
}
