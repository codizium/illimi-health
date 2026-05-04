<?php

namespace Illimi\Health\Policies;

use Codizium\Core\Models\User;

class HealthPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, $this->allowedRoles());
    }

    public function view(User $user, object $record): bool
    {
        if ($this->hasRole($user, ['parent'])) {
            $student = $record->student ?? null;

            return (bool) $student?->parents()?->where('users.id', $user->id)->exists();
        }

        return $this->hasRole($user, array_values(array_diff($this->allowedRoles(), ['parent'])));
    }

    public function viewDashboard(User $user): bool
    {
        return $this->hasRole($user, $this->healthStaffRoles());
    }

    public function viewDueImmunizations(User $user): bool
    {
        return $this->hasRole($user, $this->healthStaffRoles());
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, $this->healthStaffRoles());
    }

    public function escalate(User $user): bool
    {
        return $this->hasRole($user, $this->healthStaffRoles());
    }

    protected function allowedRoles(): array
    {
        return (array) config('illimi-health.allowed_roles', ['nurse', 'admin', 'principal', 'parent']);
    }

    protected function healthStaffRoles(): array
    {
        $managementRoles = (array) config('illimi-health.management_roles', ['admin', 'principal']);

        return array_values(array_unique(array_merge(['nurse'], $managementRoles)));
    }

    protected function hasRole(User $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return true;
            }

            if (($user->role ?? null) === $role) {
                return true;
            }
        }

        return false;
    }
}
