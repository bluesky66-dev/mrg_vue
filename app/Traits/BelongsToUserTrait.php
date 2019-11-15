<?php

namespace Momentum\Traits;

use Auth;
use Momentum\User;

/**
 * Trait that adds basic relationship functionality between 
 * a model and a user.
 *
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4
 */
trait BelongsToUserTrait
{
    /**
     * Returns scoped query, filtered it by user.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * @param User                                  $user  User to filter.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
    /**
     * Returns scoped query, filtered by current user.
     * @since 0.2.4
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentUser($query)
    {
        $user = Auth::user();
        return $user ? $query->where('user_id', $user->id) : $query->where('id', null);
    }
    /**
     * Returns flag indicating if the model belongs to the current user.
     * @since 0.2.4
     * 
     * @return bool
     */
    public function belongsToCurrentUser()
    {
        $user = Auth::user();
        return $user ? $this->user_id == $user->id : false;
    }
}