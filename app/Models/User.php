<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable implements FilamentUser, HasName, HasAvatar
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
        'picture',
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

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

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

    public function getFilamentAvatarUrl(): ?string
    {
        // TODO: Implement getFilamentAvatarUrl() method.
        return asset($this->picture ? 'storage/' . $this->picture : 'assets/defaultProfile.jpg');
    }

    public function mengerjakan()
    {
        return $this->belongsToMany(MateriTugas::class, 'mengerjakan', 'users_id', 'materi_tugas_id')
            ->withPivot('files', 'pesan_peserta', 'penilaian', 'pesan_admin', 'file_name', 'status', 'id')
            ->using(Tugas::class)
            ->withTimestamps();
    }

    public function kuis()
    {
        return $this->mengerjakan()->wherePivot('is_kuis', true);
    }

    public function mendaftar()
    {
        return $this->belongsToMany(Pelatihan::class, 'daftarPeserta', 'users_id', 'pelatihan_id')
            ->whereNot('status', 'diterima')
            ->withPivot('status', 'files', 'file_name', 'pesan', 'created_at')
            ->withTimestamps();
    }

    public function peserta()
    {
        return $this->belongsToMany(Pelatihan::class, 'daftarPeserta', 'users_id', 'pelatihan_id')
            ->wherePivotIn('status', ['diterima', 'selesai'])
            ->withPivot('status', 'files', 'file_name', 'pesan', 'created_at');
    }

    public function kelulusan()
    {
        return $this->belongsToMany(Pelatihan::class, 'daftarPeserta', 'users_id', 'pelatihan_id')
            ->wherePivotIn('status', ['selesai', 'tidak_selesai', 'diterima'])
            ->withPivot('status', 'files', 'file_name', 'pesan', 'created_at');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'causer_id');
    }

    public function sertifikat()
    {
        return $this->hasMany(Sertifikat::class, 'users_id', 'id');
    }

    public function materiTugas()
    {
        return $this->hasManyThrough(Modul::class, Pelatihan::class);
    }
}
