<?php

namespace App\Modules\Claims\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes model queries to the current user's own records unless the user
 * is allowed to view all data (admin or can_view_all_data checkbox).
 *
 * - scopeVisibleTo(): applied to listing/report queries
 * - resolveRouteBinding(): blocks direct access to other users' records via URL
 */
trait ScopedByOwner
{
    /**
     * Restrict a query to records owned by $user (user_id = $user->id)
     * unless $user can view all data.
     */
    public function scopeVisibleTo(Builder $query, $user = null): Builder
    {
        if ($user instanceof \App\Models\User && !$user->canViewAllData()) {
            $query->where($this->getTable() . '.user_id', $user->id);
        }

        return $query;
    }

    /**
     * Resolve route model binding while preserving the existing global scopes
     * (e.g. is_prepaid) and enforcing per-user ownership for restricted users.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this->newQuery();

        if ($field) {
            $query->where($field, $value);
        } else {
            $query->where($this->getRouteKeyName(), $value);
        }

        if (auth()->check() && !auth()->user()->canViewAllData()) {
            $query->where($this->getTable() . '.user_id', auth()->id());
        }

        return $query->firstOrFail();
    }
}