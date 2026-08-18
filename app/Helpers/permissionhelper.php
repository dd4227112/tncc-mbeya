<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('hasPermission')) {
    /**
     * Check if the currently authenticated user's role(s) have a given permission.
     *
     * @param string $permissionName
     * @return bool
     */
    function hasPermission(string $permissionName): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Cache permissions for this user for the duration of the request
        // to avoid running the query multiple times on the same page load.
        static $cache = [];

        $cacheKey = $user->id;

        if (! isset($cache[$cacheKey])) {
            $cache[$cacheKey] = $user->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('name')
                ->unique()
                ->values()
                ->toArray();
        }

        return in_array($permissionName, $cache[$cacheKey]);
    }
}

if (! function_exists('isValidPhone')) {
    function isValidPhone(string $phone): bool
    {
        return preg_match('/^\+255[0-9]{9}$/', $phone) === 1;
    }
}
