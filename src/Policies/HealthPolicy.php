<?php

namespace Illimi\Health\Policies;

use Codizium\Core\Models\User;

class HealthPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, ['nurse', 'admin', 'principal', 'parent']);
    }

    public function view(User $user, object $record): bool
    {
        if ($this->hasRole($user, ['nurse', 'admin', 'principal'])) {
            return true;
        }

        if (!$this->hasRole($user, ['parent'])) {
            return false;
        }

        $student = $record->student ?? null;

        return (bool) $student?->parents()?->where('users.id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, ['nurse', 'admin', 'principal']);
    }

    public function escalate(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'principal', 'nurse']);
    }

    protected function hasRole(User $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}
