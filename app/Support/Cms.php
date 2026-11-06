<?php

namespace App\Support;

class Cms
{
    /** CMS website hanya untuk super_admin (atau admin lama yang belum punya role apa pun). */
    public static function allowed(): bool
    {
        $u = auth()->user();

        return $u && ($u->hasRole('super_admin') || $u->roles()->count() === 0);
    }
}
