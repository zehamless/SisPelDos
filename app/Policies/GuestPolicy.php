<?php

namespace App\Policies;

use App\Models\Pelatihan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuestPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {

    }

    public function view(User $user, Pelatihan $pelatihan): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Pelatihan $pelatihan): bool
    {
    }

    public function delete(User $user, Pelatihan $pelatihan): bool
    {
    }

    public function restore(User $user, Pelatihan $pelatihan): bool
    {
    }

    public function forceDelete(User $user, Pelatihan $pelatihan): bool
    {
    }
}
