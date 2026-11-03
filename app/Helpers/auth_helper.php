<?php

if (!function_exists('role_level')) {
    function role_level(?string $role): int
    {
        return ['editor' => 1, 'admin' => 2, 'superadmin' => 3][$role] ?? 0;
    }
}

if (!function_exists('can')) {
    /** ¿Puede el usuario actual gestionar algo que requiere $role? */
    function can(string $role): bool
    {
        return role_level(session('role')) >= role_level($role);
    }
}